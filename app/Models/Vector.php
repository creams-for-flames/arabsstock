<?php namespace App\Models;

use App\Contexts\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Vector extends Model
{
    use SoftDeletes;
    use Content;

    protected $guarded = [];
    public $timestamps = false;

    protected $appends = ['title', 'description', 'str_slug', 'slug_vue', 'post_link', 'image', 'reserved', 'img_caption',];

    public function user()
    {
        return $this->morphTo();
    }

    public function searchableAs()
    {
        return 'vectors_index';
    }

    public function child()
    {
        return $this->hasMany(StockVector::class);
    }

    public function getImageAttribute()
    {

        if ($this->thumbnail != '') {
            return 'uploads/thumbnail/' . $this->thumbnail;
        } else {
            return '';
        }
    }


    public function likes()
    {
        return $this->hasMany(VectorLike::class, 'vector_id')->where('status', '1');
    }

    public function downloads()
    {
        return $this->morphMany(Download::class, 'entity');
    }

    public function old_downloads()
    {
        return $this->hasMany(VectorDownload::class, 'vector_id');
    }

    public function free_downloads()
    {
        return $this->morphMany(FreeDownload::class, 'entity');
    }

    public function stock()
    {
        return $this->hasMany('App\Models\StockVector')->orderBy('type', 'asc');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\VectorComment');
    }

    public function visits()
    {
        return $this->hasMany('App\Models\VisitVector', 'vector_id');
    }

    public function category()
    {
        return $this->belongsToMany(VectorCategory::class, 'category_vector', 'vector_id', 'category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(VectorCategory::class, 'category_vector', 'vector_id', 'category_id');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_image', 'image_id');
    }

    public function collectionsVectors()
    {
        return $this->hasMany('App\Models\CollectionVector');
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


    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_vector');
    }

    public function computer_vision_tags()
    {

        return $this->hasMany(ComputerVisionVectorTag::class, 'vector_id');
    }

    public function adminCollection()
    {
        return $this->belongsToMany(AdminCollection::class, 'admin_collection_vectors', 'vector_id', 'admin_collection_id');
    }

    public function getPostLinkAttribute()
    {
        $slug = $this->slug ?: "vector-{$this->id}-no-seo-url";
        // return $slug;
        return route('vector.show', $slug);
    }

    /**
     * Get the user that owns the Vector
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributor_file()
    {
        return $this->belongsTo(ContributorVector::class, 'contributor_vector_id', 'id');
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
                    $q->where('user_id', 'like', auth()->user()->id);
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
            $builder->where('vectors.status', '!=', 'deleted');
        });
    }


    public function scopeTinySelection($query)
    {
        return $query->select(
            'vectors.id',
            'vectors.sort',
            'vectors.thumbnail',
            'vectors.preview',
            'vectors.title_ar',
            'vectors.title_en',
            'vectors.slug',
            'vectors.name',
            'width_thumbnail',
            'height_thumbnail'
        );
    }

    public function folders()
    {
        return $this->belongsToMany(VectorFolder::class, 'folder_vector', 'vector_id', 'folder_id');
    }

    public function from_same_group($except = [0], $return_query = false)
    {
        $image = $this;
        $query = Vector::tinySelection()->where('status', 'active')->whereNotNull('folder_id')
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
        return cache()->remember('vectors_const', 60, function () {
            return \App\SectionConst::findOrFail(3);
        });
    }

    public function getWidthVectorAttribute($val)
    {
        return intval($val);
    }

    public function getHeightVectorAttribute($val)
    {
        return intval($val);
    }

    public function getHowUseAttribute()
    {
        return $this->how_use_vector;
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(VectorFolder::class, 'folder_id', 'id');
    }
}
