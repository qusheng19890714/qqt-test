<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class CategoriesController extends Controller
{
    //获取分类
    public function index(Category $category)
    {
        return $this->response->collection($category->orderBy('sort', 'DESC')->get(), new CategoryTransformer());
    }
}
