<section class="pb-50 pt-50">
    @php($national_day_image_category=cache()->remember('national_day_image_category',now()->addHours(3),function(){return \App\Models\ImageCategory::find(27);}))
    @php($national_day_video_category=cache()->remember('national_day_video_category',now()->addHours(3),function(){return \App\Models\VideoCategory::find(27);}))
    @php($national_day_vector_tag=cache()->remember('national_day_vector_tag',now()->addHours(3),function(){return \App\Models\Tag::where('slug','اليوم-الوطني-السعودي')->first();}))
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-12 col-sm-11 col-md-11 col-lg-11">
                <h2 class="text-capitalize color-primary">{{ __('National Day') }}</h2>
                <p class="color-secondary">{{ trans('global.Content you need at this time') }}
                </p>
            </div>
        </div>
        <div class="center-row-section row">
            @if($national_day_image_category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('{{ $national_day_image_category->thumbnail }}')">
                        <a href="{{ route('category.show',$national_day_image_category->slug) }}">
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
            @if($national_day_video_category)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('{{ $national_day_video_category->thumbnail }}')">
                        <a href="{{ route('video.category.show',$national_day_video_category->slug) }}">
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
            @if($national_day_vector_tag)
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
                    <div class="card-category"
                         style="background-image: url('https://cdn.arabsstock.com/uploads/img-category/national-day-vector-Ikum4BVpyXw7ncHXLl3.jpg')">
                        <a href="{{ route('vectors.tags.show',$national_day_vector_tag->slug) }}">
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
