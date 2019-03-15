<?php

namespace App\Admin\Extensions\Export;

use Maatwebsite\Excel;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class UserExcelExport extends AbstractExporter
{

    public function export()
    {
        $gridData = $this->getData();

        $data = [];

        //导出表头
        $data[] = ['ID', '用户名', '邮箱', '手机号', '注册时间'];

        foreach ($gridData as $v)
        {
            $data[] = [$v['id'], $v['name'], $v['email'], $v['tel'], $v['created_at']];
        }

        // 导出excel
        \Excel::create('用户数据', function($excel) use ($data) {

            $excel->sheet('用户', function($sheet) use($data) {

                $sheet->rows($data);

            });

        })->download('xls');
    }

}

