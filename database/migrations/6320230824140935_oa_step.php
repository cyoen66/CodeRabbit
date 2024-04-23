<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaStep extends Migrator
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
        $table = $this->table('step', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '阶段步骤表' ,'id' => 'id' ,'primary_key' => ['id']]);
        $table->addColumn('action_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '关联ID',])
			->addColumn('flow_name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '阶段名称',])
			->addColumn('flow_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '阶段负责人ID',])
			->addColumn('flow_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '阶段成员ID (使用逗号隔开) 1,2,3',])
			->addColumn('sort', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '排序ID',])
			->addColumn('type', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '阶段类型:1合同,2项目',])
			->addColumn('start_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '开始时间',])
			->addColumn('end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '结束时间',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
