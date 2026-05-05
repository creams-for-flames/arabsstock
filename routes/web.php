<?php

use Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Route;


/*Webhook Paypal*/
Route::post('webhook/stripe', 'WebhookController@handleStripeWebhook')->name('webhook.stripe');
Route::post('webhook/paypal', 'WebhookController@handlePaypalWebhook')->name('webhook.paypal.ipn');
Route::post('webhook/sendgrid', 'WebhookController@handleSendgridWebhook')->name('webhook.sendgrid');

Route::get('cpdf/{id}', 'WebhookController@cpdf');
Route::get('partners/subscriptions', 'PartnersController@subscriptions')->name('partners.subscriptions');
Route::group(['middleware' => \App\Http\Middleware\DeveloperAuth::class], function () {
    /*Social Login*/
    Route::group(['middleware' => 'guest'], function () {
        Route::get('oauth/{provider}', 'SocialAuthController@redirect')->where('provider', '(facebook|google)$');
        Route::get('oauth/{provider}/callback', 'SocialAuthController@callback')->where('provider',
            '(facebook|google)$');
    });

    Route::get('/auth/redirect/{provider}', 'SocialController@redirect');
    Route::any('/delete/{provider}', 'SocialController@delete');
    Route::get('/callback/{provider}', 'SocialController@callback');


    Route::group(['middleware' => 'SetLocalizationFrontend'], function () {
        $locales_available = ['ar', 'en'];
        $current_local = request()->segment(1);
        if (!in_array($current_local, $locales_available)) {
            $current_local = '';
        }

        Route::prefix($current_local)->group(function () use ($current_local) {

            Route::get('lang/{lang}', function ($id) {
                $locale = $id;
                $previous_locale = 'ar';
                if ($locale == 'ar') {
                    $previous_locale = 'en';
                }

                if ($previous_locale == 'ar') {
                    $locale = 'en';
                }

                $path = parse_url(url()->previous());
                $query = @$path['query'];
                if (isset($path['path'])) {
                    $path = parse_url(url()->previous())['path'];

                } else {
                    $path = '';

                }
                if ($query)
                    $path .= "?{$query}";
                $path = str_replace(['/ar', '/en'], ['', ''], $path);
                $previous_url = $locale . $path;
                App::setLocale($locale);
                if (in_array($path, ['/lang', 'lang']))
                    $previous_url = $locale;

                return redirect($previous_url)->withCookie(cookie()->forever('locale', $locale));
            })->name('lang.switch')->where(['lang' => '[a-z]+']);

            Route::get('/', 'HomeController@index')->name('landPage');
            Route::get('plans', 'PlansController@index')->name('plans');
            Route::get('/pricing', function () {
                return redirect()->route('plans');
            })->name('pricing');
            Route::get('/{type}/subscribe{abc?}', function () {
                return redirect()->route('plans');
            })->where('type', 'photos|videos|vectors');
            Route::get('auth/guest', 'Auth\GuestController')->name('auth.guest');

            Route::group(['middleware' => 'auth'], function () {
                Route::post('/promocode/check/{plan}', 'PlansController@check_promocode')->name('check_promocode');
                Route::post('/promocode/delete', 'PlansController@delete_promocode')->name('delete_promocode');
                Route::get('/stripe/invoice/{id}', 'PlansController@stripe_invoice')->name('stripe_invoice');
                Route::get('/purchase', 'PlansController@purchase')->name('purchase');
                Route::post('/purchase', 'PlansController@purchase');
                Route::get('/plans/subscribe', 'PlansController@subscribe')->name('plan.subscribe');

                Route::get('/paypal/package/execute-payment', 'PlansController@executePaymentPaypalPackage')->name('payment.paypal.execute');
                Route::get('/paypal/subscribtion/status', 'PlansController@subscribtionPaypalStatus')->name('paypal.subscribtion.status');
                Route::get('/stripe/payment/status/{id}', 'PlansController@stripePaymentStatus')->name('stripe.payment.status');

                Route::get('/payment/success', 'PlansController@paymentSuccess')->name('payment.success');
                Route::get('/payment/fail', 'PlansController@paymentFail')->name('payment.fail');

                Route::post('/subscribtions/{id}/activate', 'PlansController@activateSubscription')->name('subscribtion.activate');
                Route::post('/subscribtions/{id}/cancel', 'PlansController@cancelSubscription')->name('subscribtion.cancel');
                Route::post('/subscribtions', 'PlansController@update_stripe_payment_method')->name('subscribtion.update_stripe_payment_method');
                Route::get('/subscribtions_image/{id}/cancel',
                    'PlansController@cancelSubscription_image')->name('subscribtion_image.cancel');

                Route::get('/subscribtions_video/{id}/cancel',
                    'PlansController@cancelSubscription_video')->name('subscribtions_video.cancel');

                Route::get('/subscribtions_vector/{id}/cancel',
                    'PlansController@cancelSubscription_vector')->name('subscribtions_vector.cancel');
                Route::get('download-options/{type}/{id}', 'PlansController@download_options')->name('download_options')->where('id', '[0-9]+')->where('type', 'image|video|vector');

            });  // end Auth
            Route::get('vectors/search/similar/{id}', function ($id) use ($current_local) {
                return redirect()->to("{$current_local}/vectors/search/similar-illustration/{$id}", 301);
            });
            Route::get('photos/search/similar/{id}', function ($id) use ($current_local) {
                return redirect()->to("{$current_local}/photos/search/similar-image/{$id}", 301);
            });
            Route::get('videos/search/similar/{id}', function ($id) use ($current_local) {
                return redirect()->to("{$current_local}/videos/search/similar-clip/{$id}", 301);
            });
            Route::get('{type}/search/similar-{section}/{id}', 'SimilarFilesController@SimilarFiles')->name('similar.files');
            Route::group(['middleware' => [], 'prefix' => 'photos', 'as' => '',], function () {

                Route::get('/', 'HomeController@image')->name('photos.home');
                Route::get('categories', 'HomeController@categories')->name('categories');


                Route::get('latest', 'HomeController@latest')->name('latest');
                Route::get('can-reserve', 'HomeController@can_reserve')->name('photos.can_reserve');

                Route::get('category/{slug}', 'HomeController@category')->name('category.show');

                Route::get('tags/{slug}', 'HomeController@tag')->name('tags.show'); // TODO follow the same names in videos
                // disable
                // Collections
                //Route::get('collections', 'HomeController@collections')->name('collections');

                // Downloads Images
                Route::match(['get', 'post'], 'download/{token_id}', 'ImagesController@download')->name('photos.download')->middleware(['auth']);
                Route::get('download-preview/{id}', 'ImagesController@download_preview')->name('photos.download_preview');
                Route::get('redownload/{token_id}', 'ImagesController@redownload')->name('photos.redownload')->middleware(['auth']);


                /*
                 |
                 |-----------------------------------
                 | Ajax Request
                 |--------- -------------------------
                 */
                Route::post('photos/{id}/collection', 'AjaxController@imageCollection')->name('photos.imageCollection');
                Route::post('ajax/like', 'AjaxController@like')->name('photos.like');
                Route::post('ajax/follow', 'AjaxController@follow');
                Route::get('ajax/notifications', 'AjaxController@notifications');
                Route::get('ajax/users', 'AjaxController@users');
                Route::get('ajax/search', 'AjaxController@search')->name('ajax.search');
                Route::post('ajax/autocomplete/{type}', 'AjaxController@autocomplete')->name('autocomplete');
                Route::get('ajax/category', 'AjaxController@category');
                Route::get('ajax/tags', 'AjaxController@tags');
                Route::get('ajax/user/images', 'AjaxController@userImages');
                Route::get('ajax/comments', 'AjaxController@comments');

                Route::get('/search/samegroup/{image}', 'ImagesController@samegroup')->name('photo.samegroup');
                Route::get('/search/sameuser/{image}', 'ImagesController@sameuser')->name('photo.sameuser')->middleware('auth');

                // Photo Details
                Route::get('/{id}', 'ImagesController@show')->name('photo.show')->where([
                    'id' => '[A-Za-z0-9\_-]+',
                ]);
            }); // end prefix photos

            Route::group(['middleware' => [], 'prefix' => 'vectors', 'as' => '',], function () {

                Route::get('/', 'Vectors\HomeController@index')->name('vectors.home');
                Route::get('categories', 'Vectors\HomeController@categories')->name('vectors.categories');
                Route::get('category/{slug}', 'Vectors\HomeController@category')->name('vectors.category.show');
                Route::get('search/{q}', 'Vectors\HomeController@getSearch')->name('vectors.search');
                Route::post('search/ris', 'Vectors\HomeController@ris');
                Route::get('search/ris/{hash}', 'Vectors\HomeController@ris')->name('vectors.ris');
                Route::get('tags/{tags}', 'Vectors\HomeController@tag')->name('vectors.tags.show');
                Route::get('latest', 'Vectors\HomeController@latest')->name('vectors.latest');
                Route::get('can-reserve', 'Vectors\HomeController@can_reserve')->name('vectors.can_reserve');

                Route::post('vectors/{id}/collection', 'Vectors\AjaxController@vectorCollection')->name('vectors.vectorCollection');
                Route::group(['middleware' => 'auth'], function () {
                    Route::post('collection/store', 'Vectors\CollectionController@store')->name('vectors.collection.store');

                    Route::get('account/vectors', 'UserController@vectors')->name('me.vectors');
                    Route::match(['post', 'get'], 'download/{token_id}/{type?}', 'Vectors\VectorsController@download')->name('vectors.download');
                    Route::get('redownload/{token_id}/{type?}', 'Vectors\VectorsController@redownload')->name('vectors.redownload');
                    Route::get('download-preview/{id}', 'Vectors\VectorsController@download_preview')->name('vectors.download_preview');
                    Route::get('collection/{id}/i/{vector}', 'Vectors\CollectionController@addVectorCollection')->where([
                        'id' => '[0-9]+',
                        'vector' => '[0-9]+'
                    ])->name('vectors.collection.addVector');

                });

                Route::post('ajax/like', 'Vectors\AjaxController@like')->name('vectors.like');

                Route::get('/search/samegroup/{vector}', 'Vectors\VectorsController@samegroup')->name('vector.samegroup');
                Route::get('/search/sameuser/{vector}', 'Vectors\VectorsController@sameuser')->name('vector.sameuser')->middleware('auth');
                // vectors Details
                Route::get('/{id}/{slug?}', 'Vectors\VectorsController@show')->name('vector.show')->where([
                    'id' => '[A-Za-z0-9\_-]+',
                ]);
            }); // end prefix vectors

            Route::post('email/subscribe', 'HomeController@emailSubscribe')->name('email.subscribe');
            Route::get('email/unsubscribe/{id}/code/{username}/{token}', 'HomeController@EmailUnSubscribe')->name('email.unsubscribe');

            Route::auth();

            Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
            Route::group(['middleware' => 'auth'], function () {
                Route::get('account/collection/images/{id}/{slug?}',
                    'UserController@collectionDetail')->name('account.collection.images');

                Route::put('account/collection/images/{id}/{slug?}',
                    'UserController@editCollectionImages')->name('account.collection.editCollectionImages');

                Route::get('account/collection/videos/{id}/{slug?}',
                    'Video\UserController@collectionDetail')->name('acount.collection.videos');

                Route::get('account/collection/vectors/{id}/{slug?}',
                    'Vectors\UserController@collectionDetail')->name('acount.collection.vectors');

                Route::put('account/collection/videos/{id}/{slug?}',
                    'Video\UserController@collectionDetail')->name('account.collection.editCollectionVideos');
            });

            Route::get('search/{q}', 'HomeController@getSearch')->name('search');
            Route::post('search/ris', 'HomeController@ris');
            Route::get('search/ris/{hash}', 'HomeController@ris')->name('ris');


            Route::get('verify/account/{confirmation_code}', 'HomeController@getVerifyAccount')->where('confirmation_code',
                '[A-Za-z0-9]+')->name('account.verify');

            Route::get('page/{slug}', 'PagesController@show')->name('page.show');
            Route::get('technical-support', 'PagesController@support')->name('technical-support');
            Route::get('evaluation', 'PagesController@evaluation')->name('evaluation');
            Route::post('evaluation', 'PagesController@storeEvaluation')->name('evaluation');
            Route::get('casting', 'PagesController@contact')->name('model-form');
            Route::get('getCity', 'PagesController@getCity')->name('getCity');
            Route::post('contact_post', 'PagesController@contact_post')->name('contact_post');
            Route::post('technical-support', 'PagesController@storeTicket')->name('technical-support');
            Route::match(['get', 'post'], 'business', 'PagesController@business')->name('business');


            Route::get('team/invitations/{uuid}', 'UserController@invitation')->name('invitation');
            Route::group(['middleware' => 'auth'], function () {
                Route::post('account/mobile', 'UserController@update_mobile')->name('account.update-mobile');

                Route::group(['middleware' => \App\Http\Middleware\CheckMobile::class], function () {
                    /*
                     |
                     |-----------------------------------
                     | Profile User
                     |-----------------------------------
                     */
                    Route::get('account/profile', 'UserController@account')->name('user.profile');
                    Route::post('account/profile', 'UserController@update_account');
                    Route::get('team', 'UserController@team')->name('team');
                    Route::post('team/invitations', 'UserController@new_invitation')->name('team.new_invitation');
                    Route::delete('team/invitations/{id}', 'UserController@delete_invitation')->name('team.delete_invitation')->where('id', '[0-9]+');
                    Route::get('team/invitations/{uuid}/accept', 'UserController@accept_invitation')->name('accept_invitation');
                    Route::get('team/invitations/{uuid}/decline', 'UserController@decline_invitation')->name('decline_invitation');
                    Route::match(['get', 'post'], 'team/subscriptions/credits/{id}', 'UserController@subscription_credits')->name('team.subscription_credits');

                    // Password
                    Route::get('account/password', 'UserController@password')->name('account.password');
                    Route::post('account/password', 'UserController@update_password')->name('account.update_password');

                    // Account Settings
                    Route::get('account/plans', 'UserController@my_plans')->name('me.plans');
                    Route::post('account/plans/pay-invoice/{id}', 'UserController@pay_invoice')->name('pay_invoice');
                    Route::get('account/images', 'UserController@images')->name('me.images');
                    Route::get('account/videos', 'UserController@videos')->name('me.videos');

                    Route::get('account/invoices', 'UserController@invoices')->name('me.invoices');

                    Route::get('account/collections/images', 'UserController@collections')->name('me.collections');
                    Route::get('account/collections/videos', 'Video\UserController@collections')->name('me.collections.videos');
                    Route::get('account/collections/vectors', 'Vectors\UserController@collections')->name('me.collections.vectors');


                    // Likes
                    Route::get('account/likes', 'UserController@userLikes')->name('account.likes');

                    Route::get('account/{slug}', 'UserController@profile')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('account.profile');
                    Route::get('{slug}/followers', 'UserController@followers')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('profile.followers');
                    Route::get('{slug}/following', 'UserController@following')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('profile.following');


                    // Route::get('me/invoices', 'PlansController@invoices')->name('me.invoices');


                    // Delete Account
                    Route::get('account/delete', 'UserController@delete')->name('account.delete');
                    Route::post('account/delete', 'UserController@delete_account');

                    // Upload Avatar
                    Route::post('upload/avatar', 'UserController@upload_avatar')->name('avatar.upload');

                    // Upload Cover
                    Route::post('upload/cover', 'UserController@upload_cover')->name('cover.upload');


                    // Feed
                    Route::get('feed', 'UserController@followingFeed')->name('feed');

                    // Photos Pending
                    Route::get('photos/pending', 'UserController@photosPending')->name('photos.pending');

                    // Notifications
                    Route::get('notifications', 'UserController@notifications');
                    Route::get('notifications/delete', 'UserController@notificationsDelete')->name('notifications.delete');
                    Route::post('report/photo', 'ImagesController@report');

                    // Report User
                    Route::post('report/user', 'UserController@report')->name('report.user');

                    // Collections
                    Route::post('collection/store', 'CollectionController@store')->name('collection_create');

                    // Collection Edit
                    Route::post('collection/edit', 'CollectionController@edit');

                    // Collectin Delete
                    Route::get('collection/delete/{id}', 'CollectionController@destroy')->name('collection.delete');
                    Route::get('collection/imageDelete/{collectionID}/{imageID}',
                        'CollectionController@deleteImageCollection')->name('collection.imageDelete');
                    Route::get('collection/videoDelete/{collectionID}/{videoID}',
                        'Video\CollectionController@deleteVideoCollection')->name('collection.videoDelete');

                    // Add Image to Collection
                    Route::get('collection/{id}/i/{image}', 'CollectionController@addImageCollection')->where([
                        'id' => '[0-9]+',
                        'image' => '[0-9]+'
                    ])->name('photos.collection.addImage');

                    // Comments
                    Route::post('comment/store', 'CommentsController@store');

                    // Comments Delete
                    Route::post('comment/delete', 'CommentsController@destroy');

                    // Comment ImageLike
                    Route::post('comment/like', 'CommentsController@like');


                });//<------ End User Views LOGGED
            });

            Route::post('comments/likes', 'CommentsController@getLikes');

            Route::group(['middleware' => [], 'prefix' => 'videos', 'as' => 'video.',], function () {

                Route::get('/', 'Video\HomeController@index')->name('home');
                Route::get('likes', 'Video\UserController@userLikes')->name('likes')->middleware('auth');
                Route::get('search/{q}', 'Video\HomeController@getSearch')->name('search');
                Route::post('search/ris', 'Video\HomeController@ris');
                Route::get('search/ris/{hash}', 'Video\HomeController@ris')->name('ris');
                Route::get('account', 'Video\UserController@account')->name('account');
                Route::post('account', 'Video\UserController@update_account');
                Route::post('ajax/like', 'Video\AjaxController@like')->name('like');
                Route::post('ajax/follow', 'Video\AjaxController@follow');
                Route::get('ajax/notifications', 'Video\AjaxController@notifications');
                Route::get('ajax/users', 'Video\AjaxController@users');
                Route::get('ajax/search', 'Video\AjaxController@search')->name('ajax.search');

                Route::get('ajax/category', 'Video\AjaxController@category');
                Route::get('ajax/tags', 'Video\AjaxController@tag');
                Route::post('video/{id}/collection', 'Video\AjaxController@videoCollection')->name('videoCollection');


                Route::get('ajax/user/images', 'Video\AjaxController@userImages');
                Route::get('ajax/comments', 'Video\AjaxController@comments');
                // disable
                // Route::get('collection', 'Video\HomeController@collections')->name('collection');
                Route::get('latest', 'Video\HomeController@latest')->name('latest');
                Route::get('can-reserve', 'Video\HomeController@can_reserve')->name('can_reserve');


                Route::get('tags/{tags}', 'Video\HomeController@tag')->name('tags.show');
                Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
                Route::post('login', 'Auth\LoginController@login')->name('login');

                Route::get('categories', 'Video\HomeController@categories')->name('categories');
                Route::get('category/{slug}', 'Video\HomeController@category')->name('category.show');

                Route::group(['middleware' => 'auth'], function () {

                    Route::post('collection/store', 'Video\CollectionController@store')->name('collection.store');

                    // Collection Edit
                    Route::post('collection/edit', 'Video\CollectionController@edit');

                    // Collectin Delete
                    Route::get('collection/delete/{id}', 'Video\CollectionController@destroy');


                    // Add Image to Collection
                    Route::get('collection/{id}/i/{video}', 'Video\CollectionController@addVideoCollection')->where([
                        'id' => '[0-9]+',
                        'video' => '[0-9]+'
                    ])->name('collection.addVideo');


                    Route::get('profile/{slug}', 'Video\UserController@profile')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('profile');
                    Route::get('{slug}/followers', 'Video\UserController@followers')->where('slug', '[A-Za-z0-9\_-]+');
                    Route::get('{slug}/following', 'Video\UserController@following')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('user.following');
                    Route::get('{slug}/collections', 'Video\UserController@collections')->where('slug',
                        '[A-Za-z0-9\_-]+')->name('user.collections');

                    Route::match(['get', 'post'], 'download/{token_id}', 'Video\VideosController@download')->name('download');
                    Route::get('download-preview/{id}', 'Video\VideosController@download_preview')->name('download_preview');
                    Route::get('redownload/{token_id}', 'Video\VideosController@redownload')->name('redownload');
                });

                Route::get('/search/samegroup/{video}', 'Video\VideosController@samegroup')->name('samegroup');
                Route::get('/search/sameuser/{video}', 'Video\VideosController@sameuser')->name('sameuser')->middleware('auth');
                Route::get('/{id?}', 'Video\VideosController@show')->name('show')->where([
                    'id' => '[A-Za-z0-9\_-]+',
                ]);
            });

            Route::get('preview/videos/{id}', 'TestDummy\VideosController@show')->name('show')->where([
                'id' => '[A-Za-z0-9\_-]+',
            ]);
        }); // end Prefix Localization
    }); // end Middleware Localization
}); // end Middleware DeveloperAuth
