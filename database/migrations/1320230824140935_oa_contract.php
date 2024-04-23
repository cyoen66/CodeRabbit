<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaContract extends Migrator
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
        $table = $this->table('contract', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '合同表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('pid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '父协议id',])
			->addColumn('code', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '合同编号',])
			->addColumn('name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '合同名称',])
			->addColumn('cate_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '分类id',])
			->addColumn('type', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '合同性质：0未设置,1普通合同、2框架合同、3补充协议、4其他合同',])
			->addColumn('subject_id', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '签约主体',])
			->addColumn('customer_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '关联客户ID,预设数据',])
			->addColumn('customer', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户名称',])
			->addColumn('customer_name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户代表',])
			->addColumn('customer_mobile', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户电话',])
			->addColumn('customer_address', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户地址',])
			->addColumn('start_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同开始时间',])
			->addColumn('end_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同结束时间',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '创建人',])
			->addColumn('prepared_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同制定人',])
			->addColumn('sign_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同签订人',])
			->addColumn('keeper_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同保管人',])
			->addColumn('share_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '共享人员，如:1,2,3',])
			->addColumn('file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '相关附件，如:1,2,3',])
			->addColumn('sign_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同签订时间',])
			->addColumn('sign_did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '合同签订部门',])
			->addColumn('cost', 'decimal', ['precision' => 15,'scale' => 2,'null' => false,'default' => 0,'signed' => true,'comment' => '合同金额',])
			->addColumn('is_tax', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '是否含税：0未含税,1含税',])
			->addColumn('tax', 'decimal', ['precision' => 15,'scale' => 2,'null' => false,'default' => 0,'signed' => true,'comment' => '税点',])
			->addColumn('check_status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '合同状态：0待审核,1审核中,2审核通过,3审核不通过,4撤销审核,5已中止,6已作废',])
			->addColumn('check_step_sort', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '当前审批步骤',])
			->addColumn('check_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '当前审批人ID，如:1,2,3',])
			->addColumn('flow_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '历史审批人ID，如:1,2,3',])
			->addColumn('last_admin_id', 'string', ['limit' => 200,'null' => false,'default' => 0,'signed' => true,'comment' => '上一审批人',])
			->addColumn('copy_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '抄送人ID，如:1,2,3',])
			->addColumn('check_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '审核人',])
			->addColumn('check_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '审核时间',])
			->addColumn('check_remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '审核备注信息',])
			->addColumn('stop_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '中止人',])
			->addColumn('stop_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '中止时间',])
			->addColumn('stop_remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '中止备注信息',])
			->addColumn('void_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '作废人',])
			->addColumn('void_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '作废时间',])
			->addColumn('void_remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '作废备注信息',])
			->addColumn('archive_status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '归档状态：0未归档,1已归档',])
			->addColumn('archive_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '归档人',])
			->addColumn('archive_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '归档时间',])
			->addColumn('remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '备注信息',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '添加时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '修改时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
