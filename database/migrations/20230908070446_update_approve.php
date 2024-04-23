<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Db;
class UpdateApprove extends Migrator
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
        $table = $this->table('approve');
        if (!$table->hasColumn('branch_number')) {
            $table
                 ->addColumn('branch_number', 'string', ['length' => 20])
                 ->update();
        }
        if (!$table->hasColumn('branch_name')) {
            $table
                 ->addColumn('branch_name', 'string', ['length' => 100])
                 ->update();
        }
    }
}
