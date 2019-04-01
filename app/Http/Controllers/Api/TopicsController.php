<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class TopicsController extends Controller
{
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;

        $topic->save();

        return $this->response->created();
    }
}
