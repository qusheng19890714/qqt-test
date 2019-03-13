<?php

namespace App\Providers;

use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Support\ServiceProvider;
use App\Observers\TopicObserver;
use App\Observers\ReplyObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::setLocale('zh');
        Topic::observe(TopicObserver::class);
        Reply::observe(ReplyObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->isLocal()){

            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }
    }
}
