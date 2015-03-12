<?php namespace Liaol\SocialiteCn;

use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Socialite\SocialiteServiceProvider;

class SocialiteCnServiceProvider extends SocialiteCnServiceProvider
{
    public function boot(Dispatcher $event, SocialiteWasCalled $socialiteWasCalled)
    {
        $event->fire($socialiteWasCalled);
    }
}
