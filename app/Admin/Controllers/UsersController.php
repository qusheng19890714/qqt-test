<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Export\UserExcelExport;
use App\Admin\Extensions\Tools\UsersHeader;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{
    use HasResourceActions;

    /**
     * 用户列表
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('用户管理')
            ->description('用户的相关信息')
            ->breadcrumb(['text' => '用户管理']) //面包屑
            ->body($this->grid());
    }

    /**
     * 用户详情
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {

        return $content
            ->header('详情')
            ->description('用户详情信息')
            ->breadcrumb(['text'=>'用户管理', 'url'=>'/admin/users'], ['text'=>'详情'])
            ->body($this->detail($id));
    }

    /**
     * 用户编辑
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {

        return $content
            ->header('编辑')
            ->description('编辑用户信息')
            ->breadcrumb(['text'=>'用户管理', 'url'=>'/admin/users'], ['text'=>'编辑'])
            ->body($this->form()->edit($id));

    }

    /**
     * 新增用户
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建新用户')
            ->description('创建一个新的用户')
            ->body($this->form());
    }

    /**
     * 列表数据处理
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        //列数据
        $grid->id('Id')->sortable();
        $grid->column('name', '用户名');
        $grid->column('email', '邮箱');
        $grid->column('tel', '手机号码');
        $grid->column('avatar', '头像')->image('', 50,50);
        $grid->column('created_at', '注册时间');


        //查询
        $grid->filter(function($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            $filter->column(1/2, function($filter) {

                $filter->equal('tel', '手机');
                $filter->between('created_at', '注册时间')->datetime();
            });

            $filter->column(1/2, function($filter) {


                $filter->like('name', '用户名');
                $filter->like('email', '邮箱');

            });

        });

        //excel导出数据
        $grid->exporter(new UserExcelExport());

        //添加今日注册人数的自定义工具
        $grid->tools(function($tools){

            $tools->append(new UsersHeader());

        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->name('用户名');
        $show->email('Email');

        $show->tel('手机号')->as(function($tel) {
            return $tel? $tel : '无';
        });

        $show->email_verified_at('邮箱验证时间');
        $show->created_at('创建时间');
        $show->updated_at('最后修改时间');
        $show->avatar('头像')->image();
        $show->introduction('简介');

        return $show;
    }

    /**
     * 添加用户的数据处理
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->tab('基本信息', function($form) {

            $form->text('name', '用户名')->rules('required|string|max:255');

            $form->email('email', 'Email')->rules(function($form)  {

                if (!$id = $form->model()->id) {

                    return 'required|email|string|max:255|unique:users,email';
                }

                return 'required|email|string|max:255|unique:users,email,'. $form->model()->id;

            });

            $form->mobile('tel', '手机号')->rules(function($form) {

                if (!$id = $form->model()->id) {

                    return 'required|string|max:255|unique:users,tel';
                }

                return 'required|string|max:255|unique:users,tel,'.$form->model()->id;

            })->options(['mask' => '999 9999 9999']);


            $form->password('password','密码');
            $form->image('avatar', '头像')->rules('mimes:jpeg,bmp,png,gif|dimensions:min_width=208,min_height=208')->help('头像必须是 jpeg, bmp, png, gif 格式的图片');
            $form->text('introduction', '简介');


            //底部
            $form->footer(function($footer){

                //去掉"查看"checkbox
                $footer->disableViewCheck();
                //去掉"继续编辑"checkbox
                $footer->disableEditingCheck();
                //去掉"继续创建"checkbox
                $footer->disableCreatingCheck();

            });


        });


        $form->saving(function(Form $form) {

            //密码加密
            if ($form->password && $form->model()->password != $form->password) {

                $form->password = bcrypt($form->password);
            }

            //如果用户没有输入密码, 则不更新密码字段
            if (!$form->password) {

                $form->password =$form->model()->password;
            }

            $form->email_verified_at = Carbon::now()->toDateTimeString();

        });



        return $form;
    }
}
