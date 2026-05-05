<script type="application/ld+json">
{!! json_encode([
    "@context"=> "http://schema.org",
    "@type"=> "Organization",
    "name"=> "Arabsstock",
    "url"=> url(''),
    "logo"=> asset('img/logo-'.app()->getLocale().'.png'),
    "sameAs"=> [
        "https://www.facebook.com/ArabsStock",
        "https://twitter.com/ArabsStock",
        "https://www.instagram.com/arabsstock",
        "https://www.linkedin.com/company/arabsstock",
        "https://www.pinterest.com/ArabsStock"
    ]
],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}
</script>
