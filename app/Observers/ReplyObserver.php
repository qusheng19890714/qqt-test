<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

class ReplyObserver
{

    public function creating(Reply $reply)
    {
        //防止xss攻击
        $reply->content = clean($reply->content, 'user_topic_body');
    }


    public function created(Reply $reply)
    {

        $reply->topic->updateReplyCount();

        //通知话题作者有新的评论
        $reply->topic->user->notify(new TopicReplied($reply));
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
        $reply->topic->save();
    }
}
