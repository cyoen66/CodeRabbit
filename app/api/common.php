<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
//記事のカテゴリーリストを取得する
function get_article_cate()
{
    $cate = \think\facade\Db::name('ArticleCate')->order('create_time asc')->select()->toArray();
    return $cate;
}