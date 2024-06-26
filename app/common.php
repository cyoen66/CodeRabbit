<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
// 应用公共文件,内置主要的数据处理方法
use think\facade\Config;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Db;

//设置缓存
function set_cache($key, $value, $date = 86400)
{
    Cache::set($key, $value, $date);
}

//读取缓存
function get_cache($key)
{
    return Cache::get($key);
}

//清空缓存
function clear_cache($key)
{
    Cache::clear($key);
}

//读取系统配置
function get_system_config($name, $key = '')
{
    $config = [];
    if (get_cache('system_config' . $name)) {
        $config = get_cache('system_config' . $name);
    } else {
        $conf = Db::name('config')->where('name', $name)->find();
        if (isset($conf['content'])) {
            $config = unserialize($conf['content']);
        }
        set_cache('system_config' . $name, $config);
    }
    if ($key == '') {
        return $config;
    } else {
        if (isset($config[$key])) {
            return $config[$key];
        }
		else{
			return '';
		}
    }
}

//设置系统配置
function set_system_config($name, $key, $value='')
{
    $config = [];
	$conf = Db::name('config')->where('name', $name)->find();
	if ($conf['content']) {
		$config = unserialize($conf['content']);
	}
	$config[$key] = $value;
	set_cache('system_config' . $name, $config);
	$content = serialize($config);
	Db::name('config')->where('name', $name)->update(['content'=>$content]);
}


//读取文件配置
function get_config($key)
{
    return Config::get($key);
}

//判断cms是否完成安装
function is_installed()
{
    static $isInstalled;
    if (empty($isInstalled)) {
        $isInstalled = file_exists(CMS_ROOT . 'config/install.lock');
    }
    return $isInstalled;
}

//判断cms是否存在模板
function isTemplate($url='')
{
    static $isTemplate;
    if (empty($isTemplate)) {
        $isTemplate = file_exists(CMS_ROOT . 'app/'.$url);
    }
    return $isTemplate;
}

//判断模块是否存在
function isModule($name)
{
	$map = [];
	$map[] = ['name', '=', $name];
    $count = Db::name('AdminModule')->where($map)->count();
    return $count;
}
//是否是某数据权限,count>1即有权限
function isAuth($uid,$name)
{
	if($uid == 1){
		return 1;
	}
	$map = [];
	$map[] = ['name', '=', $name];
	$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")];
    $count = Db::name('DataAuth')->where($map)->count();
    return $count;
}

//获取服务器信息
function get_system_info($key)
{
    $system = [
        'os' => PHP_OS,
        'php' => PHP_VERSION,
        'upload_max_filesize' => get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许上传附件",
        'max_execution_time' => get_cfg_var("max_execution_time") . "秒 ",
    ];
    if (empty($key)) {
        return $system;
    } else {
        return $system[$key];
    }
}

//获取url参数
function get_params($key = "")
{
    return Request::instance()->param($key);
}

//生成一个不会重复的字符串
function make_token()
{
    $str = md5(uniqid(md5(microtime(true)), true));
    $str = sha1($str); //加密
    return $str;
}

//随机字符串，默认长度10
function set_salt($num = 10)
{
    $str = 'qwertyuiopasdfghjklzxcvbnm1234567890';
    $salt = substr(str_shuffle($str), 10, $num);
    return $salt;
}
//密码加密
function set_password($pwd, $salt)
{
    return md5(md5($pwd . $salt) . $salt);
}

//获取指定管理员的信息
function get_admin($id)
{
    $admin = Db::name('Admin')
	->alias('a')
	->field('a.*,d.title as department,p.title as position')
	->leftJoin ('Department d ','d.id= a.did')
	->leftJoin ('Position p ','p.id= a.position_id')
	->where(['a.id' => $id])
	->cache(true,60)
	->find();
    $admin['last_login_time'] = empty($admin['last_login_time']) ? '-' : date('Y-m-d H:i', $admin['last_login_time']);
    return $admin;
}

/**
 * 节点权限判断
 * @rule String
 * @uid Int
 * @return bool
 */
function check_auth($rule, $uid)
{
    $auth_list = Cache::get('RulesSrc' . $uid);
    if (!in_array($rule, $auth_list)) {
        return false;
    } else {
        return true;
    }
}

//读取部门列表
function get_department()
{
    $department = Db::name('Department')->order('sort desc,id asc')->where(['status' => 1])->select()->toArray();
    return $department;
}

//获取某部门的子部门id.$is_self时候包含自己
function get_department_son($did = 0, $is_self = 1)
{
    $department = get_department();
    $department_list = get_data_node($department, $did);
    $department_array = array_column($department_list, 'id');
    if ($is_self == 1) {
        //包括自己部门在内
        $department_array[] = $did;
    }
    return $department_array;
}

