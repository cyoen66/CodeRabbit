<?php

/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare(strict_types=1);

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\validate\CarCateCheck;
use app\oa\controller\Approve;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Attendance extends BaseController
{
    public function attendance_search()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
            $y = date('Y');
            $m = date('m');
            if (isset($param['report_year'])) {
                $y = $param['report_year'];
            }


            if (isset($param['report_month'])) {
                $m = $param['report_month'];
            }

            $map = [];
            if (isset($param['department_id']) && !empty($param['department_id'])) {
                $map[] = ['a.did', '=', $param['department_id']];
            }
            if (isset($param['projectCode']) && !empty($param['projectCode'])) {
                $map[] = ['f.projectCode', '=', $param['projectCode']];
            }
            $ApproveWhere = 'a.id = f.admin_id AND f.type = 26 AND f.check_status <> 5 AND f.report_year=' . $y . '  and f.report_month = ' . $m;
            if (isset($param['check_status']) && $param['check_status'] !== "") {
                if ($param['check_status'] == -1) {
                    $map[] = ['f.check_status', '=', null];
                } else {
                    $map[] = ['f.check_status', '=', $param['check_status']];
                }
            }
            $list = Db::name('Admin')
                ->field('a.name
            , a.id
            , d.title as department_name
            , f.content
            , f.create_time
            , f.projectCode
            , f.check_status 
            ,f.last_admin_id
            , f.work_hours_total
            , f.work_day_total
            , f.extra_working_hours 
             ,a.annual_leave
            ')
                ->alias('a')
                ->join('Approve f', $ApproveWhere, 'left')
                ->join('Department d', 'd.id = a.did', 'left')
                ->where($map)
                ->order('a.id asc')
                ->paginate(['list_rows' => $rows, 'query' => $param])
                ->each(function ($item, $key) {
                    if(isset($item['create_time'] )){
                        $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
                    }
                    $item[' '] = '-';
                    if ($item['check_status'] != 0 && !empty($item['last_admin_id'])) {
                        $check_user = Db::name('Admin')->where('id', 'in', $item['last_admin_id'])->column('name');
                        $item['check_user'] = implode(',', $check_user);
                    }
                    return $item;
                });
            return table_assign(0, '', $list);
        } else {
            $details = [];
            $details = Approve::initSelect($detail);
            View::assign('detail', $detail);
            return view();
        }
    }
}
