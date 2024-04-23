<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

// これはシステムが自動生成するmiddleware定義ファイルです
return [
    //session中間件を開始する
    //'think\middleware\SessionInit',
    //勾股OAがインストールを完了したかどうかを検証する
    \app\home\middleware\Install::class,
];

