<?php

namespace App\Http\Controllers;

use App\Mail\TeamInvitation;
use App\Models\AdminImageSettings;
use App\Models\Countries;
use App\Models\Download;
use App\Models\FlexSubscription;
use App\Models\Image;
use App\Models\ImageCollection;
use App\Models\ImageDownload;
use App\Models\Invitation;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Query;
use App\Models\UsersReported;
use App\Models\Notifications;
use App\Models\CollectionImage;
use App\Helper;
use App\Models\VectorDownload;
use App\Models\VideoCollection;
use App\Models\VideoDownload;
use App\Models\VisitImage;
use App\Models\ImageSubscription;
use App\Models\VectorSubscription;
use App\Models\VideoSubscription;
use App\Rules\Mobile;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Video;
use App\Models\VisitVideo;
use App\Models\Vector;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\Invoice;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{

    use Traits\userTraits;

    public function __construct(AdminImageSettings $settings)
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
            'mobile' => ['required', 'string', Rule::unique('users', 'mobile')->ignore(\auth()->id()), new Mobile()],
            // 'username'  => 'required|min:3|max:15|ascii_only|alpha_dash|letters|unique:pages,slug|unique:reserved,name|unique:users,username,' . $id,
            // 'email'     => 'required|email|unique:users,email,' . $id,
        ]);

    }//<--- End Method

    //motaz
    public function profile2()
    {
        $countries = Countries::orderBy('name_en')->get();
        return view('users.profile2', compact('countries'));
    }

    public function profile($slug, Request $request)
    {

        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin') {
                return redirect()->route('landPage');
            }
        }

        $user = \auth()->user();
        $days = 11;
        if (auth::check()) {

            $subscription = ImageSubscription::where('user_id', $user->id)->first();
            if ($subscription) {
                $fdate = $subscription->starts_at;
                $tdate = $subscription->ends_at;
                $datetime1 = Carbon::parse($fdate);
                $datetime2 = Carbon::parse($tdate);
//            $interval = $datetime2->diffInDays($datetime1,false);
                $interval = Carbon::now()->diffInDays($datetime2, false);
                $days = $interval;//now do whatever you like with $days
            }

        } else {
            $days = 11;
        }
        $user = User::where('username', '=', $slug)->firstOrFail();
        $title = e($user->username) . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }

        $images = Query::userImages($user->id);
        $videos = Query::userVideos($user->id);
        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        //<<<-- * Redirect the user real name * -->>>
        $uri = \Request::segment(2);
        $uriCanonical = $user->username;
        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }


        $data = ImageCollection::where('user_id',
            $user->id)->selectRaw('image_collections.*,(select thumbnail from images join collection_image on (collection_image.image_id=images.id) where collection_image.collection_id = image_collections.id  and images.status = "active" limit 1 ) AS thumbnail')
            ->where('image_collections.user_id', $user->id)
            ->orderBy('image_collections.id', 'desc')
            ->groupBy('image_collections.id')
            ->get();

        $dataVideo = VideoCollection::where('user_id',
            $user->id)->selectRaw('video_collections.*,(select thumbnail from videos join collection_video on (collection_video.video_id=videos.id) where collection_video.collection_id = video_collections.id  and videos.status = "active" limit 1 ) AS thumbnail')
            ->where('video_collections.user_id', $user->id)
            ->orderBy('video_collections.id', 'desc')
            ->groupBy('video_collections.id')
            ->get();


        $vistits = VisitImage::whereHas('user')->where('user_id', $user->id)->get();

        $vistitsVideos = VisitVideo::with('videos', 'user')->where('user_id',
            $user->id)->pluck('video_id');
        $vistitsVideos = Video::whereIn('id', $vistitsVideos->toArray())->get();

        return view('profile', [
            'user' => $user,
            'title' => $title,
            'data' => $data,
            'images' => $images,
            'vistits' => $vistits,
            'days' => $days,
            'dataVideo' => $dataVideo,
            'videos' => $videos,
            'vistitsVideos' => $vistitsVideos

        ]);

    }//<--- End Method

    public function followers($slug, Request $request)
    {
        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin') {
                return redirect()->route('landPage');
            }
        }

        $user = User::where('username', '=', $slug)->firstOrFail();
        $title = e($user->username) . ' - ' . trans('users.followers') . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }

        $data = User::where('users.status', 'active')
            ->leftjoin('followers', 'users.id', '=', DB::raw('followers.follower AND followers.status = "1"'))
            ->leftjoin('images', 'users.id', '=', DB::raw('images.user_id AND images.status = "active"'))
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
        $uriCanonical = $user->username . '/followers';

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }

        return view('users.followers', ['title' => $title, 'data' => $data, 'user' => $user]);
    }//<--- End Method

    public function following($slug, Request $request)
    {

        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin') {
                return redirect()->route('landPage');
            }
        }
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
        $uriCanonical = $user->username . '/following';

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }

        return view('users.following', ['title' => $title, 'data' => $data, 'user' => $user]);
    }//<--- End Method

    public function account()
    {

        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin' || $user->role == 'admin_vector') {
                return redirect()->route('landPage');
            }
        }
        $countries = Countries::orderBy('name_en')->get();
        return view('users.account', compact('countries'));
    }//<--- End Method

    public function update_account(Request $request)
    {

        // dd($request->all());
        $input = $request->all();
        $id = Auth::user()->id;
        $trigger_invoice_change = false;
        $validator = $this->validator($input, $id);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find($id);
        $user->name = $input['full_name'];
        $user->receive_newsletters = intval($request->receive_newsletters);
        $user->mobile = $request->mobile;
        if ($user->is_business != ($request->is_business ?? 0) ||
            $user->company_name != $request->company_name ||
            $user->company_address != $request->company_address ||
            $user->company_email != $request->company_email ||
            $user->company_tax_id != $request->company_tax_id ||
            $user->company_phone != $request->company_phone
        ) {
            $trigger_invoice_change = true;
        }
        $user->is_business = $request->filled('is_business') && $request->is_business == 1 ? 1 : 0;
        if ($user->is_business) {
            $user->company_name = $request->company_name;
            $user->company_address = $request->company_address;
            $user->company_email = $request->company_email;
            $user->company_tax_id = $request->company_tax_id;
            $user->company_phone = $request->company_phone;
        }
        // $user->country_id = $input['country_id'];

        $user->save();
        if ($trigger_invoice_change) {
            $user->subscriptions()->update(['invoice_file' => '']);
            ImageSubscription::where('user_id', $user->id)->update(['invoice_file' => '']);
            VideoSubscription::where('user_id', $user->id)->update(['invoice_file' => '']);
            VectorSubscription::where('user_id', $user->id)->update(['invoice_file' => '']);
            dispatch(
                new \App\Jobs\GeneratePdfInvoicesForOldPayments()
            );
        }
        \Session::flash('notification', trans('auth.success_update'));

        return redirect('account/profile');

    }//<--- End Method

    public function update_mobile(Request $request)
    {

        $this->validate($request, [
            'mobile' => ['required', 'string', Rule::unique('contributors', 'mobile'), new Mobile()],
        ]);
        \auth()->user()->update([
            'mobile' => $request->mobile
        ]);
        return ['status' => 1];
    }

    public function my_plans()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role == 'admin_video' || $user->role == 'admin' || $user->role == 'admin_vector') {
                return redirect()->route('photos.home');
            }
        }
        /**@var $user User */
        $user = Auth::user();
        $image_subscriptions = $user->image_subscriptions()->where('ends_at', '>=', now())->whereIn('status', [ImageSubscription::STATUS_ACTIVE, ImageSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0')->orWhere('renewal', 1);
        })->with('plan')->orderBy('id', 'desc')->get()->unique(function ($r) {
            if ($r->plan_type == 'package')
                return $r->id;
            return $r->subscription_id;
        })->sortByDesc('id');
        $video_subscriptions = $user->video_subscriptions()->where('ends_at', '>=', now())->whereIn('status', [VideoSubscription::STATUS_ACTIVE, VideoSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0')->orWhere('renewal', 1)->get()->unique(function ($r) {
                if ($r->plan_type == 'package')
                    return $r->id;
                return $r->subscription_id;
            })->sortByDesc('id');
        })->with('plan')->orderBy('id', 'desc')->get();
        $vector_subscriptions = $user->vector_subscriptions()->where('ends_at', '>=', now())->whereIn('status', [VectorSubscription::STATUS_ACTIVE, VectorSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0')->orWhere('renewal', 1);
        })->with('plan')->orderBy('id', 'desc')->get()->unique(function ($r) {
            if ($r->plan_type == 'package')
                return $r->id;
            return $r->subscription_id;
        })->sortByDesc('id');
        $subscriptions = $user->subscriptions()->has('plan')->with('plan')->orderBy('id', 'desc')->get()->unique(function ($r) {
            if ($r->plan_type == 'package')
                return $r->id;
            return $r->subscription_id;
        })->sortByDesc('id');
        $active_team_subscriptions = $user->active_team_subscriptions()->has('plan')->with('plan')->get();
        return view('my-plans', compact('user', 'subscriptions', 'image_subscriptions', 'video_subscriptions', 'vector_subscriptions', 'active_team_subscriptions'));
    }

    public function pay_invoice(Request $request, $id)
    {
        $invoice = \Stripe\Invoice::retrieve([
            'id' => $id,
            'expand' => ['subscription', 'customer'],
        ]);
        $user = auth()->user();
        if ($invoice->customer->id != $user->stripe_id)
            return ['status' => 0, 'message' => __('Not Allowed')];
        /**@var $paymentMethod \Stripe\PaymentMethod */
        $paymentMethod = \Stripe\PaymentMethod::retrieve($request->pmethod);
        try {
            $paymentMethod->attach([
                'customer' => $user->stripe_id,
            ]);
            $user->updateDefaultPaymentMethod($paymentMethod->id);
        } catch (CardException $exception) {
            return response()->json(['status' => 0, 'error' => __($exception->getMessage())], 500);
        }
        try {
            $invoice->pay(['payment_method' => $request->pmethod]);
        } catch (\Exception $exception) {
            return ['status' => 0, 'message' => __($exception->getMessage())];
        }
        session()->flash('notify', ['status' => 'success', 'message' => __('Paid successfully')]);
        return ['status' => 1, 'message' => __('Paid successfully'), 'subscription_id' => $invoice->subscription];
    }

    public function invoices()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role == 'admin_video' || $user->role == 'admin' || $user->role == 'admin_vector') {
                return redirect()->route('landPage');
            }
        }
        $user = Auth::user();
        $invoice_images = ImageSubscription::
        where('user_id', $user->id)
            ->where('invoice_file', '!=', '')
            ->get()
            ->map(function ($item) {
                $title = "";
                if ($item->subscription_id) {
                    $title = trans('misc.30-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Month');
                } else {
                    $title = trans('misc.365-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Years');
                }
                return [
                    'id' => $item->id,
                    'type' => 'images',
                    'title' => $title,
                    'amount' => $item->amount,
                    'invoice_file' => $item->invoice_file,
                    'timestamp' => strtotime($item->created_at),
                    'created_at' => format_date(' H:i d M Y', $item->created_at)
                ];
            });
        $invoice_videos = VideoSubscription::
        where('user_id', $user->id)
            ->where('invoice_file', '!=', '')
            ->get()
            ->map(function ($item) {
                $title = "";
                if ($item->subscription_id) {
                    $title = trans('misc.30-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Month');
                } else {
                    $title = trans('misc.365-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Years');
                }
                return [
                    'id' => $item->id,
                    'type' => 'images',
                    'title' => $title,
                    'amount' => $item->amount,
                    'invoice_file' => $item->invoice_file,
                    'timestamp' => strtotime($item->created_at),
                    'created_at' => format_date(' H:i d M Y', $item->created_at)
                ];
            });

        $invoice_vectors = VectorSubscription::
        where('user_id', $user->id)
            ->where('vector_subscriptions.invoice_file', '!=', '')
            ->get()
            ->map(function ($item) {
                $title = "";
                if ($item->subscription_id) {
                    $title = trans('misc.30-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Month');
                } else {
                    $title = trans('misc.365-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->plan->title . " " . trans('misc.Downloads_Per_Years');
                }
                return [
                    'id' => $item->id,
                    'type' => 'vectors',
                    'title' => $title,
                    'amount' => $item->amount,
                    'invoice_file' => $item->invoice_file,
                    'timestamp' => strtotime($item->created_at),
                    'created_at' => format_date(' H:i d M Y', $item->created_at)
                ];
            });
        $new_invoices = $user->subscriptions()
            ->where('subscriptions.invoice_file', '!=', '')
            ->get()
            ->map(function ($item) {
                $title = "";
                if ($item->subscription_id) {
                    $title = trans('misc.30-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->title . " " . trans('misc.Downloads_Per_Month');
                } else {
                    $title = trans('misc.365-day_Subscription') . " " . trans('misc.Standard_Licence_with') . " " . $item->title . " " . trans('misc.Downloads_Per_Years');
                }
                return [
                    'id' => $item->id,
                    'type' => 'vectors',
                    'title' => $title,
                    'amount' => $item->amount,
                    'invoice_file' => $item->invoice_file,
                    'timestamp' => strtotime($item->created_at),
                    'created_at' => format_date(' H:i d M Y', $item->created_at)
                ];
            });

        $invoices = new \Illuminate\Database\Eloquent\Collection;
        $invoices = $invoices->concat($invoice_images);
        $invoices = $invoices->concat($invoice_videos);
        $invoices = $invoices->concat($invoice_vectors);
        $invoices = $invoices->concat($new_invoices);
        $invoices = $invoices->sortByDesc(function ($r) {
            return $r['timestamp'];
        });
        return view('invoices', compact('invoices', 'user'));
    }

    public function images(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('perPage', 50);
        $types = ['standard', 'enhanced', 'exclusive'];
        $download_type = ($request->has('download_type') and $request->get('download_type') !== "") ? $request->get('download_type') : $types[0];
        if ($request->ajax()) {
            $scopes = ['not_deleted', 'is_liked', 'default_loaded_relations', 'reserved'];
            $data = Image::withoutGlobalScopes($scopes)->selectRaw('images.*,image_downloads.user_id as downloader_id,image_downloads.date as pivot_date')
                ->where(function ($q) use ($download_type) {
                    if ($download_type == 'standard')
                        $q->whereRaw('1=1');
                    else
                        $q->whereRaw('1=0');
                })
                ->join('image_downloads', 'images.id', '=', 'image_downloads.image_id')->where('image_downloads.user_id', $user->id)
                ->union(Image::withoutGlobalScopes($scopes)->selectRaw('images.*,downloads.user_id as downloader_id,downloads.created_at as pivot_date')
                    ->join('downloads', 'images.id', '=', 'downloads.entity_id')->whereNull('downloads.deleted_at')
                    ->where('downloads.license_type', $download_type)
                    ->where('downloads.entity_type', Image::class)->where(function ($q) use ($user) {
                        $q->whereExists(function (Builder $query) {
                            $query->select(DB::raw(1))
                                ->from('subscriptions')
                                ->join('download_subscription', 'subscriptions.id', 'download_subscription.subscription_id')
                                ->whereRaw('downloads.id = download_subscription.download_id')
                                ->where('subscriptions.status', 1)->whereNull('subscriptions.deleted_at')->where('completed', 1);
                        })->where('downloads.user_id', $user->id);
                        if ($user->team_id && $user->isLeader())
                            $q->orWhere('downloads.team_id', $user->team_id);
                        else
                            $q->where('downloads.hide', 0);
                    }))->orderBy('pivot_date', 'desc')->paginate($perPage);
            $data = view('images.downloads', compact('download_type', 'data', 'perPage'))->render();
            return response()->json([
                'downloads' => $data,
                'download_type' => $download_type,
            ]);
        }
        return view('images.myImages', compact('user', 'types', 'download_type', 'perPage'));
    }

    public function videos(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('perPage', 50);
        $types = ['standard', 'enhanced', 'exclusive'];
        $download_type = ($request->has('download_type') and $request->get('download_type') !== "") ? $request->get('download_type') : $types[0];
        if ($request->ajax()) {
            $scopes = ['not_deleted', 'is_liked', 'default_loaded_relations', 'reserved'];
            $data = Video::withoutGlobalScopes($scopes)->selectRaw('videos.*,video_downloads.user_id as downloader_id,video_downloads.date as pivot_date')
                ->where(function ($q) use ($download_type) {
                    if ($download_type == 'standard')
                        $q->whereRaw('1=1');
                    else
                        $q->whereRaw('1=0');
                })
                ->join('video_downloads', 'videos.id', '=', 'video_downloads.video_id')->where('video_downloads.user_id', $user->id)
                ->union(Video::withoutGlobalScopes($scopes)->selectRaw('videos.*,downloads.user_id as downloader_id,downloads.created_at as pivot_date')
                    ->join('downloads', 'videos.id', '=', 'downloads.entity_id')->whereNull('downloads.deleted_at')
                    ->where('downloads.license_type', $download_type)
                    ->where('downloads.entity_type', Video::class)->where(function ($q) use ($user) {
                        $q->where('downloads.user_id', $user->id);
                        if ($user->team_id && $user->isLeader())
                            $q->orWhere('downloads.team_id', $user->team_id);
                        else
                            $q->where('downloads.hide', 0);
                    }))->orderBy('pivot_date', 'desc')->paginate($perPage);
            $data = view('video.downloads', compact('download_type', 'data', 'perPage'))->render();
            return response()->json([
                'downloads' => $data,
                'download_type' => $download_type,
            ]);
        }
        return view('video.my-videos', compact('user', 'download_type', 'types', 'perPage'));
    }

    public function vectors(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('perPage', 50);
        $types = ['standard', 'enhanced', 'exclusive'];
        $download_type = ($request->has('download_type') and $request->get('download_type') !== "") ? $request->get('download_type') : $types[0];
        if ($request->ajax()) {
            $scopes = ['not_deleted', 'is_liked', 'default_loaded_relations', 'reserved'];
            $data = Vector::withoutGlobalScopes($scopes)->selectRaw('vectors.*,vector_downloads.user_id as downloader_id,vector_downloads.date as pivot_date')
                ->where(function ($q) use ($download_type) {
                    if ($download_type == 'standard')
                        $q->whereRaw('1=1');
                    else
                        $q->whereRaw('1=0');
                })
                ->join('vector_downloads', 'vectors.id', '=', 'vector_downloads.vector_id')->where('vector_downloads.user_id', $user->id)
                ->union(Vector::withoutGlobalScopes($scopes)->selectRaw('vectors.*,downloads.user_id as downloader_id,downloads.created_at as pivot_date')
                    ->join('downloads', 'vectors.id', '=', 'downloads.entity_id')->whereNull('downloads.deleted_at')
                    ->where('downloads.license_type', $download_type)
                    ->where('downloads.entity_type', Vector::class)->where(function ($q) use ($user) {
                        $q->where('downloads.user_id', $user->id);
                        if ($user->team_id && $user->isLeader())
                            $q->orWhere('downloads.team_id', $user->team_id);
                        else
                            $q->where('downloads.hide', 0);
                    }))->orderBy('pivot_date', 'desc')->paginate($perPage);
            $data = view('vector.vectors.downloads', compact('download_type', 'data', 'perPage'))->render();
            return response()->json([
                'downloads' => $data,
                'download_type' => $download_type,
            ]);
        }
        return view('vector.vectors.myVectors', compact('user', 'download_type', 'types'));
    }

    public function password()
    {
        return view('users.password');
    }//<--- End Method

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

            return redirect()->back()->with('incorrect_pass', trans('auth.password_incorrect'));
        }

        $user = User::find($id);
        $user->password = \Hash::make($input["password"]);
        $user->save();
        Auth::logoutOtherDevices($request->password);

        return redirect()->back()->with('success', trans('auth.success_update_password'));
    }//<--- End Method

    public function delete()
    {
        if (Auth::user()->id == 1) {
            return redirect('account');
        }
        return view('users.delete');
    }//<--- End Method

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

    }//<--- End Method

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
			image.id,
			image.title
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

    }//<--- End Method

    public function notificationsDelete()
    {

        $notifications = Notifications::where('destination', Auth::user()->id)->get();

        if (isset($notifications)) {
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }

        return redirect('notifications');

    }//<--- End Method

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

        //<--- HASFILE PHOTO
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

            }// Move
        }//<--- HASFILE PHOTO
    }//<--- End Method Avatar

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

        //<--- HASFILE PHOTO
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

            }// Move
        }//<--- HASFILE PHOTO
    }//<--- End Method Cover

    public function userLikes(Request $request)
    {

        $title = trans('users.likes') . ' - ';
        $images = Image::withoutGlobalScope('reserved')->where('images.status', 'active')
            ->leftjoin('image_likes', 'images.id', '=', \DB::raw('image_likes.image_id AND image_likes.status = "1"'))
            ->where('image_likes.user_id', Auth::user()->id)
            ->groupBy('images.id')
            ->orderBy('image_likes.id', 'DESC')
            ->select('images.*')
            ->paginate(10);


        $videos = Video::withoutGlobalScope('reserved')->where('videos.status', 'active')
            ->leftjoin('video_likes', 'videos.id', '=', \DB::raw('video_likes.video_id AND video_likes.status = "1"'))
            ->where('video_likes.user_id', Auth::user()->id)
            ->groupBy('videos.id')
            ->orderBy('video_likes.id', 'DESC')
            ->select('videos.*')
            ->paginate($this->settings->result_request);


        $vectors = Vector::withoutGlobalScope('reserved')->where('vectors.status', 'active')
            ->leftjoin('vector_likes', 'vectors.id', '=', \DB::raw('vector_likes.vector_id AND vector_likes.status = "1"'))
            ->where('vector_likes.user_id', Auth::user()->id)
            ->groupBy('vectors.id')
            ->orderBy('vector_likes.id', 'DESC')
            ->select('vectors.*')
            ->paginate(10);

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        return view('users.likes', ['title' => $title, 'images' => $images, 'videos' => $videos, 'vectors' => $vectors]);
    }//<--- End Method

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
    }//<--- End Method

    public function collections(Request $request)
    {
        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin' || $user->role == 'admin_vector') {
                return redirect()->route('landPage');
            }
        }
        $user = Auth::user();
        $title = trans('misc.collections') . ' - ';

        if ($user->status == 'suspended') {
            return view('errors.user_suspended');
        }
        $data = ImageCollection::where('user_id', $user->id)->
        selectRaw('image_collections.*,(select thumbnail from images join collection_image on (collection_image.image_id=images.id) where collection_image.collection_id = image_collections.id  and images.status = "active" limit 1 ) AS thumbnail
        ,(select count(*) from collection_image where collection_image.collection_id=image_collections.id ) as count_collection')
            ->where('image_collections.user_id', $user->id)
            ->orderBy('image_collections.id', 'desc')
            ->groupBy('image_collections.id')
            ->paginate($this->settings->result_request);
        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }
        return view('users.collections', compact('title', 'user', 'data'));

    }//<--- End Method

    public function collectionDetail(Request $request)
    {
        $user = Auth::user();
        if ($request->get("_token")) {
            $CollectionsImages = CollectionImage::find($request->id);
            $CollectionsImages->collection_id = $request->collection_id;
            $CollectionsImages->save();
            return back();
        }

        $collectionData = ImageCollection::where('user_id', $user->id)
            ->where('id', $request->id)->firstOrFail();


        //all user collection
        $collections = ImageCollection::where('user_id', $user->id)->get();

        $images = Image::where('collection_image.collection_id', $request->id)
            ->join('collection_image', 'images.id', '=', 'collection_image.image_id')
            ->where('images.status', 'active')
            ->orderBy('images.id', 'desc')
            ->select('images.*', 'collection_image.id as collections_images_id')
            ->paginate($this->settings->result_request);
        // dd($images->toArray());
        $title = trans('misc.collection') . ' - ' . $collectionData->title . ' -';

        if ($request->input('page') > $images->lastPage()) {
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

        $uriCanonical = 'account/collection/images/' . $collectionData->id . $slugUrl;

        if ($uri != $uriCanonical) {
            return redirect($uriCanonical);
        }
        return view('users.collection-detail',
            [
                'title' => $title,
                'images' => $images,
                'collectionData' => $collectionData,
                'user' => $user,
                'collections' => $collections
            ]);
    }

    public function report(Request $request)
    {

        if (auth()->check()) {
            $user = \auth()->user();


            if ($user->role == 'admin_video' || $user->role == 'admin') {
                return redirect()->route('landPage');
            }
        }
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

    }//<--- End Method

    public function photosPending(Request $request)
    {

        $images = Image::where('user_id', Auth::user()->id)->where('status',
            'pending')->paginate($this->settings->result_request);

        if ($request->input('page') > $images->lastPage()) {
            abort('404');
        }

        return view('users.photos-pending', ['images' => $images]);
    }//<--- End Method

    public function editCollectionImages(Request $request)
    {
        dd('sms');
    }


    public function GetImageDownloadByType($query, $type = "standard", $user_id, $perPage = 50)
    {
        if ($perPage > 100) $perPage = 100;
        $data = $query->where(function ($query) use ($user_id, $type) {
            switch ($type) {
                case 'standard':
                    $query->whereHas('old_downloads', function ($q) use ($user_id) {
                        $q->where('user_id', $user_id);
                    })
                        ->orWhereHas('downloads', function ($q) use ($user_id, $type) {
                            $q->where('user_id', $user_id);
                            $q->where('license_type', $type);
                        })->orWhereHas('free_downloads', function ($q) use ($user_id) {
                            $q->where('user_id', $user_id);
                        });
                    break;

                default:
                    $query
                        ->whereHas('downloads', function ($q) use ($user_id, $type) {
                            $q->where('user_id', $user_id);
                            $q->where('license_type', $type);
                        });
                    break;
            }
        })
            ->paginate($perPage);
        return $data;
    }

    public function team(Request $request)
    {
        $user = auth()->user();
        if (!$user->isLeader())
            abort(401, 'Unauthorized');
        $team = $user->team;
        $invitation = $team->invitations()->with(['user' => function ($q) {
            $q->withCount(['active_team_subscriptions as team_remaining_credits' => function ($query) {
                $query->select(DB::raw('sum(team_user_subscription.remaining_credits)'));
            }]);
        }])->get();
        $subscriptions = $user->team->subscriptions()->has('plan')->with('plan')->orderBy('id', 'desc')->get()->unique(function ($r) {
            if ($r->plan_type == 'package')
                return $r->id;
            return $r->subscription_id;
        })->sortByDesc('id');

        return view('users/team', compact('user', 'team', 'invitation', 'subscriptions'));
    }

    public function delete_invitation($id)
    {
        $user = auth()->user();
        if (!$user->isLeader())
            abort(401, 'Unauthorized');
        $team = $user->team;
        $invitation = $team->invitations()->findOrFail($id);
        if ($invitation->user) {
            $invitation->user->team_id = 0;
            $invitation->user->save();
            $user_subscription = DB::table('team_user_subscription')
                ->where('team_id', $invitation->team_id)
                ->where('user_id', $invitation->user_id)
                ->first();
            Download::where('team_id', $invitation->team_id)
                ->where('user_id', $invitation->user_id)->update(['hide' => 1]);
            if ($user_subscription)
                DB::table('team_user_subscription')
                    ->where('team_id', $user_subscription->team_id)
                    ->where('user_id', $user_subscription->user_id)
                    ->where('subscription_id', $user_subscription->subscription_id)
                    ->update(['credits' => $user_subscription->credits - $user_subscription->remaining_credits, 'remaining_credits' => 0]);

        }
        $invitation->delete();

        return ['status' => 1];
    }

    public function new_invitation(Request $request)
    {
        $this->validate($request, [
            'name' => ['required'],
            'email' => ['required'],
        ]);
        $user = auth()->user();
        if (!$user->isLeader())
            abort(401, 'Unauthorized');
        $team = $user->team;
        $target_user = User::where('email', $request->email)->first();
        if (optional($target_user)->id == $user->id)
            return ['status' => 0, 'message' => __('You cannot send an invitation to yourself')];
        if (optional($target_user)->team)
            return ['status' => 0, 'message' => __('This user is on another team')];

        if ($team->invitations()->where('email', $request->email)->count())
            return ['status' => 0, 'message' => __('I already sent him an invitation')];
        $invitation = $team->invitations()->create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $target_user ? 'pending' : 'mailed',
            'uuid' => Str::uuid(),
        ]);
        if ($target_user)
            cache()->forget("user_invitation_{$target_user->id}");
        Mail::to($request->email)->queue(new TeamInvitation(['from' => $user->name, 'to' => $request->name, 'url' => route('invitation', ['invitation' => $invitation->uuid])]));
        return ['status' => 1, 'message' => __('The invitation has been sent successfully')];
    }

    public function subscription_credits(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isLeader())
            abort(401, 'Unauthorized');
        $team = $user->team;
        /**@var $subscription Subscription */
        $subscription = $team->subscriptions()->findOrFail($id);
        $users = $team->users()->with(['team_subscriptions' => function ($q) use ($subscription) {
            $q->where('team_user_subscription.subscription_id', $subscription->id);
        }])->withCount(['downloads as consumed_credits' => function ($q) use ($subscription) {
            $q->whereHas('subscriptions', function ($q) use ($subscription) {
                $q->where('download_subscription.subscription_id', $subscription->id);
            })->select(DB::raw('sum(credits)'))->whereRaw("team_id=users.team_id");
        }])->get();
        if ($request->method() == 'GET')
            return view('users.team_subscription_credits', compact('team', 'subscription', 'users'));
        $this->validate($request, [
            'user_id' => ['required', Rule::exists('users', 'id')->where('team_id', $team->id)],
            'credits' => ['required', 'numeric', 'min:0'],
        ]);
        $target_user = $team->users()->findOrFail($request->user_id);
        if ($request->credits == 0) {
            $target_user->team_subscriptions()->syncWithoutDetaching([
                $subscription->id => [
                    'team_id' => $team->id,
                    'remaining_credits' => $request->credits,
                ]
            ]);
            return ['status' => 1];
        }
        $subscription_users = $team->users()->whereHas('team_subscriptions', function ($q) use ($subscription) {
            $q->where('team_user_subscription.subscription_id', $subscription->id)
                ->where('team_user_subscription.remaining_credits', '>', 0);
        })->get();
        if ($subscription_users->count() >= $subscription->plan->members_limit && $subscription_users->where('id', $target_user->id)->count() == 0)
            return ['status' => 0, 'message' => __('You cannot add credit to more than :count members', ['count' => $subscription->plan->members_limit])];
        $user_subscription = $target_user->team_subscriptions()->find($id);
        if ($user_subscription) {
            $remaining_credits = DB::table('team_user_subscription')->where('subscription_id', $subscription->id)->where('user_id', '<>', $target_user->id)->sum('remaining_credits');
            if (($request->credits + $remaining_credits) > $subscription->remaining_credits)
                return ['status' => 0, 'message' => __("There are only :credits credits left in the subscription", ['credits' => $subscription->remaining_credits - $remaining_credits])];

            $target_user->team_subscriptions()->syncWithoutDetaching([
                $subscription->id => [
                    'team_id' => $team->id,
                    'remaining_credits' => $request->credits,
                ]
            ]);
        } else
            $target_user->team_subscriptions()->syncWithoutDetaching([
                $subscription->id => [
                    'team_id' => $team->id,
                    'remaining_credits' => $request->credits,
                ]
            ]);
        return ['status' => 1];
        return $request->all();
    }


    public function invitation($uuid)
    {
        $invitation = Invitation::where('uuid', $uuid)->whereIn('status', ['pending', 'mailed'])->first();
        if (!$invitation)
            return redirect()->route('landPage');
        if ($invitation->pending) {
            $invitation->update(['status' => 'accepted']);
            $invitation->user->update(['team_id' => $invitation->team_id]);
            return redirect()->route('user.profile')->with('success', __('Congratulations, You now a member of a team'));
        }
        $invitation->update(['status' => 'pending']);
        return redirect()->route('register');
    }

    public function accept_invitation($uuid)
    {
        $user = auth()->user();
        $invitation = Invitation::has('team')->where('uuid', $uuid)->where('email', $user->email)->whereIn('status', ['pending', 'mailed'])->first();
        if ($invitation) {
            $invitation->update(['status' => 'accepted', 'user_id' => $user->id]);
            $user->update(['team_id' => $invitation->team_id]);
            return redirect()->back()->with('success', __('Congratulations, You now a member of a team') . " ({$invitation->team->name})");
        }
        return redirect()->back();
    }

    public function decline_invitation($uuid)
    {
        $user = auth()->user();
        $invitation = Invitation::where('uuid', $uuid)->where('email', $user->email)->whereIn('status', ['pending'])->first();
        if (optional($invitation)->status == 'pending') {
            $invitation->update(['status' => 'declined']);
        }
        return redirect()->back();
    }

    public function update_subscription_pmethod($subscription, $pmethod)
    {
        $paymentMethod = \Stripe\PaymentMethod::retrieve($pmethod);
        $customer = \Stripe\Customer::retrieve('cus_O82VRJXBF4mi5a');
        $paymentMethod->attach([
            'customer' => $customer->id,
        ]);
        $customer->invoice_settings->default_payment_method = $paymentMethod->id;
        $customer->save();
        $subscription = \Stripe\Subscription::retrieve($subscription);
        $subscription->default_payment_method = $paymentMethod->id;
        $subscription->save();
    }
}
