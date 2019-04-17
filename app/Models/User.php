<?php

namespace App\Models;

use App\Models\Traits\ActiveUserHelper;
use App\Models\Traits\LastActivedAtHelper;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    //use MustVerifyEmailTrait;

    use Notifiable {

        notify as protected laravelNotify;
    }

    use HasRoles, ActiveUserHelper, LastActivedAtHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar', 'tel', 'weixin_unionid', 'weixin_openid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id === $model->user_id;
    }


    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    //发送通知
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {

            return ;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {

            $this->increment('notification_count');

        }

        $this->laravelNotify($instance);
    }


    //清空未读消息
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();

        $this->unreadNotifications->markAsRead();
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }


}
