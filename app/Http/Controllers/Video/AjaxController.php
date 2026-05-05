<?php namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Models\CollectionVideo;
use App\Models\VideoCollection;
use App\Models\VideoLike;
use App\Models\AdminVideoSettings;
use App\Models\Notifications;
use App\Models\Messages;
use App\Models\Query;
use App\Models\Followers;
use App\Models\Comments;
use App\Helper;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class AjaxController extends Controller
{

    public function __construct(AdminVideoSettings $settings)
    {
        $this->settings = $settings::first();
    }

    public function like(Request $request)
    {
        if (Auth::check()) {
            $like = VideoLike::firstOrNew(['user_id' => Auth::user()->id, 'video_id' => $request->id]);

            $user = Video::withoutGlobalScope('reserved')->find($request->id);

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
    }

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
    }

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

    }

    public function search(Request $request)
    {

        /* $videos = Query::searchVideos($request->get('q'),$request->get('type')); */
        $q = $request->get('q');
        $videos = \App\Helper::search_in_elasticsearch('videos', $q);
        $videos = [
            'videos' => $videos,
            'page' => $request->get('page', 1),
            'title' => trans('misc.result_of') . ' ' . $request->get('q') . ' - ',
            'total' => $videos->total(),
            'q' => $request->get('q'),
        ];

        return view('video.ajax.videos-ajax')->with($videos)->render();

    }


    public function popular()
    {

        $images = Query::popularVideos();

        return view('video.ajax.videos-ajax', ['images' => $images])->render();

    }

    public function commented()
    {

        $images = Query::commentedVideos();

        return view('video.ajax.videos-ajax', ['images' => $images])->render();

    }

    public function viewed()
    {

        $images = Query::viewedVideos();

        return view('video.ajax.videos-ajax', ['images' => $images])->render();

    }

    public function downloads()
    {

        $images = Query::downloadsVideos();

        return view('video.ajax.videos-ajax', ['images' => $images])->render();

    }

    public function category(Request $request)
    {

        $slug = trim($request->slug);
        $type = $request->get('type');

        $images = Query::categoryVideo($slug, $type);

        return view('video.ajax.videos-ajax')->with($images)->render();

    }

    public function tags(Request $request)
    {

        $slug = trim($request->q);

        $images = Query::tagsVideo($slug);

        return view('video.ajax.videos-ajax')->with($images)->render();

    }


    public function userImages(Request $request)
    {

        $id = $request->id;

        $images = Query::userVideos($id);

        return view('video.ajax.videos-ajax', ['images' => $images])->render();

    }

    public function comments(Request $request)
    {

        $id = $request->photo;

        $comments_sql = Comments::where('video_id', $id)->where('status', '1')->orderBy('date', 'desc')->paginate(10);

        return view('video.includes.comments', ['comments_sql' => $comments_sql])->render();

    }


    public function videoCollection($id)
    {
        if (Auth::check()) {

                $collections = VideoCollection::where('user_id', \auth()->user()->id)->get();
                foreach ($collections as $collectionItem) {
                    $check_collection = CollectionVideo::where('collection_id', $collectionItem->id)
                        ->where('video_id', $id)->first();
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
