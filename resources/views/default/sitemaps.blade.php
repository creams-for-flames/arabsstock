<?php $date = Carbon\Carbon::yesterday()->format('Y-m-d'); ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
         <loc>{{ route('landPage') }}</loc>
         <lastmod>{{$date}}</lastmod>
         <priority>0.8</priority>
   </url>
   

   
</urlset>