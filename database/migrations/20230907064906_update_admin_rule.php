<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Db;
class UpdateAdminRule extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        
        // ディレクトリを非表示にする
		Db::name('admin_rule')->where(['id'=>110])->update(['status' => 0,]);
		Db::name('admin_rule')->where(['id'=>100])->update(['status' => 0,]);
        
        // 名前を変更
		Db::name('admin_rule')->where(['id'=>105])->update(['title' => 'タスク報告一覧',]);
		Db::name('admin_rule')->where(['id'=>99])->update(['title' => 'CC一覧',]);
        
		Db::name('admin_rule')->where(['id'=>183])->update(['title' => 'ドキュメント一覧',]);
		Db::name('admin_rule')->where(['id'=>187])->update(['title' => 'ドキュメント管理',]);
		Db::name('admin_rule')->where(['id'=>188])->update(['title' => 'ドキュメントタイプ',]);
		Db::name('admin_rule')->where(['id'=>191])->update(['title' => 'ドキュメント一覧',]);
		Db::name('admin_rule')->where(['id'=>192])->update(['title' => 'ドキュメント追加',]);
    }
}
