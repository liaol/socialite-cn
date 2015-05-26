<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class WeixinProvider extends AbstractProvider implements ProviderInterface
{

    protected $openId;


	 /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    protected function buildAuthUrlFromBase($url, $state)
    {
        $session = $this->request->getSession();

        return $url.'?'.http_build_query($this->getCodeFields($state), '', '&', $this->encodingType) . '#wechat_redirect';
    }

    protected function getCodeFields($state)
    {
        return [
            'appid' => $this->clientId, 
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator), 'state' => $state,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

      /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(),['query'=>($this->getTokenFields($code))]);
        return  $this->parseAccessToken($response->getBody());
    }

    protected function getTokenFields($code)
    {
        return array(
            'appid'=>$this->clientId,
            'secret'=>$this->clientSecret,
            'code'=>$code,
            'grant_type'=>'authorization_code',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo',['query'=>[
            'access_token'=>$token,
            'openid'=>$this->openId,
            'lang'=>'zh_CN'//简体中文
        ]]);
        return $this->checkError(json_decode($response->getBody(), true));
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['openid'], 'nickname' => $user['nickname'], 'avatar' => $user['headimgurl'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        $jsonArray =  $this->checkError(json_decode($body, true));
        $this->openId = $jsonArray['openid'];//记录openid
        return $jsonArray['access_token'];
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
        if (isset($data['errcode'])) {
            if ($data['errcode'] != 0) {
                throw new ErrorCodeException($data['errcode'],$data['errmsg']);
            }
        }
        return $data;
    }
}