//读取员工所在部门的负责人（pid=1，上一级负责人）
function get_department_leader($uid=0,$pid=0)
{
	$did = get_admin($uid)['did'];
	if($pid==0){
		$leader = Db::name('Department')->where(['id' => $did])->value('leader_id');
	}
	else{
		$pdid = Db::name('Department')->where(['id' => $did])->value('pid');
		if($pdid == 0){
			$leader = 0;
		}
		else{
			$leader = Db::name('Department')->where(['id' => $pdid])->value('leader_id');
		}		
	}    
    return $leader;
}

//读取部门负责人所在部门的数据权限【包括员工所在部门+其子部门】
function get_department_role($uid = 0)
{
	$did = get_admin($uid)['did'];
	//判断是否是部门负责人
	$is_leader = Db::name('Department')->where(['id' => $did,'leader_id'=>$uid])->count();
	if($is_leader==0){
		return [];
	}
	else{
		//获取子部门
		$department = get_department();
		$department_list = get_data_node($department, $did);
		$department_array = array_column($department_list, 'id');
		//包括自己部门在内
		$department_array[] = $did;
		return $department_array;
	}
}

//读取是否是某员工的上级领导
function get_user_role($leader_id=0,$uid = 0)
{
	$did = get_admin($uid)['did'];
	//获取子部门
	$department = get_department();
	$department_list = get_data_node($department, $did);
	$department_array = array_column($department_list, 'id');
	//包括自己部门在内
	$department_array[] = $did;
	//判断是否是部门负责人
	$is_leader = Db::name('Department')->where([['id','in',$did],['leader_id','=',$leader_id]])->count();
	return $is_leader;
}

//读取根据uid返回所在部门和所管理的子部门did
function get_user_dids($uid = 0)
{
	$did = get_admin($uid)['did'];
	$department_array = [];
	//判断是否是部门负责人
	$is_leader = Db::name('Department')->where(['id'=>$did,'leader_id'=>$uid])->count();
	if($is_leader > 0 || $uid == 1){
		//获取子部门
		$department = get_department();
		$department_list = get_data_node($department, $did);
		$department_array = array_column($department_list, 'id');
		//包括自己部门在内
		$department_array[] = $did;
	}
	else{
		//包括自己部门在内
		$department_array[] = $did;
	}
	return $department_array;
}

//读取职位
function get_position()
{
    $position = Db::name('Position')->where(['status' => 1])->select()->toArray();
    return $position;
}

//根据流程类型读取某部门某模块的审核流程
function get_cate_department_flows($cate=1,$department=0)
{
	$map1 = [];
	$map2 = [];
	$map1[] = ['status', '=', 1];
	$map1[] = ['flow_cate', '=', $cate];
	$map1[] = ['department_ids', '=', ''];

	$map2[] = ['status', '=', 1];
	$map2[] = ['flow_cate', '=', $cate];
	$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")];

    $list = Db::name('Flow')->field('id,name,check_type')->whereOr([$map1,$map2])->order('id desc')->select()->toArray();
    return $list;
}

//根据流程所属模块读取某部门某模块的审核流程
function get_type_department_flows($type=6,$department=0)
{
	$map1 = [];
	$map2 = [];
	$map1[] = ['status', '=', 1];
	$map1[] = ['type', '=', $type];
	$map1[] = ['department_ids', '=', ''];

	$map2[] = ['status', '=', 1];
	$map2[] = ['type', '=', $type];
	$map2[] = ['', 'exp', Db::raw("FIND_IN_SET('{$department}',department_ids)")];

    $list = Db::name('Flow')->field('id,name,check_type')->whereOr([$map1,$map2])->order('id desc')->select()->toArray();
    return $list;
}


/**
 * 初始化审批流程数据
 * @param  $flow_id 审批流程id
 * @return
 */
