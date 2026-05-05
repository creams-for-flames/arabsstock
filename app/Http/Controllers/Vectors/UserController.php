<?php

namespace App\Http\Controllers\Vectors;

use App\Http\Controllers\Controller;
use App\Models\AdminVectorSettings;
use App\Models\CollectionVector;
use App\Models\Countries;
use App\Models\Image;
use App\Models\User;
use App\Models\Query;
use App\Models\UsersReported;
use App\Models\Notifications;
use App\Models\Vector;
use App\Helper;
use App\Models\VectorCollection;
use App\Models\Video;
use App\Models\VideoCollection;
use App\Models\VisitVideo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class UserController extends Controller
{


    public function __construct(AdminVectorSettings $settings)
    {
        $this->settings = $settings::first();
    }

    protected function validator(array $data, $id = null)
    {

        Validator::extend('ascii_only', function ($attribute, $value, $parameters) {
            return !preg_match('/[^x00-x7F\-]/i', $value);
        });

        // Validate if have one letter
        Validator::extend('letters', function ($attribute, $value, $parameters) {
            return preg_match('/[a-zA-Z0-9]/', $value);
        });

        return Validator::make($data, [
            'full_name' => 'required|min:3|max:25',
            'username' => 'required|min:3|max:15|ascii_only|alpha_dash|letters|unique:pages,slug|unique:reserved,name|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

    }

    public function profile($slug, Request $request)
    {

        $user = User::where('username', '=', $slug)->firstOrFail();
        $title = e($user->username) . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }

        $images = Query::userVideos($user->id);

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        //<<<-- * Redirect the user real name * -->>>

        $uri = str_replace(['ar/', 'en/'], [''], request()->path());
        $uriCanonical = 'video/profile/' . $user->username;

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }


        $data = VideoCollection::where('user_id',
            $user->id)->selectRaw('vector_collections.*,(select thumbnail from videos join collection_video on (collection_video.video_id=videos.id) where collection_video.collection_id = vector_collections.id  and videos.status = "active" limit 1 ) AS thumbnail')
            ->where('vector_collections.user_id', $user->id)
            ->orderBy('vector_collections.id', 'desc')
            ->groupBy('vector_collections.id')
            ->get();


        $vistits = VisitVideo::with('videos', 'user')->where('user_id', $user->id)->pluck('video_id');
        $vistits = Video::whereIn('id', $vistits->toArray())->get();


        return view('video.profile', [
            'user' => $user,
            'title' => $title,
            'data' => $data,
            'images' => $images,
            'videos' => $vistits,

        ]);

    }

    public function followers($slug, Request $request)
    {

        $user = User::where('username', '=', $slug)->firstOrFail();
        $title = e($user->username) . ' - ' . trans('users.followers') . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }

        $data = User::where('users.status', 'active')
            ->leftjoin('followers', 'users.id', '=', \DB::raw('followers.follower AND followers.status = "1"'))
            ->leftjoin('images', 'users.id', '=', \DB::raw('images.user_id AND images.status = "active"'))
            ->where('users.status', '=', 'active')
            ->where('followers.following', $user->id)
            ->groupBy('users.id')
            ->orderBy('followers.id', 'DESC')
            ->select('users.*')
            ->paginate(10);

        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }

        //<<<-- * Redirect the user real name * -->>>
        $uri = str_replace(['ar/', 'en/'], [''], request()->path());
        $uriCanonical = 'video/' . $user->username . '/followers';

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }

        return view('video.users.followers', ['title' => $title, 'data' => $data, 'user' => $user]);
    }

    public function following($slug, Request $request)
    {

        $user = User::where('username', '=', $slug)->firstOrFail();
        $title = e($user->username) . ' - ' . trans('users.following') . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }

        $data = User::where('users.status', 'active')
            ->leftjoin('followers', 'users.id', '=', \DB::raw('followers.following AND followers.status = "1"'))
            ->leftjoin('images', 'users.id', '=', \DB::raw('images.user_id AND images.status = "active"'))
            ->where('users.status', '=', 'active')
            ->where('followers.follower', $user->id)
            ->groupBy('users.id')
            ->orderBy('followers.id', 'DESC')
            ->select('users.*')
            ->paginate(10);

        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }

        //<<<-- * Redirect the user real name * -->>>
        $uri = str_replace(['ar/', 'en/'], [''], request()->path());
        $uriCanonical = 'video/' . $user->username . '/following';

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }

        return view('video.users.following', ['title' => $title, 'data' => $data, 'user' => $user]);
    }

    public function account()
    {
        $countries = Countries::orderBy('name_en')->get();
        return view('video.users.account', compact('countries'));
    }

    public function update_account(Request $request)
    {
//        dd('alaa');

        $input = $request->all();
        $id = Auth::user()->id;

        $validator = $this->validator($input, $id);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find($id);
        $user->name = $input['full_name'];
        $user->email = trim($input['email']);
        $user->username = $input['username'];
        $user->country_id = $input['country_id'];
//	   $user->paypal_account = trim($input['paypal_account']);
//	   $user->website     = trim(strtolower($input['website']));
//	   $user->facebook  = trim(strtolower($input['facebook']));
//	   $user->twitter       = trim(strtolower($input['twitter']));
//	   $user->google  = trim(strtolower($input['google']));
//		 $user->instagram  = trim(strtolower($input['instagram']));
//	   $user->bio = $input['description'];
        $user->save();

        \Session::flash('notification', trans('auth.success_update'));

        return redirect('video/account');

    }


    public function myDownloads()
    {

        $user = Auth::user();
        $downloads = $user->downloadVector()->pluck('vector_id');
        $vectors = Vector::withTrashed()->whereIn('vectors.id', $downloads->toArray())
            // ->leftJoin('collection_image', 'collection_image.image_id', '=', 'images.id')
            //  ->leftJoin('vector_collections', 'collection_image.collection_id', '=', 'vector_collections.id')
            //  ->whereRaw('(( type="public" and images.user_id != ' . $user->id . ') or (images.user_id = ' . $user->id . ') or (images.id not in (select image_id from collection_image)))')
            ->get();
        //dd($images);
        return view('vector.vectors.myVectors', compact('vectors', 'user'));
    }

    public function password()
    {
        return view('video.users.password');
    }

    public function update_password(Request $request)
    {

        $input = $request->all();
        $id = Auth::user()->id;

        $validator = Validator::make($input, [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!\Hash::check($input['old_password'], Auth::user()->password)) {
            return redirect('account/password')->with(['incorrect_pass' => trans('misc.password_incorrect')]);
        }

        $user = User::find($id);
        $user->password = \Hash::make($input["password"]);
        $user->save();

        \Session::flash('notification', trans('auth.success_update_password'));

        return redirect('account/password');

    }

    public function delete()
    {
        if (Auth::user()->id == 1) {
            return redirect('account');
        }
        return view('users.delete');
    }

    public function delete_account()
    {

        $id = Auth::user()->id;

        $user = User::findOrFail($id);

        if ($user->id == 1) {
            return redirect('account');
            exit;
        }

        $this->deleteUser($id);

        return redirect('account');

    }

    public function notifications()
    {

        $sql = DB::table('notifications')
            ->select(DB::raw('
			notifications.id id_noty,
			notifications.type,
			notifications.created_at,
			users.id,
			users.username,
			users.name,
			users.avatar,
			images.id,
			images.title
			'))
            ->leftjoin('users', 'users.id', '=', DB::raw('notifications.author'))
            ->leftjoin('images', 'images.id', '=', DB::raw('notifications.target AND images.status = "active"'))
            ->leftjoin('comments', 'comments.image_id', '=', DB::raw('notifications.target
			AND comments.user_id = users.id
			AND comments.image_id = images.id
			AND comments.status = "1"
			'))
            ->where('notifications.destination', '=', Auth::user()->id)
            ->where('notifications.author', '!=', Auth::user()->id)
            ->where('notifications.trash', '=', '0')
            ->where('users.status', '=', 'active')
            ->groupBy('notifications.id')
            ->orderBy('notifications.id', 'DESC')
            ->paginate(10);

        // Mark seen Notification
        Notifications::where('destination', Auth::user()->id)
            ->update(['status' => '1']);

        return view('users.notifications')->withSql($sql);

    }

    public function notificationsDelete()
    {

        $notifications = Notifications::where('destination', Auth::user()->id)->get();

        if (isset($notifications)) {
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }

        return redirect('notifications');

    }

    public function upload_avatar(Request $request)
    {

        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=180,min_height=180|max:' . $this->settings->file_size_allowed . '',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        // PATHS
        $temp = public_path('temp/');
        $path = public_path('avatar/');
        $imgOld = $path . Auth::user()->avatar;


        if ($request->hasFile('photo')) {

            $extension = $request->file('photo')->getClientOriginalExtension();
            $avatar = strtolower(Auth::user()->username . '-' . Auth::user()->id . time() . str_random(10) . '.' . $extension);

            if ($request->file('photo')->move($temp, $avatar)) {

                set_time_limit(0);

                Helper::resizeImageFixed($temp . $avatar, 180, 180, $temp . $avatar);

                // Copy folder
                if (\File::exists($temp . $avatar)) {
                    /* Avatar */
                    \File::copy($temp . $avatar, $path . $avatar);
                    \File::delete($temp . $avatar);
                }//<--- IF FILE EXISTS

                //<<<-- Delete old image -->>>/
                if (\File::exists($imgOld) && $imgOld != $path . 'default.jpg') {
                    \File::delete($temp . $avatar);
                    \File::delete($imgOld);
                }//<--- IF FILE EXISTS #1

                // Update Database
                User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);

                return response()->json([
                    'success' => true,
                    'avatar' => url($path . $avatar),
                ]);

            }
        }
    }

    public function upload_cover(Request $request)
    {

        $settings = AdminSettings::first();
        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpg,gif,png,jpe,jpeg|dimensions:min_width=800,min_height=600|max:' . $settings->file_size_allowed . '',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ]);
        }

        // PATHS
        $temp = public_path('temp/');
        $path = public_path('cover/');
        $imgOld = $path . Auth::user()->cover;


        if ($request->hasFile('photo')) {

            $extension = $request->file('photo')->getClientOriginalExtension();
            $cover = strtolower(Auth::user()->username . '-' . Auth::user()->id . time() . str_random(10) . '.' . $extension);

            if ($request->file('photo')->move($temp, $cover)) {

                set_time_limit(0);

                //=============== Image Large =================//
                $width = getWidth($temp . $cover);
                $height = getHeight($temp . $cover);
                $max_width = '1500';

                if ($width < $height) {
                    $max_width = '800';
                }

                if ($width > $max_width) {
                    $scale = $max_width / $width;
                    $uploaded = Helper::resizeImage($temp . $cover, $width, $height, $scale, $temp . $cover);
                } else {
                    $scale = 1;
                    $uploaded = Helper::resizeImage($temp . $cover, $width, $height, $scale, $temp . $cover);
                }

                // Copy folder
                if (\File::exists($temp . $cover)) {
                    /* Avatar */
                    \File::copy($temp . $cover, $path . $cover);
                    \File::delete($temp . $cover);
                }//<--- IF FILE EXISTS

                //<<<-- Delete old image -->>>/
                if (\File::exists($imgOld) && $imgOld != $path . 'cover.jpg') {
                    \File::delete($temp . $cover);
                    \File::delete($imgOld);
                }//<--- IF FILE EXISTS #1

                // Update Database
                User::where('id', Auth::user()->id)->update(['cover' => $cover]);

                return response()->json([
                    'success' => true,
                    'cover' => url($path . $cover),
                ]);

            }
        }
    }

    public function userLikes(Request $request)
    {

        $title = trans('users.likes') . ' - ';

        $videos = Video::where('videos.status', 'active')
            ->leftjoin('video_likes', 'videos.id', '=', \DB::raw('video_likes.video_id AND video_likes.status = "1"'))
            ->where('video_likes.user_id', Auth::user()->id)
            ->groupBy('videos.id')
            ->orderBy('video_likes.id', 'DESC')
            ->select('videos.*')
            ->paginate($this->settings->result_request);

        if ($request->input('page') > $videos->lastPage()) {
            abort('404');
        }

        return view('video.users.likes', ['title' => $title, 'videos' => $videos]);
    }

    public function followingFeed(Request $request)
    {

        $title = trans('misc.feed') . ' - ';

        $images = Image::leftjoin('followers', 'images.user_id', '=',
            \DB::raw('followers.following AND followers.status = "1"'))
            ->where('images.status', 'active')
            ->where('followers.follower', '=', Auth::user()->id)
            ->groupBy('images.id')
            ->orderBy('images.id', 'desc')
            ->select('images.*')
            ->paginate($this->settings->result_request);

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        return view('users.feed', ['title' => $title, 'images' => $images]);
    }

    public function collections(Request $request)
    {

        $user = Auth::user();
        $title = e($user->username) . ' - ' . trans('misc.collections') . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }


        $data = VectorCollection::where('user_id', $user->id)
            ->selectRaw('vector_collections.*,(select thumbnail from vectors join collection_vector on (collection_vector.vector_id=vectors.id) where collection_vector.collection_id = vector_collections.id  and vectors.status = "active" limit 1 ) AS thumbnail
                ,(select count(*) from collection_vector where collection_vector.collection_id=vector_collections.id ) as count_collection')
            ->where('vector_collections.user_id', $user->id)
            ->orderBy('vector_collections.id', 'desc')
            ->groupBy('vector_collections.id')
            ->paginate($this->settings->result_request);


        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }

        //<<<-- * Redirect the user real name * -->>>


        return view('vector.users.collections', compact('title', 'user', 'data'));

    }

    public function collectionDetail(Request $request)
    {
        if ($request->get("_token")) {
            $CollectionsImages = CollectionVector::findOrFail($request->id);
            $CollectionsImages->collection_id = $request->collection_id;
            $CollectionsImages->save();
            return back();
        }

        $collectionData = VectorCollection::where('id', $request->id)->firstOrFail();

        $user = Auth::user();

        $images = Vector::where('collection_vector.collection_id', $request->id)
            ->join('collection_vector', 'vectors.id', '=', 'collection_vector.vector_id')
            ->join('vector_collections', 'vector_collections.id', '=', 'collection_vector.collection_id')
            ->where('vectors.status', 'active')
            ->where('vector_collections.user_id', $user->id)
            ->orderBy('vectors.id', 'desc')
            ->select('vectors.*', 'collection_vector.id as collections_vector_id')
            ->paginate($this->settings->result_request);


        $title = trans('misc.collection') . ' - ' . $collectionData->title . ' -';

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        if ($collectionData->type == 'private' && Auth::check() && Auth::user()->id != $collectionData->user_id
            || $collectionData->type == 'private' && Auth::guest()) {
            abort('404');
        }

        $slugUrl = \Illuminate\Support\Str::slug($collectionData->title);

        if ($slugUrl == '') {
            $slugUrl = '';
        } else {
            $slugUrl = '/' . $slugUrl;
        }

        //<<<-- * Redirect the user real name * -->>>
        $uri = str_replace(['ar/', 'en/'], [''], request()->path());
        $uriCanonical = 'account/collection/vectors/' . $collectionData->id . $slugUrl;
        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }
        return view('vector.users.collection-detail',
            ['title' => $title, 'images' => $images, 'collectionData' => $collectionData, 'user' => $user]);
    }

    public function report(Request $request)
    {

        $data = UsersReported::firstOrNew(['user_id' => Auth::user()->id, 'id_reported' => $request->id]);

        if ($data->exists) {
            \Session::flash('noty_error', 'error');
            return redirect()->back();
        } else {

            $data->reason = $request->reason;
            $data->save();
            \Session::flash('noty_success', 'success');
            return redirect()->back();
        }
    }

    public function photosPending(Request $request)
    {

        $images = Image::where('user_id', Auth::user()->id)->where('status',
            'pending')->paginate($this->settings->result_request);

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        return view('video.users.photos-pending', ['images' => $images]);
    }
}
