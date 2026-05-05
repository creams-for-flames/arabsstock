<?php

namespace App\Http\Controllers\Vectors;

use App\Http\Controllers\Controller;
use App\Models\AdminVectorSettings;
use App\Models\CategoryImage;
use App\Models\FlexSubscription;
use App\Models\FreeDownload;
use App\Models\ImageTag;
use App\Models\Plan;
use App\Models\Query;
use App\Models\Image;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vector;
use App\Models\ImagesReported;
use App\Models\Stock;
use App\Models\Notifications;
use App\Models\CollectionImage;
use App\Models\VectorPlan;
use App\Models\VectorDownload;
use App\Models\Purchase;
use App\Models\Contributor;
use App\Models\AccountLedger;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;


class VectorsController extends Controller
{

    public function __construct(AdminVectorSettings $settings, Request $request)
    {
        $this->settings = $settings::first();
        $this->request = $request;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id, $slug = null)
    {
        $array = explode('-', $id);
        if (!(isset($array[1]) && is_numeric($array[1]))) {
            abort(404);
        }

        $id = $array[1];

        $lang = app()->getLocale();
        $vector = Vector::with('category')
            ->where('status', 'active')
            ->select('vectors.*')
            ->where('vectors.id', $id)
            ->withoutGlobalScope('reserved')
            ->withoutGlobalScope('not_deleted')
            ->first();

        if (!$vector)
            return redirect(route('landPage'), 301);
        if ($vector->status != 'active' && auth()->check() && ($vector->downloads()->where('user_id', \auth()->id())->count() == 0 && $vector->old_downloads()->where('user_id', \auth()->id())->count() == 0))
            return redirect(route('landPage'), 301);


        if (Auth::check() && $vector->user_id != Auth::user()->id && $vector->status == 'pending' && Auth::user()->role != 'admin') {
            abort(404);
        } else {
            if (Auth::guest() && $vector->status == 'pending') {
                abort(404);
            }
        }
        if (request()->url() != $vector->post_link)
            return response()->redirectTo($vector->post_link, 301)->send();

        $vector->count_view = $vector->count_view + 1;
        $vector->save();
        $embedding_watermark = config('services.embedding_watermark.host').config('services.embedding_watermark.endpoint');

        $data = cache()->remember("vector_show_{$id}_{$lang}", 60, function () use ($id, $lang, $vector) {

            $tags = $vector
                ->tags()
                ->where('local', app()->getLocale())->get();

            $user_subscription_remaining_array = Query::user_subscription_remaining();
            $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
            $days_left = $user_subscription_remaining_array['days_left'];
            $title_plan = $user_subscription_remaining_array['title_plan'];

            try {
                $same_group = $vector->from_same_group();
                $except = $same_group->pluck('id')->toArray();
                $except[] = $vector->id;
                $same_group = $same_group->take(15);
                $title = $vector->title;
                $vectors = \App\Helper::similar_search_in_elasticsearch('vectors', $title, ["not_in_ids" => $except], 100);
                $simler_vectors = collect($vectors->items())->shuffle()->take(8);
                $simler_images = \App\Helper::similar_search_in_elasticsearch('images', $title, [], 8);
                $simler_videos = \App\Helper::similar_search_in_elasticsearch('videos', $title, [], 8);
            } catch (\Throwable $th) {
                $vectors = collect([]);
                $simler_images = collect([]);
                $simler_videos = collect([]);
                $simler_vectors = collect([]);
                $same_group = $vector->from_same_group([]);
                \Log::error($th);
            }

            $plan = VectorPlan::where('status', true)
                ->where('type', 'monthly')
                ->orderBy('price', 'asc')
                ->select('downloads_count', 'price')
                ->first();
            $plan_price_one_file = $plan ? number_format($plan->price / $plan->downloads_count, 2, '.', '') : '';

            return compact('vector', 'tags', 'simler_vectors', 'simler_videos', 'simler_images', 'user_subscription_remaining', 'days_left', 'title_plan', 'plan_price_one_file', 'same_group');
        });
        $data['embedding_watermark'] = $embedding_watermark;

        return view('vector.show', $data);


    }//<--- End Method

    public function report(Request $request)
    {

        $data = ImagesReported::firstOrNew(['user_id' => Auth::user()->id, 'image_id' => $request->id]);

        if ($data->exists) {
            \Session::flash('noty_error', 'error');
            return redirect()->back();
        } else {

            $data->reason = $request->reason;
            $data->save();
            \Session::flash('noty_success', 'success');
            return redirect()->back();
        }

    }//<--- End Method

    public function samegroup(Vector $vector)
    {
        $results = $vector->from_same_group([], true)->paginate(106);
        return view('vector.samegroup', compact('vector', 'results'));
    }

    public function sameuser(Vector $vector)
    {
        $user = $vector->user;
        if (!$user)
            abort(404);
        $results = $user->created_vectors()->where('status', 'active')->orderBy('date', 'desc')->paginate(106);
        return view('vector.sameuser', compact('vector', 'results', 'user'));
    }

