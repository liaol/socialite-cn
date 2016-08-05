<?php namespace Liaol\SocialiteCn;

use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Socialite\SocialiteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Liaol\SocialiteCn\Providers\WeixinProvider;
use Illuminate\Support\ServiceProvider;

class SocialiteCnServiceProvider extends SocialiteServiceProvider
{
    public function register()
    {
       // parent::boot();
        $this->app->singleton('Laravel\Socialite\Contracts\Factory', function ($app) {
            return new SocialiteCnManager($app);
        });
    }

}
