# socialite-cn

## laravel/socialite for Chinese.

Oauth authentication with Weixin,Weibo and QQ.

## Usage

* require in your composer.json file:
```
"liaol/socialite-cn": "dev-master"
```

* register the  ```'Liaol\SocialiteCn\SocialiteCnServiceProvider'``` in your ```config/app.php```

* add ```'Socialize' => 'Laravel\Socialite\Facades\Socialite'``` to ```config/app.php```

* Others are same with the [laravel/socialite](http://laravel.com/docs/5.0/authentication#social-authentication)

### Providers:
[weixin](http://mp.weixin.qq.com/wiki/14/bb5031008f1494a59c6f71fa0f319c66.html)    
[weixinWeb](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&lang=zh_CN)     
[weibo](http://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6)    
[qq](http://wiki.open.qq.com/wiki/website/OAuth2.0%E5%BC%80%E5%8F%91%E6%96%87%E6%A1%A3)    

weiboApp, qqApp are used for Api




## Require

laravel/socialite

## License

[MIT license](http://opensource.org/licenses/MIT)