    public function download(Request $request, $token_id)
    {
        /**@var $vector Vector */
        /**@var $user User */
        $vector = Vector::where('token_id', $token_id)->firstOrFail();
        $user = auth()->user();
        if ($user->free_vectors) {
            FreeDownload::updateOrCreate([
                'entity_id' => $vector->id,
                'entity_type' => Vector::class,
                'user_id' => $user->id,
            ], [
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return $this->downloadFile($vector, \request('type', 'vector'));
        }

        $vector_subscription = $user->active_vector_subscriptions()->first();
        if ($vector_subscription && $request->license_type == 'standard')
            return $this->old_download($vector, $vector_subscription);
        $validator = Validator::make($request->all(), [
            'license_type' => ['required', Rule::in(['standard', 'enhanced', 'exclusive'])],
            'type' => ['required', Rule::in(['vector', 'image'])],
        ]);
        if ($validator->fails())
            return redirect()->to($vector->postLink);

        if ($request->license_type == 'exclusive' && !$vector->can_reserve())
            return redirect()->to($vector->post_link)->with('error', __('Cant reserve'));
        if ($request->subscription_type == 'team_subscriptions') {
            if (Subscription::download($vector, $request->license_type, 1))
                return $this->downloadFile($vector, \request('type', 'large'));
            return redirect()->to($vector->post_link)->with('error', __('No enough subscriptions'));
        }
        if (Subscription::download($vector, $request->license_type))
            return $this->downloadFile($vector, $request->type);
        if ($request->plan_id) {
            $plan_id = Plan::findOrFail($request->plan_id)->id;
            session()->put('redirect_after_purchase', $vector->postLink);
            session()->put('open_download_options', [
                'license_type' => $request->license_type,
            ]);
            return redirect()->route('purchase',
                [
                    'plan_id' => $plan_id,
                ]);
        }
        return redirect()->to($vector->post_link)->with('error', __('No enough subscriptions'));
    }

    public function redownload(Request $request, $token_id)
    {
        /**@var $vector Vector */
        $vector = Vector::where('token_id', $token_id)->firstOrFail();
        $user = auth()->user();
        if ($user->is_downloaded($vector)) {
            return $this->downloadFile($vector, 'vector');
        }
        abort(404);
    }

    private function downloadFile($vector, $type)
    {
        cache()->forget("vector_show_{$vector->id}_ar");
        cache()->forget("vector_show_{$vector->id}_en");
        if ($type === 'image') {
            $nameFile = getNameFromUrl($vector->large);
            $path = $vector->large;
        } elseif ($type === 'vector') {
            $nameFile = getNameFromUrl($vector->vector);
            $path = $vector->vector;
        }
        $extension = pathinfo($nameFile, PATHINFO_EXTENSION);
        $filename = "arabsstock_I{$vector->id}_{$type}.{$extension}";
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        if (Storage::disk('s3')->getVisibility($path) == 'public')
            Storage::disk('s3')->setVisibility($path, 'private');
        $url = Storage::disk('s3')->temporaryUrl(
            $path, now()->addHours(1),
            [
                'url' => 'https://cdn.arabsstock.com',
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                'Expires' => '0',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ]
        );
        if (app()->isLocal())
            return redirect()->to($url);
        return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));
    }

    private function old_download($vector, $subscription)
    {
        $subscription->download_remaining = $subscription->download_remaining - 1;
        $subscription->save();

        $download = new VectorDownload;
        $download->vector_id = $vector->id;
        $download->user_id = Auth::user()->id;
        $download->ip = request()->ip();
        $download->plan_id = $subscription->plan_id;
        $download->subscription_id = $subscription->id;
        $download->save();
        $contributor = $vector->user_type == Contributor::class ? $vector->user : null;
        if ($contributor) {//
            $profit_value = (($subscription->amount / $subscription->plan->downloads_count) * $contributor->profit_ratio) / 100;
            $purchase = new Purchase();
            $purchase->user_id = Auth::user()->id;
            $purchase->contributor_id = $contributor->id;
            $purchase->plan_id = $subscription->plan_id;
            $purchase->plan_price = $subscription->amount;
            $purchase->unit_price = ($subscription->amount / $subscription->plan->downloads_count);
            $purchase->profit_ratio = $contributor->profit_ratio;
            $purchase->profit_value = $profit_value;
            $purchase->purchaseable_id = $vector->id;
            $purchase->purchaseable_type = Vector::class;
            $purchase->save();

            // save to account ledger
            $account_ledger = new AccountLedger();
            $account_ledger->proccess = "pay";
            $account_ledger->value = $profit_value;
            $account_ledger->contributor_id = $contributor->id;
            $account_ledger->accountable_id = $purchase->id;
            $account_ledger->accountable_type = Purchase::class;
            $account_ledger->save();
        }
        return $this->downloadFile($vector, \request('type', 'vector'));
    }

    public function download_preview($id)
    {
        $vector = Vector::active()->findOrFail($id);

        $path = $vector->search_large;
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $url = Storage::disk('s3')->temporaryUrl(
            $path, now()->addMinutes(10),
            [
                'url' => 'https://cdn.arabsstock.com',
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_I{$vector->id}.{$extension}" . '"',
                'Expires' => '0',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ]
        );
        if (app()->isLocal())
            return redirect()->to($url);
        return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));
    }
}
