<section class="pb-50 pt-50">
    @php($hajj_image_category=\App\Models\ImageCategory::where('slug','hajj')->first())
    @php($hajj_video_category=\App\Models\VideoCategory::where('slug','hajj')->first())
    @php($hajj_vector_tag=\App\Models\Tag::find(29611))
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                <h2 class="text-capitalize color-primary">{{ __('Hajj') }}</h2>
                <p class="color-secondary">{{ trans('global.Content you need at this time') }}
                </p>
            </div>
        </div>
        <div class="center-row-section row">
            @if($hajj_image_category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('{{ $hajj_image_category->thumbnail }}')">
                        <a href="{{ route('category.show',$hajj_image_category->slug) }}">
                            <div class="hover">
                                <div class="hover-overlay"></div>
                                <div class="card-category-content">
                                    <h3 class="card-category-title">{{ __('Images') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
            @if($hajj_video_category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('{{ $hajj_video_category->thumbnail }}')">
                        <a href="{{ route('video.category.show',$hajj_video_category->slug) }}">
                            <div class="hover">
                                <div class="hover-overlay"></div>
                                <div class="card-category-content">
                                    <h3 class="card-category-title">{{ __('Videos') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
            @if($hajj_vector_tag)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('https://cdn.arabsstock.com/uploads/img-category/hajj-v2ZfPdimwPRVAyDz8QkQdoPUCzukNF22.jpg')">
                        <a href="{{ route('vectors.tags.show',$hajj_vector_tag->slug) }}">
                            <div class="hover">
                                <div class="hover-overlay"></div>
                                <div class="card-category-content">
                                    <h3 class="card-category-title">{{ __('Vectors') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
