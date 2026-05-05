<?php namespace App\Models;

use App\Contexts\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Image extends Model
{
    use SoftDeletes;
    use Content;

    protected $guarded = [];
    public $timestamps = false;
    protected $appends = ['title', 'description', 'str_slug', 'slug_vue', 'post_link', 'reserved', 'img_caption', 'has_removebg'];

    public function user()
    {
        return $this->morphTo();
    }

    public function searchableAs()
    {
        return 'images_index';
    }

    public function likes()
    {
        return $this->hasMany(ImageLike::class, 'image_id')->where('status', '1');
    }

    public function downloads()
    {
        return $this->morphMany(Download::class, 'entity');
    }

    public function old_downloads()
    {
        return $this->hasMany(ImageDownload::class, 'image_id');
    }

    public function free_downloads()
    {
        return $this->morphMany(FreeDownload::class, 'entity');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\Stock')->orderBy('type', 'asc');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comments');
    }

    public function visits()
    {
        return $this->hasMany(VisitImage::class);
    }

    public function category()
    {
        return $this->belongsToMany(ImageCategory::class, 'category_image', 'image_id', 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ImageCategory::class, 'category_image', 'image_id', 'category_id');
    }


    public function category_image()
    {
        return $this->hasMany(CategoryImage::class, 'category_id');
    }

    public function category_admin()
    {
        return $this->belongsTo(CategoryAdmin::class);
    }

    public function collections()
    {
        return $this->belongsToMany(ImageCollection::class, 'collection_image', 'image_id', 'collection_id');
    }

    public function collectionsImages()
    {
        return $this->hasMany('App\Models\CollectionImage');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_image');
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

    public function getStrSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->title_en);
    }

    public function getSlugVueAttribute()
    {
        if ($this->relationLoaded('category'))
            return optional(optional($this->category)->first())->slug;
        return '';
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

    public function computer_vision_tags()
    {
        return $this->hasMany(ComputerVisionImageTag::class, 'image_id');
    }

    public function adminCollection()
    {
        return $this->belongsToMany(AdminCollection::class, 'admin_collection_images', 'image_id',
            'admin_collection_id');
    }

    public function getPostLinkAttribute()
    {
        $slug = $this->slug ?: "image-{$this->id}-no-seo-url";
        return route('photo.show', $slug);
    }

    public function contributor_file()
    {
        return $this->belongsTo(ContributorImage::class, 'contributor_image_id', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
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
            $builder->where('images.status', '!=', 'deleted');
        });
    }

    public function scopeTinySelection($query)
    {
        return $query->select('images.id', 'thumbnail', 'preview', 'title_ar', 'title_en', 'slug','in_home', 'date', 'width_thumbnail', 'height_thumbnail','removebg_image','removebg_status_disply','removebg_status','removebg_preview');
    }

    public function from_same_group($except = [0], $return_query = false)
    {
        $image = $this;
        $query = Image::tinySelection()->where('status', 'active')->whereNotNull('folder_id')
            ->where(function ($q) use ($image) {
                $q->where(function ($q) use ($image) {
                    $q->where('folder_id', '>', 0)->where('folder_id', $image->folder_id);
                })->orWhere(function ($q) use ($image) {
                    $q->where('contributor_submission_id', '>', 0)->where('contributor_submission_id', $image->contributor_submission_id);
                });
            })->whereNotIn('id', $except);
        if ($return_query)
            return $query;
        return $query->inRandomOrder()->get();
    }

    public static function section_const()
    {
        return cache()->remember('images_const', 60, function () {
            return \App\SectionConst::findOrFail(1);
        });
    }

    public function getWidthLargeAttribute($val)
    {
        return intval($val);
    }

    public function getHeightLargeAttribute($val)
    {
        return intval($val);
    }

    public function getHowUseAttribute()
    {
        return $this->how_use_image;
    }

    public function getHasRemoveBgAttribute()
    {
        return $this->removebg_image && ($this->removebg_status_disply == 'active') && ($this->removebg_status == 'done') && $this->removebg_preview;
    }

    /**
     * Get the user that owns the Image
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(ImageFolder::class, 'folder_id', 'id');
    }
}
