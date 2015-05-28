<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class QQAppProvider extends AbstractProvider implements ProviderInterface
{
    protected $openId;

    
	 /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token) { 
        $openId = $this->getOpenId($token);
        $response = $this->getHttpClient()->get('https://graph.qq.com/user/get_user_info',['query'=>[
            'access_token'=>$token,
            'openid'=>$openId,
            // this should be clientId not client_id,
            'oauth_consumer_key'=>$this->clientId,
        ]]);
        return $this->checkError(json_decode($this->removeCallback($response->getBody()), true));
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
        $this->openId =  $this->checkError(json_decode($this->removeCallback($response->getBody()), true))['openid'];
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
        if (isset($data['error'])) {
            if( $data['error'] != 0) {
                throw new ErrorCodeException($data['error'],$data['error_description']);
            }
        }
        return $data;
    }

    protected function parseAccessToken($body)
    {
        parse_str($body,$array);
        return $array['access_token'];
    }

    protected function removeCallback($body)
    {
        return  str_replace(['callback(',')',';'],'',$body);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        $user = $this->mapUserToObject($this->getUserByToken(
            $token = \Request::input('token')
        ));
        return $user->setToken($token);
    }
}
