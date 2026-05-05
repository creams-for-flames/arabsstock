<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Schema::defaultStringLength(255);
        Blade::withoutDoubleEncoding();
        Paginator::useBootstrapThree();
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        \DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        if (app()->isLocal())
            DB::enableQueryLog();
        Validator::extend('min_mb', function ($attribute, $value, $parameters, $validator) {
            // $validator->requireParameterCount(1, $parameters, 'min_mb');
            if ($value instanceof UploadedFile && !$value->isValid()) {
                return false;
            }
            $megabytes = round(($value->getSize() / 1000000), 2);
            $validator->addReplacer('min_mb',
                function ($message, $attribute, $rule, $parameters) {
                    return \str_replace(':min_mb', $parameters[0], $message);
                }
            );
            return $megabytes >= $parameters[0];
            //  $validator->validateSize($attribute, $megabytes , $parameters) ;
        });

        Validator::extend('max_mb', function ($attribute, $value, $parameters, $validator) {
            // $validator->requireParameterCount(1, $parameters, 'max_mb');
            if ($value instanceof UploadedFile && !$value->isValid()) {
                return false;
            }
            $megabytes = round(($value->getSize() / 1000000), 2);
            $validator->addReplacer('max_mb',
                function ($message, $attribute, $rule, $parameters) {
                    return \str_replace(':max_mb', $parameters[0], $message);
                }
            );
            return $megabytes <= $parameters[0];
            //  $validator->validateSize($attribute, $megabytes , $parameters) ;
        });
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        app('url')->forceRootUrl(config('app.url'));
        $https_value = config('app.https_value', 'https');
        app('url')->forceScheme($https_value);
        $this->ini_statistics();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    private function ini_statistics()
    {
        app()->singleton('statistics', function (){
            return cache()->remember('statistics', now()->addHour(), function () {
                $data = \DB::table('statistics')->get();
                $statistics = [];
                foreach ($data as $r) {
                    \Illuminate\Support\Arr::set($statistics, $r->key, $r->value);
                }
                return $statistics;
            });
        });

    }
}
