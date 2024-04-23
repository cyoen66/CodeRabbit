<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\adm\controller;

use app\base\BaseController;
use app\adm\validate\CarCateCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Car extends BaseController
{
	//車両の種類
    public function car_cate()
    {
        if (request()->isAjax()) {
            $cate = Db::name('CarCate')->order('create_time asc')->select();
            return to_assign(0, '', $cate);
        } else {
            return view();
        }
    }
    //車両の種類の追加
    public function car_cate_add()
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