<?php namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionImage;
use App\Models\Image;
use App\Models\ImageCollection;
use App\Models\ImageLike;
use App\Models\Notifications;
use App\Models\Messages;
use App\Models\Query;
use App\Models\Followers;
use App\Models\Comments;
use App\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class AjaxController extends Controller
{

    public function like(Request $request)
    {
        if (Auth::check()) {

                    $like = ImageLike::firstOrNew(['user_id' => Auth::user()->id, 'image_id' => $request->id]);
                    $user = Image::withoutGlobalScope('reserved')->find($request->id);
                    if ($like->exists) {

                        $notifications = Notifications::where('destination', $user->user_id)
                            ->where('author', Auth::user()->id)
                            ->where('target', $request->id)
                            ->where('type', '2')
                            ->first();

                        // IF ACTIVE DELETE FOLLOW
                        if ($like->status == '1') {
                            $like->status = '0';
                            $like->update();

                            if (isset($notifications)) {
                                // DELETE NOTIFICATION
                                $notifications->status = '1';
                                $notifications->update();
                            }

                            // ELSE ACTIVE AGAIN
                        } else {
                            $like->status = '1';
                            $like->update();

                            if (isset($notifications)) {
                                // ACTIVE NOTIFICATION
                                $notifications->status = '0';
                                $notifications->update();
                            }
                        }

                    } else {

                        // INSERT
                        $like->save();

                        // Send Notification //destination, author, type, target
                        if ($user->user_id != Auth::user()->id) {
                            Notifications::send($user->user_id, Auth::user()->id, '2', $request->id);
                        }

                    }

                    $totalLike = Helper::formatNumber($user->likes()->count());

                    return $totalLike;
        }
        $exception = [
            'success' => false,
            'message' => 'Validation Errors',
            'code' => 401,
            'msg' => __('auth.login_required'),
        ];
        throw new HttpResponseException(response()->json($exception, 422));

    }//<---- End Method

    public function follow(Request $request)
    {

        $user = Followers::firstOrNew(['follower' => Auth::user()->id, 'following' => $request->id]);

        if ($user->exists) {

            $notifications = Notifications::where('destination', $request->id)
                ->where('author', Auth::user()->id)
                ->where('target', Auth::user()->id)
                ->where('type', '1')
                ->first();

            // IF ACTIVE DELETE FOLLOW
            if ($user->status == '1') {
                $user->status = '0';
                $user->update();

                if (isset($notifications)) {
                    // DELETE NOTIFICATION
                    $notifications->status = '1';
                    $notifications->update();
                }

                // ELSE ACTIVE AGAIN
            } else {
                $user->status = '1';
                $user->update();

                if (isset($notifications)) {
                    // ACTIVE NOTIFICATION
                    $notifications->status = '0';
                    $notifications->update();
                }
            }

        } else {

            // INSERT
            $user->save();

            // Send Notification //destination, author, type, target
            if ($request->id != Auth::user()->id) {
                Notifications::send($request->id, Auth::user()->id, '1', Auth::user()->id);
            }

        }
        return response()->json([
            'status' => true,
        ]);
    }//<---- End Method

    public function notifications()
    {

        if (Auth::check()) {

            if (request()->ajax()) {
                // Notifications
                $notifications_count = Notifications::where('destination', Auth::user()->id)->where('status', '0')->count();

                if ($notifications_count == 0) {
                    $notifications_count = '0';
                }

                return response()->json(array('notifications' => $notifications_count));

            } else {
                return response()->json(array('error' => 1));
            }
        }//Auth
        else {
            return response()->json(array('error' => 1));
        }

    }//<---- * End Method

    public function users()
    {

        $data = Query::users();

        return view('ajax.users-ajax')->with($data)->render();

    }//<---- End Method

    public function search(Request $request)
    {

        /* $images = Query::searchImages($request->get('q'),$request->get('type')); */
        $q = $request->get('q');
        $images = \App\Helper::search_in_elasticsearch('images', $q);
        $images = [
            'images' => $images,
            'page' => $request->get('page', 1),
            'title' => trans('misc.result_of') . ' ' . $request->get('q') . ' - ',
            'total' => $images->total(),
            'q' => $request->get('q'),
        ];

        return view('ajax.images-ajax')->with($images)->render();

    }//<---- End Method

    public function autocomplete(Request $request, $type)
    {

        /* $images = Query::searchImages($request->get('q'),$request->get('type')); */
        $type = in_array($type, ['images', 'videos']) ? $type : 'images';
        $words = \App\Helper::autocomplete_in_elasticsearch($type, $request->get('q'));
        return response()->json(['data' => $words]);

    }//<---- End Method

    public function latest()
    {

        $images = Query::latestImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function featured()
    {

        $images = Query::featuredImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function popular()
    {

        $images = Query::popularImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function commented()
    {

        $images = Query::commentedImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function viewed()
    {

        $images = Query::viewedImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function downloads()
    {

        $images = Query::downloadsImages();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method


    public function downloadsVector()
    {

        $images = Query::downloadsVectors();

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function category(Request $request)
    {

        $slug = trim($request->slug);
        $type = $request->get('type');

        $images = Query::categoryImages($slug, $type);

        return view('ajax.images-ajax')->with($images)->render();

    }//<---- End Method

    public function tags(Request $request)
    {

        $slug = trim($request->q);

        $images = Query::tagsImages($slug);

        return view('ajax.images-ajax')->with($images)->render();

    }//<---- End Method

    public function camera(Request $request)
    {

        $slug = trim($request->q);

        $images = Query::camerasImages($slug);

        return view('ajax.images-ajax')->with($images)->render();

    }//<---- End Method


    public function userImages(Request $request)
    {

        $id = $request->id;

        $images = Query::userImages($id);

        return view('ajax.images-ajax', ['images' => $images])->render();

    }//<---- End Method

    public function comments(Request $request)
    {

        $id = $request->photo;

        $comments_sql = Comments::where('image_id', $id)->where('status', '1')->orderBy('date', 'desc')->paginate(10);

        return view('includes.comments', ['comments_sql' => $comments_sql])->render();

    }//<---- End Method


    public function imageCollection($id)
    {
        if (Auth::check()) {
            $collections = ImageCollection::where('user_id', \auth()->user()->id)->get();
            foreach ($collections as $collectionItem) {
                $check_collection = CollectionImage::where('collection_id', $collectionItem->id)
                    ->where('image_id', $id)->first();
                if ($check_collection) {
                    $collectionItem->in_collection = 1;
                } else {
                    $collectionItem->in_collection = 0;
                }
            }
            return $collections;
        }
        $exception = [
            'success' => false,
            'message' => 'Validation Errors',
            'code' => 401,
            'msg' => __('auth.login_required'),
        ];
        throw new HttpResponseException(response()->json($exception, 422));
    }

}
