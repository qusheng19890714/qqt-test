<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    public function index(Topic $topic)
    {
        $topics = $topic->paginate(20);
        return view('topics.index', compact('topics'));
    }
}
