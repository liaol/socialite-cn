<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Liaol\SocialiteCn\WeixinUser;

class WeixinProvider extends AbstractProvider implements ProviderInterface
{

    protected $openId;

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = ['snsapi_login'];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ',';


     /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/qrconnect', $state);
    }

    protected function buildAuthUrlFromBase($url, $state)
    {
        $session = $this->request->getSession();

        return $url.'?'.http_build_query($this->getCodeFields($state), '', '&', $this->encodingType) . '#wechat_redirect';
    }

    protected function getCodeFields($state)
    {
        return [
            'appid' => $this->clientId, 'redirect_uri' => $this->redirectUrl,
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

    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $result = $this->getAccessToken($this->getCode());
        $token = $result['access_token'];
        $expires_in = $result['expires_in'];
        $refresh_token = $result['refresh_token'];

        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token,$expires_in,$refreh_token);
    }


    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new WeixinUser)->setRaw($user)->map([
            'id' => $user['openid'], 'name' => $user['nickname'],'nickname' => $user['nickname'], 'avatar' => $user['headimgurl'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        $jsonArray =  $this->checkError(json_decode($body, true));
        $this->openId = $jsonArray['openid'];//记录openid
        return $jsonArray;
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
            throw new ErrorCodeException($data['errcode'],$data['errmsg']);
        }
        return $data;
    }
}
