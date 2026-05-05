@foreach($data as $collection )
<?php
$imageCollection = 'img-collection.jpg';

if ( $collection->thumbnail )
  $imageCollection = $collection->thumbnail;
?>


<div class="col-12 col-sm-6 col-md-4 col-lg-4 pd-l-r--3">
          <div class="card-category" style="background-image: url('{{ cdn($imageCollection) }}')">
          <a href="{{ route('account.collection.images', $collection->id) }}">
            <div class="hover">
              <div class="hover-overlay"></div>
              <div class="card-category-content">
                <h4 class="card-category-title">{{$collection->title}} <br>
                ({{$collection->count_collection}})<br>
                
            </h4>

              </div>
            </div>
           </a>
          </div>
        </div>


@endforeach
