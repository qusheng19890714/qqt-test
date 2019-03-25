<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;


class VerificationCodesController extends Controller
{
    //发送验证码
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {

            return $this->response->error('验证码已经失效', 422);
        }

        if (!hash_equals($captchaData['captcha'], $request->captcha_content)) {

            //验证码错误就清除缓存
            \Cache::forget($request->captcha_key);

            return $this->response->errorUnauthorized('验证码错误');
        }

        $tel = $captchaData['tel'];


        //如果是测试环境, 验证码是1234
        if (!app()->environment('production')) {

            $code = '1234';

        }else {

            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {

                $result = $easySms->send($tel, [

                    'content' => "【大洋柜】您的验证码是{$code}。如非本人操作，请忽略本短信"
                ]);

            }catch (NoGatewayAvailableException $exception){

                $message = $exception->getException('yunpian')->getMessage();

                return $this->response->errorInternal($message ?: '短信发送异常');
            }

        }

        $key = 'verificationCode_' . str_random(15);

        //过期时间10分钟
        $expiredAt = now()->addMinutes(10);

        //缓存验证码
        \Cache::put($key, ['code'=>$code, 'phone'=>$tel], $expiredAt);

        return $this->response->array([

            'key'=>$key,
            'expired_at' =>$expiredAt->toDateTimeString()

        ])->setStatusCode(201);
    }
}
