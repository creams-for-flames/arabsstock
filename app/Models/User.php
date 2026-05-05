<?php

namespace App\Models;

use App\Cashier\Billable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Config;
use DB;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // public $timestamps = false;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        'bio',
        'country_id',
        'email',
        'mobile',
        'password',
        'avatar',
        'cover',
        'status',
        'type_account',
        'website',
        'twitter',
        'paypal_account',
        'activation_code',
        'oauth_uid',
        'oauth_provider',
        'token',
        'authorized_to_upload',
        'role',
        'braintree_id',
        'paypal_email',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function sendPasswordResetNotification($token)
    {

        $this->notify(new ResetPasswordNotification($token));
    }


    public function payments_video()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'user');
    }

    public function image_subscriptions()
    {
        return $this->hasMany(ImageSubscription::class);
    }

    public function subscriptions_active()
    {
        return $this->hasMany(ImageSubscription::class)->where('ends_at', '>=', now());
    }


    public function active_subscriptions()
    {
        return $this->morphMany(Subscription::class, 'user')->where([['ends_at', '>=', now()]])->where('remaining_credits', '>', 0)
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_CANCEL])->orderBy('ends_at');
    }

    public function video_subscriptions()
    {

        return $this->hasMany(VideoSubscription::class);
    }

    public function vector_subscriptions()
    {

        return $this->hasMany(VectorSubscription::class);
    }

    public function active_image_subscriptions()
    {
        return $this->hasMany(ImageSubscription::class)->where([['ends_at', '>=', now()]])->whereIn('status', [ImageSubscription::STATUS_ACTIVE, ImageSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0');
        });
    }

    public function active_video_subscriptions()
    {
        return $this->hasMany(VideoSubscription::class)->where([['ends_at', '>=', now()]])->whereIn('status', [VideoSubscription::STATUS_ACTIVE, VideoSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0');
        });
    }

    public function active_vector_subscriptions()
    {
        return $this->hasMany(VectorSubscription::class)->where([['ends_at', '>=', now()]])->whereIn('status', [VectorSubscription::STATUS_ACTIVE, VectorSubscription::STATUS_CANCEL])->where(function ($q) {
            $q->where('download_remaining', '>', '0');
        });
    }

    public function images()
    {
        return $this->morphedByMany(Image::class, 'entity', 'downloads')->withPivot('license_type', 'date')->orderBy('downloads.created_at', 'desc');
    }

    public function videos()
    {
        return $this->morphedByMany(Video::class, 'entity', 'downloads');
    }

    public function vectors()
    {
        return $this->morphedByMany(Vector::class, 'entity', 'downloads');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function is_downloaded($object, $where = null)
    {
        $object_class = get_class($object);
        $singular = Str::singular($object->table);
        $download_class = "{$object_class}Download";
        if ($download_class::where('user_id', $this->id)->where("{$singular}_id", $object->id)->count())
            return true;
        $query = $this->downloads()->where('entity_type', get_class($object))
            ->where('entity_id', $object->id);
        if ($where)
            $query->where($where);
        return $query->count() ? true : false;
    }

    public function images_pending()
    {
        return $this->hasMany('App\Models\Image')->where('status', 'pending');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function likes()
    {
        return $this->hasMany('App\Models\ImageLike');
    }

    public function downloadImage()
    {
        return $this->hasMany(ImageDownload::class);
    }

    public function downloadVector()
    {

        return $this->hasMany('App\Models\VectorDownload');
    }


    public function comments()
    {
        return $this->hasMany('App\Models\Comments');
    }

    public function following()
    {
        return $this->hasMany('App\Models\Followers', 'follower')->where('status', '1');
    }

    public function followers()
    {
        return $this->hasMany('App\Models\Followers', 'following')->where('status', '1');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notifications', 'destination');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id')->first();
    }

    public static function totalImages($id)
    {
        return \App\Models\Image::where('user_id', '=', $id)->where('status', 'active')->count();
    }

    public function canDownload()
    {

        $subscription = ImageSubscription::where('user_id', $this->id)
            ->where([['ends_at', '>=', now()], ['status', '!=', ImageSubscription::STATUS_PENDING], ['download_remaining', '>', '0']])
            ->orderBy('plan_type')
            ->first();

        if ($subscription) {
            $plan = ImagePlan::where('id', $subscription->plan_id)->first();
            $remaining_days = $subscription->ends_at > now();

            if ($remaining_days and $subscription->download_remaining > 0)
                return true;
        }

        return false;

    }

    public function isDownload($image_id)
    {
        $downloadCheckUser = ImageDownload::where('image_id', $image_id)->where('user_id', $this->id)->first();
        if ($downloadCheckUser) {
            return true;
        } else {
            return false;
        }
    }


    public function canDownloadVideo($image_id)
    {
        $downloadCheckUser = VideoDownload::where('video_id', $image_id)->where('user_id', $this->id)->first();
        if ($downloadCheckUser) {
            return true;
        } else {
            return false;
        }
    }

    public function canDownloadVideosub()
    {
        $subscription = VideoSubscription::where('user_id', $this->id)
            ->where([['ends_at', '>=', now()], ['status', '!=', VideoSubscription::STATUS_PENDING], ['download_remaining', '>', '0']])
            ->orderBy('plan_type')
            ->first();

        if ($subscription) {
            $plan = VideoPlan::where('id', $subscription->plan_id)->first();
            $remaining_days = $subscription->ends_at > now();

            if ($remaining_days and $subscription->download_remaining > 0)
                return true;
        }

        return false;

    }


    public function canDownloadVector($vector_id)
    {
        $downloadCheckUser = VectorDownload::where('vector_id', $vector_id)->where('user_id', $this->id)->first();
        if ($downloadCheckUser) {
            return true;
        } else {
            return false;
        }

    }


//    public function role()
//    {
//        return $this->belongsToMany(\App\Role::class, 'role_user');
//    }

    public function cart_count()
    {
        $cart = new \App\Models\CartVideo();
        return $cart->where('user_id', $this->id)->count();
    }

    public function reviewed_images()
    {
        return $this->hasMany(Image::class, 'reviewer_id');
    }

    public function published_images()
    {
        return $this->hasMany(Image::class, 'publisher_id');
        return Image::where('publisher_id', $this->id);
    }

    public function created_images()
    {
        return $this->morphMany(Image::class, 'user');
    }

    public function created_videos()
    {
        return $this->morphMany(Video::class, 'user');
    }

    public function created_vectors()
    {
        return $this->morphMany(Vector::class, 'user');
    }

    public function bounced_emails()
    {
        return $this->hasMany(BouncedEmail::class, 'email', 'email');
    }

    public function newsletters()
    {
        return $this->belongsToMany(Newsletter::class);
    }

    public function getAvatarAttribute($val)
    {
        if ($this->role != 'normal')
            return asset('avatar/as.jpg');
        return asset($val) ?: asset('avatar/default.jpg');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function isLeader()
    {
        return $this->team && $this->team->leader_id == $this->id;
    }

    public function team_subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'team_user_subscription')->withPivot('credits', 'remaining_credits');
    }

    public function active_team_subscriptions()
    {
        return $this->team_subscriptions()->wherePivot('remaining_credits', '>', 0)->where('status', 1);
    }
}
