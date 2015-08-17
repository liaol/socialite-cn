<?php namespace Liaol\SocialiteCn;

use Laravel\Socialite\SocialiteManager;

class SocialiteCnManager extends SocialiteManager
{
	protected function createWeixinDriver()
	{
		$config = $this->app['config']['services.weixin'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\WeixinProvider', $config
        );
	}

	protected function createWeiboDriver()
	{
		$config = $this->app['config']['services.weibo'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\WeiboProvider', $config
        );
	}

	protected function createQQDriver()
	{
		$config = $this->app['config']['services.qq'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\QQProvider', $config
        );
	}

	protected function createWeixinWebDriver()
	{
		$config = $this->app['config']['services.weixinWeb'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\WeixinWebProvider', $config
        );
	}

	protected function createQQAppDriver()
	{
		$config = $this->app['config']['services.qqApp'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\QQAppProvider', $config
        );
	}

	protected function createWeiboAppDriver()
	{
		$config = $this->app['config']['services.weiboApp'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\WeiboAppProvider', $config
        );
	}

    protected function createDoubanDriver()
    {
        $config = $this->app['config']['services.douban'];
        return $this->buildProvider(
            'Liaol\SocialiteCn\Providers\DoubanProvider', $config
        );
    }
}
