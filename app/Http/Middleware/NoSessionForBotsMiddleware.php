<?php

namespace App\Http\Middleware;

use App\Contexts\Utils\CrawlerDetect\CrawlerDetect;
use Closure;

class NoSessionForBotsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $CrawlerDetect = new CrawlerDetect();
        if ($CrawlerDetect->isCrawler($request->header('User-Agent')) || strpos($request->header('User-Agent'), 'bot')) {
            \Config::set('session.driver', 'file');
        }
        return $next($request);
    }
}
