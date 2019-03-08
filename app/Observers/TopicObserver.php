<?php

namespace App\Observers;

use App\Models\Topic;

class TopicObserver
{
    public function saving(Topic $topic)
    {
        $topic->excerpt = make_excerpt($topic->body);

        //防止xss攻击
        $topic->body = clean($topic->body, 'user_topic_body');
    }
}
