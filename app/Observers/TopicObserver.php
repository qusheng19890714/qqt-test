<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;

class TopicObserver
{
    public function saving(Topic $topic)
    {
        $topic->excerpt = make_excerpt($topic->body);

        //防止xss攻击
        $topic->body = clean($topic->body, 'user_topic_body');

        //slug
        if (!$topic->slug) {

            $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);

            if (trim($topic->slug) === 'edit') {
                $topic->slug = 'edit-slug';
            }
        }
    }
}
