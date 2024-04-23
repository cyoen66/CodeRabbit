<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaApprove extends Migrator
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
        $table = $this->table('approve', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '日常审批表' ,'id' => 'id' ,'primary_key' => ['id']]);
        $table->addColumn('type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '审批类型',])
			->addColumn('flow_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '审批流程ID',])
			->addColumn('content', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '内容',])
			->addColumn('name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '名称',])
			->addColumn('mobile', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '手机号码',])
			->addColumn('remark', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '备注',])
			->addColumn('remark1', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '备注1',])
			->addColumn('detail_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '时间日期',])
			->addColumn('start_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '开始时间',])
			->addColumn('end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '结束时间',])
			->addColumn('duration', 'decimal', ['precision' => 10,'scale' => 1,'null' => false,'default' => 0,'signed' => true,'comment' => '时长',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '创建人ID',])
			->addColumn('department_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '创建人部门ID',])
			->addColumn('check_step_sort', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '当前审批步骤',])
			->addColumn('check_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '当前审批人ID，如:1,2,3',])
			->addColumn('flow_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '历史审批人ID，如:1,2,3',])
			->addColumn('copy_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '抄送人ID，如:1,2,3',])
			->addColumn('file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '附件ID，如:1,2,3',])
			->addColumn('check_status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '状态 0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',])
			->addColumn('last_admin_id', 'string', ['limit' => 200,'null' => false,'default' => 0,'signed' => true,'comment' => '上一审批人',])
			->addColumn('detail_type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '假期类型:1事假,2年假,3调休假,4病假,5婚假,6丧假,7产假,8陪产假,9其他',])
			->addColumn('other_type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '其他类型:1公告类,2规则制度类,3合同类,4资质更新类,5员工证明,6其他',])
			->addColumn('department_type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '部门类型',])
			->addColumn('position_type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '职位类型',])
			->addColumn('bank', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '银行卡账号',])
			->addColumn('address', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '地址',])
			->addColumn('num', 'integer', ['limit' => MysqlAdapter::INT_BIG,'null' => false,'default' => 0,'signed' => true,'comment' => '数量',])
			->addColumn('num1', 'integer', ['limit' => MysqlAdapter::INT_BIG,'null' => false,'default' => 0,'signed' => true,'comment' => '数量1',])
			->addColumn('amount', 'decimal', ['precision' => 18,'scale' => 2,'null' => false,'default' => 0,'signed' => true,'comment' => '金额',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
