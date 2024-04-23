<?php
// これはシステムによって自動生成されたmiddlewareの定義ファイルです
return [
    // セッションの初期化ミドルウェアを有効にする
    //'think\middleware\SessionInit',
    // 勾配CMSのインストールを検証する
    \app\home\middleware\Install::class,
];