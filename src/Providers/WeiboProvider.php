<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;


class WeiboProvider extends AbstractProvider implements ProviderInterface
{

	 /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://api.weibo.com/oauth2/authorize', $state);
    }


    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.weibo.com/oauth2/access_token';
    }

      /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(),['query'=>($this->getTokenFields($code))]);
        return  $this->parseAccessToken($response->getBody());
    }


    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $uid = $this->getUid($token);
        $response = $this->getHttpClient()->get('https://api.weibo.com/2/users/show.json',['query'=>[
            'access_token'=>$token,
            'uid'=>$uid,
        ]]);
        return $this->checkError(json_decode($response->getBody(), true));
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['idstr'], 'nickname' => $user['name'], 'avatar' => $user['avatar_large'],
        ]);
    }

    /**
        * @Synopsis  get uid
        *
        * @Param $token
        *
        * @Returns  uid string 
     */
    protected function getUid($token)
    {
        $response = $this->getHttpClient()->get('https://api.weibo.com/2/account/get_uid.json',['query'=>['access_token'=>$token]]);
        return $this->checkError(json_decode($response->getBody(), true))['uid'];
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
        if (isset($data['error_code'])) {
            throw new ErrorCodeException($data['error_code'],$data['error']);
        }
        return $data;
    }
}
