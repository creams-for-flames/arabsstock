<?php $date = Carbon\Carbon::yesterday()->format('Y-m-d'); ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
         <loc>{{ route('landPage') }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>

	@foreach( App\Models\Pages::all() as $page )
	<url>
         <loc>{{ route('page.show',$page->slug) }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>
 @endforeach

	@foreach( App\Models\User::where('status','active')->get() as $user )
	<url>
         <loc>{{ url($user->username) }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>
   @endforeach

   @foreach( App\Models\Image::where('status','active')->get() as $response )
	<url>
         <loc>{{ $response->post_link }}</loc>
         <lastmod>{{ date('Y-m-d', strtotime($response->date) ) }}</lastmod>
         <priority>0.8</priority>
   </url>
 @endforeach

</urlset>
