<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\AccountLedger;
use App\Models\ContentTransfer;
use App\Models\Image;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Vector;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contributor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WareHouseContributorContoller extends Controller
{
    public function index(Request $request, $type)
    {
        $index_url = route('admin.warehouse-contributor.datatable', ['type' => $type]);
        $review = route('admin.warehouse-contributor.review', ['type' => $type, 'id' => 0]);
        $html_breadcrumbs = [
            'title' => __('views.ContributorFilesToSubmit'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [

        ];


        return view('admin_v2.warehouse-contributor.index',
            compact(
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'review',
        )

        );
    }

    public function datatable(Request $request, $type)
    {
        // dd($type);
        $query = Contributor::whereHas($type, function ($q) {
            $q->where('contributor_stage', 0);
        })
            ->select('username', 'name', 'email', 'id', 'status')
            ->withCount(["$type as files_count" => function ($query) {
                $query->where('contributor_stage', 0);
            }
            ]);

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('id', 'DESC')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function WareHouseContributor(Request $request, $type, $id)
    {
        // return $request->all();
        $lang = app()->getLocale();
        $routes = [
            'images' => [
                'options' => route('admin.api.contributor_images.options'),
                'filters' => route('admin.api.contributor_images.filters'),
                'index' => route('admin.api.contributor_images.index'),
                'update_multi' => route('admin.api.contributor_images.update_multi'),
                'delete' => route('admin.api.contributor_images.delete'),
                'delete_all' => route('admin.api.contributor_images.delete_all'),
                // 'releases' => route('admin.api.releases.store'),
                // 'releases_forms' => route('releases'),
                'submit' => route('admin.api.contributor_images.submit'),
                'resubmit' => route('admin.api.contributor_images.resubmit'),
            ],
            'videos' => [
                'options' => route('admin.api.contributor_videos.options'),
                'filters' => route('admin.api.contributor_videos.filters'),
                'index' => route('admin.api.contributor_videos.index'),
                'update_multi' => route('admin.api.contributor_videos.update_multi'),
                'delete' => route('admin.api.contributor_videos.delete'),
                'delete_all' => route('admin.api.contributor_videos.delete_all'),
                // 'releases' => route('api.releases.store'),
                // 'releases_forms' => route('releases'),
                'submit' => route('admin.api.contributor_videos.submit'),
                'resubmit' => route('admin.api.contributor_videos.resubmit'),
            ],
            'vectors' => [
                'options' => route('admin.api.contributor_vectors.options'),
                'filters' => route('admin.api.contributor_vectors.filters'),
                'index' => route('admin.api.contributor_vectors.index'),
                'update_multi' => route('admin.api.contributor_vectors.update_multi'),
                'delete' => route('admin.api.contributor_vectors.delete'),
                'delete_all' => route('admin.api.contributor_vectors.delete_all'),
                // 'releases' => route('api.releases.store'),
                'submit' => route('admin.api.contributor_vectors.submit'),
                'resubmit' => route('admin.api.contributor_vectors.resubmit'),
            ],
        ];
        $routes = $routes[$type];

        // dd($routes);

        $user = \DB::table('contributors')->select(
            'id',
            'email',
            'api_token'
        )->where('id', $id)->first();


        // return $user;

        return view('admin.warehouse-contributor.warehouse', compact('routes', 'user', 'type', 'lang'));
    }

    public function transfer_content(Request $request)
    {
        if ($request->method() == 'GET')
            return view('admin.warehouse-contributor.transfer_content');
        $this->validate($request, [
            'contributor_id' => ['required', Rule::exists('contributors', 'id')],
            'contents' => ['required'],
        ]);
        $transfer = ContentTransfer::create([
            'to_contributor_id' => $request->contributor_id,
        ]);
        $transfer->load('to_contributor');
        $types = [
            'photo.show' => Image::class,
            'video.show' => Video::class,
            'vector.show' => Vector::class,
        ];
        $urls = explode(PHP_EOL, $request->contents);
        $count = 0;
        foreach ($urls as $url) {
            $url = trim($url);
            if ($url) {
                $url = str_replace(['/ar', '/en'], '', parse_url($url)['path']);
                try {
                    $route = app('router')->getRoutes()->match(app('request')->create($url));
                    if (isset($types[$route->getName()])) {
                        $type = $types[$route->getName()];
                        $id = $route->parameter('id');
                        $array = explode('-', $id);
                        if (!(isset($array[1]) && is_numeric($array[1]))) {
                            abort(404);
                        }
                        $id = $array[1];
                        $record = $type::find($id);
                        if ($record) {
                            if ($record->user_type == User::class) {
                                if ($type == Image::class)
                                    $this->transfer_image($record, $transfer);
                                elseif ($type == Video::class)
                                    $this->transfer_video($record, $transfer);
                                elseif ($type == Vector::class)
                                    $this->transfer_vector($record, $transfer);
                                $count++;
                            }
                        }
                    }
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
        cache()->clear();
        return redirect()->back()->with('success', __(":count Items transfered successfully", ['count' => $count]));
    }

    private function transfer_image($image, $transfer)
    {
        $contributor = $transfer->to_contributor;
        $image->user_type = \App\Models\Contributor::class;
        $image->user_id = $transfer->to_contributor_id;
        $image->save();
        $contributor_image = new \App\Models\ContributorImage;
        $contributor_image->title_en = $image->title_en;
        $contributor_image->title_ar = $image->title_ar;
        $contributor_image->thumbnail = $image->thumbnail;
        $contributor_image->contributor_id = $transfer->to_contributor_id;
        $contributor_image->original_name = $image->original_name;
        $contributor_image->large = $image->large;
        $contributor_image->extension = $image->extension;
        $contributor_image->contributor_stage = 8;
        $contributor_image->hash = $image->stock->first()->hash;
        $contributor_image->stage_edit = 2;
        $contributor_image->created_at = now();
        $contributor_image->preview = $image->preview;
        $contributor_image->reviewer_id = 1;
        $contributor_image->reviewed_at = null;
        $contributor_image->publisher_id = 1;
        $contributor_image->publisher_id = $image->publisher_id;
        $contributor_image->published_at = $image->published_at;
        $contributor_image->is_uploaded = 1;
        $contributor_image->license = 'commercial';
        $contributor_image->save();
        if ($image->downloads->count())
            foreach ($image->downloads as $download) {
                $this->create_purchases($contributor, $image, $download);
            }
    }

    private function transfer_video($video, $transfer)
    {
        $contributor = $transfer->to_contributor;
        $video->user_type = \App\Models\Contributor::class;
        $video->user_id = $transfer->to_contributor_id;
        $video->save();
        $contributor_video = new \App\Models\ContributorVideo();
        $contributor_video->thumbnail = $video->thumbnail;
        $contributor_video->contributor_id = $transfer->to_contributor_id;
        $contributor_video->thumbnail_width = $video->thumbnail_width;
        $contributor_video->thumbnail_height = $video->thumbnail_height;
        $contributor_video->title_ar = $video->title_ar;
        $contributor_video->title_en = $video->title_en;
        $contributor_video->extension = $video->extension;
        $contributor_video->stage_edit = 2;
        $contributor_video->contributor_stage = 8;
        $contributor_video->hash = $video->hash;
        $contributor_video->original_name = $video->original_name;
        $contributor_video->is_uploaded = 1;
        $contributor_video->token_id = Str::random(20);
        $contributor_video->preview = $video->preview;
        $contributor_video->reviewer_id = 1;
        $contributor_video->reviewed_at = null;
        $contributor_video->duration = $video->duration;
        $contributor_video->created_at = now();
        $contributor_video->license = 'commercial';
        $contributor_video->publisher_id = $video->publisher_id;
        $contributor_video->published_at = $video->published_at;

        $contributor_video->save();
        if ($video->downloads->count())
            foreach ($video->downloads as $download) {
                $this->create_purchases($contributor, $video, $download);
            }
    }

    private function transfer_vector($vector, $transfer)
    {
        $contributor = $transfer->to_contributor;
        $vector->user_type = \App\Models\Contributor::class;
        $vector->user_id = $transfer->to_contributor_id;
        $vector->save();

        $contributor_vector = new \App\Models\ContributorVector();
        $contributor_vector->title_en = $vector->title_en;
        $contributor_vector->title_ar = $vector->title_ar;
        $contributor_vector->thumbnail = $vector->thumbnail;
        $contributor_vector->contributor_id = $transfer->to_contributor_id;
        $contributor_vector->original_name = $vector->original_name;
        $contributor_vector->large = $vector->large;
        $contributor_vector->extension = $vector->extension;
        $contributor_vector->contributor_stage = 8;
        $contributor_vector->hash = $vector->hash;
        $contributor_vector->stage_edit = 2;
        $contributor_vector->created_at = now();
        $contributor_vector->reviewer_id = $vector->reviewer_id;
        $contributor_vector->reviewed_at = $vector->reviewed_at;
        $contributor_vector->publisher_id = $vector->publisher_id;
        $contributor_vector->published_at = $vector->published_at;
        $contributor_vector->is_uploaded = 1;
        $contributor_vector->preview = $vector->preview;


        $contributor_vector->save();
        if ($vector->downloads->count())
            foreach ($vector->downloads as $download) {
                $this->create_purchases($contributor, $vector, $download);
            }
    }

    private function create_purchases($contributor, $record, $download)
    {
        if ($contributor->profit_ratio) {
            $profit_value = $download->unit_price * ($contributor->profit_ratio / 100);
            $purchase = new Purchase();
            $purchase->user_id = auth()->id();
            $purchase->contributor_id = $contributor->id;
            $purchase->download_id = $download->id;
            $purchase->unit_price = $download->unit_price;
            $purchase->profit_ratio = $contributor->profit_ratio;
            $purchase->profit_value = $profit_value;
            $purchase->purchaseable_id = $record->id;
            $purchase->purchaseable_type = get_class($record);
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
    }
}
