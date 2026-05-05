<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');





Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users', function () {


  return  UserResource::collection(\App\Models\User::paginate(10));
});


Route::get('file/store', 'api\ImageController@getImages');
Route::get('admin/categories', 'api\ImageController@getAdminCategories');
Route::get('categories', 'api\ImageController@getCategories');
Route::get('Image/{id}/show', 'api\ImageController@Imageshow');


Route::get('Video/{id}/show', 'api\VideoController@Videoshow');
Route::get('Vector/{vector}/show', 'api\VectorController@Vectorshow');

// images
Route::get('images/options', 'api\ImageController@options')->name('admin.api.images.options');;
Route::get('images/filters', 'api\ImageController@filters')->name('admin.api.images.filters');;
Route::get('images', 'api\ImageController@index')->name('admin.api.images.index');;
Route::post('images/multi', 'api\ImageController@update_multi')->name('admin.api.images.update_multi');
Route::post('images/update_multi_remove_bg', 'api\ImageController@update_multi_remove_bg')->name('admin.api.images.update_multi_remove_bg');
Route::post('images/update_multi_remove_bg_display', 'api\ImageController@update_multi_remove_bg_display')->name('admin.api.images.update_multi_remove_bg_display');

// images filemanager
Route::get('images/filemanager/options', 'api\ImageController@options_filemanager')->name('admin.api.images.filemanager.options');;
Route::post('images/filemanager', 'api\ImageController@store_filemanager')->name('admin.api.images.filemanager.store');;
// reviews
Route::get('contributors/submissions/{id}/reviews', 'api\ReviewController@index')->name('admin.api.reviews.index');;
Route::get('contributors/submissions/{id}/reviews/filters', 'api\ReviewController@filters')->name('admin.api.reviews.filters');;
Route::middleware('auth:api')->post('contributors/submissions/{id}/reviews/submit', 'api\ReviewController@submit')->name('admin.api.reviews.submit');
Route::get('contributors/submissions/{id}/reviews/options', 'api\ReviewController@options')->name('admin.api.reviews.options');;
Route::middleware('auth:api')->post('contributors/submissions/{id}/reviews/multi', 'api\ReviewController@update_multi')->name('admin.api.reviews.update_multi');
Route::post('contributors/submissions/{id}/releases', 'api\ReviewController@create_release')->name('admin.api.releases.store');
Route::post('contributors/submissions/file/change_status', 'api\ReviewController@ChangeStatusContributorFileAfterPublished')->name('admin.api.reviews.change_status');
Route::middleware('auth:api')->post('contributors/submissions/{id}/reviews/update_after_publish', 'api\ReviewController@update_after_publish')->name('admin.api.reviews.update_after_publish');

// videos
Route::get('videos/options', 'api\VideoController@options')->name('admin.api.videos.options');;
Route::get('videos/filters', 'api\VideoController@filters')->name('admin.api.videos.filters');;
Route::get('videos', 'api\VideoController@index')->name('admin.api.videos.index');;
Route::post('videos/multi', 'api\VideoController@update_multi')->name('admin.api.videos.update_multi');

// reviews
Route::get('videos/contributors/submissions/{id}/reviews', 'api\VideoReviewController@index')->name('admin.videos.api.reviews.index');;
Route::get('videos/contributors/submissions/{id}/reviews/filters', 'api\VideoReviewController@filters')->name('admin.videos.api.reviews.filters');;
Route::middleware('auth:api')->post('videos/contributors/submissions/{id}/reviews/submit', 'api\VideoReviewController@submit')->name('admin.videos.api.reviews.submit');
Route::get('videos/contributors/submissions/{id}/reviews/options', 'api\VideoReviewController@options')->name('admin.videos.api.reviews.options');;
Route::middleware('auth:api')->post('videos/contributors/submissions/{id}/reviews/multi', 'api\VideoReviewController@update_multi')->name('admin.videos.api.reviews.update_multi');
Route::post('videos/contributors/submissions/file/change_status', 'api\VideoReviewController@ChangeStatusContributorFileAfterPublished')->name('admin.videos.api.reviews.change_status');
Route::middleware('auth:api')->post('videos/contributors/submissions/{id}/reviews/update_after_publish', 'api\VideoReviewController@update_after_publish')->name('admin.videos.api.reviews.update_after_publish');







// images
Route::get('vectors/options', 'api\VectorController@options')->name('admin.api.vectors.options');;
Route::get('vectors/filters', 'api\VectorController@filters')->name('admin.api.vectors.filters');;
Route::get('vectors', 'api\VectorController@index')->name('admin.api.vectors.index');;
Route::post('vectors/multi', 'api\VectorController@update_multi')->name('admin.api.vectors.update_multi');
// images filemanager
Route::get('vectors/filemanager/options', 'api\VectorController@options_filemanager')->name('admin.api.vectors.filemanager.options');
Route::post('vectors/filemanager', 'api\VectorController@store_filemanager')->name('admin.api.vectors.filemanager.store');
// reviews
Route::middleware('auth:api')->get('vectors/contributors/submissions/{id}/reviews', 'api\VectorReviewController@index')->name('admin.vectors.api.reviews.index');;
Route::middleware('auth:api')->get('vectors/contributors/submissions/{id}/reviews/filters', 'api\VectorReviewController@filters')->name('admin.vectors.api.reviews.filters');;
Route::middleware('auth:api')->post('vectors/contributors/submissions/{id}/reviews/submit', 'api\VectorReviewController@submit')->name('admin.vectors.api.reviews.submit');
Route::middleware('auth:api')->get('vectors/contributors/submissions/{id}/reviews/options', 'api\VectorReviewController@options')->name('admin.vectors.api.reviews.options');;
Route::middleware('auth:api')->post('vectors/contributors/submissions/{id}/reviews/multi', 'api\VectorReviewController@update_multi')->name('admin.vectors.api.reviews.update_multi');
Route::middleware('auth:api')->post('vectors/contributors/submissions/{id}/releases', 'api\VectorReviewController@create_release')->name('admin.vectors.api.releases.store');
Route::middleware('auth:api')->post('vectors/contributors/submissions/file/change_status', 'api\VectorReviewController@ChangeStatusContributorFileAfterPublished')->name('admin.vectors.api.reviews.change_status');
Route::middleware('auth:api')->post('vectors/contributors/submissions/{id}/reviews/update_after_publish', 'api\VectorReviewController@update_after_publish')->name('admin.vectors.api.reviews.update_after_publish');

