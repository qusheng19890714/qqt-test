<?php

namespace App\Admin\Extensions\Export;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel;



class TopicExcelExport extends AbstractExporter
{
    public function export()
    {

        $gridData = $this->getData();

        $data = [];
        $data[] = ['话题ID', '话题标题', '分类名称', '话题作者', '内容', '回复数', '发布时间'];

        foreach ($gridData as $k=>$v)
        {
            $data[] = [

                $v['id'],
                $v['title'],
                $v['category']['name'],
                $v['user']['name'],
                $v['body'],
                $v['reply_count'],
                $v['created_at']

            ];
        }


        // 导出excel
        \Excel::create('话题数据', function($excel) use ($data) {

            $excel->sheet('话题数据', function($sheet) use($data) {

                $sheet->rows($data);

            });

        })->download('xls');
    }
}