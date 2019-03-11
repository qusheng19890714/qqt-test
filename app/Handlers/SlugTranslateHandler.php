<?php

namespace  App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        $client = new Client;

        //初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        if (empty($appid) || empty($key)) {

            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值

        $sign = md5($appid.$text.$salt.$key);

        //构建请求函数
        $query = http_build_query([

            'q'     => $text,
            'from'  => 'zh_cn',
            'to'    => 'en',
            'appid' => $appid,
            'salt'  => $salt,
            'sign'  => $sign

        ]);

        // 发送 HTTP Get 请求
        $response = $client->get($api.$query);

        $result = json_decode($response->getBody(), true);


        if (isset($result['trans_result'][0]['dst'])) {

            return str_slug($result['trans_result'][0]['dst']);

        }else {

            return $this->pinyin($text);
        }
    }


    public function pinyin($text)
    {
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}