<?php

namespace App\Admin\Controllers;

use App\Models\Reply;
use App\Http\Controllers\Controller;
use App\Models\Topic;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;

class RepliesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function replies(Content $content, $id)
    {


        $topic = Topic::find($id);

        return $content
            ->header('评论列表')
            ->description($topic->title)
            ->body($this->grid($id));

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
    protected function grid($id)
    {
        $grid = new Grid(new Reply);

        //查询当前文章的评论
        $grid->model()->where('topic_id', $id);

        $grid->id('Id');
        $grid->column('user.name', '评论用户');
        $grid->column('content', '评论内容')->editable('textarea');
        $grid->column('created_at', '评论时间');

        $grid->actions(function($actions) {

            $actions->disableEdit();
            $actions->disableView();
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
        $show = new Show(Reply::findOrFail($id));

        $show->id('Id');
        $show->topic_id('Topic id');
        $show->user_id('User id');
        $show->content('Content');
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
        $form = new Form(new Reply);

        $form->number('topic_id', 'Topic id');
        $form->number('user_id', 'User id');
        $form->textarea('content', 'Content');

        return $form;
    }
}
