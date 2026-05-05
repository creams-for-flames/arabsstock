<?php

namespace App\Http\Controllers;

use App\Models\AdminImageSettings;
use App\Models\FlexSubscription;
use App\Models\FreeDownload;
use App\Models\ImageTag;
use App\Models\Plan;
use App\Models\Query;
use App\Models\Image;
use App\Models\ImagesReported;
use App\Models\ImageDownload;
use App\Models\ImagePlan;
use App\Models\Purchase;
use App\Models\Contributor;
use App\Models\AccountLedger;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ImagesController extends Controller
{

    public function __construct(AdminImageSettings $settings)
    {
        $this->settings = $settings::first();
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
        $image = Image::where('id', $id)->with('stock')->withoutGlobalScopes(['default_loaded_relations', 'reserved', 'not_deleted'])->first();
        if (!$image)
            return redirect(route('landPage'), 301);
        if ($image->status != 'active' && auth()->check() && ($image->downloads()->where('user_id', \auth()->id())->count() == 0 && $image->old_downloads()->where('user_id', \auth()->id())->count() == 0))
            return redirect(route('landPage'), 301);

        if (Auth::check() && $image->user_id != Auth::user()->id && $image->status == 'pending' && Auth::user()->role != 'admin') {
            abort(404);
        } else {
            if (Auth::guest() && $image->status == 'pending') {
                abort(404);
            }
        }
        if (request()->url() != $image->post_link)
            return response()->redirectTo($image->post_link, 301)->send();

        $image->count_view = $image->count_view + 1;
        $image->save();

        $image->model_release = ($image->contributor_image_id && Image::where('id', $image->id)->has('contributor_file.release_image')->count()) ? true : ($image->contributor_image_id ? false : true);
        $image->extension_preview = pathinfo($image->large, PATHINFO_EXTENSION) ?? "jpg";
        $embedding_watermark = config('services.embedding_watermark.host') . config('services.embedding_watermark.endpoint');
        $data = cache()->tags(['image', 'tag', "image_{$image->id}"])->remember("image_show_{$id}_{$lang}", now()->addDay(), function () use ($id, $image, $lang) {
            $tags = $image->tags()->where('local', $lang)->select('title', 'slug')->get();
            $user_subscription_remaining_array = Query::user_subscription_remaining();
            $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
            $days_left = $user_subscription_remaining_array['days_left'];
            $title_plan = $user_subscription_remaining_array['title_plan'];

            try {
                $same_group = $image->from_same_group();
                $except = $same_group->pluck('id')->toArray();
                $except[] = $image->id;
                $same_group = $same_group->take(15);
                $title = $image->title;
                $images = \App\Helper::similar_search_in_elasticsearch('images', $title, ["not_in_ids" => []], 100);
                $simler_videos = \App\Helper::similar_search_in_elasticsearch('videos', $title, [], 8);
                $simler_vectors = \App\Helper::similar_search_in_elasticsearch('vectors', $title, [], 8);
                $simler_images = collect($images->items())->shuffle()->take(8);
            } catch (\Throwable $th) {
                $images = collect([]);
                $simler_images = collect([]);
                $simler_videos = collect([]);
                $simler_vectors = collect([]);
                $same_group = $image->from_same_group([]);
                \Log::error($th);
            }

            $plan = ImagePlan::where('status', true)
                ->where('type', 'monthly')
                ->orderBy('price', 'asc')
                ->select('downloads_count', 'price')
                ->first();
            $plan_price_one_file = $plan ? number_format($plan->price / $plan->downloads_count, 2, '.', '') : '';;
            return compact('tags', 'simler_images', 'simler_videos', 'simler_vectors', 'user_subscription_remaining', 'days_left', 'title_plan', 'plan_price_one_file', 'same_group');
        });
        $data['image'] = $image;
        $user = auth()->user();
//        $team_subscriptions = $user->active_team_subscriptions()->get();
//        return $team_subscriptions;
        $data['embedding_watermark'] = $embedding_watermark;
        return view('images.show', $data);
    }//<--- End Method

    public function download(Request $request, $token_id)
    {
        /**@var $image Image */
        /**@var $user User */
        $image = Image::where('token_id', $token_id)->firstOrFail();
        $user = auth()->user();
        if ($user->free_images) {
            FreeDownload::updateOrCreate([
                'entity_id' => $image->id,
                'entity_type' => Image::class,
                'user_id' => $user->id,
            ], [
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return $this->downloadFile($image, \request('type', 'large'));
        }

        $image_subscription = $user->active_image_subscriptions()->first();
        if ($image_subscription && $request->license_type == 'standard')
            return $this->old_download($image, $image_subscription);
        $types = $image->stock->pluck('type');
        if ($image->psd)
            $types[] = 'psd';
        $validator = Validator::make($request->all(), [
            'license_type' => ['required', Rule::in(['standard', 'enhanced', 'exclusive'])],
            'type' => ['required', Rule::in($types)],
        ]);
        if ($validator->fails())
            return redirect()->to($image->postLink);

        if ($request->license_type == 'exclusive' && !$image->can_reserve())
            return redirect()->to($image->post_link)->with('error', __('Cant reserve'));
        if ($request->subscription_type == 'team_subscriptions') {
            if (Subscription::download($image, $request->license_type, 1))
                return $this->downloadFile($image, \request('type', 'large'));
            return redirect()->to($image->post_link)->with('error', __('No enough subscriptions'));
        }
        if (Subscription::download($image, $request->license_type))
            return $this->downloadFile($image, \request('type', 'large'));
        if ($request->plan_id) {
            $plan_id = Plan::findOrFail($request->plan_id)->id;
            session()->put('redirect_after_purchase', $image->postLink);
            session()->put('open_download_options', [
                'license_type' => $request->license_type,
            ]);
            return redirect()->route('purchase',
                [
                    'plan_id' => $plan_id,
                ]);
        }
        return redirect()->to($image->post_link)->with('error', __('No enough subscriptions'));
    }

    public function redownload(Request $request, $token_id)
    {
        /**@var $image Image */
        $image = Image::where('token_id', $token_id)->withoutGlobalScope('not_deleted')->firstOrFail();
        $user = auth()->user();
        if ($user->is_downloaded($image)) {
            return $this->downloadFile($image, request('type', 'large'));
        }
        abort(404);
    }

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

    public function samegroup(Image $image)
    {
        $results = $image->from_same_group([], true)->paginate(106);
        return view('images.samegroup', compact('image', 'results'));
    }

    public function sameuser(Image $image)
    {
        $user = $image->user;
        if (!$user)
            abort(404);
        $results = $user->created_images()->where('status', 'active')->orderBy('date', 'desc')->paginate(106);
        return view('images.sameuser', compact('image', 'results', 'user'));
    }

    private function downloadFile($image, $size)
    {
        cache()->forget("image_show_{$image->id}_ar");
        cache()->forget("image_show_{$image->id}_en");
        cache()->tags(["image_{$image->id}"])->flush();
        if (\request('removebg') && ($image->has_removebg && auth()->user()->is_downloaded($image, function ($q) {
                    $q->where('downloads.removebg', 1);
                }) or auth()->user()->free_images)) {
            $path = $image->removebg_image;
            $path = str_replace('\\', '/', $path);
            $path = trim($path, '/');
            if (Storage::disk('s3')->getVisibility($path) == 'public')
                Storage::disk('s3')->setVisibility($path, 'private');
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $url = Storage::disk('s3')->temporaryUrl(
                $path, now()->addHours(1),
                [
                    'url' => 'https://cdn.arabsstock.com',
                    'ResponseContentType' => 'application/octet-stream',
                    'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_P{$image->id}_removebg.{$extension}" . '"',
                    'Expires' => '0',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Pragma' => 'public',
                ]
            );
            if (app()->isLocal())
                return redirect()->to($url);
            return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));

        }
        $path = $image->{$size};
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        if (Storage::disk('s3')->getVisibility($path) == 'public')
            Storage::disk('s3')->setVisibility($path, 'private');
        $extension = $image->extension;
        if ($size == 'psd')
            $extension = 'psd';
        $url = Storage::disk('s3')->temporaryUrl(
            $path, now()->addHours(12),
            [
                'url' => 'https://cdn.arabsstock.com',
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_P{$image->id}_{$size}.{$extension}" . '"',
            ]
        );
        if (app()->isLocal())
            return redirect()->to($url);
        return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));
    }

    private function old_download($image, $subscription)
    {

        $subscription->download_remaining = $subscription->download_remaining - 1;
        $subscription->save();

        $download = new ImageDownload;
        $download->image_id = $image->id;
        $download->user_id = Auth::user()->id;
        $download->ip = request()->ip();
        $download->plan_id = $subscription->plan_id;
        $download->subscription_id = $subscription->id;
        $download->save();

        $contributor = $image->user_type == Contributor::class ? $image->user : null;

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
            $purchase->purchaseable_id = $image->id;
            $purchase->purchaseable_type = Image::class;
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
        return $this->downloadFile($image, \request('type'));
    }

    public function download_preview($id)
    {
        $image = Image::active()->findOrFail($id);

        $path = $image->preview;
        if (request('removebg') && $image->has_removebg)
            $path = $image->removebg_preview;
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $url = Storage::disk('s3')->temporaryUrl(
            $path, now()->addMinutes(10),
            [
                'url' => 'https://cdn.arabsstock.com',
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_P{$image->id}.{$extension}" . '"',
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
