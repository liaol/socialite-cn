<?php
namespace Liaol\SocialiteCn\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

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
        //https://open.weixin.qq.com/connect/qrconnect?appid=wxa7cab46e4f3f7e2f&redirect_uri=http%3A%2F%2F127.0.0.1%2Fsocialite%2Fweixin%2Fcallback&response_type=code&scope=snsapi_login&state=0b32666e9e6e694a9b9b059eb141a294e8ca698a#wechat_redirect
        //https://open.weixin.qq.com/connect/qrconnect?appid=wxbdc5610cc59c1631&redirect_uri=https%3A%2F%2Fpassport.yhd.com%2Fwechat%2Fcallback.do&response_type=code&scope=snsapi_login&state=3d6be0a4035d839573b04816624a415e#wechat_redirect
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

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
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
            throw new ErrorCodeException($data['errcode'],$data['errmsg']);
        }
        return $data;
    }
}
