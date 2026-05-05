<?php namespace App\Models;

use App\Contexts\Content;
use App\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Video extends Model
{
    use SoftDeletes;
    use Content;

    protected $guarded = [];
    public $timestamps = false;


    protected $appends = [
        'title',
        'description',
        /*'thumbnail_resize',*/
        'str_slug',
        'post_link',
        'parent_post_link',
        'reserved',
        'img_caption',
    ];
    protected $with = ['user', 'parent'];

    public function user()
    {
        return $this->morphTo();
    }

    public function searchableAs()
    {
        return 'images_index';
    }


    public function child()
    {
        return $this->hasMany(Video::class, 'parent_id')->where('parent_id', '!=', null)
            ->orderBy('videos.size', 'desc');
    }

    public function parent()
    {
        return $this->belongsTo(Video::class, 'parent_id', 'id');
    }

    // public function getThumbnailResizeAttribute($value)
    // {
    //     return str_replace('/uploads', '', $this->thumbnail);
    // }

    public function likes()
    {
        return $this->hasMany(VideoLike::class)->where('status', '1');
    }

    public function downloads()
    {
        return $this->morphMany(Download::class, 'entity');
    }

    public function old_downloads()
    {
        return $this->hasMany(VideoDownload::class, 'video_id');
    }

    public function free_downloads()
    {
        return $this->morphMany(FreeDownload::class, 'entity');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\StockVideo', 'video_id')->orderBy('type', 'asc');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\VideoComment', 'video_id');
    }

    public function visits()
    {
        return $this->hasMany('App\Models\VisitVideo', 'video_id');
    }

    public function category()
    {
        return $this->belongsToMany(VideoCategory::class, 'category_video', 'video_id', 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(VideoCategory::class, 'category_video', 'video_id', 'category_id');
    }

    public function category_admin()
    {
        return $this->belongsTo(CategoryVideoAdmin::class, 'category_admin_id');
    }

    public function collections()
    {
        return $this->belongsToMany(VideoCollection::class, 'collection_video', 'video_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_video');
    }

    public function getTitleAttribute()
    {
        $lang = app()->getLocale();
        $name = 'title_' . $lang;
        if (@$this->$name) {
            return $this->$name;
        } else {
            return $this->title_ar;
        }
    }

    public function getDescriptionAttribute()
    {
        $lang = app()->getLocale();
        $name = 'description_' . $lang;
        if (@$this->$name) {
            return $this->$name;
        } else {
            if ($this->description_ar)
                return $this->description_ar;
        }
        return $this->{"title_$lang"};
    }

    public function getDurationAttribute($value)
    {
        return format_duration($value, 'i:s');
    }

    public function computer_vision_tags()
    {
        return $this->hasMany(ComputerVisionVideoTag::class, 'video_id');
    }

    public function adminCollection()
    {

        return $this->belongsToMany(AdminCollection::class, 'admin_collection_videos', 'video_id',
            'admin_collection_id');
    }

    public function getStrSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->title_en);
    }

    public function getPostLinkAttribute()
    {
        $slug = $this->slug ?: "clip-{$this->id}-no-seo-url";
        return route('video.show', $slug);
    }

    public function getParentPostLinkAttribute()
    {
        if ($this->parent_id != null) {

            $slug = "clip-{$this->parent_id}-no-seo-url";
        } else {
            $slug = "clip-{$this->id}-no-seo-url";
        }
        return route('video.show', $slug);
    }

    public function contributor_file()
    {
        return $this->belongsTo(ContributorVideo::class, 'contributor_video_id', 'id');
    }


    public function reviewer()
    {
        if (!$this->reviewer_id)
            return null;
        return User::find($this->reviewer_id);
    }

    public function publisher()
    {
        if (!$this->publisher_id)
            return null;
        return User::find($this->publisher_id);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('default_loaded_relations', function (Builder $builder) {
            $builder->with('category', 'user');
        });
        static::addGlobalScope('is_liked', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role == 'normal')
                $builder->withCount(['likes as is_like' => function ($q) {
                    $q->where('user_id', auth()->user()->id);
                }]);
        });
        static::addGlobalScope('reserved', function (Builder $builder) {
            if (!in_array(optional(auth()->user())->role, ['admin_video', 'admin', 'admin_vector'])) {
                $builder->whereNull('reserved_until')->orWhere('reserved_until', '<', now());
                if (auth()->check())
                    $builder->orWhere('reserved_to', auth()->id());
            }
        });

        static::addGlobalScope('not_deleted', function (Builder $builder) {
            $builder->where('videos.status', '!=', 'deleted');
        });
    }

    public function scopeTinySelection($query)
    {
        return $query->select(
            'videos.id',
            'videos.sort',
            'videos.thumbnail',
            'videos.thumbnail_sm',
            'videos.cut_video',
            'videos.gif_video',
            'videos.size_240p',
            'videos.price',
            'videos.preview',
            'videos.title_ar',
            'videos.title_en',
            'videos.slug',
            'videos.width',
            'videos.height',
            'videos.date'
        );
    }

    public function from_same_group($except = [0], $return_query = false)
    {
        $image = $this;
        $query = Video::tinySelection()->where('status', 'active')->whereNotNull('folder_id')
            ->where(function ($q) use ($image) {
                $q->where(function ($q) use ($image) {
                    $q->where('folder_id', '>', 0)->where('folder_id', $image->folder_id);
                })->orWhere(function ($q) use ($image) {
                    $q->where('contributor_submission_id', '>', 0)->where('contributor_submission_id', $image->contributor_submission_id);
                });
            })->whereNotIn('id', $except);
        if ($return_query)
            return $query;
        return $query->inRandomOrder()->take(15)->get();
    }


    public static function section_const()
    {
        return cache()->remember('videos_const', 60, function () {
            return \App\SectionConst::findOrFail(2);
        });
    }


    public function getHowUseAttribute()
    {
        return $this->how_use_image;
    }

    public function getThumbnailAttribute($val)
    {
        return trim($val, '/');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(VideoFolder::class, 'folder_id', 'id');
    }

    public function raw()
    {
        return $this->hasOne(RawVideo::class, 'video_id', 'id');
    }

    public function has_raw()
    {
        return optional($this->raw)->is_uploaded_original && optional($this->raw)->is_uploaded_preview && optional($this->raw)->status == 'active';
    }
}
