<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Db;
class UpdateFlowType extends Migrator
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
		Db::name('flow_type')->where(['id'=>14])->update(['status' => 0,]);
        
        // 名前を変更
		Db::name('flow_type')->where(['id'=>22])->update(['title' => 'その他申請',]);
        
	
    }
}
