<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaFlowStep extends Migrator
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
        $table = $this->table('flow_step', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '审批步骤表' ,'id' => 'id' ,'primary_key' => ['id']]);
        $table->addColumn('action_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '审批内容ID',])
			->addColumn('flow_type', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '0自由指定,1当前部门负责人，2上一级部门负责人，3指定用户（任意一人），4指定用户（多人会签）',])
			->addColumn('flow_name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '流程名称',])
			->addColumn('flow_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '审批人ID (使用逗号隔开) 1,2,3',])
			->addColumn('sort', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '排序ID',])
			->addColumn('type', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '审批类型:1日常审批,2报销审批,3发票审批,4合同审批',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
