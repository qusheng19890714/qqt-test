<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request, Topic $topic)
    {
        $topics = $topic->withOrder($request->order)->paginate(20);

        return view('topics.index', compact('topics'));
    }

    /**
     * 创建帖子
     * @param Request $request
     */
    public function create(Topic $topic)
    {
        $categories = Category::all();

        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();

        $topic->save();

        return redirect()->route('topics.show', $topic->id)->with('success', '帖子创建成功');
    }


    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

    /**
     * 编辑器上传图片
     * @param Request            $request
     * @param ImageUploadHandler $imageUploadHandler
     */
    public function uploadImage(Request $request, ImageUploadHandler $imageUploadHandler)
    {
        $data = [

            'success'=>false,
            'msg' => '上传失败',
            'file_path' => '',
        ];

        if ($request->upload_file) {

            $result = $imageUploadHandler->save($request->upload_file, 'topics', Auth::id(), '1024');

            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }

        return $data;
    }
}
