<?php

/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare(strict_types=1);

namespace app\oa\controller;

use app\base\BaseController;
use think\facade\Db;
use think\facade\View;
use think\helper\Arr;

class Approve extends BaseController
{
	public function index()
	{
		if (request()->isAjax()) {
			$param = get_params();
			$where = [];
			if (isset($param['status'])) {
				if ($param['status'] == 1) {
					$where[] = ['check_status', '<', 2];
				}
				if ($param['status'] == 2) {
					$where[] = ['check_status', '=', 2];
				}
				if ($param['status'] == 3) {
					$where[]  = ['check_status', '>', 2];
					$where[]  = ['check_status', '<', 5];
				}
				if ($param['status'] == 4) {
					$where[] = ['check_status', '=', 5];
				}
			}
			$where[] = ['admin_id', '=', $this->uid];
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('Approve')
				->field('f.*,a.name,d.title as department_name,t.title as flow_type')
				->alias('f')
				->join('Admin a', 'a.id = f.admin_id', 'left')
				->join('Department d', 'd.id = f.department_id', 'left')
				->join('FlowType t', 't.id = f.type', 'left')
				->where($where)
				->order('f.id desc')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function ($item, $key) {
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					$item['check_user'] = '-';
					if ($item['check_status'] < 2 && !empty($item['check_admin_ids'])) {
						$check_user = Db::name('Admin')->where('id', 'in', $item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',', $check_user);
					}
					return $item;
				});
			return table_assign(0, '', $list);
		} else {
			$uid = $this->uid;
			$department = $this->did;
			// 超级用户可以查看所有的审批流程
			if ($uid == 1) {
				$list = Db::name('FlowType')->where(['status' => 1])->select()->toArray();
			} else {
				//基于部门显示审批流程
				$map1 = [];
				$map2 = [];
				$map1[] = ['status', '=', 1];
				$map1[] = ['department_ids', '=', ''];

				//(status = 1) AND (FIND_IN_SET('{$department}', department_ids))
				$map2[] = ['status', '=', 1];
				$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")];

				$list = Db::name('FlowType')->whereOr([$map1, $map2])->select()->toArray();
			}
			View::assign('list', $list);
			View::assign('type', get_config('approve.type'));
			return view();
		}
	}

	public function list()
	{
		if (request()->isAjax()) {
			$param = get_params();
			$status = isset($param['status']) ? $param['status'] : 0;
			$user_id = $this->uid;
			//查询条件
			$map1 = [];
			$map2 = [];
			$map1[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',f.check_admin_ids)")];
			$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',f.flow_admin_ids)")];

			if (!empty($param['type'])) {
				$map1[] = ['f.type', '=', $param['type']];
				$map2[] = ['f.type', '=', $param['type']];
			}
			if (!empty($param['uid'])) {
				$map1[] = ['f.admin_id', '=', $param['uid']];
				$map2[] = ['f.admin_id', '=', $param['uid']];
			}
			//按时间检索
			$start_time = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
			$end_time = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
			if ($start_time > 0 && $end_time > 0) {
				$map1[] = ['f.create_time', 'between', [$start_time, $end_time]];
				$map2[] = ['f.create_time', 'between', [$start_time, $end_time]];
			}

			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];

			if ($status == 0) {
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->whereOr([$map1, $map2])
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function ($item, $key) {
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if ($item['check_status'] < 2 && !empty($item['check_admin_ids'])) {
							$check_user = Db::name('Admin')->where('id', 'in', $item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',', $check_user);
						}
						return $item;
					});
			}

			if ($status == 1) {
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->where($map1)
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function ($item, $key) {
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if ($item['check_status'] < 2 && !empty($item['check_admin_ids'])) {
							$check_user = Db::name('Admin')->where('id', 'in', $item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',', $check_user);
						}
						return $item;
					});
			}
			if ($status == 2) {
				$list = Db::name('Approve')
					->field('f.*,a.name,d.title as department_name,t.title as flow_type')
					->alias('f')
					->join('Admin a', 'a.id = f.admin_id', 'left')
					->join('Department d', 'd.id = f.department_id', 'left')
					->join('FlowType t', 't.id = f.type', 'left')
					->where($map2)
					->order('f.id desc')
					->group('f.id')
					->paginate(['list_rows' => $rows, 'query' => $param])
					->each(function ($item, $key) {
						$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
						$item['check_user'] = '-';
						if ($item['check_status'] < 2 && !empty($item['check_admin_ids'])) {
							$check_user = Db::name('Admin')->where('id', 'in', $item['check_admin_ids'])->column('name');
							$item['check_user'] = implode(',', $check_user);
						}
						return $item;
					});
			}
			return table_assign(0, '', $list);
		} else {
			$type = Db::name('FlowType')->whereOr('status', 1)->select()->toArray();
			View::assign('type', $type);
			return view();
		}
	}

	public function copy()
	{
		if (request()->isAjax()) {
			$param = get_params();
			$user_id = $this->uid;
			//查询条件
			$map = [];
			$map[] = ['f.check_status', '=', 2];
			$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$user_id}',f.copy_uids)")];
			if (!empty($param['type'])) {
				$map[] = ['f.type', '=', $param['type']];
			}
			if (!empty($param['uid'])) {
				$map[] = ['f.admin_id', '=', $param['uid']];
			}
			//按时间检索
			$start_time = isset($param['start_time']) ? strtotime($param['start_time']) : 0;
			$end_time = isset($param['end_time']) ? strtotime($param['end_time']) : 0;
			if ($start_time > 0 && $end_time > 0) {
				$map[] = ['f.create_time', 'between', [$start_time, $end_time]];
			}

			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('Approve')
				->field('f.*,a.name,d.title as department_name,t.title as flow_type')
				->alias('f')
				->join('Admin a', 'a.id = f.admin_id', 'left')
				->join('Department d', 'd.id = f.department_id', 'left')
				->join('FlowType t', 't.id = f.type', 'left')
				->where($map)
				->order('f.id desc')
				->group('f.id')
				->paginate(['list_rows' => $rows, 'query' => $param])
				->each(function ($item, $key) {
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					$item['check_user'] = '-';
					if ($item['check_status'] < 2 && !empty($item['check_admin_ids'])) {
						$check_user = Db::name('Admin')->where('id', 'in', $item['check_admin_ids'])->column('name');
						$item['check_user'] = implode(',', $check_user);
					}
					return $item;
				});
			return table_assign(0, '', $list);
		} else {
			$type = Db::name('FlowType')->whereOr('status', 1)->select()->toArray();
			View::assign('type', $type);
			return view();
		}
	}

	//添加新增/编辑
	public function add()
	{
		$param = get_params();
		if (request()->isAjax()) {
			if (isset($param['report_month']) && isset($param['report_year'])) {
				$countWhere = [];
				$countWhere[] = ['report_year', '=',  $param['report_year']];
				$countWhere[] = ['report_month', '=',  $param['report_month']];
				$countWhere[] = ['admin_id', '=',  $this->uid];
				$countWhere[] = ['check_status', '<>',  3];
				$countWhere[] = ['check_status', '<>',  4];
				$countWhere[] = ['check_status', '<>',  5];
				$count = Db::name('Approve')->where($countWhere)->count();

				if ($count > 0) {
					return to_assign(1, '該当報告年月の勤怠報告は存在ありました。');
				}
			}
			if (isset($param['detail_time'])) {
				$param['detail_time'] = strtotime($param['detail_time']);
			}
			if (isset($param['start_time'])) {
				$param['start_time'] = strtotime($param['start_time']);
			}
			if (isset($param['end_time'])) {
				$param['end_time'] = strtotime($param['end_time']);
				if ($param['end_time'] < $param['start_time']) {
					return to_assign(1, "時間の選択が誤っています");
				}
			}
			if (isset($param['start_time_a'])) {
				$param['start_time'] = strtotime($param['start_time_a'] . '' . $param['start_time_b']);
			}
			if (isset($param['end_time_a'])) {
				$param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "終了時刻は開始時刻よりも遅い必要があります");
				}
			}

			$flow_list = Db::name('Flow')->where('id', $param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
			if ($param['id'] > 0) {
				$param['update_time'] = time();
				$param['check_status'] = 0;
				$param['check_step_sort'] = 0;
				//删除原来的审核流程和审核记录
				$delFlowRecordCount = Db::name('FlowStep')->where(['action_id' => $param['id'], 'type' => 1, 'delete_time' => 0])->update(['delete_time' => time()]);
				Db::name('FlowRecord')->where(['action_id' => $param['id'], 'type' => 1, 'delete_time' => 0])->update(['delete_time' => time()]);

				if (!isset($param['check_admin_ids'])) {
					if ($flow[0]['flow_type'] == 1) {
						//部门负责人
						$leader = get_department_leader($this->uid);
						if ($leader == 0) {
							return to_assign(1, '承認フローに問題があります：所属部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else if ($flow[0]['flow_type'] == 2) {
						//上级部门负责人
						$leader = get_department_leader($this->uid, 1);
						if ($leader == 0) {
							return to_assign(1, '承認フローに問題があります：上位部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else {
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
					Db::name('Approve')->strict(false)->field(true)->update($param);
					foreach ($flow as $key => &$value) {
						$value['action_id'] = $param['id'];
						$value['sort'] = $key;
						$value['create_time'] = time();
					}
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				} else {
					Db::name('Approve')->strict(false)->field(true)->update($param);
					$flow_step = array(
						'action_id' => $param['id'],
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				//添加申請する记录
				$content ="再申請";
				if($delFlowRecordCount==0){
					$content="申請する";
				}
				$checkData = array(
					'action_id' => $param['id'],
					'check_user_id' => $this->uid,
					'content' => $content,
					'check_time' => time(),
					'create_time' => time()
				);
				$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('edit', $param['id'], $param);
				//发送消息通知
				$msg = [
					'from_uid' => $this->uid,
					'title' => Db::name('FlowType')->where('id', $param['type'])->value('title'),
					'action_id' => $param['id']
				];
				$users = $param['check_admin_ids'];
				sendMessage($users, 21, $msg);
			} else {
				$param['admin_id'] = $this->uid;
				$param['department_id'] = $this->did;
				$param['create_time'] = time();
				$isUpdate = false;
				if (isset($param['report_month']) && isset($param['report_year'])) {
					$countWhere = [];
					$countWhere[] = ['report_year', '=',  $param['report_year']];
					$countWhere[] = ['report_month', '=',  $param['report_month']];
					$countWhere[] = ['admin_id', '=',  $this->uid];
					$approveData = Db::name('Approve')->where($countWhere)->select();
					if (count($approveData)>0) {
						$param['check_status'] = 0;
						$param['id'] = $approveData[0]["id"];
						$isUpdate = true;
					} 
					}
				if (!isset($param['check_admin_ids'])) {
					if ($flow[0]['flow_type'] == 1) {
						//部门负责人
						$leader = get_department_leader($this->uid);
						if ($leader == 0) {
							return to_assign(1, '所属部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else if ($flow[0]['flow_type'] == 2) {
						//上级部门负责人
						$leader = get_department_leader($this->uid, 1);
						if ($leader == 0) {
							return to_assign(1, '上位部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else {
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}

					if ($isUpdate) {
						$aid = Db::name('Approve')->strict(false)->field(true)->update($param);
					} else {
						$aid = Db::name('Approve')->strict(false)->field(true)->insertGetId($param);
					}

					foreach ($flow as $key => &$value) {
						$value['action_id'] = $aid;
						$value['sort'] = $key;
						$value['create_time'] = time();
					}
					$res = Db::name('FlowStep')->strict(false)->field(true)->insertAll($flow);
				} else {
					if ($isUpdate) {
						$aid = Db::name('Approve')->strict(false)->field(true)->update($param);
					} else {
						$aid = Db::name('Approve')->strict(false)->field(true)->insertGetId($param);
					}
					$flow_step = array(
						'action_id' => $aid,
						'flow_uids' => $param['check_admin_ids'],
						'create_time' => time()
					);
					$step_id = Db::name('FlowStep')->strict(false)->field(true)->insertGetId($flow_step);
				}
				//添加申請する记录
				$checkData = array(
					'action_id' => $aid,
					'check_user_id' => $this->uid,
					'content' => '申請する',
					'check_time' => time(),
					'create_time' => time()
				);
				$record_id = Db::name('FlowRecord')->strict(false)->field(true)->insertGetId($checkData);
				add_log('add', $aid, $param);
				//给审核人发送消息通知
				$msg = [
					'from_uid' => $this->uid,
					'title' => Db::name('FlowType')->where('id', $param['type'])->value('title'),
					'action_id' => $aid
				];
				$users = $param['check_admin_ids'];
				sendMessage($users, 21, $msg);
			}

			if (isset($param['annual_leave'])) {
				$userInfo=[];
				$userInfo['id']= $this->uid;
				$userInfo['annual_leave'] = $this->annual_leave-$param['duration'];
				$aid = Db::name('Admin')->strict(false)->field(true)->update($userInfo);
			}
			return to_assign();
		} else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$type = isset($param['type']) ? $param['type'] : 0;
			
			// 編集
			if ($id > 0) {
				$detail = Db::name('Approve')->where('id', $id)->find();
				$detail['start_time_a'] = date('Y-m-d', $detail['start_time']);
				$detail['start_time_b'] = date('H:i', $detail['start_time']);
				$detail['end_time_a'] = date('Y-m-d', $detail['end_time']);
				$detail['end_time_b'] = date('H:i', $detail['end_time']);
				$detail['detail_time'] = date('Y-m-d', $detail['detail_time']);
				$detail['annual_leave'] =$this->annual_leave;
				$type = $detail['type'];
				if ($detail['file_ids'] != '') {
					$fileArray = Db::name('File')->where('id', 'in', $detail['file_ids'])->select();
					$detail['fileArray'] = $fileArray;
				}
				$detail = $this->initSelect($detail);
				// $detail = $this ->tableDataInit($detail);
				$detail["count"] = 0;
				View::assign('detail', $detail);
			} else {

				$countWhere = [];
				$countWhere[] = ['report_year', '=',  date('Y')];
				$countWhere[] = ['report_month', '=',  date('m')];
				$countWhere[] = ['admin_id', '=',  $this->uid];
				// $countWhere[] =['check_status', '<>',  3];
				// $countWhere[] =['check_status', '<>',  4];
				// $countWhere[] =['check_status', '<>',  5];
				$count = Db::name('Approve')->where($countWhere)->count();
				$detail = [];
				$detail = $this->initSelect($detail);
				$detail = $this->tableDataInit($detail);
				$detail["count"] = $count;
				$detail['annual_leave'] =$this->annual_leave;
				View::assign('detail', $detail);
			}

			$department = $this->did;
			//获取审批流程
			// 获取流程列表
			$flows = get_cate_department_flows($type, $department);
			// 获取flowtype名称，用下面的3行代码，拼接模板路径
			$moban = Db::name('FlowType')->where('id', $type)->value('name');
			$module = strtolower(app('http')->getName());
			$class = strtolower(app('request')->controller());

			$action = strtolower(app('request')->action());
			// $template = $module . '/view/'. $class .'/add_'.$moban.'.html';
			$template = "$module/view/$class/add_$moban.html";
			View::assign([
				'flows' => $flows,
				'id' => $id,
				'type' => $type,
				'admin_info' => get_admin($this->uid)
			]);
			if (isTemplate($template)) {
				return view('add_' . $moban);
			} else {
				return view('../../base/view/common/errortemplate', ['file' => $template]);
			}
		}
	}

	//查看
	public function view()
	{
		$param = get_params();
		$detail = Db::name('Approve')->where('id', $param['id'])->find();
		$detail = $this->initSelect($detail);
		$check_record = [];
		if ($detail['start_time'] > 0) {
			$detail['start_time'] = date('Y-m-d', $detail['start_time']);
		}
		if ($detail['end_time'] > 0) {
			$detail['end_time'] = date('Y-m-d', $detail['end_time']);
		}
		if ($detail['detail_time'] > 0) {
			$detail['detail_time'] = date('Y-m-d', $detail['detail_time']);
		}
		$detail['create_user'] = Db::name('Admin')->where('id', $detail['admin_id'])->value('name');
		$flows = Db::name('FlowStep')->where(['action_id' => $detail['id'], 'type' => 1, 'sort' => $detail['check_step_sort'], 'delete_time' => 0])->find();
		$detail['check_user'] = '-';
		$detail['copy_user'] = '-';
		$check_user_ids = [];
		/**
		 * 0: 待审核
		 * 1: 审核中
		 * 2: 审核通过
		 * 3: 审核不通过
		 * 4: 撤销审核
		 */
		if ($detail['check_status'] < 2) {
			if ($flows['flow_type'] == 1) {
				$detail['check_user'] = '部门负责人';
				$check_user_ids = explode(',', $detail['check_admin_ids']);
			} else if ($flows['flow_type'] == 2) {
				$detail['check_user'] = '上级部门负责人';
				$check_user_ids = explode(',', $detail['check_admin_ids']);
			} else {
				$check_user_ids = explode(',', $flows['flow_uids']);
				$check_user = Db::name('Admin')->where('id', 'in', $flows['flow_uids'])->column('name');
				$detail['check_user'] = implode(',', $check_user);
			}
		}
		if ($detail['copy_uids'] != '') {
			$copy_user = Db::name('Admin')->where('id', 'in', $detail['copy_uids'])->column('name');
			$detail['copy_user'] = implode(',', $copy_user);
		}
		if ($detail['file_ids'] != '') {
			$fileArray = Db::name('File')->where('id', 'in', $detail['file_ids'])->select();
			$detail['fileArray'] = $fileArray;
		}

		$is_check_admin = 0;
		$is_create_admin = 0;
		if ($detail['admin_id'] == $this->uid) {
			$is_create_admin = 1;
		}
		if (in_array($this->uid, $check_user_ids)) {
			$is_check_admin = 1;
			//当前审核节点详情
			$step = Db::name('FlowStep')->where(['action_id' => $detail['id'], 'type' => 1, 'sort' => $detail['check_step_sort'], 'delete_time' => 0])->find();
			if ($step['flow_type'] == 4) {
				$check_count = Db::name('FlowRecord')->where(['action_id' => $detail['id'], 'type' => 1, 'step_id' => $step['id'], 'check_user_id' => $this->uid])->count();
				if ($check_count > 0) {
					$is_check_admin = 0;
				}
			}
		}
		$check_record = Db::name('FlowRecord')->field('f.*,a.name,a.thumb')
			->alias('f')
			->join('Admin a', 'a.id = f.check_user_id', 'left')
			->where(['f.action_id' => $detail['id'], 'f.type' => 1])
			->order('check_time desc')
			->select()->toArray();
		foreach ($check_record as $kk => &$vv) {
			$vv['check_time_str'] = date('Y-m-d H:i', $vv['check_time']);
			$vv['status_str'] = '提交';
			if ($vv['status'] == 1) {
				$vv['status_str'] = '审核通过';
			} else if ($vv['status'] == 2) {
				$vv['status_str'] = '审核拒绝';
			}
			if ($vv['status'] == 3) {
				$vv['status_str'] = '撤销';
			}
		}
		$moban = Db::name('FlowType')->where('id', $detail['type'])->value('name');
		$module = strtolower(app('http')->getName());
		$class = strtolower(app('request')->controller());
		$action = strtolower(app('request')->action());
		$template = $module . '/view/' . $class . '/view_' . $moban . '.html';
		//var_dump($detail['fileArray']);
		View::assign('is_create_admin', $is_create_admin);
		View::assign('is_check_admin', $is_check_admin);
		View::assign('check_record', $check_record);
		View::assign('detail', $detail);
		View::assign('flows', $flows);
		View::assign('admin_info',  get_admin($this->uid));
		$id = isset($param['id']) ? $param['id'] : 0;
		$type = isset($param['type']) ? $param['type'] : 0;
		View::assign('id',  $id);
		View::assign('type', $type);
		if (isTemplate($template)) {
			return view('view_' . $moban);
		} else {
			return view('../../base/view/common/errortemplate', ['file' => $template]);
		}
	}

	//一時保存
	public function temporarySave()
	{
		$param = get_params();

		if (request()->isAjax()) {
			$param['admin_id'] = $this->uid;
			$param['department_id'] = $this->did;
			$param['create_time'] = time();
			$flow_list = Db::name('Flow')->where('id', $param['flow_id'])->value('flow_list');
			$flow = unserialize($flow_list);
			$param['check_status'] = 5;



			if ($param['id'] > 0) {
				$param['update_time'] = time();
				$param['check_step_sort'] = 0;
				if (!isset($param['check_admin_ids'])) {
					if ($flow[0]['flow_type'] == 1) {
						//部门负责人
						$leader = get_department_leader($this->uid);
						if ($leader == 0) {
							return to_assign(1, '承認フローに問題があります：所属部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else if ($flow[0]['flow_type'] == 2) {
						//上级部门负责人
						$leader = get_department_leader($this->uid, 1);
						if ($leader == 0) {
							return to_assign(1, '承認フローに問題があります：上位部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else {
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
					Db::name('Approve')->strict(false)->field(true)->update($param);
				} else {
					Db::name('Approve')->strict(false)->field(true)->update($param);
				}
			} else {
				$param['admin_id'] = $this->uid;
				$param['department_id'] = $this->did;
				$param['create_time'] = time();

				if (!isset($param['check_admin_ids'])) {
					if ($flow[0]['flow_type'] == 1) {
						//部门负责人
						$leader = get_department_leader($this->uid);
						if ($leader == 0) {
							return to_assign(1, '現在の部門が設定されていません。人事または管理者に連絡してください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else if ($flow[0]['flow_type'] == 2) {
						//上级部门负责人
						$leader = get_department_leader($this->uid, 1);
						if ($leader == 0) {
							return to_assign(1, '上位部門の担当者はまだ決まっていない，人事または管理者に連絡してください');
						} else {
							$param['check_admin_ids'] = $leader;
						}
					} else {
						$param['check_admin_ids'] = $flow[0]['flow_uids'];
					}
				}

				$countWhere = [];
				$countWhere[] = ['report_year', '=',  $param['report_year']];
				$countWhere[] = ['report_month', '=',  $param['report_month']];
				$countWhere[] = ['admin_id', '=',  $this->uid];

				$approveData = Db::name('Approve')->where($countWhere)->select();
				if (count($approveData)>0) {
					if ($approveData[0]["check_status"] == 0 
						|| $approveData[0]["check_status"] == 1 
						|| $approveData[0]["check_status"] == 2){
							return to_assign(1, '該当報告年月の勤怠報告は提出ありました。');
					}
					$param['id'] = $approveData[0]["id"];
					$aid = Db::name('Approve')->strict(false)->field(true)->update($param);
				} else {
					$aid = Db::name('Approve')->strict(false)->field(true)->insertGetId($param);
				}
			}
		}
		return to_assign();;
	}


	public static function initSelect($detail)
	{
		//select init
		$y = date('Y');
		$m = date('m');
		$detail['reportY'] = [$y - 4, $y - 3, $y - 2, $y - 1, $y];
		$detail['reportM'] = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
		$detail['thisY']  = $y;
		$detail['thisM']  = $m;

		$split = 15;
		$count = 60 / $split;
		$initIndex = 0;
		$minute = "";
		$hour = "";
		$dropdownDataArray[] = "";
		for ($i = 0; $i < 24; $i++) {
			if ($i < 10) {
				$hour = "0" . strval($i);
			} else {
				$hour = strval($i);
			}
			for ($j = 0; $j < $count; $j++) {
				if ($j == 0) {
					$minute = "00";
				} else {
					$minute = strval($j * 15);
				}
				$dropdownDataArray[$initIndex] = $hour . ':' . $minute;
				$initIndex++;
			}
		}
		$detail['workStartSelect']  = $dropdownDataArray;
		$detail['workEndSelect']  = $dropdownDataArray;
		array_splice($detail['workEndSelect'], 0, 1);
		$detail['workEndSelect'][$initIndex - 1] = "23:59";
		$detail['workDeductSelect'] = $dropdownDataArray;
		array_splice($detail['workDeductSelect'], 21);

		return $detail;
	}

	// テーブル初期化
	function tableDataInit($detail)
	{
		$y = date('Y');
		$m = date('m');
		$days = date('t');
		$weekArr = array(
			"0" => "日",
			"1" => "月",
			"2" => "火",
			"3" => "水",
			"4" => "木",
			"5" => "金",
			"6" => "土"
		);
		$weekNo = "";
		$startTimeVal = "";
		$endTimeVal = "";
		// $normalTimeVal = "";
		$exceptTimeVal = "00:00";
		$deductTimeVal = "";
		$sumDayTimeVal = "";
		$demandDay = $days;
		$tableData = array();
		for ($i = 1; $i < $days + 1; $i++) {
			$d = $i;
			if ($i < 10) {
				$d = "0" + $i;
			}
			$weekNo = $this->get_week($y . "-" . $m . "-" . $d);
			if ($weekNo == 0 || $weekNo == 6) {
				// $normalTimeVal = "0";
				$deductTimeVal = "";
				$demandDay--;
			} else {
				$deductTimeVal = "01:00";
				// $normalTimeVal = "8";
			}
			$row["day"] = $y . "-" . $m . "-" . $d;
			$row["week"] = $weekArr[$weekNo];
			$row["workStartTime"] = $startTimeVal;
			$row["workEndTime"] = $endTimeVal;
			// $row["standardsTime"] = $normalTimeVal;
			$row["exceptTime"] = $exceptTimeVal;
			$row["deductTime"] = $deductTimeVal;
			$row["sumDayTime"] = $sumDayTimeVal;
			$row["remark"] = "";
			$tableData[] = $row;
		}
		$demandHour = $demandDay * 8;
		$detail['work_record']  = json_encode($tableData);
		$detail['demandHour']  = $demandHour;
		$detail['demandDay']  = $demandDay;
		return $detail;
	}

	function   get_week($date)
	{
		$date_str = date('Y-m-d', strtotime($date));
		$arr = explode("-", $date_str);
		$year = (int)$arr[0];
		$month = (int)$arr[1];
		$day = (int)$arr[2];
		$hour = $minute = $second = 0;
		$strap = mktime($hour, $minute, $second, $month, $day, $year);
		$number_wk = date("w", $strap);

		return $number_wk;
	}
}
