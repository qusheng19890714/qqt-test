<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'uploadImage']]);
    }

    public function index(Request $request, Topic $topic, User $user)
    {
        $topics = $topic->withOrder($request->order)->paginate(20);

        $active_users = $user->getActiveUsers();

        return view('topics.index', compact('topics', 'active_users'));
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

        return redirect()->to($topic->link())->with('success', '帖子创建成功');
    }


    public function show(Topic $topic, Request $request)
    {
        if (!empty($topic->slug) && $topic->slug != $request->slug) {

            return redirect($topic->link(), 301);
        }

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

        $wang_data = [

            'errno' => 0,
            'data'  => [],

        ];

        //Simditor编辑器
        if ($request->hasFile('upload_file')) {

            if ($request->upload_file) {

                $result = $imageUploadHandler->save($request->upload_file, 'topics', Auth::id(), '1024');

                // 图片保存成功的话
                if ($result) {
                    $data['file_path'] = $result['path'];
                    $data['msg']       = "上传成功!";
                    $data['success']   = true;
                    $data['data'][]    = $result['path'];
                }
            }

            return $data;

        }

        //wangeditor编辑器
        if ($request->hasFile('wang_upload_file')) {

            if ($request->wang_upload_file) {

                $result = $imageUploadHandler->save($request->wang_upload_file, 'topics', Auth::id(), '1024');

                // 图片保存成功的话
                if ($result) {
                    $wang_data['errno']     = 0;
                    $wang_data['data'][]    = $result['path'];
                }
            }

            return $wang_data;

        }

    }


    /**
     * 编辑
     */
    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);

        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    /**
     * 修改
     * @param TopicRequest $request
     * @param Topic        $topic
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect()->route('topics.show', $topic->id)->with('success', '更新成功！');
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return redirect()->route('topics.index')->with('succeess', '删除成功');
    }
}