function set_flow($flow_id,$check_admin_ids,$uid)
{
    $flow_detail = Db::name('Flow')->where('id',$flow_id)->find();
    $check_type = $flow_detail['check_type'];
    $flow = unserialize($flow_detail['flow_list']);
    if ($check_type == 1) {
        if($flow[0]['flow_type'] == 1){
            //部门负责人
            $leader = get_department_leader($uid);
            if($leader == 0){
                return to_assign(1,'承認フローに問題があります：所属部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
            }
            else{
                $check_admin_ids = $leader;
            }                        
        }
        else if($flow[0]['flow_type'] == 2){
            //上级部门负责人
            $leader = get_department_leader($uid,1);
            if($leader == 0){
                return to_assign(1,'承認フローに問題があります：上位部門の責任者が未設定です。人事部またはシステム管理者にご連絡ください');
            }
            else{
                $check_admin_ids = $leader;
            }
        }
        else{
            $check_admin_ids = $flow[0]['flow_uids'];
        }
    }
    else if ($check_type == 3) {
        $check_admin_ids = $flow[0]['flow_uids'];
    }
    $flow_data = array(
        'check_type' => $check_type,
        'flow' => $flow,
        'check_admin_ids' => $check_admin_ids
    );
    return $flow_data;
}

/**
 * 获取审批流程数据
 * @param  $uid 当前登录用户
 * @param  $flows 当前步骤内容
 * @return
 */
function get_flow($uid,$flows)
{
    $check_user = '';
    $check_user_ids = [];
    if($flows['flow_type']==1){
        $check_user = '部门负责人-';                
        $check_user_ids[]=get_department_leader($uid);
    }
    else if($flows['flow_type']==2){
        $check_user = '上级部门负责人-';
        $check_user_ids[]=get_department_leader($uid,1);
    }
    else{
        $check_user_ids = explode(',',$flows['flow_uids']);        
    }
    $check_user_array = Db::name('Admin')->where('id','in',$check_user_ids)->column('name');
    $res = array(
        'check_user' => $check_user.implode(',',$check_user_array),
        'check_user_ids' => $check_user_ids
    );
    return $res;
}


/**
 * 隐藏电话号码中间4位和邮箱
 */
function hidetel($phone)
{
	//隐藏邮箱
	if (strpos($phone, '@')) {
		$email_array = explode("@", $phone);
		$prevfix = (strlen($email_array[0]) < 4) ? "" : substr($phone, 0, 3); //邮箱前缀
		$count = 0;
		$str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $phone, -1, $count);
		$rs = $prevfix . $str;
		return $rs;
	} else {
		//隐藏联系方式中间4位
		$Istelephone = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
		if ($Istelephone) {
			return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
		} else {
			return preg_replace('/(1[0-9]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
		}
	}
}

/**
 * @Method: 文件格式大小
 * @param[type] $file_size [文件大小]
 */
function to_size($file_size){
	$file_size = $file_size-1;
	if ($file_size >= 1099511627776){
		$show_filesize = number_format(($file_size / 1099511627776),2) . " TB";
	}
	elseif ($file_size >= 1073741824) {
		$show_filesize = number_format(($file_size / 1073741824),2) . " GB";
	}
	elseif ($file_size >= 1048576) {
		$show_filesize = number_format(($file_size / 1048576),2) . " MB";
	}
	elseif ($file_size >= 1024) {
		$show_filesize = number_format(($file_size / 1024),2) . " KB";
	}
	elseif ($file_size > 0) {
		$show_filesize = $file_size . " b";
	}
	elseif ($file_size == 0 || $file_size == -1) {
		$show_filesize = "0 b";
	}
	return $show_filesize;
}

//格式化附件展示
function file_card($file,$view=''){
	$image=['jpg','jpeg','png','gif'];
	$type_icon = 'icon-sucaiziyuan';
	$view_btn = '<a class="blue" href="'.$file['filepath'].'" download="'.$file['name'].'" target="_blank" title="下载查看"><i class="iconfont icon-tuiguangshezhi"></i></a>';
	if($file['fileext'] == 'pdf'){
		$type_icon = 'icon-lunwenguanli';
		$view_btn = '<span class="file-view-pdf blue" data-href="'.$file['filepath'].'" title="在线查看"><i class="iconfont icon-yuejuan"></i></span>';
	}
	if(in_array($file['fileext'], $image)){
		$type_icon = 'icon-sucaiguanli';
		$view_btn = '<span class="file-view-img blue" data-href="'.$file['filepath'].'" title="在线查看"><i class="iconfont icon-tupianguanli"></i></span>';
	}
	$item = '<div class="file-card file-'.$view.'" id="fileItem'.$file['id'].'">
		<i class="file-icon iconfont '.$type_icon.'"></i>
		<div class="file-info">
			<div class="file-title" title="'.$file['name'].'">'.$file['name'].'</div>
			<div class="file-ops">'.to_size($file['filesize']).'，'.date('Y-m-d H:i',$file['create_time']).'</div>
		</div>
		<div class="file-tool">'.$view_btn.'<span class="btn-delete red" data-id="'.$file['id'].'" data-uid="'.$file['admin_id'].'" title="删除"><i class="iconfont icon-shanchu"></i></span></div>
	</div>';
	return $item;
}

//读取报销类型
function get_expense_cate()
{
    $expense = Db::name('ExpenseCate')->where(['status' => 1])->select()->toArray();
    return $expense;
}

//读取费用类型
function get_cost_cate()
{
    $cost = Db::name('CostCate')->where(['status' => 1])->select()->toArray();
    return $cost;
}

//读取印章类型
function get_seal_cate()
{
    $seal = Db::name('SealCate')->where(['status' => 1])->select()->toArray();
    return $seal;
}

//读取车辆类型
function get_car_cate()
{
    $car = Db::name('CarCate')->where(['status' => 1])->select()->toArray();
    return $car;
}

//读取企业主体
function get_subject()
{
    $subject = Db::name('Subject')->where(['status' => 1])->select()->toArray();
    return $subject;
}

//读取行业类型
function get_industry()
{
    $industry = Db::name('Industry')->where(['status' => 1])->select()->toArray();
    return $industry;
}

//读取服务类型
function get_services()
{
    $services = Db::name('Services')->where(['status' => 1])->select()->toArray();
    return $services;
}

//读取工作类型
function get_work_cate()
{
    $work = Db::name('WorkCate')->where(['status' => 1])->select()->toArray();
    return $work;
}

//读取所属地区
function get_region_name($id){
    $region = Db::name('city')->where(['id'=>$id])->find();
	if(empty($region)){
		return '';
	}
	else{
		return $region['name'];
	}
}

/**
 * 根据附件表的id返回url地址
 * @param  [type] $id [description]
 */
function get_file($id)
{
    if ($id) {
        $geturl = Db::name("file")->where(['id' => $id])->find();
        if ($geturl['status'] == 1) {
            //审核通过
            //获取签名的URL
            $url = $geturl['filepath'];
            return $url;
        } elseif ($geturl['status'] == 0) {
            //待审核
            return '/static/home/images/none_pic.jpg';
        } else {
            //不通过
            return '/static/home/images/none_pic.jpg';
        }
    }
    return false;
}

/**
 * 间隔时间段格式化
 * @param int $time 时间戳
 * @param string $format 格式 【d：显示到天 i显示到分钟 s显示到秒】
 * @return string
 */
function time_trans($time, $format = 'd')
{
	$now = time();
	$diff = $now - $time;
	if ($diff < 60) {
		return '1分钟前';
	} else if ($diff < 3600) {
		return floor($diff / 60) . '分钟前';
	} else if ($diff < 86400) {
		return floor($diff / 3600) . '小时前';
	}
	$yes_start_time = strtotime(date('Y-m-d 00:00:00', strtotime('-1 days'))); //昨天开始时间
	$yes_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-1 days'))); //昨天结束时间
	$two_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-2 days'))); //2天前结束时间
	$three_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-3 days'))); //3天前结束时间
	$four_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-4 days'))); //4天前结束时间
	$five_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-5 days'))); //5天前结束时间
	$six_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-6 days'))); //6天前结束时间
	$seven_end_time = strtotime(date('Y-m-d 23:59:59', strtotime('-7 days'))); //7天前结束时间

	if ($time > $yes_start_time && $time < $yes_end_time) {
		return '昨天';
	}

	if ($time > $yes_start_time && $time < $two_end_time) {
		return '1天前';
	}

	if ($time > $yes_start_time && $time < $three_end_time) {
		return '2天前';
	}

	if ($time > $yes_start_time && $time < $four_end_time) {
		return '3天前';
	}

	if ($time > $yes_start_time && $time < $five_end_time) {
		return '4天前';
	}

	if ($time > $yes_start_time && $time < $six_end_time) {
		return '5天前';
	}

	if ($time > $yes_start_time && $time < $seven_end_time) {
		return '6天前';
	}

	switch ($format) {
		case 'd':
			$show_time = date('Y-m-d', $time);
			break;
		case 'i':
			$show_time = date('Y-m-d H:i', $time);
			break;
		case 's':
			$show_time = date('Y-m-d H:i:s', $time);
			break;
	}
	return $show_time;
}

/**
 * 计算按天数
 */
function countDays($a, $b = 0)
{
	if ($b == 0) {
		$b = date("Y-m-d");
	}
	$date_1 = $a;
	$date_2 = $b;
	$d1 = strtotime($date_1);
	$d2 = strtotime($date_2);
	$days = round(($d2 - $d1) / 3600 / 24);
	if ($days > 0) {
		return $days;
	} else {
		return 0;
	}
}


/**
 * fullcalendar日历控件方法1
 */
function parseDateTime($string, $timeZone=null) {
  $date = new DateTime(
	$string,
	$timeZone ? $timeZone : new DateTimeZone('UTC')
  );
  if ($timeZone) {
	$date->setTimezone($timeZone);
  }
  return $date;
}

/**
 * fullcalendar日历控件方法2
 */
function stripTime($datetime) {
  return new DateTime($datetime->format('Y-m-d'));
}


function add_log($type, $param_id = '', $param = [] ,$subject='')
{
	$title = '操作';
	$session_admin = get_config('app.session_admin');
	$uid = \think\facade\Session::get($session_admin);
	$type_action = get_config('log.type_action');
	if($type_action[$type]){
		$title = $type_action[$type];
	}
	$data = [
		'uid' => $uid,
		'type' => $type,
		'action' => $title,
		'param_id' => $param_id,
		'param' => json_encode($param),
		'module' => strtolower(app('http')->getName()),
		'controller' => strtolower(app('request')->controller()),
		'function' => strtolower(app('request')->action()),
		'ip' => app('request')->ip(),
		'create_time' => time(),
		'subject' => 'システム'
	];
	if($subject!=''){
		$data['subject'] =$subject;
	}
	else{
		$rule = $data['module'] . '/' . $data['controller'] . '/' . $data['function'];
		$rule_menu = Db::name('AdminRule')->where(array('src' => $rule))->find();
		if($rule_menu){
			$data['subject'] = $rule_menu['name'];
		}
	}
	Db::name('AdminLog')->strict(false)->field(true)->insert($data);
}

/**
 * 发送站内信
 * @param  $user_id 接收人
 * @param  $template 消息模板
 * @param  $data 操作内容
 * @return
 */
function sendMessage($user_id, $template, $data=[])
{
    $title = get_config('message.template')[$template]['title'];
    $content = get_config('message.template')[$template]['content'];
	foreach ($data as $key => $val) {
		$title = str_replace('{' . $key . '}', $val, $title);
		$content = str_replace('{' . $key . '}', $val, $content);
	}
	if(isset($data['from_uid'])){
		$title = str_replace('{from_user}', get_admin($data['from_uid'])['name'], $title);
		$content = str_replace('{from_user}', get_admin($data['from_uid'])['name'], $content);
	}
	$content = str_replace('{date}', date('Y-m-d'), $content);
		
    if (!$user_id) return false;
    if (!$content) return false;
    if (!is_array($user_id)) {
        $users = explode(",", strval($user_id));
    } else {
        $users = $user_id;
    }
    $users = array_unique(array_filter($users));
	//组合要发的消息
	$send_data = [];
	foreach ($users as $key => $value) {
		$send_data[] = array(
			'to_uid' => $value,//接收人
			'action_id' => $data['action_id'],
			'title' => $title,
			'content' => $content,
			'template' => $template,
			'module_name' => strtolower(app('http')->getName()),
			'controller_name' => strtolower(app('request')->controller()),
			'action_name' => strtolower(app('request')->action()),
			'send_time' => time(),
			'create_time' => time()
		);
	}
	$res = Db::name('Message')->strict(false)->field(true)->insertAll($send_data);
    return $res;
}

function getMessageLink($template,$action_id){
	$content='';
	if(isset(get_config('message.template')[$template]['link'])){
		$link = get_config('message.template')[$template]['link'];
		$content = str_replace('{action_id}', $action_id, $link);
	}	
	return $content;
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject 邮件标题
 * @param string $content 邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to, $subject = '', $content = '')
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $email_config = Db::name('config')->where('name', 'email')->find();
    $config = unserialize($email_config['content']);

    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    $mail->SMTPDebug = 0;

    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];
    if ($mail->Port == '465') {
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
    }
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //发送邮箱
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['email'], $config['from']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if (is_array($to)) {
        foreach ($to as $v) {
            $mail->addAddress($v);
        }
    } else {
        $mail->addAddress($to);
    }

    $mail->isHTML(true); // send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    $status = $mail->send();
    if ($status) {
        return true;
    } else {
        //  echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
        //  die;
        return false;
    }
}

