<?php

namespace App\Providers;

use App\Models\Word;
use App\Observers\WordObserver;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Mockery\Exception;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            if (env('ELASTICSEARCH_ENABLED')) {
                try {
                    return ClientBuilder::create()
                        ->setHosts(config('elasticsearch.hosts'))
                        ->build();
                } catch (Exception $ex) {
                    return null;
                }
            } else {
                return null;
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Word::observe(WordObserver::class);
    }
}
