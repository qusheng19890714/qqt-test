<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class Simditor extends Field
{
    protected $view = 'admin.simditor';

    protected static $css = [

        '/css/simditor.css'

    ];

    protected static $js = [

        '/js/module.js',
        '/js/hotkeys.js',
        '/js/uploader.js',
        '/js/simditor.js'

    ];

    public function render()
    {
        $token = csrf_token();
        $url   = route('topics.upload_image');
        $this->script = <<<EOT
        var editor = new Simditor({

            textarea:$('#simditor{$this->id}'),
            upload:{

                url: '{$url}',
                params:{

                    _token:'{$token}',
                },
                fileKey: 'upload_file',
                connectionCount: 3,
                leaveConfirm: '文件上传中，关闭此页面将取消上传。'
            },

            pasteImage: true,


        })


EOT;
        return parent::render();

    }
}