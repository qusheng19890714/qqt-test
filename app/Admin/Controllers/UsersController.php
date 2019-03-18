<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Export\UserExcelExport;
use App\Admin\Extensions\Tools\UsersHeader;
use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
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
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {

        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
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
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
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

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->email_verified_at('Email verified at');
        $show->password('Password');
        $show->remember_token('Remember token');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->avatar('Avatar');
        $show->introduction('Introduction');
        $show->notification_count('Notification count');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', '用户名')->rules('required|string|max:255');
        $form->email('email', 'Email')->rules('required|email|string|max:255|unique:users');
        $form->mobile('tel', '手机号')->rules('required|numeric|unique:users')->options(['mask' => '999 9999 9999']);
        $form->password('password', '密码')->rules('required');
        $form->image('avatar', '头像')->rules('mimes:jpeg,bmp,png,gif|dimensions:min_width=208,min_height=208')->help('头像必须是 jpeg, bmp, png, gif 格式的图片');
        $form->simditor('introduction', '简介');


        //底部
        $form->footer(function($footer){

            //去掉"查看"checkbox
            $footer->disableViewCheck();
            //去掉"继续编辑"checkbox
            $footer->disableEditingCheck();
            //去掉"继续创建"checkbox
            $footer->disableCreatingCheck();

        });


        return $form;
    }
}
