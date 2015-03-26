<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;


class QQProvider extends AbstractProvider implements ProviderInterface
{
    protected $openId;

	 /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://graph.qq.com/oauth2.0/authorize', $state);
    }


    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://graph.qq.com/oauth2.0/token';
    }
/** * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        //if the code is setted ,use it instead
        if (!is_null($this->code)) {
            $code = $this->code;
        }
        $response = $this->getHttpClient()->post($this->getTokenUrl(),['query'=>($this->getTokenFields($code))]);
        return  $this->parseAccessToken($response->getBody());
    }


    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token) { 
        $openId = $this->getOpenId($token);
        $response = $this->getHttpClient()->get('https://graph.qq.com/user/get_user_info',['query'=>[
            'access_token'=>$token,
            'openid'=>$openId,
            'oauth_consumer_key'=>$this->client_id,
        ]]);
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $this->openId, 'nickname' => $user['nickname'], 'avatar' => $user['figureurl'],
        ]);
    }

    /**
        * @Synopsis  get openid
        *
        * @Param $token
        *
        * @Returns  uid string 
     */
    protected function getOpenId($token)
    {
        $response = $this->getHttpClient()->get('https://graph.qq.com/oauth2.0/me',['query'=>['access_token'=>$token]]);
        $this->openId =  json_decode($response->getBody(), true)['openid'];
        return $this->openId;
    }

    /**
        * @Synopsis  check http error 
        *
        * @Param $data
        *
        * @Returns  mix 
     */
    protected function checkError($data)
    {
        if ($data['errcode'] != 0) {
            throw new ErrorCodeException($data['errcode'],$data['errmsg']);
        }
        return $data;
    }
}
