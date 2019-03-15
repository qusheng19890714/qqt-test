<?php

namespace App\Admin\Extensions\Tools;

use App\Models\User;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Carbon\Carbon;

class UsersHeader extends AbstractTool
{
    public function render()
    {
        //今日注册人数
        $new_user_register_count = User::where('created_at', '>', Carbon::today())->count();

        return view('admin.tools.headers._user', compact('new_user_register_count'));
    }
}