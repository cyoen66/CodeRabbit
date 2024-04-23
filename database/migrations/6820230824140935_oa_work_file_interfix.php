<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaWorkFileInterfix extends Migrator
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
    public function change()
    {
        $table = $this->table('work_file_interfix', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '汇报工作关联的附件表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('wid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => false,'comment' => '汇报工作id',])
			->addColumn('file_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '相关联附件id',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '创建人',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
            ->create();
    }
}
