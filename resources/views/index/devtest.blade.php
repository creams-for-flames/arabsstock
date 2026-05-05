<link rel="stylesheet" href="{{ asset('css/bootstrap_4.4.1_css_bootstrap.min.css') }}" >
<div class="row">
    @foreach( $data as $imageItem )
        <div class="col-3 m-5" title="{{$imageItem->title_ar}}">
            <hr/> large
            <div class="item card-photo"  >
                <div class="hover">
                    <a href="{{ $imageItem->post_link }}">
                        <img class="w-100" width="150"height="150" srcset="{{ url($imageItem->large) }}" src="{{ url($imageItem->large) }}" alt="{{$imageItem->title}}">
                        <div class="hover-overlay"></div>
                    </a>

                </div>
            </div>
            <hr/> preview
            <div class="item card-photo"  >
                <div class="hover">
                    <a href="{{ $imageItem->post_link }}">
                        <img width="150"height="150" class="w-100" srcset="{{ url($imageItem->preview) }}" src="{{ url($imageItem->preview) }}" alt="{{$imageItem->title}}">
                        <div class="hover-overlay"></div>
                    </a>

                </div>
            </div>
            <p>
                {{$imageItem->contributor_image_id === 0?'Arabsstock':'Contributor'}} - id :  {{$imageItem->id}} - cimg: {{$imageItem->contributor_image_id}} - user_id: {{$imageItem->user_id ===1?'Admin':$imageItem->user_id}}
                {{$imageItem->user?'name : '.$imageItem->user->username:''}}
                - Download Count :  {{$imageItem->downloads_count}}
                - Dpi :
            @foreach($imageItem->stock as $stock)
                <p>
                    type: {{$stock->type}}
                    dpi: {{$stock->dpi}}
                </p>
                @endforeach
                </p>
        </div>

    @endforeach
</div>
<div class="d-flex justify-content-center">

    {{ $data->links() }}
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="{{ asset('js/bootstrap_4.4.1_js_bootstrap.min.js') }}" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
