<?php

namespace App\Http\Controllers\Video;

use App\Models\AccountLedger;
use App\Models\Contributor;
use App\Models\FreeDownload;
use App\Models\Image;
use App\Models\Plan;
use App\Models\Purchase;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use App\Models\CartVideo;
use App\Models\VideoDownload;
use App\Models\VideoPlan;
use App\Models\OrderVideo;
use Illuminate\Http\Request;
use App\Models\OrderItemsVideo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideosController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show($id, $slug = null)
    {
        $array = explode('-', $id);
        if (!(isset($array[1]) && is_numeric($array[1]))) {
            abort(404);
        }
        $id = $array[1];
        $lang = app()->getLocale();
        $video = Video::with('category')
            ->where('videos.video_fail', 0)
            ->where('status', 'active')->select('videos.*')
            ->where('videos.id', $id)
            ->whereNull('videos.parent_id')
            ->withoutGlobalScope('reserved')
            ->withoutGlobalScope('not_deleted')
            ->first();
        if (!$video)
            return redirect(route('landPage'), 301);
        if ($video->status != 'active' && auth()->check() && ($video->downloads()->where('user_id', \auth()->id())->count() == 0 && $video->old_downloads()->where('user_id', \auth()->id())->count() == 0))
            return redirect(route('landPage'), 301);

        if (Auth::check() && $video->user_id != Auth::user()->id && $video->status == 'pending' && Auth::user()->role != 'admin') {
            abort(404);
        } else {
            if (Auth::guest() && $video->status == 'pending') {
                abort(404);
            }
        }
        if (request()->url() != $video->post_link)
            return response()->redirectTo($video->post_link, 301)->send();
        $video->count_view = $video->count_view + 1;
        $video->save();
        $video->model_release = ($video->contributor_video_id && Video::where('id', $video->id)->has('contributor_file.release_video')->count()) ? true : ($video->contributor_video_id ? false : true);

        $data = cache()->remember("video_show_{$id}_{$lang}", 60, function () use ($id, $lang, $video) {
            try {
                $same_group = $video->from_same_group();
                $except = $same_group->pluck('id')->toArray();
                $except[] = $video->id;
                $same_group = $same_group->take(15);
                $title = $video->title;
                $videos = \App\Helper::similar_search_in_elasticsearch('videos', $title, ["not_in_ids" => $except], 100);
                $simler_videos = collect($videos->items())->shuffle()->take(8);
                $simler_images = \App\Helper::similar_search_in_elasticsearch('images', $title, [], 8);
                $simler_vectors = \App\Helper::similar_search_in_elasticsearch('vectors', $title, [], 8);
            } catch (\Throwable $th) {
                $videos = collect([]);
                $simler_videos = collect([]);
                $simler_images = collect([]);
                $simler_vectors = collect([]);
                $same_group = $video->from_same_group([]);
                \Log::error($th);
            }
            $child_ids = $video->child->pluck('id');
            $video_in_cart_ids = CartVideo::where(['user_id' => Auth::id()])->whereIn('video_id', $child_ids)->pluck('video_id')->toArray();

            $exists_in_download = OrderItemsVideo::whereIn('video_id', $child_ids)
                ->where(['order_items_videos.user_id' => Auth::id(), 'orders_videos.status' => OrderVideo::STATUS_ACTIVE])
                ->join('orders_videos', 'orders_videos.id', '=', 'order_items_videos.order_id')->pluck('video_id')->toArray();

            $tags = $video->tags()->where('local', $lang)->select('title', 'slug')->get();

            $plan = VideoPlan::where('status', true)
                ->where('type', 'monthly')
                ->orderBy('price', 'asc')
                ->select('downloads_count', 'price')
                ->first();
            $plan_price_one_file = $plan ? number_format($plan->price / $plan->downloads_count, 2, '.', '') : '';
            return compact('video', 'exists_in_download', 'video_in_cart_ids', 'simler_videos', 'simler_images', 'simler_vectors', 'tags', 'plan_price_one_file', 'same_group');
        });
        $data['video']['is_like'] = $video->is_like;
        return view('video.videos.show', $data);
    }


    public function samegroup(Video $video)
    {
        $videos['images'] = $video->from_same_group([], true)->paginate(106);
        return view('video.videos.samegroup', compact('video', 'videos'));
    }

    public function sameuser(Video $video)
    {
        $user = $video->user;
        if (!$user)
            abort(404);
        $videos['images'] = $user->created_videos()->whereNull('parent_id')->where('status', 'active')->orderBy('date', 'desc')->paginate(106);
        return view('video.videos.sameuser', compact('videos', 'video', 'user'));
    }

    public function download(Request $request, $token_id)
    {
        /**@var $video Video */
        /**@var $user User */
        $video = Video::where('token_id', $token_id)->firstOrFail();
        $this->validate($request, [
            'type' => [Rule::requiredIf(function () {
                return !(\request('raw'));
            }), Rule::in($video->child->pluck('type'))],
        ]);
        $user = auth()->user();
        if ($user->free_videos) {
            FreeDownload::updateOrCreate([
                'entity_id' => $video->id,
                'entity_type' => Video::class,
                'user_id' => $user->id,
            ], [
                'ip' => $request->ip(),
                'date' => now(),
            ]);
            return $this->downloadFile($video, \request('type'));
        }

        $video_subscription = $user->active_video_subscriptions()->first();
        if ($video_subscription && $request->license_type == 'standard')
            return $this->old_download($video, $video_subscription);
        $validator = Validator::make($request->all(), [
            'license_type' => ['required', Rule::in(['standard', 'enhanced', 'exclusive'])],
        ]);
        if ($validator->fails())
            return redirect()->to($video->postLink);

        if ($request->license_type == 'exclusive' && !$video->can_reserve())
            return redirect()->to($video->post_link)->with('error', __('Cant reserve'));
        if ($request->subscription_type == 'team_subscriptions') {
            if (Subscription::download($video, $request->license_type, 1))
                return $this->downloadFile($video, \request('type', 'large'));
            return redirect()->to($video->post_link)->with('error', __('No enough subscriptions'));
        }
        if (Subscription::download($video, $request->license_type))
            return $this->downloadFile($video, $request->type);
        if ($request->plan_id) {
            $plan_id = Plan::findOrFail($request->plan_id)->id;
            session()->put('redirect_after_purchase', $video->postLink);
            session()->put('open_download_options', [
                'license_type' => $request->license_type,
            ]);
            return redirect()->route('purchase',
                [
                    'plan_id' => $plan_id,
                ]);
        }
        return redirect()->to($video->post_link)->with('error', __('No enough subscriptions'));
    }

    public function redownload(Request $request, $token_id)
    {
        /**@var $video Image */
        $video = Video::where('token_id', $token_id)->firstOrFail();
        $user = auth()->user();
        if ($user->is_downloaded($video)) {
            return $this->downloadFile($video, $video->child()->orderBy('size')->first()->type);
        }
        abort(404);
    }

    private function downloadFile($video, $type)
    {
        /**@var $video Video */
        cache()->forget("video_show_{$video->id}_ar");
        cache()->forget("video_show_{$video->id}_en");
        if (\request('raw') && ($video->has_raw() && (auth()->user()->is_downloaded($video, function ($q) {
                        $q->where('downloads.raw', 1);
                    }) or auth()->user()->free_videos))) {
            $path = $video->raw->original;
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
                    'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_V{$video->id}_raw.{$extension}" . '"',
                    'Expires' => '0',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Pragma' => 'public',
                ]
            );
            if (app()->isLocal())
                return redirect()->to($url);
            return redirect()->to(str_replace(parse_url($url)['host'], parse_url(config('filesystems.disks.s3.url'))['host'], $url));

        }
        $path = $video->child->firstWhere('type', $type)->preview;
        $path = str_replace('\\', '/', $path);
        $filename = "arabsstock_V{$video->id}_{$type}.{$video->extension}";
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

    private function old_download($video, $subscription)
    {
        $subscription->download_remaining = $subscription->download_remaining - 1;
        $subscription->save();

        $download = new VideoDownload;
        $download->video_id = $video->id;
        $download->user_id = Auth::user()->id;
        $download->ip = request()->ip();
        $download->plan_id = $subscription->plan_id;
        $download->subscription_id = $subscription->id;
        $download->save();

        $contributor = $video->user_type == Contributor::class ? $video->user : null;
        if ($contributor) {
            $profit_value = (($subscription->amount / $subscription->plan->downloads_count) * $contributor->profit_ratio) / 100;
            $purchase = new Purchase();
            $purchase->user_id = Auth::user()->id;
            $purchase->contributor_id = $contributor->id;
            $purchase->plan_id = $subscription->plan_id;
            $purchase->plan_price = $subscription->amount;
            $purchase->unit_price = ($subscription->amount / $subscription->plan->downloads_count);
            $purchase->profit_ratio = $contributor->profit_ratio;
            $purchase->profit_value = $profit_value;
            $purchase->purchaseable_id = $video->id;
            $purchase->purchaseable_type = Video::class;
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
        return $this->downloadFile($video, \request('type'));
    }

    public function download_preview($id)
    {
        $video = Video::active()->findOrFail($id);

        $path = $video->cut_video;
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        $extension = $video->extension;
        $url = Storage::disk('s3')->temporaryUrl(
            $path, now()->addMinutes(10),
            [
                'url' => 'https://cdn.arabsstock.com',
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="' . "arabsstock_V{$video->id}.{$extension}" . '"',
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
