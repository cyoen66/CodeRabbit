<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\adm\validate;

use think\Validate;

class PcCheck extends Validate
{
    protected $rule = [
        'name' => 'require',
        'id' => 'require',
        'status' => 'require',
        'user_id' => 'number',
    ];

    protected $message = [
        'name.require' => 'PC名称を空にすることはできません。',
        'id.require' => '更新条件の不備',
        'status.require' => 'ステータスは必須です',
        'user_id.number' => 'ユーザーIDは数字でなければなりません',
    ];

    protected $scene = [
        'add' => ['title', 'status', 'user_id'],
        'edit' => ['id', 'name', 'status', 'user_id'],
    ];
}