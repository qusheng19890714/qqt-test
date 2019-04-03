<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = $this->user()->notifications()->paginate(20);

        return $this->response->paginator($notifications, new NotificationTransformer());
    }
}
