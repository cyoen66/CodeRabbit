<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaExpense extends Migrator
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
        $table = $this->table('expense', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '报销表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('code', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '报销编码',])
			->addColumn('income_month', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '入账月份',])
			->addColumn('expense_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '原始单据日期',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '报销人',])
			->addColumn('did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '报销部门ID',])
			->addColumn('ptid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '预定字段:关联项目ID',])
			->addColumn('check_step_sort', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '当前审批步骤',])
			->addColumn('check_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '当前审批人ID，如:1,2,3',])
			->addColumn('flow_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '历史审批人ID，如:1,2,3',])
			->addColumn('copy_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '抄送人ID，如:1,2,3',])
			->addColumn('file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '附件ID，如:1,2,3',])
			->addColumn('check_status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '状态 0待审核,1审核中,2审核通过,3审核不通过,4撤销审核,5已打款',])
			->addColumn('last_admin_id', 'string', ['limit' => 200,'null' => false,'default' => 0,'signed' => true,'comment' => '上一审批人',])
			->addColumn('pay_admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '打款人ID',])
			->addColumn('pay_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '打款时间',])
			->addColumn('remark', 'string', ['limit' => 1000,'null' => true,'signed' => true,'comment' => '备注',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
