<?php namespace Liaol\SocialiteCn;

use Laravel\Socialite\AbstractUser;

class WeixinUser extends AbstractUser
{

    /**
     * The user's access token.
     *
     * @var string
     */
    public $token;

    /**
     * the weixin's refresh token
     * @var string
     */
    public $refresh_token;

    /**
     * accesstoken expires
     * @var int
     */
    public $expires_in;

    /**
     * Set the token on the user.
     *
     * @param  string  $token
     * @return $this
     */
    public function setToken($token,$expires_in,$refresh_token)
    {
        $this->token = $token;
        $this->expires_in = $expires_in;
        $this->refresh_token = $refresh_token;

        return $this;
    }
}
