<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

class TopicObserver
{
    public function saving(Topic $topic)
    {
        $topic->excerpt = make_excerpt($topic->body);

        //防止xss攻击
        $topic->body = clean($topic->body, 'user_topic_body');


    }

    public function saved(Topic $topic)
    {
        //slug
        if (!$topic->slug) {

            //推送翻译队列
            dispatch(new TranslateSlug($topic));

        }
    }
}
