<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class ImagesController extends Controller
{
    //保存图片
    public function store(ImageRequest $request, ImageUploadHandler $imageUploadHandler, Image $image)
    {
        $user = $this->user();

        $size = $request->type == 'avatar' ? 362: 1024;

        $result = $imageUploadHandler->save($request->image, str_plural($request->type), $user->id, str_plural($request->type), $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }
}
