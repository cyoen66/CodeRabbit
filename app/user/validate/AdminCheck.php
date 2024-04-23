<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\user\validate;

use think\facade\Db;
use think\Validate;

class AdminCheck extends Validate
{
    protected $regex = ['checkUser' => '/^[A-Za-z0-9_-]{4,20}$/'];
    // 自定义验证规则
    protected function checkUnique($value, $rule, $data)
    {
        [$table, $field, $id] = explode(',', $rule);
        $idField = $id ?: 'id';
        $idValue = $data[$idField] ?? null;
        $map = [
            [$field, '=', $value],
        ];
        if (!is_null($idValue)) {
            $map[] = [$idField, '<>', $idValue];
        }
        $map[] = ['status', '>=', 0];
        return !Db::name($table)->where($map)->count();
    }

    protected $rule = [
        'name' => 'require|chs',
        'username' => 'require|regex:checkUser',
        'mobile' => 'require|number',
        'email' => 'require|email|checkUnique:Admin,email,id',
        'reg_pwd' => 'require|min:6',
        'did' => 'require',
        'position_id' => 'require',
        'type' => 'require',
        'entry_time' => 'require',
        'id' => 'require',
        'pwd' => 'require|min:6|confirm',
        'status' => 'require|checkStatus:-1,1',
        'old_pwd' => 'require|different:pwd',
    ];

    protected $message = [
        'name.require' => '员工姓名不能为空',
        'name.chs' => '员工姓名只能是汉字',
        'username.require' => 'ユーザーIDを入力してください',
        'username.regex' => 'ユーザーIDは英数字・アンダースコア・ハイフンのみで、4～20文字で入力してください',
        'mobile.require' => '電話番号を入力してください',
        'mobile.number' => '電話番号は数字・半角で入力してください。',
        'email.require' => 'メールアドレスを入力してください',
        'email.email' => '正しいメールアドレスを入力してください',
        'email.checkUnique' => '同じメールアドレスが存在します',
        'reg_pwd.require' => 'パスワードを入力してください',
        'reg_pwd.min' => 'cは6文字以上で入力してください',
        'did.require' => '所属部門を選択してください',
        'position_id.require' => '役職を選んでください',
        'type.require' => '社員種類を選んでください',
        'entry_time.require' => '入社日を選んでください',
        'id.require' => '更新するデータを不足しています',
        'pwd.require' => 'パスワードを入力してください',
        'pwd.min' => 'パスワードは6文字以上で入力してください',
        'pwd.confirm' => 'パスワードが一致しません',
        'old_pwd.require' => 'パスワードを入力してください',
        'old_pwd.different' => '現在のパスワードと新しいパスワードが同じです',
    ];

    protected $scene = [
        'add' => ['name', 'username', 'mobile', 'email', 'reg_pwd', 'did', 'position_id', 'type', 'entry_time'],
        'edit' => ['name', 'username', 'mobile', 'email', 'did', 'position_id', 'entry_time', 'id'],
        'editPwd' => ['old_pwd', 'pwd'],
    ];

}