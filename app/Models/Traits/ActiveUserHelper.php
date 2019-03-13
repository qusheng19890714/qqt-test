<?php

namespace App\Models\Traits;

use App\Models\User;
use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Cache;
use DB;

trait ActiveUserHelper
{
    //用于存放临时数组的接口
    protected $users = [];

    //配置信息
    protected $topic_weight = 4; //话题权重
    protected $reply_weight = 1; //回复权重
    protected $pass_days = 7; //多少天内发布的内容
    protected $user_num = 6; //取多少用户


    // 缓存相关配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_minutes = 65;


    //计算话题分数
    private function calculateTopicScore()
    {
        // 从话题数据表里取出限定时间范围（$pass_days）内，有发表过话题的用户
        // 并且同时取出用户此段时间内发布话题的数量
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
                                     ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                                     ->groupBy('user_id')->get();

        //根据话题数量计算得分
        foreach($topic_users as $v)
        {
            $this->users[$v->user_id]['score'] = $v->topic_count * $this->topic_weight;
        }
    }


    //计算回复分数
    private function calculateReplyScore()
    {
        // 从回复数据表里取出限定时间范围（$pass_days）内，有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')->get();

        //根据回复数量计算得分
        foreach($reply_users as $v)
        {
            $reply_score = $v->reply_count * $this->reply_weight;

            if (isset($this->users[$v->user_id]['score'])) {

                $this->users[$v->user_id]['score'] += $reply_score;

            }else {

                $this->users[$v->user_id]['score'] = $reply_score;
            }


        }
    }


    //计算活跃用户
    public function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        //数组按照得分顺序倒序排序
        $users = array_sort($this->users, function($user){

            return $user['score'];
        });

        $users = array_reverse($users, true);

        // 只获取我们想要的数量
        $users = array_slice($users, 0, $this->user_num, true);

        // 新建一个空集合
        $active_users = collect();

        foreach ($users as $user_id => $user)
        {
            // 找寻下是否可以找到用户
            $user = User::find($user_id);

            // 如果数据库里有该用户的话
            if ($user) {

                // 将此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    //将数据放入缓存
    private function cacheActiveUsers($active_uesrs)
    {
        Cache::put($this->cache_key, $active_uesrs, $this->cache_expire_in_minutes);
    }


    public function cacheAndCalculateActiveUsers()
    {
        //取得活跃用户
        $active_users = $this->calculateActiveUsers();
        //缓存
        $this->cacheActiveUsers($active_users);
    }

    public function getActiveUsers()
    {
        // 尝试从缓存中取出 cache_key 对应的数据。如果能取到，便直接返回数据。
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做了缓存。

        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function(){

            return $this->calculateActiveUsers();
        });
    }

}