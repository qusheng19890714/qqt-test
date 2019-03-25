<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CaptchasController extends Controller
{
    //图形验证码
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.str_random(15);

        //手机号码
        $tel = $request->tel;
        //验证码
        $captcha = $captchaBuilder->build();
        //过期时间
        $expiredAt = now()->addMinutes(2);

        \Cache::put($key, ['tel'=>$tel, 'captcha'=>$captcha->getPhrase()], $expiredAt);

        $result = [

            'captcha_key' =>$key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_content' =>$captcha->inline(),
        ];

        return $this->response->array($result)->setCode(201);
    }
}
