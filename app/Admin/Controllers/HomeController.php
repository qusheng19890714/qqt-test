<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use Carbon\Carbon;

class HomeController extends Controller
{
    //数据统计
    public function index(Content $content)
    {

        return $content
            ->header('今日数据统计')
            ->description(date('Y-m-d'))
            ->row(function (Row $row) {

                //今日新增用户
                $row->column(4, function (Column $column)  {

                    //今日注册人数
                    $new_user_count = User::where('created_at', '>', Carbon::today())->count();

                    $column->append(new InfoBox('注册人数', 'users', 'aqua', 'admin/users', $new_user_count));

                });

                $row->column(4, function (Column $column) {

                    //今日话题数量
                    $new_topic_count = Topic::where('created_at', '>', Carbon::today())->count();

                    $column->append(new InfoBox('话题数量', 'copy', 'green', 'admin/topics', $new_topic_count));
                });

                $row->column(4, function (Column $column) {

                    //今日评论数量
                    $new_reply_count = Reply::where('created_at', '>', Carbon::today())->count();

                    $column->append(new InfoBox('评论数量', 'comment', 'yellow', '', $new_reply_count));
                });
            });
    }


}
