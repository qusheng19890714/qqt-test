<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
//use Laravel\Socialite;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {

        //目前支持的第三方
        if (!in_array($type, ['weixin'])) {

            return $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try {

            if ($code = $request->code) {

                $response = $driver->getAccessTokenResponse($code);

                $token = array_get($response, 'access_token');

            }else {

                $token = $request->access_token;

                if ($type == 'weixin') {

                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);

        }catch(\Exception $e) {

            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch($type)
        {
            case 'weixin' :

                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::find('weixin_unionid', $unionid)->first();
                }else {
                    $user = User::find('weixin_openid', $oauthUser->getId())->first();
                }

                //没有则创建
                if (!$user) {

                    $user = User::create([

                        'name'   => $oauthUser->getNickName(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid

                    ]);
                }

                break;
        }

        return $this->response->array(['token' => $user->id]);
    }
}
