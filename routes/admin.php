<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => \App\Http\Middleware\DeveloperAuth::class], function () {
    Route::get('panel/v2/super/contact/preview', 'AdminV2\ContactController@preview')->name('admin.super.contact.preview');
    Route::middleware(\App\Http\Middleware\DeveloperAuth::class)->group(function () {
        Route::get('devusers/{id}', 'AdminV2\DevLoginToUser@loginDev');
    });

    Route::get('admin/login', 'Auth\LoginController@showLoginFormAdmin')->name('loginAdmin');
    Route::post('admin/login', 'Auth\LoginController@loginAdmin')->name('sendLoginAdmin');
    Route::group(['middleware' => ['language', 'auth', 'role:admin_video|admin_video_editor|admin|admin_image_editor|admin_vector|admin_vector_editor']], function () {
//    videos
        // Route::get('pinterest/callback', 'AdminV2\PinterestContoller@callback')->name('admin.pinterest.callback');
        Route::get('panel/v2/{type}/warehouse-contributor', 'AdminV2\WareHouseContributorContoller@index')->name('admin.warehouse-contributor.index');
        Route::post('panel/v2/{type}/warehouse-contributor/datatable', 'AdminV2\WareHouseContributorContoller@datatable')->name('admin.warehouse-contributor.datatable');
        Route::get('panel/v2/{type}/warehouse-contributor/{id?}/user', 'AdminV2\WareHouseContributorContoller@WareHouseContributor')->name('admin.warehouse-contributor.review');
        Route::prefix('panel/v2/{type}/warehouse_check_requests')->group(function () {
            Route::get('/', 'AdminV2\WarehouseCheckController@index')->where('type', 'images|vectors|videos')->name('admin.warehouse_check_requests.index');
            Route::get('{id}/warehouse_check', 'AdminV2\WarehouseCheckController@warehouse_check')->where('type', 'images|vectors|videos')->name('admin.warehouse_check_requests.warehouse_check.index');
            Route::post('{id}/warehouse_check/datatable', 'AdminV2\WarehouseCheckController@warehouse_check_datatable')->where('type', 'images|vectors|videos')->name('admin.warehouse_check_requests.warehouse_check.datatable');
            Route::get('create', 'AdminV2\WarehouseCheckController@create_warehouse_check_request')->where('type', 'images|vectors|videos')->name('admin.warehouse_check_requests.create');
            Route::post('warehouse_check/{id}', 'AdminV2\WarehouseCheckController@insert_queue')->where('type', 'images|vectors|videos')->name('admin.warehouse_check.insert_queue');
            Route::get('warehouse_check/{id}/admin_file_reupload_not_found', 'AdminV2\WarehouseCheckController@admin_file_reupload_not_found')->where('type', 'images|vectors|videos')->name('admin.warehouse_check.admin_file_reupload_not_found');
        });
//videos
    });

    Route::group(['middleware' => ['language', 'auth', 'role:admin|admin_video|admin_vector|accountant']], function () {
        Route::match(['get', 'post'], 'panel/v2/transfer-content', 'AdminV2\WareHouseContributorContoller@transfer_content')->name('admin.transfer_content');
        Route::get('panel/v2/accountant/downloads', 'AdminV2\AccountantController@downloads')->name('admin.accountant.downloads');
        Route::get('panel/v2/accountant/payments', 'AdminV2\AccountantController@payments')->name('admin.accountant.payments');
        Route::get('panel/v2/accountant/contributor_downloads', 'AdminV2\AccountantController@contributor_downloads')->name('admin.accountant.contributor_downloads');
        Route::get('panel/v2/accountant/payouts', 'AdminV2\AccountantController@payouts')->name('admin.accountant.payouts');
        Route::get('panel/v2/accountant/contents', 'AdminV2\AccountantController@contents')->name('admin.accountant.contents');
        Route::get('panel/v2/accountant/export-contents', 'AdminV2\AccountantController@export_contents')->name('admin.accountant.export_contents');

        Route::get('panel/v2/user-plans/export_downloads', 'AdminV2\UserPlanController@export_downloads')->name('admin.user_plans.items.export_downloads');
        Route::get('panel/v2/videos/user-plans/export_downloads', 'AdminV2\UserPlanController@export_downloads_videos')->name('admin.videos.user_plans.items.export_downloads');
        Route::get('panel/v2/vectors/user-plans/export_downloads', 'AdminV2\UserPlanController@export_downloads_vectors')->name('admin.vector.user_plans.items.export_downloads');
        Route::get('panel/v2/performance-reports/payments/export', 'AdminV2\PerformanceReportController@export_payments')->name('admin.performance_reports.payment.export');
        Route::get('panel/v2/performance-reports/image-payment/export', 'AdminV2\PerformanceReportController@export_image_payments')->name('admin.performance_reports.image_payment.export');
        Route::get('panel/v2/performance-reports/video-payment/export', 'AdminV2\PerformanceReportController@export_video_payments')->name('admin.performance_reports.video_payment.export');
        Route::get('panel/v2/performance-reports/vector-payment/export', 'AdminV2\PerformanceReportController@export_vector_payments')->name('admin.performance_reports.vector_payment.export');
        Route::get('panel/v2/user-plans/export_contributor_downloads', 'AdminV2\UserPlanController@export_contributor_downloads')->name('admin.user_plans.items.export_contributor_downloads');
        Route::get('panel/v2/contributors/payout/export', 'AdminV2\PayoutController@export_payout')->name('admin.payout.export');
        Route::get('panel/v2/contributors/statistics/export', 'AdminV2\PayoutController@statistics_export')->name('admin.contributors.statistics.export');
        Route::get('panel/v2/members/ajax', 'AdminV2\MemberController@ajax')->name('admin.members.ajax');
        Route::get('panel/v2/teams/ajax', 'AdminV2\TeamsController@ajax')->name('admin.teams.ajax');
        Route::get('panel/v2/contributors/ajax', 'AdminV2\ContributorController@ajax')->name('admin.contributors.ajax');
        Route::get('panel/v2/statistics/export', 'AdminV2\PerformanceReportController@export_statistics')->name('admin.statistics.export');
        Route::get('panel/v2/statistics/update', 'AdminV2\DashboardController@update_statistics')->name('admin.statistics.update');

    });

// images
    Route::group(['middleware' => ['role:admin|admin_image_editor', 'language']], function () {
        // Folders
        Route::get('panel/v2/folders', 'AdminV2\FolderController@index')->name('admin.folders.index');
        Route::post('panel/v2/folders/datatable',
            'AdminV2\FolderController@datatable')->name('admin.folders.datatable');

        //images
        Route::get('api/contributor_images', 'AdminV2\ContributorImageController@index')->name('admin.api.contributor_images.index');;
        Route::get('api/contributor_images/delete', 'AdminV2\ContributorImageController@delete')->name('admin.api.contributor_images.delete');;
        Route::get('api/contributor_images/delete-all', 'AdminV2\ContributorImageController@delete_all')->name('admin.api.contributor_images.delete_all');;
        Route::get('api/contributor_images/options', 'AdminV2\ContributorImageController@options')->name('admin.api.contributor_images.options');
        Route::get('api/contributor_images/filters', 'AdminV2\ContributorImageController@filters')->name('admin.api.contributor_images.filters');
        Route::post('api/contributor_images/multi', 'AdminV2\ContributorImageController@update_multi')->name('admin.api.contributor_images.update_multi');
        Route::post('api/contributor_images/submit', 'AdminV2\ContributorImageController@submit')->name('admin.api.contributor_images.submit');
        Route::post('api/contributor_images/resubmit', 'AdminV2\ContributorImageController@resubmit')->name('admin.api.contributor_images.resubmit');
        Route::delete('panel/v2/images/{id}', 'AdminV2\ImageController@destroy')->name('admin.images.destroy');

        Route::get('panel/v2/images/{id}/edit', 'AdminV2\ImageController@edit')->name('admin.images.edit');
        Route::post('panel/v2/images/{id}/activate', 'AdminV2\ImageController@activate')->name('admin.images.activate');
        Route::post('panel/v2/images/{id}/admin-collection',
            'AdminV2\ImageController@add_to_admin_collection')->name('admin.images.admin_collections.update');
        Route::put('panel/v2/Images/{id}', 'AdminV2\ImageController@update')->name('admin.images.update');


        Route::get('panel/v2/images/pending', 'AdminV2\ImageController@index_pending')->name('admin.images.pending.index');
        Route::post('panel/v2/images/pending/datatable',
            'AdminV2\ImageController@datatable_pending')->name('admin.images.pending.datatable');
        /* s:route-deleted-imges */
        Route::get('panel/v2/images/deleted', 'AdminV2\ImageController@index_deleted')->name('admin.images.deleted.index');
        Route::post('panel/v2/images/deleted/datatable',
            'AdminV2\ImageController@datatable_deleted')->name('admin.images.deleted.datatable');
        /* e:route-deleted-imges */
        /* s:route-deleted-contributor-imges */
        Route::get('panel/v2/contributor_images/deleted', 'AdminV2\ImageController@index_deleted_contributor_images')->name('admin.contributor_images.deleted.index');
        Route::post('panel/v2/contributor_images/deleted/datatable',
            'AdminV2\ImageController@datatable_deleted_contributor_images')->name('admin.contributor_images.deleted.datatable');
        /* e:route-deleted-contributor-imges */

        Route::get('panel/v2/images', 'AdminV2\ImageController@index')->name('admin.images.index');
        Route::post('panel/v2/images/datatable', 'AdminV2\ImageController@datatable')->name('admin.images.datatable');
        Route::get('panel/v2/images/export', 'AdminV2\ImageController@export')->name('admin.images.export');


        Route::get('panel/v2/images/warehouse', 'AdminV2\ImageController@index_warehouse')->name('admin.images.warehouse.index');
        Route::get('panel/v2/images/search-keys', 'AdminV2\ImageController@search_keys')->name('admin.images_search_keys');

        // Submissions
        Route::match(['get', 'post'], 'panel/v2/contributors/submissions/update-images-data', 'AdminV2\ContributorController@update_images_data')->name('admin.contributors.submissions.update_images_data');
        Route::get('panel/v2/contributors/submissions', 'AdminV2\ContributorController@index_submissions')->name('admin.contributors.submissions.index');
        Route::get('panel/v2/contributors/submissions/{id}/review', 'AdminV2\ContributorController@index_review')->name('admin.contributors.submissions.review');
        Route::post('panel/v2/contributors/submissions/datatable', 'AdminV2\ContributorController@datatable_submissions')->name('admin.contributors.submissions.datatable');
        Route::post('panel/v2/contributors/submissions/datatable/review', 'AdminV2\ContributorController@datatable_submissions_update_after_publish')->name('admin.contributors.datatable_submissions_update_after_publish.datatable');

        Route::name('admin.')->group(function () {
            Route::resource('panel/v2/weekly_letters', 'AdminV2\WeeklyLetterController')->only('index', 'create', 'store', 'show');
            Route::post('panel/v2/weekly_letters/{id}/submit', 'AdminV2\WeeklyLetterController@submit')->name('weekly_letters.submit');

        });

    });
    Route::group(['middleware' => ['role:admin|admin_image_editor|admin_vector_editor|admin_video_editor', 'language']], function () {
        // Folders

        Route::name('admin.')->group(function () {
            Route::resource('panel/v2/{category}/rejection_reasons', 'AdminV2\RejectionReasonsController')->only('index', 'create', 'store', 'edit', 'update');
        });

    });

    Route::get('panel/v2/logout', 'Auth\LoginController@logout')->name('admin.logout'); // TODO
    Route::get('panel/v2/downloads', 'AdminV2\DownloadsController@index')->name('admin.downloads.index')->middleware('role:admin|admin_video|admin_vector,language');
    Route::get('panel/v2/downloads/free', 'AdminV2\DownloadsController@free')->name('admin.downloads.free')->middleware('role:admin|admin_video|admin_vector,language');
    Route::get('panel/v2/downloads/export', 'AdminV2\DownloadsController@export')->name('admin.downloads.export')->middleware('role:admin|admin_video|admin_vector|accountant,language');
    Route::get('panel/v2/downloads/{download}', 'AdminV2\DownloadsController@show')->name('admin.downloads.show')->middleware('role:admin|admin_video|admin_vector,language');
    Route::delete('panel/v2/downloads/{download}', 'AdminV2\DownloadsController@destroy')->name('admin.downloads.destroy')->middleware('role:admin|admin_video|admin_vector,language');
    Route::post('panel/v2/contributors/datatable', 'AdminV2\ContributorController@datatable')->name('admin.contributor.datatable')->middleware('role:admin|admin_video|admin_vector|admin_image_editor|accountant,language');
    Route::group(['middleware' => ['role:admin', 'language']], function () {
        Route::get('panel/v2/dashboard', 'AdminV2\DashboardController@index')->name('admin.dashboard.index');
        Route::get('panel/v2/dashboard/lang/{lang}',
            'AdminV2\DashboardController@lang')->name('admin.dashboard.lang'); // new
        Route::get('panel/v2/users/{id}/edit', 'AdminV2\DashboardController@index')->name('admin.users.edit'); // TODO


        // Categories
        Route::get('panel/v2/categories', 'AdminV2\CategoryController@index')->name('admin.categories.index');
        Route::get('panel/v2/categories/create', 'AdminV2\CategoryController@create')->name('admin.categories.create');
        Route::post('panel/v2/categories', 'AdminV2\CategoryController@store')->name('admin.categories.store');
        Route::get('panel/v2/categories/{id}/edit', 'AdminV2\CategoryController@edit')->name('admin.categories.edit');
        Route::post('panel/v2/categories/{id}/activate',
            'AdminV2\CategoryController@activate')->name('admin.categories.activate');
        Route::put('panel/v2/categories/{id}', 'AdminV2\CategoryController@update')->name('admin.categories.update');
        Route::delete('panel/v2/categories/{id}', 'AdminV2\CategoryController@destroy')->name('admin.categories.destroy');
        Route::post('panel/v2/categories/datatable',
            'AdminV2\CategoryController@datatable')->name('admin.categories.datatable');

        // Categories for admin
        Route::get('panel/v2/categories_admin/',
            'AdminV2\CategoryAdminController@index')->name('admin.categories_admin.index');
        Route::get('panel/v2/categories_admin/create',
            'AdminV2\CategoryAdminController@create')->name('admin.categories_admin.create');
        Route::post('panel/v2/categories_admin',
            'AdminV2\CategoryAdminController@store')->name('admin.categories_admin.store');
        Route::get('panel/v2/categories_admin/{id}/edit',
            'AdminV2\CategoryAdminController@edit')->name('admin.categories_admin.edit');
        Route::put('panel/v2/categories_admin/{id}',
            'AdminV2\CategoryAdminController@update')->name('admin.categories_admin.update');
        Route::delete('panel/v2/categories_admin/{id}',
            'AdminV2\CategoryAdminController@destroy')->name('admin.categories_admin.destroy');
        Route::post('panel/v2/categories_admin/datatable',
            'AdminV2\CategoryAdminController@datatable')->name('admin.categories_admin.datatable');


        // Categories for contributers
        Route::get('panel/v2/categories_contributor/', 'AdminV2\CategoryContributorController@index')->name('admin.categories_contributores.index');
        Route::get('panel/v2/categories_contributor/create', 'AdminV2\CategoryContributorController@create')->name('admin.categories_contributores.create');
        Route::post('panel/v2/categories_contributor', 'AdminV2\CategoryContributorController@store')->name('admin.categories_contributores.store');
        Route::get('panel/v2/categories_contributor/{id}/edit', 'AdminV2\CategoryContributorController@edit')->name('admin.categories_contributores.edit');
        Route::put('panel/v2/categories_contributor/{id}', 'AdminV2\CategoryContributorController@update')->name('admin.categories_contributores.update');
        Route::delete('panel/v2/categories_contributor/{id}', 'AdminV2\CategoryContributorController@destroy')->name('admin.categories_contributores.destroy');
        Route::post('panel/v2/categories_contributor/datatable', 'AdminV2\CategoryContributorController@datatable')->name('admin.categories_contributores.datatable');

        //Folders
        Route::get('panel/v2/folders/create', 'AdminV2\FolderController@create')->name('admin.folders.create');
        Route::post('panel/v2/folders', 'AdminV2\FolderController@store')->name('admin.folders.store');
        Route::get('panel/v2/folders/{id}/edit', 'AdminV2\FolderController@edit')->name('admin.folders.edit');
        Route::post('panel/v2/folders/{id}/activate', 'AdminV2\FolderController@activate')->name('admin.folders.activate');
        Route::put('panel/v2/folders/{id}', 'AdminV2\FolderController@update')->name('admin.folders.update');
        Route::delete('panel/v2/folders/{id}', 'AdminV2\FolderController@destroy')->name('admin.folders.destroy');

        //evaluations
        Route::get('panel/v2/evaluations', 'AdminV2\EvaluationController@index')->name('admin.evaluations.index');
        Route::get('panel/v2/evaluations/{id}', 'AdminV2\EvaluationController@show')->name('admin.evaluations.show');
        Route::post('panel/v2/evaluations/datatable', 'AdminV2\EvaluationController@datatable')->name('admin.evaluations.datatable');
        // Images
        Route::get('panel/v2/images/filemanger/create', 'AdminV2\ImageController@create_filemanger')->name('admin.images.filemanger.create');
        Route::get('panel/v2/images/filemanger/replace', 'AdminV2\ImageController@replace_filemanger')->name('admin.images.filemanger.replace');
        Route::get('panel/v2/images/filemanger/psd', 'AdminV2\ImageController@psd_filemanger')->name('admin.images.filemanger.psd');
        Route::post('panel/v2/images/filemanger/check_unique',
            'AdminV2\ImageController@check_unique')->name('admin.images.filemanger.check_unique');
        Route::post('panel/v2/images/filemanger',
            'AdminV2\ImageController@store_filemanger')->name('admin.images.filemanger.store');


        // Members
        Route::get('panel/v2/members', 'AdminV2\MemberController@index')->name('admin.members.index');
        Route::get('panel/v2/members/create', 'AdminV2\MemberController@create')->name('admin.members.create');
        Route::post('panel/v2/members/store', 'AdminV2\MemberController@store')->name('admin.members.store');
        Route::get('panel/v2/members/{id}/edit', 'AdminV2\MemberController@edit')->name('admin.members.edit');
        Route::put('panel/v2/members/{id}', 'AdminV2\MemberController@update')->name('admin.members.update');
        Route::post('panel/v2/members/{id}/activate', 'AdminV2\MemberController@activate')->name('admin.members.activate');
        Route::delete('panel/v2/members/{id}', 'AdminV2\MemberController@destroy')->name('admin.members.destroy');
        Route::get('panel/v2/members/datatable', 'AdminV2\MemberController@datatable')->name('admin.members.datatable');

        //payout
        Route::get('panel/v2/payout', 'AdminV2\PayoutController@index')->name('admin.payout.index');
        Route::get('panel/v2/payout/create', 'AdminV2\PayoutController@create')->name('admin.payout.create');
        Route::post('panel/v2/payout/store', 'AdminV2\PayoutController@store')->name('admin.payout.store');
        Route::get('panel/v2/payout/{id}/edit', 'AdminV2\PayoutController@edit')->name('admin.payout.edit');
        Route::put('panel/v2/payout/{id}', 'AdminV2\PayoutController@update')->name('admin.payout.update');
        Route::post('panel/v2/payout/{id}/payout', 'AdminV2\PayoutController@payout')->name('admin.payout.payout');
        Route::delete('panel/v2/payout/{id}', 'AdminV2\PayoutController@destroy')->name('admin.payout.destroy');
        Route::post('panel/v2/payout/datatable', 'AdminV2\PayoutController@datatable')->name('admin.payout.datatable');

        Route::get('panel/v2/payout/check_payout', 'AdminV2\PayoutController@check_payout');
        Route::get('panel/v2/payout/payout_request', 'AdminV2\PayoutController@payout_request')->name('admin.payout.payout_request');
        //payout_batch
        Route::get('panel/v2/payout/payout_batch', 'AdminV2\PayoutController@index_payoutBatch')->name('admin.payout.bayout_batch');
        Route::post('panel/v2/payout/datatable_payoutBatch', 'AdminV2\PayoutController@datatable_payoutBatch')->name('admin.payout.datatable_payoutBatch');
        Route::get('panel/v2/payout/payout_item/{id}', 'AdminV2\PayoutController@index_payoutItem')->name('admin.payout.bayout_item');
        Route::post('panel/v2/payout/datatable_payoutItem', 'AdminV2\PayoutController@datatable_payoutItem')->name('admin.payout.datatable_payoutItem');

        // Contributors
        Route::get('panel/v2/contributors', 'AdminV2\ContributorController@index')->name('admin.contributor.index');
        Route::get('panel/v2/contributors/create', 'AdminV2\ContributorController@create')->name('admin.contributor.create');
        Route::post('panel/v2/contributors/store', 'AdminV2\ContributorController@store')->name('admin.contributor.store');
        Route::get('panel/v2/contributors/{id}/edit', 'AdminV2\ContributorController@edit')->name('admin.contributor.edit');
        Route::put('panel/v2/contributors/{id}', 'AdminV2\ContributorController@update')->name('admin.contributor.update');
        Route::post('panel/v2/contributors/{id}/activate', 'AdminV2\ContributorController@activate')->name('admin.contributor.activate');
        Route::delete('panel/v2/contributors/{id}', 'AdminV2\ContributorController@destroy')->name('admin.contributor.destroy');

        Route::post('panel/v2/contributors/export', 'AdminV2\ContributorController@export')->name('admin.contributor.export');


        // Pages
        Route::get('panel/v2/pages', 'AdminV2\PageController@index')->name('admin.pages.index');
        Route::get('panel/v2/pages/create', 'AdminV2\PageController@create')->name('admin.pages.create');
        Route::post('panel/v2/pages/store', 'AdminV2\PageController@store')->name('admin.pages.store');
        Route::get('panel/v2/pages/{id}/edit', 'AdminV2\PageController@edit')->name('admin.pages.edit');
        Route::put('panel/v2/pages/{id}', 'AdminV2\PageController@update')->name('admin.pages.update');
        Route::delete('panel/v2/pages/{id}', 'AdminV2\PageController@destroy')->name('admin.pages.destroy');
        Route::post('panel/v2/pages/datatable', 'AdminV2\PageController@datatable')->name('admin.pages.datatable');

        // Pages
        Route::get('panel/v2/articles', 'AdminV2\ArticleController@index')->name('admin.articles.index');
        Route::get('panel/v2/articles/create', 'AdminV2\ArticleController@create')->name('admin.articles.create');
        Route::post('panel/v2/articles/store', 'AdminV2\ArticleController@store')->name('admin.articles.store');
        Route::get('panel/v2/articles/{id}/edit', 'AdminV2\ArticleController@edit')->name('admin.articles.edit');
        Route::put('panel/v2/articles/{id}', 'AdminV2\ArticleController@update')->name('admin.articles.update');
        Route::delete('panel/v2/articles/{id}', 'AdminV2\ArticleController@destroy')->name('admin.articles.destroy');
        Route::post('panel/v2/articles/datatable', 'AdminV2\ArticleController@datatable')->name('admin.articles.datatable');

        //blogs
        Route::get('panel/v2/blogs', 'AdminV2\BlogController@index')->name('admin.blogs.index');
        Route::get('panel/v2/blogs/create', 'AdminV2\BlogController@create')->name('admin.blogs.create');
        Route::post('panel/v2/blogs/store', 'AdminV2\BlogController@store')->name('admin.blogs.store');
        Route::get('panel/v2/blogs/{id}/edit', 'AdminV2\BlogController@edit')->name('admin.blogs.edit');
        Route::put('panel/v2/blogs/{id}', 'AdminV2\BlogController@update')->name('admin.blogs.update');
        Route::delete('panel/v2/blogs/{id}', 'AdminV2\BlogController@destroy')->name('admin.blogs.destroy');
        Route::post('panel/v2/blogs/datatable', 'AdminV2\BlogController@datatable')->name('admin.blogs.datatable');
        // Admin Collections
        Route::get('panel/v2/admin-collections',
            'AdminV2\AdminCollectionController@index')->name('admin.admin_collections.index');
        Route::get('panel/v2/admin-collections/create',
            'AdminV2\AdminCollectionController@create')->name('admin.admin_collections.create');
        Route::post('panel/v2/admin-collections',
            'AdminV2\AdminCollectionController@store')->name('admin.admin_collections.store');
        Route::get('panel/v2/admin-collections/{id}/edit',
            'AdminV2\AdminCollectionController@edit')->name('admin.admin_collections.edit');
        Route::put('panel/v2/admin-collections/{id}',
            'AdminV2\AdminCollectionController@update')->name('admin.admin_collections.update');
        Route::post('panel/v2/admin-collections/{id}/activate',
            'AdminV2\AdminCollectionController@activate')->name('admin.admin_collections.activate');
        Route::delete('panel/v2/admin-collections/{id}',
            'AdminV2\AdminCollectionController@destroy')->name('admin.admin_collections.destroy');
        Route::delete('panel/v2/admin-collections/{id}/images/{image_id}',
            'AdminV2\AdminCollectionController@delete_image')->name('admin.admin_collections.delete_image');
        Route::post('panel/v2/admin-collections/datatable',
            'AdminV2\AdminCollectionController@datatable')->name('admin.admin_collections.datatable');

        Route::get('panel/v2/admin-collections/{id}/dash',
            'AdminV2\AdminCollectionController@index_dash')->name('admin.admin_collections.dash.index');
        Route::post('panel/v2/admin-collections/{id}/dash/datatable',
            'AdminV2\AdminCollectionController@datatable_dash')->name('admin.admin_collections.dash.datatable');
        Route::get('panel/v2/admin-collections/select2',
            'AdminV2\AdminCollectionController@select2')->name('admin.admin_collections.select2');

        // plans
        Route::get('panel/v2/cities/ajax', 'AdminV2\PlanController@cities')->name('admin.cities.ajax');
        Route::get('panel/v2/plans', 'AdminV2\PlanController@index')->name('admin.plans.index');
        Route::get('panel/v2/plans/create', 'AdminV2\PlanController@create')->name('admin.plans.create');
        Route::post('panel/v2/plans', 'AdminV2\PlanController@store')->name('admin.plans.store');
        Route::get('panel/v2/plans/{id}', 'AdminV2\PlanController@show')->name('admin.plans.show');
        Route::post('panel/v2/plans/{id}/activate', 'AdminV2\PlanController@activate')->name('admin.plans.activate');
        Route::delete('panel/v2/plans/{id}', 'AdminV2\PlanController@destroy')->name('admin.plans.destroy');
        Route::post('panel/v2/plans/datatable', 'AdminV2\PlanController@datatable')->name('admin.plans.datatable');


        Route::get('panel/v2/user-plans', 'AdminV2\UserPlanController@index')->name('admin.user_plans.index');
        Route::post('panel/v2/user-plans/datatable', 'AdminV2\UserPlanController@datatable')->name('admin.user_plans.datatable');

        Route::get('panel/v2/user-plans/{subscribtion_id}/items', 'AdminV2\UserPlanController@items')->name('admin.user_plans.items');
        Route::get('panel/v2/user-plans/{subscribtion_id}/items-free', 'AdminV2\UserPlanController@itemsFree')->name('admin.user_plans.items.free');

        Route::post('panel/v2/user-plans/datatable/{subscribtion_id}/items',
            'AdminV2\UserPlanController@datatable_items')->name('admin.user_plans.items.datatable');

        Route::get('panel/v2/user-plans/downloads', 'AdminV2\UserPlanController@downloads')->name('admin.user_plans.downloads');
        Route::delete('panel/v2/user-plans/downloads/{download}', 'AdminV2\UserPlanController@delete_download')->name('admin.user_plans.delete_download');
        Route::post('panel/v2/user-plans/datatable_downloads',
            'AdminV2\UserPlanController@datatable_downloads')->name('admin.user_plans.items.datatable_downloads');

        Route::get('panel/v2/user-plans/contributor_downloads', 'AdminV2\UserPlanController@contributor_downloads')->name('admin.user_plans.contributor_downloads');
        Route::get('panel/v2/user-plans/datatable_contributor_downloads',
            'AdminV2\UserPlanController@datatable_contributor_downloads')->name('admin.user_plans.items.datatable_contributor_downloads');


        // invoices
        Route::get('panel/v2/invoices', 'AdminV2\InvoiceController@index')->name('admin.invoices.index');
        Route::post('panel/v2/invoices/datatable', 'AdminV2\InvoiceController@datatable')->name('admin.invoices.datatable');

        // Stats (Reports)
        Route::get('panel/v2/performance-reports/payments', 'AdminV2\PerformanceReportController@payments')->name('admin.performance_reports.payments');
        Route::get('panel/v2/performance-reports/payment', 'AdminV2\PerformanceReportController@index_payment')->name('admin.performance_reports.payment.index');
        Route::match(['get', 'post'], 'panel/v2/performance-reports/monthly-new-payments', 'AdminV2\PerformanceReportController@monthly_new_image_payments')->name('admin.performance_reports.monthly_new_image_payments');
        Route::post('panel/v2/performance-reports/payment/datatable',
            'AdminV2\PerformanceReportController@datatable_payment')->name('admin.performance_reports.payment.datatable');


        Route::get('panel/v2/payments-logs',
            'AdminV2\PerformanceReportController@index_payments_logs')->name('admin.payments_log.index');
        Route::post('panel/v2/payments-logs/datatable',
            'AdminV2\PerformanceReportController@datatable_payments_logs')->name('admin.payments_log.datatable');

        ////Settings
        Route::get('panel/v2/settings', 'AdminV2\SettingController@edit')->name('admin.settings.edit');
        Route::post('panel/v2/settings', 'AdminV2\SettingController@update')->name('admin.settings.update');
        Route::get('images/ar/tags', 'AdminV2\SettingController@tags_ar_image_select2')->name('images.tags.ar.select2');
        Route::get('images/en/tags', 'AdminV2\SettingController@tags_en_image_select2')->name('images.tags.en.select2');
        Route::get('videos/ar/tags', 'AdminV2\SettingController@tags_ar_video_select2')->name('videos.tags.ar.select2');
        Route::get('videos/en/tags', 'AdminV2\SettingController@tags_en_video_select2')->name('videos.tags.en.select2');
        Route::get('vectors/ar/tags', 'AdminV2\SettingController@tags_ar_vector_select2')->name('vectors.tags.ar.select2');
        Route::get('vectors/en/tags', 'AdminV2\SettingController@tags_en_vector_select2')->name('vectors.tags.en.select2');


        // email subscribe
        Route::get('panel/v2/email_subscribe', 'AdminV2\EmailSubscriptionController@index')->name('admin.email_subscribe.index');
        Route::name('admin.')->group(function () {
            Route::resource('panel/v2/newsletter', 'AdminV2\NewsletterController')->only('index', 'create', 'store');
            Route::resource('panel/v2/promocodes', 'AdminV2\PromocodesController');
        });
        Route::get('panel/v2/image-reviews', 'AdminV2\ImageReviewsController@index')->name('admin.image-reviews.index');
        Route::get('panel/v2/failed_jobs', 'AdminV2\FailedJobController@index')->name('admin.failed_jobs.index');
        Route::post('panel/v2/failed_jobs/datatable', 'AdminV2\FailedJobController@datatable')->name('admin.failed_jobs.datatable');
        Route::delete('panel/v2/failed_jobs/{id}', 'AdminV2\FailedJobController@destroy')->name('admin.failed_jobs.destroy');
        Route::post('panel/v2/failed_jobs/{id}', 'AdminV2\FailedJobController@insert_queue')->name('admin.failed_jobs.insert_queue');

        Route::get('panel/v2/images/warehouse_remove_bg/check/admin', 'AdminV2\ImageController@warehouse_remove_bg_check_admin')->name('admin.images.warehouse_remove_bg.check.admin');
        Route::post('panel/v2/images/warehouse_remove_bg/check/admin/datatable', 'AdminV2\ImageController@datatable_warehouse_remove_bg_check_admin')->name('admin.images.warehouse_remove_bg.check.admin.datatable');
        Route::post('panel/v2/images/warehouse_remove_bg/{id}/update_status_removebg_display/admin', 'AdminV2\ImageController@update_status_removebg_display_admin')->name('admin.images.warehouse_remove_bg.update_status_removebg_display_admin');

    }); // end middleware Role IMAGE
    //Subscriptions
    Route::name('admin.')->middleware(['role:admin|admin_video|admin_vector', 'language'])->group(function () {
        Route::resource('panel/v2/subscriptions', 'AdminV2\SubscriptionsController');
        Route::post('panel/v2/subscriptions/{id}/status', 'AdminV2\SubscriptionsController@status')->name('subscriptions.status');
        Route::resource('panel/v2/teams', 'AdminV2\TeamsController');
        Route::get('panel/v2/teams-subscriptions', 'AdminV2\TeamsController@subscriptions')->name('teams.subscriptions');
        Route::post('panel/v2/teams-subscriptions', 'AdminV2\TeamsController@store_subscription')->name('teams.store_subscription');
        Route::get('panel/v2/teams-subscriptions/create', 'AdminV2\TeamsController@create_subscription')->name('teams.create_subscription');
    });
    // videos
    Route::group(['middleware' => ['language', 'auth', 'role:admin_video|admin_video_editor']], function () {
        /* pending */
        Route::get('panel/v2/videos/videos/filemanger/create', 'AdminV2\VideoController@create_filemanger')->name('admin.videos.videos.filemanger.create');
        Route::get('panel/v2/videos/videos/filemanger/create/raw/{id}', 'AdminV2\VideoController@create_raw')->name('admin.videos.videos.filemanger.create_raw');
        Route::get('panel/v2/videos/videos/filemanger/replace', 'AdminV2\VideoController@replace_filemanger')->name('admin.videos.videos.filemanger.replace');
        Route::post('panel/v2/videos/videos/filemanger', 'AdminV2\VideoController@store_filemanger')->name('admin.videos.videos.filemanger.store');

        Route::get('panel/v2/videos/videos/warehouse',
            'AdminV2\VideoController@index_warehouse')->name('admin.videos.videos.warehouse.index');
        Route::get('panel/v2/videos/videos', 'AdminV2\VideoController@index')->name('admin.videos.videos.index');
        Route::post('panel/v2/videos/videos/datatable', 'AdminV2\VideoController@datatable')->name('admin.videos.videos.datatable');
        Route::get('panel/v2/videos/raw', 'AdminV2\VideoController@raw')->name('admin.videos.raw');
        Route::post('panel/v2/videos/raw/datatable', 'AdminV2\VideoController@raw_datatable')->name('admin.videos.raw_datatable');
        Route::post('panel/v2/videos/raw/{id}/edit', 'AdminV2\VideoController@raw_edit')->name('admin.videos.raw.edit');
        Route::delete('panel/v2/videos/videos/{id}', 'AdminV2\VideoController@destroy')->name('admin.videos.videos.destroy');

        //Folders
        Route::get('panel/v2/video/folders', 'AdminV2\FolderController@index_video')->name('admin.videos.folders.index');
        Route::post('panel/v2/video/folders/datatable', 'AdminV2\FolderController@datatable_video')->name('admin.videos.folders.datatable');

        // Submissions
        Route::get('panel/v2/videos/contributors/submissions',
            'AdminV2\VideoContributorController@index_submissions')->name('admin.videos.contributors.submissions.index');
        Route::get('panel/v2/videos/contributors/submissions/{id}/review', 'AdminV2\VideoContributorController@index_review')->name('admin.videos.contributors.submissions.review');
        Route::post('panel/v2/videos/contributors/submissions/datatable',
            'AdminV2\VideoContributorController@datatable_submissions')->name('admin.videos.contributors.submissions.datatable');
        Route::post('panel/v2/videos/contributors/submissions/datatable/review',
            'AdminV2\VideoContributorController@datatable_submissions_update_after_publish')->name('admin.videos.contributors.submissions.datatable_submissions_update_after_publish');

        // videos
        Route::get('api/contributor_videos', 'AdminV2\ContributorVideoController@index')->name('admin.api.contributor_videos.index');;
        Route::get('api/contributor_videos/delete', 'AdminV2\ContributorVideoController@delete')->name('admin.api.contributor_videos.delete');;
        Route::get('api/contributor_videos/delete-all', 'AdminV2\ContributorVideoController@delete_all')->name('admin.api.contributor_videos.delete_all');;
        Route::get('api/contributor_videos/options', 'AdminV2\ContributorVideoController@options')->name('admin.api.contributor_videos.options');
        Route::get('api/contributor_videos/filters', 'AdminV2\ContributorVideoController@filters')->name('admin.api.contributor_videos.filters');
        Route::post('api/contributor_videos/multi', 'AdminV2\ContributorVideoController@update_multi')->name('admin.api.contributor_videos.update_multi');
        Route::post('api/contributor_videos/submit', 'AdminV2\ContributorVideoController@submit')->name('admin.api.contributor_videos.submit');
        Route::post('api/contributor_videos/resubmit', 'AdminV2\ContributorVideoController@resubmit')->name('admin.api.contributor_videos.resubmit');

    });
    Route::group(['middleware' => ['language', 'auth', 'role:admin_video']], function () {
        Route::get('panel/v2/videos/dashboard',
            'AdminV2\DashboardController@index_video')->name('admin.videos.dashboard.index');

        Route::get('panel/v2/videos/dashboard/lang/{lang}',
            'AdminV2\DashboardController@lang')->name('admin.videos.dashboard.lang'); // new
        Route::get('panel/v2/videos/users/{id}/edit',
            'AdminV2\DashboardController@index_video')->name('admin.videos.users.edit'); // TODO
        Route::post('panel/v2/videos/logout', 'Auth\LoginController@logout')->name('admin.videos.logout'); // TODO

        Route::get('panel/v2/videos/payments', 'AdminV2\PaymentsVideoController@index')->name('admin.videos.payments.index'); // TODO

        Route::post('panel/v2/videos/payments/datatable', 'AdminV2\PaymentsVideoController@datatable')->name('admin.videos.payments.datatable'); // TODO


        Route::get('panel/v2/videos/payments/{order_id}/items', 'AdminV2\PaymentsVideoController@order_list')->name('admin.videos.payments.items'); // TODO

        Route::post('panel/v2/videos/payments/{order_id}/items/datatable', 'AdminV2\PaymentsVideoController@datatable_items')->name('admin.videos.payments.items.datatable'); // TODO

        Route::get('panel/v2/videos/payments/itemsAll', 'AdminV2\PaymentsVideoController@order_all')->name('admin.videos.payment.items.all'); // TODO

        Route::post('panel/v2/videos/payments/items/datatable', 'AdminV2\PaymentsVideoController@datatable_items_all')->name('admin.videos.payments.items.datatable.all'); // TODO

        Route::get('panel/v2/videos/payments/itemsAll_v2', 'AdminV2\PaymentsVideoController@order_all_v2')->name('admin.videos.payment.items.all_v2'); // TODO

        Route::post('panel/v2/videos/payments/items/datatable_v2', 'AdminV2\PaymentsVideoController@datatable_items_all_v2')->name('admin.videos.payments.items.datatable.all_v2'); // TODO


        // Categories
        Route::get('panel/v2/video/categories',
            'AdminV2\CategoryController@index_video')->name('admin.videos.categories.index');
        Route::get('panel/v2/video/categories/create',
            'AdminV2\CategoryController@create_video')->name('admin.videos.categories.create');
        Route::post('panel/v2/video/categories',
            'AdminV2\CategoryController@store_video')->name('admin.videos.categories.store');
        Route::get('panel/v2/video/categories/{id}/edit',
            'AdminV2\CategoryController@edit_video')->name('admin.videos.categories.edit');
        Route::post('panel/v2/video/categories/{id}/activate',
            'AdminV2\CategoryController@activate_video')->name('admin.videos.categories.activate');
        Route::put('panel/v2/video/categories/{id}',
            'AdminV2\CategoryController@update_video')->name('admin.videos.categories.update');
        Route::delete('panel/v2/video/categories/{id}',
            'AdminV2\CategoryController@destroy_video')->name('admin.videos.categories.destroy');
        Route::post('panel/v2/video/categories/datatable',
            'AdminV2\CategoryController@datatable_video')->name('admin.videos.categories.datatable');

        // Categories for admin
        Route::get('panel/v2/video/categories_admin/',
            'AdminV2\CategoryAdminController@index_video')->name('admin.videos.categories_admin.index');
        Route::get('panel/v2/video/categories_admin/create',
            'AdminV2\CategoryAdminController@create_video')->name('admin.videos.categories_admin.create');
        Route::post('panel/v2/video/categories_admin',
            'AdminV2\CategoryAdminController@store_video')->name('admin.videos.categories_admin.store');
        Route::get('panel/v2/video/categories_admin/{id}/edit',
            'AdminV2\CategoryAdminController@edit_video')->name('admin.videos.categories_admin.edit');
        Route::put('panel/v2/video/categories_admin/{id}',
            'AdminV2\CategoryAdminController@update_video')->name('admin.videos.categories_admin.update');
        Route::delete('panel/v2/video/categories_admin/{id}',
            'AdminV2\CategoryAdminController@destroy_video')->name('admin.videos.categories_admin.destroy');
        Route::post('panel/v2/video/categories_admin/datatable',
            'AdminV2\CategoryAdminController@datatable_video')->name('admin.videos.categories_admin.datatable');

        //Folders
        Route::get('panel/v2/video/folders/create', 'AdminV2\FolderController@create_video')->name('admin.videos.folders.create');
        Route::post('panel/v2/video/folders', 'AdminV2\FolderController@store_video')->name('admin.videos.folders.store');
        Route::get('panel/v2/video/folders/{id}/edit', 'AdminV2\FolderController@edit_video')->name('admin.videos.folders.edit');
        Route::post('panel/v2/video/folders/{id}/activate', 'AdminV2\FolderController@activate_video')->name('admin.videos.folders.activate');
        Route::put('panel/v2/video/folders/{id}', 'AdminV2\FolderController@update_video')->name('admin.videos.folders.update');
        Route::delete('panel/v2/video/folders/{id}', 'AdminV2\FolderController@destroy_video')->name('admin.videos.folders.destroy');

        // Videos

        Route::get('panel/v2/videos/videos/{id}/edit', 'AdminV2\VideoController@edit')->name('admin.videos.videos.edit');
        Route::post('panel/v2/videos/videos/{id}/activate',
            'AdminV2\VideoController@activate')->name('admin.videos.videos.activate');
        Route::post('panel/v2/videos/videos/{id}/admin-collection',
            'AdminV2\VideoController@add_to_admin_collection')->name('admin.videos.videos.admin_collections.update');
        Route::put('panel/v2/videos/videos/{id}', 'AdminV2\VideoController@update')->name('admin.videos.videos.update');
        Route::get('panel/v2/videos/videos/{id}/price',
            'AdminV2\VideoController@edit_price')->name('admin.videos.videos.price.edit');
        Route::put('panel/v2/videos/videos/{id}/price',
            'AdminV2\VideoController@update_price')->name('admin.videos.videos.price.update');

        Route::post('panel/v2/videos/videos/export',
            'AdminV2\VideoController@export')->name('admin.videos.videos.export');
        /* pending */
        Route::get('panel/v2/videos/pending', 'AdminV2\VideoController@index_pending')->name('admin.videos.pending.index');
        Route::post('panel/v2/videos/pending/datatable',
            'AdminV2\VideoController@datatable_pending')->name('admin.videos.pending.datatable');

        /* pending */
        /* s:route-deleted-videos */
        Route::get('panel/v2/videos/deleted', 'AdminV2\VideoController@index_deleted')->name('admin.videos.deleted.index');
        Route::post('panel/v2/videos/deleted/datatable',
            'AdminV2\VideoController@datatable_deleted')->name('admin.videos.deleted.datatable');
        /* e:route-deleted-videos */
        /* s:route-deleted-contributor-videos */
        Route::get('panel/videos/contributor_videos/deleted', 'AdminV2\VideoController@index_deleted_contributor_videos')->name('admin.videos.contributor_videos.deleted.index');
        Route::post('panel/videos/contributor_videos/deleted/datatable',
            'AdminV2\VideoController@datatable_deleted_contributor_videos')->name('admin.videos.contributor_videos.deleted.datatable');
        /* e:route-deleted-contributor-videos */

        // Submissions


        // Pages
        Route::get('panel/v2/video/pages', 'AdminV2\PageController@index_video')->name('admin.videos.pages.index');
        Route::get('panel/v2/video/pages/create', 'AdminV2\PageController@create_video')->name('admin.videos.pages.create');
        Route::post('panel/v2/video/pages/store', 'AdminV2\PageController@store_video')->name('admin.videos.pages.store');
        Route::get('panel/v2/video/pages/{id}/edit', 'AdminV2\PageController@edit_video')->name('admin.videos.pages.edit');
        Route::put('panel/v2/video/pages/{id}', 'AdminV2\PageController@update_video')->name('admin.videos.pages.update');
        Route::delete('panel/v2/video/pages/{id}',
            'AdminV2\PageController@destroy_video')->name('admin.videos.pages.destroy');
        Route::post('panel/v2/video/pages/datatable',
            'AdminV2\PageController@datatable_video')->name('admin.videos.pages.datatable');

        // Admin Collections
        Route::get('panel/v2/videos/admin-collections',
            'AdminV2\AdminCollectionController@index_video')->name('admin.videos.admin_collections.index');
        Route::get('panel/v2/videos/admin-collections/create',
            'AdminV2\AdminCollectionController@create_video')->name('admin.videos.admin_collections.create');
        Route::post('panel/v2/videos/admin-collections',
            'AdminV2\AdminCollectionController@store_video')->name('admin.videos.admin_collections.store');
        Route::get('panel/v2/videos/admin-collections/{id}/edit',
            'AdminV2\AdminCollectionController@edit_video')->name('admin.videos.admin_collections.edit');
        Route::put('panel/v2/videos/admin-collections/{id}',
            'AdminV2\AdminCollectionController@update_video')->name('admin.videos.admin_collections.update');
        Route::post('panel/v2/videos/admin-collections/{id}/activate',
            'AdminV2\AdminCollectionController@activate_video')->name('admin.videos.admin_collections.activate');
        Route::delete('panel/v2/videos/admin-collections/{id}',
            'AdminV2\AdminCollectionController@destroy_video')->name('admin.videos.admin_collections.destroy');
        Route::delete('panel/v2/videos/admin-collections/{id}/images/{image_id}',
            'AdminV2\AdminCollectionController@delete_image_video')->name('admin.videos.admin_collections.delete_image');
        Route::post('panel/v2/videos/admin-collections/datatable',
            'AdminV2\AdminCollectionController@datatable_video')->name('admin.videos.admin_collections.datatable');

        Route::get('panel/v2/videos/admin-collections/select2',
            'AdminV2\AdminCollectionController@select2_video')->name('admin.videos.admin_collections.select2');

        // Members
        Route::get('panel/v2/video/members', 'AdminV2\MemberController@index_video')->name('admin.videos.members.index');
        Route::get('panel/v2/video/members/create',
            'AdminV2\MemberController@create_video')->name('admin.videos.members.create');
        Route::post('panel/v2/video/members/store',
            'AdminV2\MemberController@store_video')->name('admin.videos.members.store');
        Route::get('panel/v2/video/members/{id}/edit',
            'AdminV2\MemberController@edit_video')->name('admin.videos.members.edit');
        Route::put('panel/v2/video/members/{id}',
            'AdminV2\MemberController@update_video')->name('admin.videos.members.update');
        Route::post('panel/v2/video/members/{id}/activate',
            'AdminV2\MemberController@activate_video')->name('admin.videos.members.activate');
        Route::delete('panel/v2/video/members/{id}',
            'AdminV2\MemberController@destroy_video')->name('admin.videos.members.destroy');
        Route::post('panel/v2/video/members/datatable',
            'AdminV2\MemberController@datatable_video')->name('admin.videos.members.datatable');
        // plans
        Route::get('panel/v2/video/plans', 'AdminV2\PlanController@index_video')->name('admin.videos.plans.index');
        Route::get('panel/v2/video/plans/create', 'AdminV2\PlanController@create_video')->name('admin.videos.plans.create');
        Route::post('panel/v2/video/plans', 'AdminV2\PlanController@store_video')->name('admin.videos.plans.store');
        Route::get('panel/v2/video/plans/{id}', 'AdminV2\PlanController@show_video')->name('admin.videos.plans.show');
        Route::post('panel/v2/video/plans/{id}/activate', 'AdminV2\PlanController@activate_video')->name('admin.videos.plans.activate');
        Route::delete('panel/v2/video/plans/{id}', 'AdminV2\PlanController@destroy_video')->name('admin.videos.plans.destroy');
        Route::post('panel/v2/video/plans/datatable', 'AdminV2\PlanController@datatable_video')->name('admin.videos.plans.datatable');

//    Route::get('panel/admin', 'Video\AdminController@admin')->name('devtest');


        Route::get('/panel/v2/videos/reports/payment',
            'AdminV2\PerformanceReportController@index_payment_videos')->name('admin.videos.reports.payment.index');
        Route::post('panel/v2/videos/reports/payment/datatable',
            'AdminV2\PerformanceReportController@datatable_payment_videos')->name('admin.videos.reports.payment.datatable');
        Route::match(['get', 'post'], 'panel/v2/video/performance-reports/monthly-new-payments', 'AdminV2\PerformanceReportController@monthly_new_video_payments')->name('admin.performance_reports.monthly_new_video_payments');
        Route::get('panel/v2/videos/reports/payments-logs', 'AdminV2\PerformanceReportController@index_payments_logs_videos')->name('admin.videos.reports.payments_log.index');

        Route::post('panel/v2/videos/reports/payments-logs/datatable', 'AdminV2\PerformanceReportController@datatable_payments_logs_videos')->name('admin.videos.reports.payments_log.datatable');


        Route::get('panel/v2/videos/user-plans', 'AdminV2\UserPlanController@index_videos')->name('admin.videos.user_plans.index');
        Route::post('panel/v2/videos/user-plans/datatable', 'AdminV2\UserPlanController@datatable_videos')->name('admin.videos.user_plans.datatable');


        Route::get('panel/v2/videos/user-plans/{subscribtion_id}/items', 'AdminV2\UserPlanController@items_videos')->name('admin.videos.user_plans.items');

        Route::post('panel/v2/videos/user-plans/datatable/{subscribtion_id}/items',
            'AdminV2\UserPlanController@datatable_items_videos')->name('admin.videos.user_plans.items.datatable');

        Route::get('panel/v2/videos/user-plans/downloads', 'AdminV2\UserPlanController@downloads_videos')->name('admin.videos.user_plans.downloads');
        Route::post('panel/v2/videos/user-plans/datatable_downloads',
            'AdminV2\UserPlanController@datatable_downloads_videos')->name('admin.videos.user_plans.items.datatable_downloads');

        Route::get('panel/v2/video/categories_contributor/', 'AdminV2\CategoryContributorVideoController@index')->name('admin.video.categories_contributores.index');
        Route::get('panel/v2/video/categories_contributor/create', 'AdminV2\CategoryContributorVideoController@create')->name('admin.video.categories_contributores.create');
        Route::post('panel/v2/video/categories_contributor', 'AdminV2\CategoryContributorVideoController@store')->name('admin.video.categories_contributores.store');
        Route::get('panel/v2/video/categories_contributor/{id}/edit', 'AdminV2\CategoryContributorVideoController@edit')->name('admin.video.categories_contributores.edit');
        Route::post('panel/v2/video/categories_contributor/datatable', 'AdminV2\CategoryContributorVideoController@datatable')->name('admin.video.categories_contributores.datatable');
        Route::put('panel/v2/video/categories_contributor/{id}', 'AdminV2\CategoryContributorVideoController@update')->name('admin.video.categories_contributores.update');
        Route::delete('panel/v2/video/categories_contributor/{id}', 'AdminV2\CategoryContributorVideoController@destroy')->name('admin.video.categories_contributores.destroy');

        Route::get('panel/v2/video-reviews', 'AdminV2\VideoReviewsController@index')->name('admin.video-reviews.index');
        Route::get('panel/v2/video/search-keys', 'AdminV2\VideoController@search_keys')->name('admin.videos_search_keys');
        Route::get('panel/v2/video/raw', 'AdminV2\VideoController@raw')->name('admin.videos.raw');
    });

// models
    Route::group(['middleware' => ['adminModels', 'language']], function () {
        Route::get('panel/v2/models/dashboard', 'AdminV2\DashboardController@index_models')->name('admin.models.dashboard.index');
        Route::get('panel/v2/models/dashboard/lang/{lang}', 'AdminV2\DashboardController@lang')->name('admin.models.dashboard.lang'); // new
        Route::get('panel/v2/models/requests', 'AdminV2\CastingController@index')->name('admin.models.dashboard.requests');
        Route::get('panel/v2/models/requests/show/{id}', 'AdminV2\CastingController@show')->name('admin.models.requests.show');
        Route::post('panel/v2/models/requests/datatable', 'AdminV2\CastingController@datatable')->name('admin.models.requests.datatable');

    });
//end models
    Route::group(['middleware' => ['role:admin_vector|admin_vector_editor', 'language']], function () {
        Route::get('panel/v2/vectors', 'AdminV2\VectorController@index')->name('admin.vectors.index');
        Route::get('panel/v2/vectors/{id}/edit', 'AdminV2\VectorController@edit')->name('admin.vectors.edit');
        Route::post('panel/v2/vectors/{id}/activate', 'AdminV2\VectorController@activate')->name('admin.vectors.members.activate');
        Route::delete('panel/v2/vectors/{id}', 'AdminV2\VectorController@destroy')->name('admin.vectors.destroy');
        Route::put('panel/v2/vectors/{id}', 'AdminV2\VectorController@update')->name('admin.vectors.update');
        Route::post('panel/v2/vectors/datatable', 'AdminV2\VectorController@datatable')->name('admin.vectors.datatable');
        Route::get('panel/v2/vectors/pending', 'AdminV2\VectorController@index_pending')->name('admin.vectors.pending.index');
        Route::post('panel/v2/vectors/pending/datatable',
            'AdminV2\VectorController@datatable_pending')->name('admin.vectors.pending.datatable');
// Folders
        Route::get('panel/v2/vector/folders', 'AdminV2\FolderController@index_vector')->name('admin.vectors.folders.index');
        Route::post('panel/v2/vector/folders/datatable', 'AdminV2\FolderController@datatable_vector')->name('admin.vectors.folders.datatable');

        /* s:route-deleted-vectors */
        Route::get('panel/v2/vectors/deleted', 'AdminV2\VectorController@index_deleted')->name('admin.vectors.deleted.index');
        Route::post('panel/v2/vectors/deleted/datatable',
            'AdminV2\VectorController@datatable_deleted')->name('admin.vectors.deleted.datatable');
        /* e:route-deleted-vectors */
        /* s:route-deleted-contributor-vectors */
        Route::get('panel/vectors/contributor_vectors/deleted', 'AdminV2\VectorController@index_deleted_contributor_vectors')->name('admin.vectors.contributor_vectors.deleted.index');
        Route::post('panel/vectors/contributor_vectors/deleted/datatable',
            'AdminV2\VectorController@datatable_deleted_contributor_vectors')->name('admin.vectors.contributor_vectors.deleted.datatable');
        /* e:route-deleted-contributor-vectors */

        // Submissions
        Route::get('panel/vectors/v2/contributors/submissions',
            'AdminV2\VectorContributorController@index_submissions')->name('admin.vectors.contributors.submissions.index');
        Route::get('panel/vectors/v2/contributors/submissions/{id}/review', 'AdminV2\VectorContributorController@index_review')->name('admin.vectors.contributors.submissions.review');
        Route::post('panel/vectors/v2/contributors/submissions/datatable',
            'AdminV2\VectorContributorController@datatable_submissions')->name('admin.vectors.contributors.submissions.datatable');
        Route::post('panel/vectors/v2/contributors/submissions/datatable/review',
            'AdminV2\VectorContributorController@datatable_submissions_update_after_publish')->name('admin.vectors.contributors.submissions.datatable_submissions_update_after_publish');

        Route::get('panel/v2/vectors/warehouse',
            'AdminV2\VectorController@index_warehouse')->name('admin.vectors.warehouse.index');

        // vectors
        Route::get('api/contributor_vectors', 'AdminV2\ContributorVectorController@index')->name('admin.api.contributor_vectors.index');;
        Route::get('api/contributor_vectors/delete', 'AdminV2\ContributorVectorController@delete')->name('admin.api.contributor_vectors.delete');;
        Route::get('api/contributor_vectors/delete-all', 'AdminV2\ContributorVectorController@delete_all')->name('admin.api.contributor_vectors.delete_all');;
        Route::get('api/contributor_vectors/options', 'AdminV2\ContributorVectorController@options')->name('admin.api.contributor_vectors.options');
        Route::get('api/contributor_vectors/filters', 'AdminV2\ContributorVectorController@filters')->name('admin.api.contributor_vectors.filters');
        Route::post('api/contributor_vectors/multi', 'AdminV2\ContributorVectorController@update_multi')->name('admin.api.contributor_vectors.update_multi');
        Route::post('api/contributor_vectors/submit', 'AdminV2\ContributorVectorController@submit')->name('admin.api.contributor_vectors.submit');
        Route::post('api/contributor_vectors/resubmit', 'AdminV2\ContributorVectorController@resubmit')->name('admin.api.contributor_vectors.resubmit');

    });
    Route::group(['middleware' => ['language', 'role:admin_vector']], function () {

        Route::get('panel/v2/vectors/dashboard',
            'AdminV2\DashboardController@index_vectors')->name('admin.vector.dashboard.index');

        Route::get('panel/v2/vectors/users/{id}/edit', 'AdminV2\DashboardController@index_vectors')->name('admin.vectors.users.edit'); // TODO
        Route::post('panel/v2/vectors/logout', 'Auth\LoginController@logout')->name('admin.vectors.logout'); // TODO


// Vectors
        Route::post('panel/v2/vectors/{id}/admin-collection',
            'AdminV2\VectorController@add_to_admin_collection')->name('admin.vectors.admin_collections.update');
        Route::post('panel/v2/vectors/export', 'AdminV2\VectorController@export')->name('admin.vectors.export');

        Route::get('panel/v2/vectors/filemanger/create',
            'AdminV2\VectorController@create_filemanger')->name('admin.vectors.filemanger.create');
        Route::post('panel/v2/vectors/filemanger/check_unique',
            'AdminV2\VectorController@check_unique')->name('admin.vectors.filemanger.check_unique');
        Route::post('panel/v2/vectors/filemanger',
            'AdminV2\VectorController@store_filemanger')->name('admin.vectors.filemanger.store');
        Route::get('panel/v2/vectors/filemanger/replace',
            'AdminV2\VectorController@replace_filemanger')->name('admin.vectors.filemanger.replace');
        //Folders
        Route::get('panel/v2/vector/folders/create', 'AdminV2\FolderController@create_vector')->name('admin.vectors.folders.create');
        Route::post('panel/v2/vector/folders', 'AdminV2\FolderController@store_vector')->name('admin.vectors.folders.store');
        Route::get('panel/v2/vector/folders/{id}/edit', 'AdminV2\FolderController@edit_vector')->name('admin.vectors.folders.edit');
        Route::post('panel/v2/vector/folders/{id}/activate', 'AdminV2\FolderController@activate_vector')->name('admin.vectors.folders.activate');
        Route::put('panel/v2/vector/folders/{id}', 'AdminV2\FolderController@update_vector')->name('admin.vectors.folders.update');
        Route::delete('panel/v2/vector/folders/{id}', 'AdminV2\FolderController@destroy_vector')->name('admin.vectors.folders.destroy');


// Categories
        Route::get('panel/v2/vector/categories', 'AdminV2\CategoryController@index_vector')->name('admin.vector.categories.index');
        Route::get('panel/v2/vector/categories/create', 'AdminV2\CategoryController@create_vector')->name('admin.vector.categories.create');
        Route::post('panel/v2/vector/categories', 'AdminV2\CategoryController@store_vector')->name('admin.vector.categories.store');
        Route::get('panel/v2/vector/categories/{id}/edit', 'AdminV2\CategoryController@edit_vector')->name('admin.vector.categories.edit');
        Route::post('panel/v2/vector/categories/{id}/activate',
            'AdminV2\CategoryController@activate_vector')->name('admin.vector.categories.activate');
        Route::put('panel/v2/vector/categories/{id}', 'AdminV2\CategoryController@update_vector')->name('admin.vector.categories.update');
        Route::delete('panel/v2/vector/categories/{id}', 'AdminV2\CategoryController@destroy_vector')->name('admin.vector.categories.destroy');
        Route::post('panel/v2/vector/categories/datatable',
            'AdminV2\CategoryController@datatable_vector')->name('admin.vector.categories.datatable');

// Categories for admin
        Route::get('panel/v2/vector/categories_admin/',
            'AdminV2\CategoryAdminController@index_vector')->name('admin.vector.categories_admin.index');
        Route::get('panel/v2/vector/categories_admin/create',
            'AdminV2\CategoryAdminController@create_vector')->name('admin.vector.categories_admin.create');
        Route::post('panel/v2/vector/categories_admin',
            'AdminV2\CategoryAdminController@store_vector')->name('admin.vector.categories_admin.store');
        Route::get('panel/v2/vector/categories_admin/{id}/edit',
            'AdminV2\CategoryAdminController@edit_vector')->name('admin.vector.categories_admin.edit');
        Route::put('panel/v2/vector/categories_admin/{id}',
            'AdminV2\CategoryAdminController@update_vector')->name('admin.vector.categories_admin.update');
        Route::delete('panel/v2/vector/categories_admin/{id}',
            'AdminV2\CategoryAdminController@destroy_vector')->name('admin.vector.categories_admin.destroy');
        Route::post('panel/v2/vector/categories_admin/datatable',
            'AdminV2\CategoryAdminController@datatable_vector')->name('admin.vector.categories_admin.datatable');


// Admin Collections
        Route::get('panel/v2/vectors/admin-collections',
            'AdminV2\AdminCollectionController@index_vector')->name('admin.vectors.admin_collections.index');
        Route::get('panel/v2/vectors/admin-collections/create',
            'AdminV2\AdminCollectionController@create_vector')->name('admin.vectors.admin_collections.create');
        Route::post('panel/v2/vectors/admin-collections',
            'AdminV2\AdminCollectionController@store_vector')->name('admin.vectors.admin_collections.store');
        Route::get('panel/v2/vectors/admin-collections/{id}/edit',
            'AdminV2\AdminCollectionController@edit_vector')->name('admin.vectors.admin_collections.edit');
        Route::put('panel/v2/vectors/admin-collections/{id}',
            'AdminV2\AdminCollectionController@update_vector')->name('admin.vectors.admin_collections.update');
        Route::post('panel/v2/vectors/admin-collections/{id}/activate',
            'AdminV2\AdminCollectionController@activate_vector')->name('admin.vectors.admin_collections.activate');
        Route::delete('panel/v2/vectors/admin-collections/{id}',
            'AdminV2\AdminCollectionController@destroy_vector')->name('admin.vectors.admin_collections.destroy');
        Route::delete('panel/v2/vectors/admin-collections/{id}/images/{image_id}',
            'AdminV2\AdminCollectionController@delete_image_vector')->name('admin.vectors.admin_collections.delete_image');
        Route::post('panel/v2/vectors/admin-collections/datatable',
            'AdminV2\AdminCollectionController@datatable_vector')->name('admin.vectors.admin_collections.datatable');

        Route::get('panel/v2/vectors/admin-collections/select2',
            'AdminV2\AdminCollectionController@select2_vector')->name('admin.vectors.admin_collections.select2');

// Members
        Route::get('panel/v2/vectors/members', 'AdminV2\MemberController@index_vectors')->name('admin.vectors.members.index');
        Route::get('panel/v2/vectors/members/create', 'AdminV2\MemberController@create_vectors')->name('admin.vectors.members.create');
        Route::post('panel/v2/vectors/members/store', 'AdminV2\MemberController@store_vectors')->name('admin.vectors.members.store');
        Route::get('panel/v2/vectors/members/{id}/edit', 'AdminV2\MemberController@edit_vectors')->name('admin.vectors.members.edit');
        Route::put('panel/v2/vectors/members/{id}', 'AdminV2\MemberController@update_vectors')->name('admin.vectors.members.update');
        Route::post('panel/v2/vectors/members/{id}/activate', 'AdminV2\MemberController@activate_vectors')->name('admin.vectors.members.activate');
        Route::delete('panel/v2/vectors/members/{id}', 'AdminV2\MemberController@destroy_vectors')->name('admin.vectors.members.destroy');
        Route::post('panel/v2/vectors/members/datatable', 'AdminV2\MemberController@datatable_vectors')->name('admin.vectors.members.datatable');


//  // Vectors
        Route::post('panel/v2/vectors/{id}/activate', 'AdminV2\VectorsController@activate')->name('admin.vectors.activate');

// Pages
        Route::get('panel/v2/vectors/pages', 'AdminV2\PageController@index_vectors')->name('admin.vectors.pages.index');
        Route::get('panel/v2/vectors/pages/create', 'AdminV2\PageController@create_vectors')->name('admin.vectors.pages.create');
        Route::post('panel/v2/vectors/pages/store', 'AdminV2\PageController@store_vectors')->name('admin.vectors.pages.store');
        Route::get('panel/v2/vectors/pages/{id}/edit', 'AdminV2\PageController@edit_vectors')->name('admin.vectors.pages.edit');
        Route::put('panel/v2/vectors/pages/{id}', 'AdminV2\PageController@update_vectors')->name('admin.vectors.pages.update');
        Route::delete('panel/v2/vectors/pages/{id}',
            'AdminV2\PageController@destroy_vectors')->name('admin.vectors.pages.destroy');
        Route::post('panel/v2/vectors/pages/datatable',
            'AdminV2\PageController@datatable_vectors')->name('admin.vectors.pages.datatable');

// plans
        Route::get('panel/v2/vectors/plans', 'AdminV2\PlanController@index_vectors')->name('admin.vectors.plans.index');
        Route::get('panel/v2/vectors/plans/create', 'AdminV2\PlanController@create_vectors')->name('admin.vectors.plans.create');
        Route::post('panel/v2/vectors/plans', 'AdminV2\PlanController@store_vectors')->name('admin.vectors.plans.store');
        Route::get('panel/v2/vectors/plans/{id}', 'AdminV2\PlanController@show_vectors')->name('admin.vectors.plans.show');
        Route::post('panel/v2/vectors/plans/{id}/activate', 'AdminV2\PlanController@activate_vectors')->name('admin.vectors.plans.activate');
        Route::delete('panel/v2/vectors/plans/{id}', 'AdminV2\PlanController@destroy_vectors')->name('admin.vectors.plans.destroy');
        Route::post('panel/v2/vectors/plans/datatable', 'AdminV2\PlanController@datatable_vectors')->name('admin.vectors.plans.datatable');


//tqarere al addaa
        Route::get('panel/v2/vectors/reports/payments-logs', 'AdminV2\PerformanceReportController@index_payments_logs_vectors')->name('admin.vector.reports.payments_log.index');
        Route::post('panel/v2/vectors/reports/payments-logs/datatable', 'AdminV2\PerformanceReportController@datatable_payments_logs_vectors')->name('admin.vector.reports.payments_log.datatable');
        Route::get('/panel/v2/vectors/reports/payment', 'AdminV2\PerformanceReportController@index_payment_vectors')->name('admin.vector.reports.payment.index');
        Route::post('panel/v2/vectors/reports/payment/datatable', 'AdminV2\PerformanceReportController@datatable_payment_vectors')->name('admin.vector.reports.payment.datatable');
        Route::match(['get', 'post'], 'panel/v2/vectors/performance-reports/monthly-new-payments', 'AdminV2\PerformanceReportController@monthly_new_vector_payments')->name('admin.performance_reports.monthly_new_vector_payments');
        Route::get('panel/v2/vectors/user-plans', 'AdminV2\UserPlanController@index_vectors')->name('admin.vector.user_plans.index');
        Route::post('panel/v2/vectors/user-plans/datatable', 'AdminV2\UserPlanController@datatable_vectors')->name('admin.vector.user_plans.datatable');
        Route::get('panel/v2/vectors/user-plans/{subscribtion_id}/items', 'AdminV2\UserPlanController@items_vectors')->name('admin.vector.user_plans.items');
        Route::post('panel/v2/vectors/user-plans/datatable/{subscribtion_id}/items', 'AdminV2\UserPlanController@datatable_items_vectors')->name('admin.vector.user_plans.items.datatable');
        Route::get('panel/v2/vectors/user-plans/downloads', 'AdminV2\UserPlanController@downloads_vectors')->name('admin.vector.user_plans.downloads');
        Route::post('panel/v2/vectors/user-plans/datatable_downloads', 'AdminV2\UserPlanController@datatable_downloads_vectors')->name('admin.vector.user_plans.items.datatable_downloads');
        Route::get('panel/v2/vectors/categories_contributor/', 'AdminV2\CategoryContributorVectorController@index')->name('admin.vector.categories_contributores.index');
        Route::get('panel/v2/vectors/categories_contributor/create', 'AdminV2\CategoryContributorVectorController@create')->name('admin.vector.categories_contributores.create');
        Route::post('panel/v2/vectors/categories_contributor', 'AdminV2\CategoryContributorVectorController@store')->name('admin.vector.categories_contributores.store');
        Route::get('panel/v2/vectors/categories_contributor/{id}/edit', 'AdminV2\CategoryContributorVectorController@edit')->name('admin.vector.categories_contributores.edit');
        Route::post('panel/v2/vectors/categories_contributor/datatable', 'AdminV2\CategoryContributorVectorController@datatable')->name('admin.vector.categories_contributores.datatable');
        Route::put('panel/v2/vectors/categories_contributor/{id}', 'AdminV2\CategoryContributorVectorController@update')->name('admin.vector.categories_contributores.update');
        Route::delete('panel/v2/vectors/categories_contributor/{id}', 'AdminV2\CategoryContributorVectorController@destroy')->name('admin.vector.categories_contributores.destroy');

        Route::get('panel/v2/vector-reviews', 'AdminV2\VectorReviewsController@index')->name('admin.vector-reviews.index');
        Route::get('panel/v2/vector/search-keys', 'AdminV2\VectorController@search_keys')->name('admin.vectors_search_keys');
    });


    Route::group(['middleware' => ['language', 'role:admin_super']], function () {
        // contact
        Route::name('admin.super.')->group(function () {
            Route::resource('panel/v2/super/contact', 'AdminV2\ContactController')->except('show');
            Route::post('panel/v2/super/contact/datatable', 'AdminV2\ContactController@datatable')->name('contact.datatable');
            Route::post('panel/v2/super/contact/{id}', 'AdminV2\ContactController@upload')->name('contact.upload')->where('id', '[0-9]+');
            Route::delete('panel/v2/super/contact/image/{id}', 'AdminV2\ContactController@delete_image')->name('contact.delete_image')->where('id', '[0-9]+');
        });
        Route::get('panel/v2/super/contact/export', 'AdminV2\ContactController@export')->name('admin.super.contact.export');
        Route::post('panel/v2/super/logout', 'Auth\LoginController@logout')->name('admin.super.logout'); // TODO

        // slider
        Route::get('panel/v2/super/slider', 'AdminV2\SliderController@index')->name('admin.super.slider.index');
        Route::get('panel/v2/super/slider/create', 'AdminV2\SliderController@create')->name('admin.super.slider.create');
        Route::post('panel/v2/super/slider/store', 'AdminV2\SliderController@store')->name('admin.super.slider.store');
        Route::get('panel/v2/super/slider/{id}/edit', 'AdminV2\SliderController@edit')->name('admin.super.slider.edit');
        Route::put('panel/v2/super/slider/{id}', 'AdminV2\SliderController@update')->name('admin.super.slider.update');
        Route::post('panel/v2/super/slider/{id}/activate', 'AdminV2\SliderController@activate')->name('admin.super.slider.activate');
        Route::delete('panel/v2/super/slider/{id}', 'AdminV2\SliderController@destroy')->name('admin.super.slider.destroy');
        Route::post('panel/v2/super/slider/datatable', 'AdminV2\SliderController@datatable')->name('admin.super.slider.datatable');

    });

    Route::group(['middleware' => ['language', 'auth', 'role:admin_video|admin|admin_vector']], function () {

        Route::get('panel/v2/{type}/rejected', 'AdminV2\ImageController@rejected')->name('admin.files.rejected')->where('type', 'images|videos|vectors');
        Route::post('panel/v2/{type}/datatable/rejected', 'AdminV2\ImageController@datatable_rejected')->name('admin.files.datatable.rejected')->where('type', 'images|videos|vectors');
    });
    //removebg designer and admin
    Route::group(['middleware' => ['role:admin|designer', 'language']], function () {
        Route::get('panel/v2/images/warehouse_remove_bg', 'AdminV2\ImageController@warehouse_remove_bg')->name('admin.images.warehouse_remove_bg.index');
        Route::get('panel/v2/images/warehouse_remove_bg/check', 'AdminV2\ImageController@warehouse_remove_bg_check')->name('admin.images.warehouse_remove_bg.check');
        Route::get('panel/v2/images/warehouse_remove_bg/check/manual', 'AdminV2\ImageController@warehouse_remove_bg_check')->name('admin.images.warehouse_remove_bg.check_manual');
        Route::get('panel/v2/images/download/{id}/{type}/image', 'AdminV2\ImageController@downloadImage')->name('admin.images.downloadImage');

        Route::post('panel/v2/images/warehouse_remove_bg/{id}/update_status_removebg_display', 'AdminV2\ImageController@update_status_removebg_display')->name('admin.images.warehouse_remove_bg.update_status_removebg_display');

    });

    Route::name('admin.sessions.')->prefix('panel/v2/{type}/sessions/photography')->group( function () {
        Route::resource('/', AdminV2\SessionsPhotographyController::class)->except(['edit', 'update','show']);
        Route::get('{id}/edit', 'AdminV2\SessionsPhotographyController@edit')->name('edit');
        // Route::get('{id}', 'AdminV2\SessionsPhotographyController@show')->name('show');
        Route::post('{id}', 'AdminV2\SessionsPhotographyController@update')->name('update');;
        Route::get('photographers', 'AdminV2\SessionsPhotographyController@getPhotographers')->name('photographers');
        Route::get('actors', 'AdminV2\SessionsPhotographyController@getActors')->name('actors');
        Route::get('locations', 'AdminV2\SessionsPhotographyController@getLocations')->name('locations');
        
        
        
        Route::post('datatable', 'AdminV2\SessionsPhotographyController@datatable')->name('datatable');
        Route::resource('contracts', AdminV2\SessionsPhotographyContractController::class);
        Route::post('contracts/datatable', 'AdminV2\SessionsPhotographyContractController@datatable')->name('contracts.datatable');


    });
}); // end Middleware DeveloperAuth
