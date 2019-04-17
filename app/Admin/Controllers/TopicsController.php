<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Export\TopicExcelExport;
use App\Admin\Extensions\Tools\TopicCategory;
use App\Models\Category;
use App\Models\Topic;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Request;

class TopicsController extends Controller
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
            ->header('话题列表')
            ->description('话题列表')
            ->breadcrumb(['text' => '话题管理'])
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
        $topic = Topic::find($id);

        return $content
            ->header('话题详情')
            ->description($topic->title)
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
            ->header('Edit')
            ->description('description')
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
        $grid = new Grid(new Topic);

        $grid->id('Id')->sortable();
        $grid->column('title', '话题名称')->editable('textarea');
        $grid->column('user.name', '用户名');
        $grid->column('category.name', '分类名称');
        $grid->order('排序');
        $grid->created_at('创建时间')->sortable();
        $grid->column('content', '内容');

        //时间筛选
        $grid->filter(function($filter)  {

            //创建时间查询
            $filter->between('creatd_at', '创建时间')->datetime();

            //话题分类查询
            $categories = Category::all()->pluck('name', 'id');
            $filter->equal('category_id',  '话题分类')->select($categories);


        });

        //关闭导出
        $grid->disableExport();

        //添加文章评论的按钮
        $grid->actions(function($actions) {

            $id = $actions->getKey();

            $actions->append('<a href="'.route('topic.reply', $id).'"><i class="fa fa-comment"></i></a>');
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
        $show = new Show(Topic::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->body('Body');
        $show->user_id('User id');
        $show->category_id('Category id');
        $show->reply_count('Reply count');
        $show->view_count('View count');
        $show->last_reply_user_id('Last reply user id');
        $show->order('Order');
        $show->excerpt('Excerpt');
        $show->slug('Slug');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Topic);

        $form->text('title', '话题标题');
        $form->simditor('body', '话题内容');
        $form->number('user_id', '作者')->disable();
        //$form->number('category_id', '所属分类');

        //获取所有分类
        $categories = Category::all();

        $options = [];

        foreach ($categories as $category)
        {
            $options[$category->id] = $category->name;
        }


        $form->select('category_id', '话题分类')->options($options);

        $form->number('reply_count', '回复数量');
        $form->number('view_count', '浏览数量');
        $form->number('last_reply_user_id', '最后回复人')->disable();
        $form->number('order', '排序');
        $form->textarea('excerpt', '摘要');
        //$form->text('slug', 'Slug');

        return $form;
    }
}
