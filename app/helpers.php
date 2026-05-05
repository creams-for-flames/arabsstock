<?php

use App\Models\AdminImageSettings;
use App\Models\AdminVectorSettings;
use App\Models\AdminVideoSettings;
use GeoIp2\Database\Reader;
use App\Models\Countries;
use App\Models\Cities;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Tag;

if (!function_exists('geoip')) {
    function geoip($ip_address = '')
    {
        // return '199.123.122.121';

        // This creates the Reader object, which should be reused across
        // lookups.
        $reader = new Reader(base_path('database/geoip_db/GeoLite2-City.mmdb'));

        // Replace "city" with the appropriate method for your database, e.g.,
        // "country".
        try {
            $record = $reader->city($ip_address);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::channel('info')->warning("GeoIp No data,IP:{$ip_address}");
        }
        if (!isset($record))
            return;
        try {
            $country_isoCode = $record->country->isoCode; // 'US'
            $country_name = $record->country->name; // 'United States'
            $state_geonameId = $record->mostSpecificSubdivision->geonameId; // 'Minnesota'
            $state_name = $record->mostSpecificSubdivision->name; // 'Minnesota'
            $state_isoCode = $record->mostSpecificSubdivision->isoCode; // 'MN'

            $country = Countries::where('iso_code_2', $country_isoCode)->first();
            $city_code = 0;
            $city_name = 0;
            $city_geonameId = 0;
            $city_id = 0;
            if ($record->city && $record->city->geonameId) {
                $city_geonameId = $record->city->geonameId; // '576'
                $city_name = $record->city->name; // 'Minneapolis'
                $city = Cities::where('geoname_id', $city_geonameId)->first();
                if (!$city) {
                    $city = Cities::forceCreate([
                        'country_id' => $country->id,
                        'name_ar' => $city_name,
                        'name_en' => $city_name,
                        'code' => $record->postal->code,
                        'geoname_id' => $city_geonameId,
                        'status' => 1,
                    ]);
                }
                $city_id = $city->id;
                $city_code = $city->code;
            }
            $country_id = $country->id;
            $main_lang = @$country->main_lang;
            return compact('country_id', 'country_isoCode', 'country_name', 'state_geonameId', 'state_name', 'state_isoCode', 'city_geonameId', 'city_id', 'city_name', 'city_code', 'main_lang');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::channel('info')->warning("GeoIp data founded:", [
                'ip' => $ip_address,
                'data' => json_encode($record)
            ]);
            return [];
        }
    }
}

if (!function_exists('url_resizer')) {
    function url_resizer($path, $dimensions)
    {
        try {
            # use server resizer in de
            $url = url($path);
            $uploads_pos = mb_strpos($url, '/uploads/');
            $path = parse_url($url)['path'] ?? '';
            \Log::channel('info')->info("helper:url_resizer {$path} ");
            return url("resizer/resize/$dimensions" . $path);
        } catch (\Throwable $th) {
            report($th);
        }
    }
}

if (!function_exists('get_datatable_params')) {
    function get_datatable_params($data)
    {
        $params = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case "pagination":
                    $params["pagination"] = [
                        "page" => $value["page"],
                        "perpage" => $value["perpage"]
                    ];
                    break;
                case "query":
                    $params["search"] = isset($value['generalSearch']) ? $value['generalSearch'] : '';
                    $params['date_range'] = isset($value['date_range']) ? $value['date_range'] : '';
                    break;
                case "sort":
                    $params["sort"] = [
                        "column" => $value["field"],
                        "dir" => $value["sort"]
                    ];
                    break;
            }
        }
        return $params;
    }
}

if (!function_exists('process_datatable_query')) {
    function process_datatable_query($query, $search_callback = null, $post_query = null)
    {


        $meta = [
            'page' => 1,
            'pages' => 0,
            'perpage' => 12,
            'total' => 0,
            'sort' => '',
            'field' => ''
        ];
        $datatable_params = get_datatable_params(request()->all());

        if (($search_callback !== null) && @$datatable_params['search']) {
            $value = $datatable_params['search'];
            $query = $search_callback($query, $value);
        }
        $meta['total'] = $query->count();
        $meta['pages'] = (int)ceil($meta['total'] / $meta['perpage']);

        if ($datatable_params['pagination']) {
            $value = $datatable_params['pagination'];
            $meta['page'] = (int)$value["page"];
            $meta['perpage'] = (int)$value["perpage"];
            $offset = ($value["page"] - 1) * $value["perpage"];
            $query->offset($offset)->limit($value["perpage"]);
        }
        if (isset($datatable_params['sort']) && $datatable_params['sort']) {
            $value = $datatable_params['sort'];
            $query->orderBy($value['column'], $value['dir']);
            $meta['sort'] = $value['dir'];
            $meta['field'] = $value['column'];
        } else {
            $query->orderBy('id', 'desc');
        }
        if ($post_query) {
            $data = $query->get()->pipe($post_query)->toArray();
            // $data = $query->get()->toArray();
        } else {
            $data = $query->get()->toArray();
        }
        return [
            'meta' => $meta,
            'data' => $data,
        ];
    }
}