/**
 * 生成时间编号
 * $prefix前缀
 */
function get_codeno($prefix=1){
    $no    =   $prefix . date('YmdHis') . rand(10,99);
    return $no;
}
/**
 * 截取文章摘要
 *  @return bool
 */
function get_desc_content($content, $count)
{
    $content = preg_replace("@<script(.*?)</script>@is", "", $content);
    $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
    $content = preg_replace("@<style(.*?)</style>@is", "", $content);
    $content = preg_replace("@<(.*?)>@is", "", $content);
    $content = str_replace(PHP_EOL, '', $content);
    $space = array(" ", "　", "  ", " ", " ");
    $go_away = array("", "", "", "", "");
    $content = str_replace($space, $go_away, $content);
    $res = mb_substr($content, 0, $count, 'UTF-8');
    if (mb_strlen($content, 'UTF-8') > $count) {
        $res = $res . "...";
    }
    return $res;
}

//查找数组索引
function arraySearch($array, $searchFor) {
    foreach($array as $key => $value) {
		if(is_array($value)){
			foreach($value as $key1 => $value1) {
				if($value1 == $searchFor) {
					return array("index" => $key, "key" => $key1);
				}
			}
		}
		else{
			if($value == $searchFor) {
				return $key;
			}
		}
    } 
    return false;
}

