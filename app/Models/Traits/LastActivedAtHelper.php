<?php
namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    //缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';


    //将时间写入redis 的hash表中
    public function recordLastActivedAt()
    {
        //获取今天的日期
        $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        $hash = $this->hash_prefix . $date;

        //字段名称
        $filed = $this->field_prefix . $this->id;

        //当前的值
        $now = Carbon::now()->toDateTimeString();

        Redis::hSet($hash, $filed, $now);
    }


    //将redis存的最后活跃时间同步到数据库中
    public function syncUserActivedAt()
    {
        //获取昨天的数据
        $yesterday = Carbon::yesterday()->toDateString();

        $hash = $this->hash_prefix.$yesterday;

        $data = Redis::hGetAll($hash);

        foreach($data as $user_id=>$actived_at)
        {
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // 只有当用户存在时才更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hash);
    }
}