if (!function_exists('get_admin_menu_list')) {
    function get_admin_menu_list()
    {
        return $menu_list = [
            [
                'url' => route('admin.dashboard.index'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.Home'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.dashboard.index'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.folders.index'),
                'icon' => 'flaticon2-folder',
                'text' => __('views.Folders'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.folders.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.failed_jobs.index'),
                'icon' => 'flaticon2-gear',
                'text' => __('views.failed_jobs'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.failed_jobs.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.evaluations.index'),
                'icon' => 'flaticon-comment',
                'text' => __('views.evaluations'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.evaluation.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.categories.index'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('views.Categories'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.categories.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.categories_admin.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesAdmin'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.categories_admin.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.categories_contributores.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesContributer'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.categories_contributores.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.images.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.ImageUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.images.'
                ),
                'children' => [
                    [
                        'url' => route('admin.images.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('views.Images'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.index'
                        ),
                    ],
                    [
                        'url' => route('admin.images.pending.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.ImagesPending'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.pending.'
                        ),
                    ],
                    [
                        'url' => route('admin.files.rejected', ['type' => "images"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.Images') . __('views.hard_rejected'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.files.rejected'
                        ),
                    ],
                    [
                        'url' => route('admin.images.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeleted'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.contributor_images.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeletedContributor'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.warehouse-contributor.index', ['type' => "images"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.ContributorFilesToSubmit'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse-contributor.index'
                        ),
                    ],
                    [
                        'url' => route('admin.images.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.ImagesFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.filemanger.create'
                        ),
                    ],

                    [
                        'url' => route('admin.images.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.ImagesWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.warehouse.'
                        ),
                    ],
                    [
                        'url' => route('admin.rejection_reasons.index', ['category' => "images"]),
                        'icon' => 'flaticon2-grids',
                        'text' => __('admin.RejectionReason'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.rejection_reasons.'
                        ),
                    ],
                    [
                        'url' => route('admin.images.warehouse_remove_bg.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.ImagesWarehouseRemoveBg'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.warehouse_remove_bg.'
                        ),
                    ],

                    [
                        'url' => route('admin.images.warehouse_remove_bg.check'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.ImagesWarehouseRemoveBgCheck'),
                        'is_active' =>
                            starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.images.warehouse_remove_bg.check'
                            ),
                    ],
                    [
                        'url' => route('admin.images.warehouse_remove_bg.check_manual'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.ImagesWarehouseRemoveBgCheckManual'), //
                        'is_active' =>
                            starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.images.warehouse_remove_bg.check_manual'
                            ),
                    ],


                    [
                        'url' => route('admin.warehouse_check_requests.index', ['type' => "images"]),
                        'icon' => 'flaticon2-grids',
                        'text' => __('admin.warehouse_check'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse_check_requests.index'
                        ),
                    ],
                    [
                        'url' => route('admin.images.warehouse_remove_bg.check.admin'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('admin.check_images_removebg_admin'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.warehouse_remove_bg.check.admin'
                        ),
                    ],

                ],
            ],
            [
                'url' => route('admin.members.index'),
                'icon' => 'fa fa-users',
                'text' => __('views.Members'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.members.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.teams.index'),
                'icon' => 'fa fa-users',
                'text' => __('Teams'),
                'is_active' => Str::startsWith(
                    request()
                        ->route()
                        ->getName(),
                    'admin.teams.index'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.pages.index'),
                'icon' => 'flaticon2-open-text-book',
                'text' => __('views.Pages'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.pages.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => request()
                        ->route()
                        ->getName() == 'admin.contributors.submissions.index' && count(request()->all()) == 0,
                'children' => [],
            ],
            [
                'url' => route('admin.contributors.submissions.index', ['status' => "update"]),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => request()
                        ->route()
                        ->getName() == 'admin.contributors.submissions.index' && request('status') == 'update',
                'children' => [],
            ],
            [
                'url' => route('admin.contributors.submissions.update_images_data'),
                'icon' => 'la la-refresh',
                'text' => __('views.update_after_publish'),
                'is_active' => request()
                        ->route()
                        ->getName() == 'admin.contributors.submissions.update_images_data',
                'children' => [],
            ],
            [
                'url' => route('admin.contributor.index'),
                'icon' => 'fa fa-users',
                'text' => __('views.contributor'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.contributor.'
                ),
                'children' => [],
            ],
            [
                'url' => '#',
                'icon' => 'fa fa-users',
                'text' => __('paypal.Payout'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.payout.'
                ),
                'children' => [
                    [
                        'url' => route('admin.payout.payout_request'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('paypal.Withdrawal Requests'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.payout.payout_request'
                        ),
                    ],
                    [
                        'url' => route('admin.payout.bayout_batch'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('paypal.Payout Proccess'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.payout.index_payoutBatch'
                        ),
                    ],
                    [
                        'url' => route('admin.payout.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('paypal.Payout'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.payout.index'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.articles.index'),
                'icon' => 'flaticon2-open-text-book',
                'text' => __('views.articles'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.articles.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.blogs.index'),
                'icon' => 'flaticon2-open-text-book',
                'text' => __('views.blogs'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.blogs.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.admin_collections.index'),
                'icon' => 'flaticon2-files-and-folders',
                'text' => __('views.Collections'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.admin_collections.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.transfer_content'),
                'icon' => 'flaticon2-refresh',
                'text' => __('Transfer content ownership'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.transfer_content'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.plans.index'),
                'icon' => 'flaticon2-shopping-cart',
                'text' => __('views.PlansAndSubscriptions'),
                'is_active' =>
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.plans.'
                    ) || starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.promocodes.'
                    )
                //  ||
                // starts_with(
                //     request()
                //         ->route()
                //         ->getName(),
                //     'admin.invoices.'
                // )
                ,
                'children' => [
                    [
                        'url' => route('admin.plans.index'),
                        'icon' => 'flaticon2-box-1',
                        'text' => __('views.Plans'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.plans.index'
                        ),
                    ],
                    [
                        'url' => route('admin.promocodes.index'),
                        'icon' => 'fa fa-percentage',
                        'text' => __('views.Promocodes'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.promocodes.'
                        ),
                    ],

                    // [
                    //     'url' => route('admin.invoices.index'),
                    //     'icon' => 'flaticon2-list',
                    //     'text' => __('views.Invoices'),
                    //     'is_active' => starts_with(
                    //         request()
                    //             ->route()
                    //             ->getName(),
                    //         'admin.invoices.index'
                    //     ),
                    // ],
                ],
            ],
            [
                'url' => route('admin.performance_reports.payment.index'),
                'icon' => 'flaticon2-chart',
                'text' => __('views.PerformanceReports'),
                'is_active' => starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.performance_reports.payment.'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.user_plans.'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.performance_reports.monthly_new_payments'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.performance_reports.payments'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.payments_log.index'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.subscriptions.'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.teams.subscriptions'
                    ) ||
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.downloads.'
                    ),
                'children' => [
                    [
                        'url' => route('admin.payments_log.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Payments Logs'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.payments_log.'
                        ),
                    ],
                    [
                        'url' => route('admin.performance_reports.payments'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('Flex Sales'),
                        'is_active' =>
                            request()
                                ->route()
                                ->getName() == 'admin.performance_reports.payments'
                    ],
                    [
                        'url' => route('admin.performance_reports.payment.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Sales'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.performance_reports.payment.'
                        ),
                    ],
                    [
                        'url' => route('admin.image-reviews.index'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.Reviewed Images'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.image-reviews.index'
                        ),
                    ],
                    [
                        'url' => route('admin.user_plans.index'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.Image subscriptions'),
                        'is_active' => (
                            request()
                                ->route()
                                ->getName() ==
                            'admin.user_plans.index'
                        ),
                    ],
                    [
                        'url' => route('admin.subscriptions.index'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.UserPlans'),
                        'is_active' => (request()->route()->getName() == 'admin.subscriptions.index')
                    ],
                    [
                        'url' => route('admin.teams.subscriptions'),
                        'icon' => 'fa fa-users',
                        'text' => __('views.TeamsPlans'),
                        'is_active' => (request()->route()->getName() == 'admin.teams.subscriptions')
                    ],
                    [
                        'url' => route('admin.user_plans.downloads'),
                        'icon' => 'flaticon2-box',
                        'text' => __('Image subscriptions downloads'),
                        'is_active' => request()
                                ->route()
                                ->getName() == 'admin.user_plans.downloads',
                    ],
                    [
                        'url' => route('admin.downloads.index', ['type' => 'Image']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.Downoloads'),
                        'is_active' => starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.downloads.index'
                            ) || starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.downloads.show'
                            ),
                    ],
                    [
                        'url' => route('admin.downloads.free', ['type' => 'Image']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.DownoloadsFree'),
                        'is_active' => starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.downloads.free'
                            ) || starts_with(
                                request()
                                    ->route()
                                    ->getName(),
                                'admin.downloads.show'
                            ),
                    ],
                    [
                        'url' => route('admin.user_plans.contributor_downloads'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.ContributorDownloads'),
                        'is_active' => request()
                                ->route()
                                ->getName() == 'admin.user_plans.contributor_downloads',
                    ],
                    [
                        'url' => route('admin.performance_reports.monthly_new_image_payments'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('Monthly New Payments'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.performance_reports.monthly_new_image_payments'
                        ),
                    ],
                    [
                        'url' => route('admin.statistics.export'),
                        'icon' => 'flaticon2-download-2',
                        'text' => __('Export Statistics'),
                        'target' => '_blank',
                        'class' => 'export-statistics',
                        'is_active' => false,
                    ],

                ],
            ],
            [
                'url' => route('admin.settings.edit'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.settings'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.settings.edit'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.images_search_keys'),
                'icon' => 'flaticon-search',
                'text' => __('Search Keys'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.images_search_keys'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.email_subscribe.index'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('views.Email_subscribe'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.email_subscribe.index'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.newsletter.index'),
                'icon' => 'flaticon-email',
                'text' => __('views.Newsletter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.newsletter.index'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.weekly_letters.index'),
                'icon' => 'flaticon-email',
                'text' => __('views.WeeklyLetter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.weekly_letters.index'
                ),
                'children' => [],
            ],
        ];
    }
}

if (!function_exists('get_accountant_menu_list')) {
    function get_accountant_menu_list()
    {
        $route_name = request()->route()->getName();
        return $menu_list = [
            [
                'url' => route('admin.accountant.downloads'),
                'icon' => 'flaticon2-download',
                'text' => __('views.Downoloads'),
                'is_active' => $route_name == 'admin.accountant.downloads',
            ],
            [
                'url' => route('admin.accountant.payments'),
                'icon' => 'flaticon2-chart',
                'text' => __('views.Sales'),
                'is_active' => $route_name == 'admin.accountant.payments',
            ],
            [
                'url' => route('admin.accountant.contributor_downloads'),
                'icon' => 'flaticon2-box',
                'text' => __('views.ContributorDownloads'),
                'is_active' => $route_name == 'admin.accountant.contributor_downloads',
            ],
            [
                'url' => route('admin.accountant.payouts'),
                'icon' => 'fa fa-money-bill-alt',
                'text' => __('paypal.Payout'),
                'is_active' => $route_name == 'admin.accountant.payouts',
            ],
            [
                'url' => route('admin.statistics.export'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('Content Statistics'),
                'target' => '_blank',
                'class' => 'export-statistics',
                'is_active' => false,
            ],
            [
                'url' => route('admin.accountant.contents'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('Content'),
                'is_active' => $route_name == 'admin.accountant.contents',
            ],
        ];
    }
}

if (!function_exists('get_admin_image_editor_menu_list')) {
    function get_admin_image_editor_menu_list()
    {
        $menu_list = [
            [
                'url' => route('admin.images.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.ImageUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.images.'
                ),
                'children' => [
                    [
                        'url' => route('admin.images.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('views.Images'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.index'
                        ),
                    ],
                    [
                        'url' => route('admin.images.pending.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.ImagesPending'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.pending.'
                        ),
                    ],
                    [
                        'url' => route('admin.images.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.ImagesFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.filemanger.'
                        ),
                    ],
                    [
                        'url' => route('admin.images.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.ImagesWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.warehouse.'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.folders.index'),
                'icon' => 'flaticon2-folder',
                'text' => __('views.Folders'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.folders.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.contributors.submissions.index', ['status' => 'update']),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.contributors.submissions.'
                ),
                'children' => [],
            ],

        ];
        if (auth()->check() and auth()->user()->role === "admin_image_editor" and auth()->user()->email === "sarahp@arabsstock.com") {
            $menu_list[] = [
                'url' => route('admin.weekly_letters.index'),
                'icon' => 'flaticon-email',
                'text' => __('views.WeeklyLetter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.weekly_letters.index'
                ),
                'children' => [],
            ];
            $menu_list[] = [
                'url' => route('admin.rejection_reasons.index', ['category' => "images"]),
                'icon' => 'flaticon-file-1',
                'text' => __('admin.RejectionReason'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.rejection_reasons.index'
                ),
                'children' => [],
            ];

        }
        return $menu_list;
    }
}

if (!function_exists('get_admin_video_editor_menu_list')) {
    function get_admin_video_editor_menu_list()
    {
        $menu_list = [
            [
                'url' => route('admin.videos.folders.index'),
                'icon' => 'flaticon2-folder',
                'text' => __('views.Folders'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.folders.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.videos.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.VideoUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.videos.'
                ),
                'children' => [
                    [
                        'url' => route('admin.videos.videos.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('views.Videos'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.index'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.videos.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.VideosFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.filemanger.'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.videos.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.VideosWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.warehouse.'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.videos.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.contributors.submissions.index', ['status' => 'update']),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.contributors.submissions.'
                ),
                'children' => [],
            ],
        ];
        if (auth()->check() and auth()->user()->role === "admin_video_editor" and auth()->user()->email === "sarahv@arabsstock.com") {

            $menu_list[] = [
                'url' => route('admin.rejection_reasons.index', ['category' => "videos"]),
                'icon' => 'flaticon-file-1',
                'text' => __('admin.RejectionReason'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.rejection_reasons.index'
                ),
                'children' => [],
            ];

        }
        return $menu_list;
    }
}

if (!function_exists('get_admin_vector_editor_menu_list')) {
    function get_admin_vector_editor_menu_list()
    {
        $menu_list = [
            [
                'url' => route('admin.vectors.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.VectorUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.'
                ),
                'children' => [
                    [
                        'url' => route('admin.vectors.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('Vectors'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.index'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.pending.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.VectorsPending'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.pending.'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.VectorsFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.filemanger.'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.VectorsWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.warehouse.'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.vectors.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.contributors.submissions.'
                ),
                'children' => [],
            ],

            [
                'url' => route('admin.vectors.contributors.submissions.index', ['status' => 'update']),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.contributors.submissions.'
                ),
                'children' => [],
            ],
        ];

        if (auth()->check() and auth()->user()->role === "admin_vector_editor" and auth()->user()->email === "sarahi@arabsstock.com") {

            $menu_list[] = [
                'url' => route('admin.rejection_reasons.index', ['category' => "vectors"]),
                'icon' => 'flaticon-file-1',
                'text' => __('admin.RejectionReason'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.rejection_reasons.index'
                ),
                'children' => [],
            ];

        }

        return $menu_list;
    }
}

if (!function_exists('get_admin_vectors_menu_list')) {
    function get_admin_vectors_menu_list()
    {
        return $menu_list = [
            [
                'url' => route('admin.vector.dashboard.index'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.Home'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vector.dashboard'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.folders.index'),
                'icon' => 'flaticon2-folder',
                'text' => __('views.Folders'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.folders.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vector.categories.index'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('views.Categories'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vector.categories.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vector.categories_admin.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesAdmin'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vector.categories_admin.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vector.categories_contributores.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesContributer'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.categories_contributores.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.VectorUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.'
                ),
                'children' => [
                    [
                        'url' => route('admin.vectors.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('views.Vectors'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.index'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.pending.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.VectorsPending'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.pending.'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeleted'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.files.rejected', ['type' => "vectors"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.Vectors') . __('views.hard_rejected'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.files.rejected'
                        ),
                    ],
                    [
                        'url' => route('admin.warehouse-contributor.index', ['type' => "vectors"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.ContributorFilesToSubmit'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse-contributor.index'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.contributor_vectors.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeletedContributor'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.VectorsFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.filemanger.'
                        ),
                    ],
                    [
                        'url' => route('admin.vectors.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.VectorsWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.warehouse.'
                        ),
                    ],
                    [
                        'url' => route('admin.warehouse_check_requests.index', ['type' => "vectors"]),
                        'icon' => 'flaticon2-grids',
                        'text' => __('admin.warehouse_check'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse_check_requests.index'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.vector-reviews.index'),
                'icon' => 'flaticon2-box',
                'text' => __('Reviewed Vectors'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vector-reviews.index'
                ),
            ],
            [
                'url' => route('admin.vector.reports.payments_log.index'),
                'icon' => 'flaticon2-chart',
                'text' => __('views.PerformanceReports'),
                'is_active' => starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.vector.reports'
                    ) || starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.vector.user_plans'
                    ) || starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.downloads.'
                    ),


                'children' => [
                    [
                        'url' => route('admin.vector.reports.payments_log.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Payments Logs'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vector.reports.payments_log.'
                        ),
                    ],
                    [
                        'url' => route('admin.vector.reports.payment.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Sales'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vector.reports.payment.'
                        ),
                    ],

                    [
                        'url' => route('admin.vector.user_plans.index'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.UserPlans'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vector.user_plans.index'
                        ),
                    ],

                    [
                        'url' => route('admin.vector.user_plans.downloads'),
                        'icon' => 'fa fa-money-bill-wave',
                        'text' => __('views.Downoloads'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vector.user_plans.downloads'
                        ),
                        'children' => [],
                    ], [
                        'url' => route('admin.downloads.index', ['type' => 'Vector']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.Downoloads') . ' Flex',
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.downloads.index'
                        ),
                    ], [
                        'url' => route('admin.downloads.free', ['type' => 'Vector']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.DownoloadsFree') . ' Flex',
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.downloads.free'
                        ),
                    ],


                ],


            ],
            [
                'url' => route('admin.vectors.members.index'),
                'icon' => 'fa fa-users',
                'text' => __('views.Members'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.members.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.pages.index'),
                'icon' => 'flaticon2-open-text-book',
                'text' => __('views.Pages'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.pages.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.contributors.submissions.index', ['status' => "update"]),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.plans.index'),
                'icon' => 'flaticon2-shopping-cart',
                'text' => __('views.PlansAndSubscriptions'),
                'is_active' =>
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.videos.plans.'
                    )
                //  ||
                // starts_with(
                //     request()
                //         ->route()
                //         ->getName(),
                //     'admin.invoices.'
                // )
                ,
                'children' => [
                    [
                        'url' => route('admin.vectors.plans.index'),
                        'icon' => 'flaticon2-box-1',
                        'text' => __('views.Plans'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.vectors.plans.'
                        ),
                    ],

                ],
            ],
            //  [
            //     'url' => route('admin.vector.payments.index'),
            //     'icon' => 'fa fa-money-bill-wave',
            //     'text' => __('views.Payments'),
            //      'is_active' => starts_with(
            //         request()
            //             ->route()
            //             ->getName(),
            //         'admin.vector.payments.'
            //     ),
            //     'children' => [],
            // ],
            [
                'url' => route('admin.vectors_search_keys'),
                'icon' => 'flaticon-search',
                'text' => __('Search Keys'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors_search_keys'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.vectors.admin_collections.index'),
                'icon' => 'flaticon2-files-and-folders',
                'text' => __('views.Collections'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vectors.admin_collections.'
                ),
                'children' => [],
            ],


        ];
    }
}

if (!function_exists('get_admin_videos_menu_list')) {
    function get_admin_videos_menu_list()
    {
        return $menu_list = [
            [
                'url' => route('admin.videos.dashboard.index'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.Home'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.dashboard.index'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.folders.index'),
                'icon' => 'flaticon2-folder',
                'text' => __('views.Folders'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.folders.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.categories.index'),
                'icon' => 'flaticon2-indent-dots',
                'text' => __('views.Categories'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.categories.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.categories_admin.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesAdmin'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.categories_admin.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.video.categories_contributores.index'),
                'icon' => 'flaticon2-list-1',
                'text' => __('views.CategoriesContributer'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.categories_contributores.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.videos.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.VideoUploadCenter'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.videos.'
                ),
                'children' => [
                    [
                        'url' => route('admin.videos.videos.index'),
                        'icon' => 'flaticon2-image-file',
                        'text' => __('views.Videos'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.index'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeleted'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.files.rejected', ['type' => "videos"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.Videos') . __('views.hard_rejected'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.files.rejected'
                        ),
                    ],
                    [
                        'url' => route('admin.warehouse-contributor.index', ['type' => "videos"]),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.ContributorFilesToSubmit'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse-contributor.index'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.contributor_videos.deleted.index'),
                        'icon' => 'flaticon2-time',
                        'text' => __('views.filesDeletedContributor'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.images.deleted.'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.videos.filemanger.create'),
                        'icon' => 'flaticon-upload',
                        'text' => __('views.VideosFilemanger'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.filemanger.'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.videos.warehouse.index'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.VideosWarehouse'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.videos.warehouse.'
                        ),
                    ],
                    [
                        'url' => route('admin.warehouse_check_requests.index', ['type' => "videos"]),
                        'icon' => 'flaticon2-grids',
                        'text' => __('admin.warehouse_check'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.warehouse_check_requests.index'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.raw'),
                        'icon' => 'flaticon2-grids',
                        'text' => __('views.raw_videos'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.raw'
                        ),
                    ],
                ],
            ],
            [
                'url' => route('admin.videos.members.index'),
                'icon' => 'fa fa-users',
                'text' => __('views.Members'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.members.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.pages.index'),
                'icon' => 'flaticon2-open-text-book',
                'text' => __('views.Pages'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.pages.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.contributors.submissions.index'),
                'icon' => 'flaticon-upload',
                'text' => __('views.Submissions'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.contributors.submissions.index', ['status' => "update"]),
                'icon' => 'flaticon-upload',
                'text' => __('views.update_from_contributor_after_publish'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.contributors.submissions.'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.plans.index'),
                'icon' => 'flaticon2-shopping-cart',
                'text' => __('views.PlansAndSubscriptions'),
                'is_active' =>
                    starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.videos.plans.'
                    )
                //  ||
                // starts_with(
                //     request()
                //         ->route()
                //         ->getName(),
                //     'admin.invoices.'
                // )
                ,
                'children' => [
                    [
                        'url' => route('admin.videos.plans.index'),
                        'icon' => 'flaticon2-box-1',
                        'text' => __('views.Plans'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.plans.'
                        ),
                    ],

                ],
            ],


            [
                'url' => route('admin.videos.reports.payments_log.index'),
                'icon' => 'flaticon2-chart',
                'text' => __('views.PerformanceReports'),
                'is_active' => starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.videos.reports'
                    ) || starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.videos.user_plans'
                    ) || starts_with(
                        request()
                            ->route()
                            ->getName(),
                        'admin.downloads.'
                    ),

                'children' => [
                    [
                        'url' => route('admin.videos.reports.payments_log.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Payments Logs'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.reports.payments_log.'
                        )
                    ],
                    [
                        'url' => route('admin.videos.reports.payment.index'),
                        'icon' => 'flaticon2-chart',
                        'text' => __('views.Sales'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.reports.payment.'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.user_plans.index'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.UserPlans'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.user_plans.index'
                        ),
                    ],
                    [
                        'url' => route('admin.videos.user_plans.downloads'),
                        'icon' => 'flaticon2-box',
                        'text' => __('views.Downoloads'),
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.videos.user_plans.downloads'
                        ),
                    ],
                    [
                        'url' => route('admin.downloads.index', ['type' => 'Video']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.Downoloads') . ' Flex',
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.downloads.index'
                        ),
                    ],
                    [
                        'url' => route('admin.downloads.free', ['type' => 'Video']),
                        'icon' => 'flaticon2-download',
                        'text' => __('views.DownoloadsFree') . ' Flex',
                        'is_active' => starts_with(
                            request()
                                ->route()
                                ->getName(),
                            'admin.downloads.free'
                        ),
                    ],
                    // [
                    //     'url' => route('admin.invoices.index'),
                    //     'icon' => 'flaticon2-list',
                    //     'text' => __('views.Invoices'),
                    //     'is_active' => starts_with(
                    //         request()
                    //             ->route()
                    //             ->getName(),
                    //         'admin.invoices.index'
                    //     ),
                    // ],
                ],
            ],
            //        [
            //            'url' => route('admin.video-reviews.index'),
            //            'icon' => 'flaticon2-box',
            //            'text' => __('Reviewed Video'),
            //            'is_active' => starts_with(
            //                request()
            //                    ->route()
            //                    ->getName(),
            //                'admin.video-reviews.index'
            //            ),
            //        ],
            [
                'url' => route('admin.videos.payments.index'),
                'icon' => 'fa fa-money-bill-wave',
                'text' => __('views.Payments'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.payments.'
                ),
                'children' => [],
            ],
            // [
            //     'url' => route('admin.videos.payment.items.all'),
            //     'icon' => 'fa fa-money-bill-wave',
            //     'text' => __('views.Downoloads'),
            //      'is_active' => starts_with(
            //         request()
            //             ->route()
            //             ->getName(),
            //         'admin.videos.payment.items.all'
            //     ),
            //     'children' => [],
            // ],
            [
                'url' => route('admin.videos_search_keys'),
                'icon' => 'flaticon-search',
                'text' => __('Search Keys'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos_search_keys'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.videos.admin_collections.index'),
                'icon' => 'flaticon2-files-and-folders',
                'text' => __('views.Collections'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.videos.admin_collections.'
                ),
                'children' => [],
            ],
        ];
    }
}

if (!function_exists('get_super_menu_list')) {
    function get_super_menu_list()
    {
        return $menu_list = [
            [
                'url' => route('admin.super.contact.index'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.contact'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.super.contact.index'
                ),
                'children' => [],
            ],


        ];
    }
}

if (!function_exists('get_admin_models_menu_list')) {
    function get_admin_models_menu_list()
    {
        return $menu_list = [
            [
                'url' => route('admin.models.dashboard.index'),
                'icon' => 'flaticon2-analytics',
                'text' => __('views.Home'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.vector.dashboard'
                ),
                'children' => [],
            ],
            [
                'url' => route('admin.models.dashboard.requests'),
                'icon' => 'flaticon2-request',
                'text' => __('views.Requests'),
                'is_active' => starts_with(
                    request()
                        ->route()
                        ->getName(),
                    'admin.models.requests.'
                ),
                'children' => [],
            ],


        ];
    }
}

if (!function_exists('is_in_models_website')) {
    function is_in_models_website()
    {
        if (request()->segment(1) == 'models' || request()->segment(2) == 'models')
            return true;
    }
}

if (!function_exists('is_in_video_website')) {
    function is_in_video_website()
    {
        if (request()->segment(1) == 'videos' || request()->segment(2) == 'videos')
            return true;
    }
}

if (!function_exists('is_in_vector_website')) {
    function is_in_vector_website()
    {
        if (request()->segment(1) == 'vectors' || request()->segment(2) == 'vectors')
            return true;
    }
}

if (!function_exists('is_in_photos_website')) {
    function is_in_photos_website()
    {
        if (request()->segment(1) == 'photos' || request()->segment(2) == 'photos')
            return true;
    }
}

if (!function_exists('is_in_segment')) {
    function is_in_segment($segment, $increment = 0)
    {
        $segment_number = 1;
        if (in_array(request()->segment(1), ['ar', 'en']))
            $segment_number = 2;

        $segment_number += $increment;

        if (request()->segment($segment_number) == $segment)
            return true;
    }
}

if (!function_exists('format_duration')) {
    function format_duration($seconds, $format)
    {
        $seconds = intval($seconds);
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval("PT{$seconds}S"));
        return $start->format($format);
    }
}

if (!function_exists('iso8601_duration')) {
    function iso8601_duration($seconds)
    {
        $intervals = array('D' => 60 * 60 * 24, 'H' => 60 * 60, 'M' => 60, 'S' => 1);

        $pt = 'P';
        $result = '';
        foreach ($intervals as $tag => $divisor) {
            $qty = floor($seconds / $divisor);
            if (!$qty && $result == '') {
                $pt = 'T';
                continue;
            }

            $seconds -= $qty * $divisor;
            $result .= "$qty$tag";
        }
        if ($result == '')
            $result = '0S';
        return "$pt$result";
    }
}

if (!function_exists('get_aspect_ratio')) {
    function get_aspect_ratio($width, $height)
    {
        // credit https://wistia.com/learn/production/choosing-the-aspect-ratio-for-your-video
        // 4:3 Academy format
        // 16:9 widescreen
        // 21:9 anamorphic
        // 9:16 vertical video
        // 1:1 square video
        // 4:5 portrait video
        // 2:3
        $aspect_ratios = [
            '4:3' => 4 / 3,
            '16:9' => 16 / 9,
            '21:9' => 21 / 9,
            '9:16' => 9 / 16,
            '1:1' => 1 / 1,
            '4:5' => 4 / 5,
            '2:3' => 2 / 3,
        ];

        $ratio = 0;
        $height = intval($height);
        if ($height > 0)
            $ratio = $width / $height;
        $aspect_ratio = "";
        $distance = 9999;
        foreach ($aspect_ratios as $key => $value) {
            if (abs($value - $ratio) < $distance) {
                $distance = abs($value - $ratio);
                $aspect_ratio = $key;
            }
        }
        return $aspect_ratio;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($size, $precision = 1)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'kB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}

if (!function_exists('countries_list')) {
    function countries_list()
    {
        $lang = app()->getLocale();
        return \App\Models\Countries::pluck('name_' . $lang, 'id');
    }
}

if (!function_exists('get_soundex')) {
    function get_soundex($word)
    {
        $words = explode(" ", $word);
        $final_word = "";
        foreach ($words as $word) {

            $count = 0;
            $orginal_word = trim($word);
            $word_length = strlen($word);
            $soundex_name = "";
            $soundex_char = '';
            if ($word_length > 1) {
                while ($count <= $word_length + 1) {

                    $checked_char = mb_substr($orginal_word, $count, 1);
                    //                if ($count == 0 && mb_substr($orginal_word, 0, 2) == "ال") {
                    //                    $count = 1;
                    //                    $soundex_char = '';
                    //                } else
                    if ($count == $word_length && $checked_char == "ت") {
                        $soundex_char = 'ه';
                    } elseif ($checked_char == 'ء') {
                        $soundex_char = '';
                    } elseif ($checked_char == 'آ') {
                        $soundex_char = 'ا';
                    } elseif ($checked_char == 'أ') {
                        $soundex_char = 'ا';
                    } elseif ($checked_char == 'ؤ') {
                        $soundex_char = 'و';
                    } elseif ($checked_char == 'إ') {
                        $soundex_char = 'ا';
                    } elseif ($checked_char == 'ئ') {
                        $soundex_char = 'ي';
                    } elseif ($checked_char == 'ة') {
                        $soundex_char = 'ه';
                    } elseif ($checked_char == 'ى') {
                        $soundex_char = 'ا';
                    } elseif ($checked_char == ' ') {
                        $soundex_char = '-';
                    } else {
                        $soundex_char = $checked_char;
                    }
                    $count++;
                    if ($count > $word_length + 1) {
                        break;
                    }

                    $soundex_name = $soundex_name . $soundex_char;

                }
            }
            $final_word = $final_word . "-" . $soundex_name;
        }

        return ltrim($final_word, "-");
    }
}


if (!function_exists('is_arabic')) {
    function is_arabic($string)
    {
        $re = '/^[\x{0621}-\x{064A}\{06FF}-\x{FFFF} \s\.\"\\\\\\\\ً(\)\-ـ\/,،, {1-9}]+$/mu';
        preg_match($re, $string, $matches, PREG_OFFSET_CAPTURE, 0);
        return count($matches);
    }
}

if (!function_exists('is_english')) {
    function is_english($string)
    {
        $re = "/^[a-zA-Z,-.-_.’' +]+$/mu";
        return (preg_match($re, $string));
    }
}

if (!function_exists('add_to_redirect_url_list')) {
    function add_to_redirect_url_list($from_url, $to_url, $type = 'redirect')
    {
        // type: "in:redirect|permanent"

        $domain_name = url('/');
        $from_url = str_replace($domain_name, '', $from_url);
        $to_url = str_replace($domain_name, '', $to_url);

        // this follow the current active mysql connection
        \DB::table('url_rewrites')->where('from_url', $from_url)->delete();

        /* // TODO prevent redirect loop bug */

        \DB::table('url_rewrites')->insert([
            'from_url' => $from_url,
            'to_url' => $to_url,
            'type' => $type,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        return true;
    }
}

if (!function_exists('dump_rewrite_rules_to_file')) {
    function dump_rewrite_rules_to_file()
    {
        // get data from all mysql connections
        $images_url = \DB::table('url_rewrites')
            ->select(['from_url', 'to_url', 'type'])
            ->get()
            ->map(function ($row) {
                return "rewrite \"^{$row->from_url}$\" {$row->to_url} redirect;";
            });

        $videos_url = \DB::table('url_rewrites')
            ->select(['from_url', 'to_url', 'type'])
            ->get()
            ->map(function ($row) {
                return "rewrite \"^{$row->from_url}$\" {$row->to_url} redirect;";
            });


        $vectors_url = \DB::table('url_rewrites')
            ->select(['from_url', 'to_url', 'type'])
            ->get()
            ->map(function ($row) {
                return "rewrite \"^{$row->from_url}$\" {$row->to_url} redirect;";
            });

        $content = $images_url
            ->merge($videos_url)
            ->merge($vectors_url)
            ->pipe(function ($collection) {
                return implode("\n", $collection->toArray());
            });

        file_put_contents(public_path('nginx.conf'), $content);
        devops_reload_nginx();
        return true;
    }
}

if (!function_exists('devops_reload_nginx')) {
    function devops_reload_nginx()
    {
        $devops_commands = base_path('storage/devops_commands');
        if (!file_exists($devops_commands)) {
            mkdir($devops_commands, 0755, true);
        }

        file_put_contents($devops_commands . "/nginx_reload", "1");
        return true;
    }
}

if (!function_exists('slugify_v2_remove')) {
    function slugify_v2_remove($search, $replace, $str)
    {
        $str = str_replace($search, $replace, $str);
        if (mb_strpos($str, trim($search) . " ") === 0) {
            $str = mb_substr($str, mb_strlen(trim($search)) + 1);
        }
        return $str;
    }
}

if (!function_exists('slugify_v2')) {
    function slugify_v2($str)
    {
        $str = strtolower($str);
        // remove prepositions
        $prepositions = [
            'a',
            'an',
            'of',
            'with',
            'and',
            'the',
            'in',
            'to',
            'from',
            'at',
            'is',
            'up',
            'for',
            'are',
            'or',
            'on',
        ];
        foreach ($prepositions as $preposition) {
            $str = slugify_v2_remove(" $preposition ", ' ', $str);
        }

        $str = explode(' ', $str, 11);
        if (isset($str[10])) {
            unset($str[10]);
        }
        $str = implode(" ", $str);
        $max_length = 65;
        while (1) {
            if (mb_strlen($str) >= $max_length) {
                $index = mb_strrpos($str, ' ');
                $str = mb_substr($str, 0, $index);
            }
            if (mb_strlen($str) <= $max_length) {
                break;
            }
        }

        // slugify
        $str = \Illuminate\Support\Str::slug($str);
        return $str;
    }
}

if (!function_exists('startsWith')) {
    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}

if (!function_exists('get_vue_image_store_routes')) {
    function get_vue_image_store_routes($submission_id = 0)
    {

        $routes = [
            'videos' => [
                'arabsstock' => [
                    'options' => route('admin.api.videos.options'),
                    'filters' => route('admin.api.videos.filters'),
                    'index' => route('admin.api.videos.index'),
                    'update_multi' => route('admin.api.videos.update_multi'),
                    'submit' => route('admin.videos.api.reviews.change_status'),
                ],
                'contributor_reviews' => [
                    'options' => route('admin.videos.api.reviews.options', $submission_id),
                    'update_multi' => route('admin.videos.api.reviews.update_multi', $submission_id),
                    'releases' => route('admin.api.releases.store', $submission_id),
                    'filters' => route('admin.videos.api.reviews.filters', $submission_id),
                    'index' => route('admin.videos.api.reviews.index', $submission_id),
                    'submit' => route('admin.videos.api.reviews.submit', $submission_id),
                    'change_status' => route('admin.videos.api.reviews.change_status'),
                    'update_after_publish' => route('admin.videos.api.reviews.update_after_publish', $submission_id),

                ],
            ],
            'images' => [
                'arabsstock' => [
                    'options' => route('admin.api.images.options'),
                    'filters' => route('admin.api.images.filters'),
                    'index' => route('admin.api.images.index'),
                    'update_multi' => route('admin.api.images.update_multi'),
                    'submit' => route('admin.api.reviews.change_status'),

                ],
                'contributor_reviews' => [
                    'options' => route('admin.api.reviews.options', $submission_id),
                    'update_multi' => route('admin.api.reviews.update_multi', $submission_id),
                    'releases' => route('admin.api.releases.store', $submission_id),
                    'filters' => route('admin.api.reviews.filters', $submission_id),
                    'index' => route('admin.api.reviews.index', $submission_id),
                    'submit' => route('admin.api.reviews.submit', $submission_id),
                    'change_status' => route('admin.api.reviews.change_status'),
                    'update_after_publish' => route('admin.api.reviews.update_after_publish', $submission_id),

                ]
            ],

            'vectors' => [
                'arabsstock' => [
                    'options' => route('admin.api.vectors.options'),
                    'filters' => route('admin.api.vectors.filters'),
                    'index' => route('admin.api.vectors.index'),
                    'update_multi' => route('admin.api.vectors.update_multi'),
                    'submit' => route('admin.vectors.api.reviews.change_status'),
                ],
                'contributor_reviews' => [
                    'options' => route('admin.vectors.api.reviews.options', $submission_id),
                    'update_multi' => route('admin.vectors.api.reviews.update_multi', $submission_id),
                    'releases' => route('admin.vectors.api.releases.store', $submission_id),
                    'filters' => route('admin.vectors.api.reviews.filters', $submission_id),
                    'index' => route('admin.vectors.api.reviews.index', [$submission_id]),
                    'submit' => route('admin.vectors.api.reviews.submit', $submission_id),
                    'change_status' => route('admin.vectors.api.reviews.change_status'),
                    'update_after_publish' => route('admin.vectors.api.reviews.update_after_publish', $submission_id),


                ]
            ],
        ];

        return $routes;
    }
}


// function get_vue_image_store_routes($submission_id = 0) {

//     $routes = [
//         'videos' => [
//             'arabsstock' => [
//                 'options' => route('admin.api.videos.options'),
//                 'filters' => route('admin.api.videos.filters'),
//                 'index' => route('admin.api.videos.index'),
//                 'update_multi' => route('admin.api.videos.update_multi'),
//             ],
//             'contributor_reviews' => [
//                 'options' => route('admin.videos.api.reviews.options', $submission_id),
//                 'update_multi' => route('admin.videos.api.reviews.update_multi', $submission_id),
//                 'releases' => route('admin.api.releases.store', $submission_id),
//                 'filters' => route('admin.videos.api.reviews.filters', $submission_id),
//                 'index' => route('admin.videos.api.reviews.index', $submission_id),
//                 'submit' => route('admin.videos.api.reviews.submit', $submission_id),
//             ],
//         ],
//         'images' => [
//             'arabsstock' => [
//                 'options' => route('admin.api.images.options'),
//                 'filters' => route('admin.api.images.filters'),
//                 'index' => route('admin.api.images.index'),
//                 'update_multi' => route('admin.api.images.update_multi'),
//             ],
//             'contributor_reviews' => [
//                 'options' => route('admin.api.reviews.options', $submission_id),
//                 'update_multi' => route('admin.api.reviews.update_multi', $submission_id),
//                 'releases' => route('admin.api.releases.store', $submission_id),
//                 'filters' => route('admin.api.reviews.filters', $submission_id),
//                 'index' => route('admin.api.reviews.index', $submission_id),
//                 'submit' => route('admin.api.reviews.submit', $submission_id),
//             ]
//         ],
//     ];

//     return $routes;
// }

if (!function_exists('statsd_increment')) {
    function statsd_increment($event)
    {
        $statsd = new League\StatsD\Client();
        $statsd->configure(array(
            'host' => env('STATSD_HOST'),
            'port' => env('STATSD_PORT'),
            'namespace' => env('STATSD_NAMESPACE'),
            'throwConnectionExceptions' => false
        ));

        $statsd->increment($event);
    }
}

if (!function_exists('updateSlug')) {
    function updateSlug(array $keywords, string $title)
    {
        $title = str_replace('-', " ", $title);
        $title = str_replace($keywords, " ", $title);
        $slug = Str::slug($title, '-');
        return $slug;
    }
}

if (!function_exists('filterSearchKeyword')) {
    function filterSearchKeyword($q)
    {
        $spcial_charchter = [
            ',', '#', '>', '$', '%', '@', '&', '*', '(', ')', '|'
        ];
        $q = str_replace($spcial_charchter, '', $q);
        return $q;
    }
}

if (!function_exists('cdn')) {
    function cdn($path = '')
    {
        $path = trim($path, '/\\');
        return trim(config('filesystems.disks.s3.url') . "/$path", '/\\');
    }
}

if (!function_exists('getNameFromUrl')) {
    function getNameFromUrl($Url)
    {
        $name = substr($Url, strrpos($Url, '/') + 1);
        return $name;
    }
}

if (!function_exists('settings')) {
    function settings()
    {
        try {
            if (is_in_video_website()) {
                return video_settings();
            } elseif (is_in_vector_website()) {
                return vector_settings();
            } else
                return image_settings();
        } catch (\Exception $exception) {
            return false;
        }

    }
}

if (!function_exists('getHeight')) {
    function getHeight($image)
    {
        $size = getimagesize($image);
        $height = intval($size[1]);
        return $height;
    }
}

if (!function_exists('getWidth')) {
    function getWidth($image)
    {
        $size = getimagesize($image);
        $width = intval($size[0]);
        return $width;
    }
}

if (!function_exists('removeSpecialChar')) {
    function removeSpecialChar($str)
    {
        $res = preg_replace('/[\@\.\;\' "]+/', '', $str);
        return $res;
    }

}
if (!function_exists('getLicenses')) {
    function getLicenses(array $data)
    {
        $licenses_key = $data;
        $licenses = [];
        foreach ($licenses_key as $key => $value) {
            $license = new \stdClass();
            $license->name = $value;
            $license->title = __("global.{$value}");
            $licenses[$value] = $license;
        }

        return $licenses;

    }
}


if (!function_exists('resizeImageWithImagick')) {

    function resizeImageWithImagick($filename, $new_image, $new_width, $new_height, $dpi = 96, $scale = false, $quality = 90)
    {
        \Log::channel('info')->info("file  {$filename}");

        // Calculate right height
        if ($scale) {
            $new_width = ceil($new_width * $scale);
            $new_height = ceil($new_height * $scale);
        } else {
            list($width, $height) = getimagesize($filename);
            if (!$new_width) {
                $new_width = floor(round($new_height * $width / $height, 2));
            }
            if (!$new_height) {
                $new_height = floor(round($new_width * $height / $width, 2));
            }
        }

        // Get image
        $image = new \Imagick(realpath($filename));
        autoRotateImage($image);
        $image->setImageUnits(\Imagick::RESOLUTION_PIXELSPERINCH);
        $image->setImageResolution($dpi, $dpi);
        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality($quality);
        \Log::channel('info')->info("new file dimention w {$new_width}  h {$new_height}");
        $image->resizeImage($new_width, $new_height, \Imagick::FILTER_LANCZOS, 1);
        // Some code to correct the color profile
        $version = $image->getVersion();
        $profile = public_path("sRGB_IEC61966-2-1_no_black_scaling.icc");
        if ((is_array($version) === true) && (array_key_exists("versionString", $version) === true)) {
            $version = preg_replace("~ImageMagick ([^-]*).*~", "$1", $version["versionString"]);
            if (is_file(sprintf("/usr/share/ImageMagick-%s/config/sRGB.icm", $version)) === true) {
                $profile = sprintf("/usr/share/ImageMagick-%s/config/sRGB.icm", $version);
            }
        }
        if (($srgb = file_get_contents($profile)) !== false) {
            $image->profileImage("icc", $srgb);
            $image->setImageColorSpace(\Imagick::COLORSPACE_SRGB);
        }
        header("Content-Type: image/jpg");
        $image->writeImage($new_image);
        // Clear all resources associated to the \Imagick object
        $image->clear();
    }
}

if (!function_exists('autoRotateImage')) {
    function autoRotateImage($image)
    {
        $orientation = $image->getImageOrientation();

        \Log::channel('info')->info("orientation  {$orientation}");
        switch ($orientation) {
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case \Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);

    }
}


if (!function_exists('addMaskedImages')) {
    function addMaskedImages($image)
    {
        $img = Image::make(cdn($image->preview));
        $img->rectangle(0, $img->getHeight() - 40, $img->getWidth(), $img->getHeight(), function ($draw) {
            $draw->background('#eee');
        });
        $img->text('arabsstock.com .  P' . $image->id, $img->getWidth() / 2, $img->getHeight() - 15, function ($font) {
            $font->file(public_path('fonts/font/font-bold/HelveticaNeueW23foSKY-Bd.ttf'));
            $font->size(18);
            $font->color('#000');
            $font->align('center');
            $font->valign('bottom');
        });
        $img->stream();
        $image->search = strtr($image->preview, ["preview" => "search"]);
        $image->height_search = $image->hight_preview;
        $image->width_search = $image->width_preview;
        $image->save();
        Storage::disk('s3')->put($image->search, file_get_contents($img->__toString()));
    }
}

if (!function_exists('enable_ql')) {
    function enable_ql()
    {
        return \Illuminate\Support\Facades\DB::enableQueryLog();
    }
}

if (!function_exists('ql')) {
    function ql()
    {
        return \Illuminate\Support\Facades\DB::getQueryLog();
    }
}
if (!function_exists('sync_tags')) {
    function sync_tags($record, array $tags = [], $local = 'ar')
    {
        $tags = array_filter($tags);
        if ($record) {
            \Illuminate\Support\Facades\Log::channel('info')->info("sync_tags: ({" . class_basename($record) . "," . $record->id . "},[" . implode(',', $tags) . "],{$local}) -> " . @debug_backtrace()[1]['function'] . '--auth:' . auth()->id());
            $tags_to_sync = [];
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (!empty($tag)) {
                    $tag = strtolower($tag);
                    $item = Tag::firstOrCreate(['title' => $tag, 'slug' => slug_tag($tag), 'local' => $local]);
                    $tags_file[] = $item;
                }
            }
            $collection = collect($tags_file);
            $collection = $collection->unique('id');
            $ids = $record->tags()->where('local', $local)->pluck('id')->toArray();

            if (count($ids))
                $collection = $collection->whereNotIn('id', $ids);

            if (count($collection))
                $record->tags()->where('local', $local)->saveMany($collection);


        }
    }
}
if (!function_exists('slug_tag')) {
    function slug_tag($string, $separator = '-')
    {
        if (is_null($string)) {
            return "";
        }

        $string = trim($string);

        $string = mb_strtolower($string, "UTF-8");;

        $string = preg_replace("/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $string);

        $string = preg_replace("/[\s-]+/", " ", $string);

        $string = preg_replace("/[\s_]/", $separator, $string);

        return $string;
    }
}

/**
 * @param $format
 * @param null $time
 * @return false|int|null|string
 */
if (!function_exists('format_date')) {
    function format_date($format, $time = null)
    {
        if ($time instanceof Carbon\Carbon) {
            $time = $time->timestamp;
        } elseif ($time instanceof DateTime) {
            $time = $time->getTimestamp();
        } elseif (is_numeric($time))
            $time = $time;
        elseif (strtotime($time))
            $time = strtotime($time);
        else
            $time = time();
        if (is_null($time)) $time = time();
        $ar_vars = [
            'January' => 'يناير',
            'Jan' => 'يناير',
            'February' => 'فبراير',
            'Feb' => 'فبراير',
            'March' => 'مارس',
            'Mar' => 'مارس',
            'April' => 'ابريل',
            'Apr' => 'ابريل',
            'May' => 'مايو',
            'June' => 'يونيو',
            'Jun' => 'يونيو',
            'July' => 'يوليو',
            'Jul' => 'يوليو',
            'August' => 'اغسطس',
            'Aug' => 'اغسطس',
            'September' => 'سبتمبر',
            'Sep' => 'سبتمبر',
            'October' => 'أكتوبر',
            'Oct' => 'أكتوبر',
            'November' => 'نوفمبر',
            'Nov' => 'نوفمبر',
            'December' => 'ديسمبر',
            'Dec' => 'ديسمبر',
            'am' => 'ص',
            'AM' => 'صباحا',
            'pm' => 'م',
            'PM' => 'مساءاً',
            'Sat' => 'السبت',
            'Saturday' => 'السبت',
            'Sun' => 'الأحد',
            'Sunday' => 'الأحد',
            'Mon' => 'الإثنين',
            'Monday' => 'الإثنين',
            'Tue' => 'الثلاثاء',
            'Tuesday' => 'الثلاثاء',
            'Wed' => 'الأربعاء',
            'Wednesday' => 'الأربعاء',
            'Thu' => 'الخميس',
            'Thursday' => 'الخميس',
            'Fri' => 'الجمعة',
            'Friday' => 'الجمعة',
        ];
        $time = date($format, $time);
        if (app()->getLocale() == 'ar')
            $time = str_replace(array_keys($ar_vars), array_values($ar_vars), $time);
        //    $time_array = explode(' ', $time);
        //    $time_array = array_reverse($time_array);
        //    $time = implode(' ', $time_array);
        return $time;
    }
}
if (!function_exists('size_format')) {
    function size_format($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}


if (!function_exists('dimensions_format')) {
    function dimensions_format($height, $width)
    {
        $lang = app()->getLocale();
        $dimensions = $lang === "ar" ? "PX {$height}X{$width}" : "{$height}X{$width} PX";
        return $dimensions;
    }
}

if (!function_exists('paginate_array')) {
    function paginate_array($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }
}

if (!function_exists('paginate_array')) {
    function paginate_array($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);

        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }
}


if (!function_exists('get_vue_image_store_remove_bg_routes')) {
    function get_vue_image_store_remove_bg_routes($submission_id = 0)
    {

        $routes = [

            'images' => [
                'arabsstock' => [
                    'options' => route('admin.api.images.options'),
                    'filters' => route('admin.api.images.filters'),
                    'index' => route('admin.api.images.index'),

                    'update_multi' => route('admin.api.images.update_multi_remove_bg'),
                    'update_multi_remove_bg_display' => route('admin.api.images.update_multi_remove_bg_display'),
                    'submit' => route('admin.api.reviews.change_status'),

                ],

            ],


        ];

        return $routes;
    }
}


if (!function_exists('get_designer_menu_list')) {
    function get_designer_menu_list()
    {
        $route_name = request()->route()->getName();
        return $menu_list = [
            [
                'url' => route('admin.accountant.downloads'),
                'icon' => 'flaticon2-grids',
                'text' => __('views.ImagesWarehouseRemoveBg'),
                'is_active' => $route_name == 'admin.images.warehouse_remove_bg.index',
            ],
            [
                'url' => route('admin.images.warehouse_remove_bg.check'),
                'icon' => 'flaticon2-grids',
                'text' => __('views.ImagesWarehouseRemoveBgCheck'),
                'is_active' => $route_name == 'admin.images.warehouse_remove_bg.check',
            ],
            [
                'url' => route('admin.images.warehouse_remove_bg.check_manual'),
                'icon' => 'flaticon2-grids',
                'text' => __('views.ImagesWarehouseRemoveBgCheckManual'), //
                'is_active' => $route_name == 'admin.images.warehouse_remove_bg.check_manual',

            ]


        ];
    }
}
function statistic($key, $default = null)
{
    return \Illuminate\Support\Arr::get(app('statistics'), $key, $default);
}

function image_settings()
{
    return cache()->rememberForever('admin_image_settings', function () {
        return AdminImageSettings::first();
    });
}

function video_settings()
{
    return cache()->rememberForever('admin_video_settings', function () {
        return AdminVideoSettings::first();
    });
}

function vector_settings()
{
    return cache()->rememberForever('admin_vector_settings', function () {
        return AdminVectorSettings::first();
    });
}