/**
 * PHP去除空格
 * @param string $str 字符串
 * @return string     字符串
 */
function trim_space($str=''){
	$str = mb_ereg_replace('^(　| )+', '', $str);
	$str = mb_ereg_replace('(　| )+$', '', $str);
	return mb_ereg_replace('　　', "\n　　", $str);
}
/**
 * PHP格式化字节大小
 * @param number $size      字节数
 * @param string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 截取字符串
 * @param $start 开始截取位置
 * @param $length 截取长度
 * @return
 */
function msubstr($str, $start = 0, $length=1, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if (utf8_strlen($str) < $length) $suffix = false;
    return $suffix ? $slice . '...' : $slice;
}

function utf8_strlen($string = null)
{
    preg_match_all("/./us", $string, $match);
    return count($match[0]);
}

/**
 * PHP截取文字长度
 * @return string
 */
function sub_str($str,$len=20){
    $strlen=strlen($str)/3;#在编码utf8下计算字符串的长度，并把它交给变量$strlen
    #echo $strlen;#输出字符串长度
    if($strlen<$len){
        return $str;
    }else{
        return mb_substr($str,0,$len,"utf-8")."...";
    }
}

/**
 *数据处理成树形格式1
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[$data[$pk]] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][$data[$pk]] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 *数据处理成树形格式2
 * @return array
 */
