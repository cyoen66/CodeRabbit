<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\adm\validate;

use think\Validate;

class MeetingCateCheck extends Validate
{
    protected $rule = [
        'title' => 'require|unique:meeting_cate',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '名前は必須です',
        'title.unique' => '同じ名前が既に存在しています',
        'id.require' => '更新条件が不足しています',
    ];

    protected $scene = [
        'add' => ['title'],
        'edit' => ['id', 'title'],
    ];
}
