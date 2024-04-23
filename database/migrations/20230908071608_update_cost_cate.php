<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Db;
class UpdateCostCate extends Migrator
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
 
        // 名前を変更
          Db::name('cost_cate')->where('id', 1)->update(['title' => '旅費']);
          Db::name('cost_cate')->where('id', 2)->update(['title' => '消耗品費']);
		  Db::name('cost_cate')->where('id', 3)->update(['title' => '交際費']);
		  Db::name('cost_cate')->where('id', 4)->update(['title' => '交通費']);
		  Db::name('cost_cate')->where('id', 5)->update(['title' => '通信費']);
		  Db::name('cost_cate')->where('id', 6)->update(['title' => '外注費']);
		  Db::name('cost_cate')->where('id', 7)->update(['title' => 'その他']);
    }
}
