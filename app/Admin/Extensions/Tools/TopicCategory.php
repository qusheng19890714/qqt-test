<?php

namespace App\Admin\Extensions\Tools;

use App\Models\Category;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class TopicCategory extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['category_id' => '_category_']);

        return <<<EOT

$('.category-select').change(function () {

    var url = "$url".replace('_category_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        //获取所有分类
        $categories = Category::all();

        $options = [];

        foreach ($categories as $category)
        {
            $options[$category->id] = $category->name;
        }

        return view('admin.tools.category', compact('options'));
    }
}