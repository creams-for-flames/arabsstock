@if (isset($data) and $data->count())
    <div class="download-pagination mt-4">
        {{ $data->links() }}
    </div>
    <div class="flex-images btn-block margin-bottom-40 dataResult">
        @foreach ($data as $video)
            <div class="item card-photo" data-w="{{($video->width)}}px"
                 data-h="{{($video->height)}}px">
                <div class="hover border-file">
                    <a href="{{ $video->post_link }}">
                        <img class="w-100" srcset="{{ cdn($video->thumbnail) }}"
                             src="{{ cdn($video->thumbnail) }}" alt="{{$video->title}}">
                        <div class="hover-overlay"></div>
                    </a>
                    @if($video->downloader_id!=auth()->id())
                        @php($downloader=\App\Models\User::find($video->downloader_id))
                        @if($downloader)
                            <span data-toggle="tooltip"
                                  data-placement="top"
                                  title="<span>{{ $downloader->name }}</span> </br><span>{{ $video->pivot_date }}</span>"
                            >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                    </svg>
                                </span>
                        @endif
                    @endif
                    <div class="card-photo-content">
                        <h3 class="card-photo-title">{{$video->title}}</h3>
                        @if($download_type==='exclusive')
                            <div class="icon">
                                <div
                                    class="foot fs-12 d-flex">
                                    <span class="text-light">
                                        {{ __('The license expires on') }}
                                : {{ format_date('d M Y',$video->reserved_until) }}
                                    </span>
                                    @if($video->reserved_until < now())
                                        <span class="text-danger ml-2">{{trans('global.status.finished')}}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="download-pagination mt-4 col-lg-8 col-12">
            {{ $data->links() }}
        </div>
        <div class="kt-pagination kt-pagination--brand mt-4 col-lg-4 col-12">
            <div class="kt-pagination__toolbar">
                <select class="form-control" onchange="getPerPage(this)" name="perPage">
                    <option
                        @if (isset($perPage) && $perPage == 50)
                        selected
                        @endif
                        value="50">50
                    </option>
                    <option
                        @if (isset($perPage) && $perPage == 100)
                        selected
                        @endif
                        value="100">100
                    </option>
                </select>
                <span class="pagination__desc">
                {{__("pagination.desc",['start'=> $data->firstItem(),'end'=> $data->lastItem(),'total'=> $data->total()])}}
            </span>
            </div>
        </div>
    </div>
@else
    <section class="border rounded p-4">
        <p class="mb-1"> {{ __("You don't have any downloads") }} </p>
        <small>
            <a href="{{ route('video.home') }}"> {{ __('You can download from here') }} </a>
        </small>
    </section>
@endif
<script>
    $('[data-toggle="tooltip"]').tooltip({
        animated: 'fade',
        placement: 'bottom',
        html: true,
        placement: 'top',
    });
</script>
