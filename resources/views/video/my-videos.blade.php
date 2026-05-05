@extends('includes.profile')
@section('profile_content')
    <div class="container my-downloads">
        <h3 class="pb-3"> @lang('global.myVideos') </h3>
        <ul class="nav mb-3" id="pills-tab" role="tablist">
            @if(count($types))
            @foreach ($types as $type)
            <li class="nav-item" role="presentation">
              <button class="mb-0 nav-link  @if ($loop->first) active @endif"
                 id="{{$type}}-tab" data-toggle="pill" onclick="ChangeDownloadType('{!! $type !!}')"  data-target="#{{$type}}" type="button"
              role="tab" aria-controls="{{$type}}" aria-selected="true">{{__(ucfirst($type)." license")}}</button>
            </li>

            @endforeach
            @endif
          </ul>
          <div class="tab-content" id="pills-tabContent">
            @if(count($types))
            @foreach ($types as $type)
              <div class="tab-pane fade show @if ($loop->first) active @endif" id="{{$type}}" role="tabpanel"
                   aria-labelledby="{{$type}}-tab">
              </div>
            @endforeach
            @endif

          </div>
    </div>
@endsection
@push('javascript_navbar')
@endpush
@push('javascript_navbar')
    <script>
            var  perPage = "{{ $perPage??50 }}";
            var  download_type = "{{ $download_type }}";
            var  my_downloads_url = "{{route('me.videos')}}";
            $(function () {
                $(document).on('click', '.download-pagination .pagination a', function (e) {
                    e.preventDefault();
                    var $url = $(this).attr('href') + "&download_type=" + download_type;
                    FeatchImage($url)
                    return false;
                });
                ChangeDownloadType('standard');
            });

            function ChangeDownloadType(type) {
                download_type = type;
                new_url = my_downloads_url + "?download_type=" + download_type;
                FeatchImage(new_url)
                $('.dataResult').flexImages({rowHeight: 200});
            }

            function getPerPage(e) {
                per_page = e.value;
                perPage = per_page;
                new_url = my_downloads_url + "?download_type=" + download_type;
                FeatchImage(new_url)
            }

            function FeatchImage($url) {
                $.ajax({
                    type: "GET",
                    url: $url + "&perPage=" + perPage,
                    success: function (response) {
                        var $response = response;
                        $('#' + $response.download_type).html($response.downloads);
                        $('.dataResult').flexImages({rowHeight: 200});
                    },
                    erroor: function (error) {
                        console.log(error)
                    }
                });
            }
    </script>
@endpush
