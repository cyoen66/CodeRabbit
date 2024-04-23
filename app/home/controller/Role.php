<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare (strict_types = 1);

namespace app\home\controller;

use app\base\BaseController;
use app\home\model\AdminGroup;
use app\home\validate\GroupCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Role extends BaseController
{
    public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            if (!empty($param['keywords'])) {
                $where[] = ['id|title|desc', 'like', '%' . $param['keywords'] . '%'];
            }
			$list = Db::name('AdminGroup')->where($where)->order('create_time asc')->select();
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加&编辑
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $ruleData = isset($param['rule']) ? $param['rule'] : 0;
            $layoutData = isset($param['layout']) ? $param['layout'] : 0;
			if($ruleData==0){
				return to_assign(1, '権限は少なくとも1つ選択してください');
			}
			if($layoutData==0){
				return to_assign(1, '表示モジュールは少なくとも1つ選択してください');
			}
            $param['rules'] = implode(',', $ruleData);
            $param['layouts'] = implode(',', $layoutData);
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(GroupCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                //为了系统安全id为1的系统所有者管理组不允许修改
                if ($param['id'] == 1) {
                    return to_assign(1, 'システムの安定性を考慮するため、スーパーロールの修正が不可です');
                }
                Db::name('AdminGroup')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(GroupCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $gid = Db::name('AdminGroup')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $gid, $param);
            }
            //清除菜单\权限缓存
            clear_cache('adminMenu');
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $rule = admin_rule();
			$layouts = get_config('layout');
            if ($id > 0) {
                $rules = admin_group_info($id);
                $role_rule = create_tree_list(0, $rule, $rules);
                $role = Db::name('AdminGroup')->where(['id' => $id])->find();				

				$layout_selected = explode(',', $role['layouts']);
				foreach ($layouts as $key =>&$vo) {
					if (!empty($layout_selected) and in_array($vo['id'], $layout_selected)) {
						$vo['checked'] = true;
					} else {
						$vo['checked'] = false;
					}
				}
                View::assign('role', $role);
            } else {
                $role_rule = create_tree_list(0, $rule, []);
				foreach ($layouts as $key =>&$vo) {
					$vo['checked'] = false;
				}
            }
            View::assign('role_rule', $role_rule);			
            View::assign('layout', $layouts);
            View::assign('id', $id);
            return view();
        }
    }

    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            if ($id == 1) {
                return to_assign(1, "スーパーロールであるため、削除不可です");
            }
            $count = Db::name('PositionGroup')->where(["group_id" => $id])->count();
            if ($count > 0) {
                return to_assign(1, "このロールはまだ使用中です　削除する前に関連するユーザーを削除してください");
            }
            if (Db::name('AdminGroup')->delete($id) !== false) {
                add_log('delete', $id, []);
                return to_assign(0, "削除しました");
            } else {
                return to_assign(1, "削除失敗しました");
            }
        } else {
            return to_assign(1, "操作エラーです");
        }
    }
}
