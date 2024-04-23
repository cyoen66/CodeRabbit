<?php
/**
 * @copyright Copyright (c) YourFuture Corporation
 */

declare (strict_types = 1);

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\validate\CarCateCheck;
use app\adm\validate\PcCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class PC extends BaseController
{
	//車両の種類
    public function pc_cate()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (isset($param['keywords'])&&$param['keywords'] == 0 ||!empty($param['keywords'])) {
                $where[] = ['pc.management_number|pc.name|pc.serial_number|a.name', 'like', '%' . $param['keywords'] . '%'];
            }
            if (isset($param['status'])&&$param['status'] == 0 || !empty($param['status'])) {
                $where[] = ['pc.status', '=', $param['status']];
            }
            $cate = Db::name('PcCate')
            ->where($where)
            ->field('pc.*,a.name as user_name')
            ->alias('pc')
            ->join('Admin a', 'a.id = pc.user_id', 'left')
            ->order('create_time asc')
            ->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //車両の種類の追加
    public function add_cate()
    {
        if (request()->isAjax()) {
            $param = get_params();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CarCateCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // バリデーションエラーが発生した場合はエラーメッセージを返す
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = Db::name('CarCate')->strict(false)->field(true)->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(CarCateCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // バリデーションエラーが発生した場合はエラーメッセージを返す
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $insertId = Db::name('CarCate')->strict(false)->field(true)->insertGetId($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
    }
	
    public function add()
    {
        $param = get_params();
        $param['purchase_date'] = !empty($param['purchase_date']) ? $param['purchase_date'] : null;
        $param['warranty_end_date'] = !empty($param['warranty_end_date']) ? $param['warranty_end_date'] : null;
        $param['start_date'] = !empty($param['start_date']) ? $param['start_date'] : null;
        $param['end_date'] = !empty($param['end_date']) ? $param['end_date'] : null;

        if (request()->isAjax()) {
            if ($param['id'] > 0) {
                try {
                    validate(PcCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }

                Db::name('PcCate')->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
                return to_assign();
            } else {
                try {
                    validate(PcCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $management_number = Db::name('PcCate')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $management_number, $param);
                return to_assign();
            }
        } else {


            $id = isset($param['id']) ? $param['id'] : 0;

            if ($id > 0) {
                $detail = Db::name('PcCate')
                ->field('pc.*,a.name as user_name')
                ->alias('pc')
                ->join('Admin a', 'a.id = pc.user_id', 'left')
                ->order('create_time asc')
                ->where(['management_number' => $id])
                ->find();
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
            return view();
        }
    }
    //車両の種類の設定
    public function car_cate_check()
    {
		$param = get_params();
        $res = Db::name('CarCate')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失敗');
		}
    }   
}