function create_tree_list($pid, $arr, $group, &$tree = [])
{
    foreach ($arr as $key => $vo) {
        if ($key == 0) {
            $vo['spread'] = true;
        }
        if (!empty($group) and in_array($vo['id'], $group)) {
            $vo['checked'] = true;
        } else {
            $vo['checked'] = false;
        }
        if ($vo['pid'] == $pid) {
            $child = create_tree_list($vo['id'], $arr, $group);
            if ($child) {
                $vo['children'] = $child;
            }
            $tree[] = $vo;
        }
    }
    return $tree;
}


//递归排序，用于分类选择
function set_recursion($result, $pid = 0, $level=-1)
{
    /*记录排序后的类别数组*/
    static $list = array();
    static $space = ['','├─','§§├─','§§§§├─','§§§§§§├─'];
	$level++;
    foreach ($result as $k => $v) {
        if ($v['pid'] == $pid) {
            if ($pid != 0) {
                $v['title'] = $space[$level] . $v['title'];
            }
            /*将该类别的数据放入list中*/
            $list[] = $v;
            set_recursion($result, $v['id'],$level);
        }
    }
    return $list;
}


//递归返回树形菜单数据
function get_tree($data, $pId ,$open=0,$deep=0)
{
	$tree = [];		
	foreach($data as $k => $v)
	{
		$v['checkArr']=array('type'=>0, 'isChecked'=>0);	
		$v['spread']=true;	
		$v['parentId']=$v['pid'];	
		if($deep>=$open){
			$v['spread']=false;	
		}			
		$v['name']=$v['title'];	
		if($v['pid'] == $pId){ 
		//父亲找到儿子
		$deep++;
		$v['children'] = get_tree($data, $v['id'],$open,$deep);
		$tree[] = $v;
		//unset($data[$k]);
	   }
	}
	return array_values($tree);
}

//递归返回树形菜单数据
function get_select_tree($data, $pId ,$deep=0, $selected=[])
{
	$tree = [];		
	foreach($data as $k => $v)
	{	
		$vv=[];
		$vv['name']=$v['title'];	
		$vv['value']=$v['id'];
		$vv['selected']='';
		if(in_array($v['id'],$selected)){
			$vv['selected'] = 'selected';
		}
		if($v['pid'] == $pId){ 
		//父亲找到儿子
		$deep++;
		$vv['children'] = get_select_tree($data, $v['id'],$deep,$selected);
		$tree[] = $vv;
	   }
	}
	return array_values($tree);
}

/**
 * 根据id递归返回子数据
 * @param  $data 数据
 * @param  $pid 父节点id
 */
function get_data_node($data=[],$pid=0){
	$dep = [];		
	foreach($data as $k => $v){			
		if($v['pid'] == $pid){
			$node=get_data_node($data, $v['id']);
			array_push($dep,$v);
			if(!empty($node)){					
				$dep=array_merge($dep,$node);
			}
		}   	
	}
	return array_values($dep);
}


function generateTree($flatArray, $parentId = 0) {
  $tree = [];
  foreach ($flatArray as $item) {
    if ($item['pid'] === $parentId) {
      $node = $item;
	  $node['children'] = generateTree($flatArray, $item['id']);
      $tree[] = $node;
    }
  }
  return $tree;
}

//访问按小时归档统计
function hour_document($arrData)
{
    $documents = array();
    $hour = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23];
    foreach ($hour as $val) {
        $documents[$val] = 0;
    }
    foreach ($arrData as $index => $value) {
        $archivesTime = intval(date("H", $value['create_time']));
        $documents[$archivesTime] += 1;
    }
    return $documents;
}

