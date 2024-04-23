<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaFlow extends Migrator
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
        $table = $this->table('flow', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '审批流程表' ,'id' => 'id' ,'primary_key' => ['id']]);
        $table->addColumn('name', 'string', ['limit' => 50,'null' => false,'default' => null,'signed' => true,'comment' => '审批流名称',])
			->addColumn('check_type', 'boolean', ['null' => false,'default' => null,'signed' => true,'comment' => '1固定审批流,2自由审批流,3可回退的审批流',])
			->addColumn('type', 'boolean', ['null' => false,'default' => null,'signed' => true,'comment' => '应用模块,1假勤,2行政,3财务,4人事,5其他,6报销,7发票,8合同',])
			->addColumn('flow_cate', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '应用审批类型id',])
			->addColumn('department_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '应用部门ID（0为全部）1,2,3',])
			->addColumn('copy_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '抄送人ID',])
			->addColumn('remark', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '流程说明',])
			->addColumn('flow_list', 'string', ['limit' => 1000,'null' => true,'signed' => true,'comment' => '流程数据序列化',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '创建人ID',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('status', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '状态 1启用，0禁用',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
			->addColumn('delete_user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除人ID',])
            ->create();
    }
}
