<?php namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider as BaseProvider;

class AbstractProvider extends BaseProvider 
{
    protected $code;

    protected $token;

    /**
        * @Synopsis  set code input from mobile client
        *
        * @Param $code
        *
        * @Returns  $this 
     */
    public function setCode($code)
    {
         $this->code = $code;
         return $this;
    }

    /**
        * @Synopsis  set access token if necessary
        *
        * @Param $token
        *
        * @Returns  $this 
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
}