//访问按日期归档统计
function date_document($arrData)
{
    $documents = array();
    foreach ($arrData as $index => $value) {
        $archivesTime = date("Y-m-d", $value['create_time']);
        if (empty($documents[$archivesTime])) {
            $documents[$archivesTime] = 1;
        } else {
            $documents[$archivesTime] += 1;
        }
    }
    return $documents;
}


/**
 * 返回json数据，用于接口
 * @param    integer    $code
 * @param    string     $msg
 * @param    array      $data
 * @param    string     $url
 * @param    integer    $httpCode
 * @param    array      $header
 * @param    array      $options
 * @return   json
 */
function to_assign($code = 0, $msg = "操作成功", $data = [], $action = '', $url = '', $httpCode = 200, $header = [], $options = [])
{
    $res = ['code' => $code];
    $res['msg'] = $msg;
    $res['action'] = $action;
    $res['url'] = $url;
    if (is_object($data)) {
        $data = $data->toArray();
    }
    $res['data'] = $data;
    $response = \think\Response::create($res, "json", $httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

/**
 * 适配layui table数据列表的返回数据方法，用于接口
 * @param    integer    $code
 * @param    string     $msg
 * @param    array      $data
 * @param    integer    $httpCode
 * @param    array      $header
 * @param    array      $options
 * @return   json
 */
function table_assign($code = 0, $msg = '请求成功', $data = [], $httpCode = 200, $header = [], $options = [])
{
    $res['code'] = $code;
    $res['msg'] = $msg;
    if (is_object($data)) {
        $data = $data->toArray();
    }
    if (!empty($data['total'])) {
        $res['count'] = $data['total'];
    } else {
        $res['count'] = 0;
    }
    $res['data'] = $data['data'];
    $response = \think\Response::create($res, "json", $httpCode, $header, $options);
    throw new \think\exception\HttpResponseException($response);
}

/**
 * 人民币转大写
 * @param
 */
function cny($ns)
{
    static $cnums = array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖"),
    $cnyunits = array("圆", "角", "分"),
    $grees = array("拾", "佰", "仟", "万", "拾", "佰", "仟", "亿");
    list($ns1, $ns2) = explode(".", $ns, 2);
    $ns2 = array_filter(array($ns2[1], $ns2[0]));
    $ret = array_merge($ns2, array(implode("", _cny_map_unit(str_split($ns1), $grees)), ""));
    $ret = implode("", array_reverse(_cny_map_unit($ret, $cnyunits)));
    return str_replace(array_keys($cnums), $cnums, $ret);
}

function _cny_map_unit($list, $units)
{
    $ul = count($units);
    $xs = array();
    foreach (array_reverse($list) as $x) {
        $l = count($xs);
        if ($x != "0" || !($l % 4)) {
            $n = ($x == '0' ? '' : $x) . ($units[($l - 1) % $ul]);
        } else {
            $n = is_numeric($xs[0][0]) ? $x : '';
        }
        array_unshift($xs, $n);
    }
    return $xs;
}


/**
 * 金额展示规则,超过1万时以万为单位，低于1万时以千为单位，低于1千时以元为单位
 * @param string $money 金额
 * @return string
 */
function format_money($money)
{
    $data = '0元';
    if (($money / 10000) > 1) {
        $data = is_int($money / 10000) ? ($money / 10000) . '万' : rand(($money / 10000), 2) . '万';
    } elseif (($money / 1000) > 1) {
        $data = is_int($money / 1000) ? ($money / 1000) . '千' : rand(($money / 1000), 2) . '千';
    } else {
        $data = $money . '元';
    }
    return $data;
}

/**
 * 数组转换字符串（以逗号隔开）
 * @param
 * @return
 */
function arrayToString($array)
{
    if (!is_array($array)) {
        $data_arr[] = $array;
    } else {
        $data_arr = $array;
    }
    $data_arr = array_filter($data_arr); //数组去空
    $data_arr = array_unique($data_arr); //数组去重
    $data_arr = array_merge($data_arr);
    $string = $data_arr ? ',' . implode(',', $data_arr) . ',' : '';
    return $string ?: '';
}

/**
 * 字符串转换数组（以逗号隔开）
 * @param
 * @return
 */
function stringToArray($string)
{
    if (is_array($string)) {
        $data_arr = array_unique(array_filter($string));
    } else {
        $data_arr = $string ? array_unique(array_filter(explode(',', $string))) : [];
    }
    $data_arr = $data_arr ? array_merge($data_arr) : [];
    return $data_arr ?: [];
}

/**
 * 二维数组排序(选择)
 * @param $select 要进行排序的select结果集
 * @param $field  排序的字段
 * @param $order 排序方式1降序2升序
 */
function sort_select($select = array(), $field='', $order = 1)
{
    $count = count($select);
    if ($order == 1) {
        for ($i = 0; $i < $count; $i++) {
            $k = $i;
            for ($j = $i; $j < $count; $j++) {
                if ($select[$k][$field] < $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    } else {
        for ($i = 0; $i < $count; $i++) {
            $k = $i;
            for ($j = $i; $j < $count; $j++) {
                if ($select[$k][$field] > $select[$j][$field]) {
                    $k = $j;
                }
            }
            $temp = $select[$i];
            $select[$i] = $select[$k];
            $select[$k] = $temp;
        }
        return $select;
    }
}

/**
 * 时间戳格式化
 * @param int    $time
 * @param string $format 默认'Y-m-d H:i'，x代表毫秒
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i:s')
{
    $usec = $time = $time === null ? '' : $time;
    if (strpos($time, '.')!==false) {
        list($usec, $sec) = explode(".", $time);
    } else {
        $sec = 0;
    }
    return $time != '' ? str_replace('x', $sec, date($format, intval($usec))) : '';
}



/**
 * 判断是否是手机浏览器
 *  @return bool
 */
function is_mobile()
{ 
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证输入的邮件地址是否合法
 * @param $user_email 邮箱
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false) {
        if (preg_match($chars, $user_email)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * 获取客户浏览器类型
 */
function getBrowser()
{
    $Browser = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/i', $Browser)) {
        $Browser = 'MSIE';
    } elseif (preg_match('/Firefox/i', $Browser)) {
        $Browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $Browser)) {
        $Browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $Browser)) {
        $Browser = 'Safari';
    } elseif (preg_match('/Opera/i', $Browser)) {
        $Browser = 'Opera';
    } else {
        $Browser = 'Other';
    }
    return $Browser;
}

/**
 * 获取客户端系统
 */
function getOS()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/win/i', $agent)) {
        if (preg_match('/nt 6.1/i', $agent)) {
            $OS = 'Windows 7';
        } else if (preg_match('/nt 6.2/i', $agent)) {
            $OS = 'Windows 8';
        } else if (preg_match('/nt 10.0/i', $agent)) {
            $OS = 'Windows 10';
        } else {
            $OS = 'Windows';
        }
    } elseif (preg_match('/mac/i', $agent)) {
        $OS = 'MAC';
    } elseif (preg_match('/linux/i', $agent)) {
        $OS = 'Linux';
    } elseif (preg_match('/unix/i', $agent)) {
        $OS = 'Unix';
    } elseif (preg_match('/bsd/i', $agent)) {
        $OS = 'BSD';
    } else {
        $OS = 'Other';
    }
    return $OS;
}

/**
 * 根据IP获取地址
 */
function getAddress($ip)
{
    $res = file_get_contents("http://ip.360.cn/IPQuery/ipquery?ip=" . $ip);
    $res = json_decode($res, 1);
    if ($res && $res['errno'] == 0) {
        return explode("\t", $res['data'])[0];
    } else {
        return '';
    }
}

/**
 * 导出数据为excel表格
 * @param $data    一个二维数组,结构如同从数据库查出来的数组
 * @param $title   excel的第一行标题,一个数组,如果为空则没有标题
 * @param $filename 下载的文件名
 * @param exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function exportexcel($data = array(), $title = array(), $filename = 'report')
{
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=" . $filename . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    //导出xls 开始
    if (!empty($title)) {
        foreach ($title as $k => $v) {
            $title[$k] = iconv("UTF-8", "GB2312", $v);
        }
        $title = implode("\t", $title);
        echo "$title\n";
    }
    if (!empty($data)) {
        foreach ($data as $key => $val) {
            foreach ($val as $ck => $cv) {
                $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
            }
            $data[$key] = implode("\t", $data[$key]);
        }
        echo implode("\n", $data);
    }
}


//根据数据库查询出来数组获取某个字段拼接字符串
function getFieldArray($array = array(), $field = '')
{
    if (is_array($array) && $field) {
        $ary = array();
        foreach ($array as $value) {
            $ary[] = $value[$field];
        }
        $str = implode(',', $ary);
        return $str;
    } else {
        return false;
    }
}

/**
 * curl 模拟GET请求
 * @author lee
 ***/
function curl_get($url)
{
    //初始化
    $ch = curl_init();
    //设置抓取的url
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // https请求 不验证hosts
	curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);//添加这个获取请求头信息
    //执行命令
    $output = curl_exec($ch);
	$meta = curl_getinfo($ch,CURLINFO_HEADER_OUT);
	$accept = substr($meta,0,strpos($meta, 'Accept:'));
	$host = substr($accept,strpos($accept, 'Host:')+5);
    curl_close($ch); //释放curl句柄
    return $output;
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param string $param
 */
function curl_post($url = '', $post = array())
{
	$post['host'] = $_SERVER['HTTP_HOST'];
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $res = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $res; // 返回数据，json格式
}
