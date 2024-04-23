<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaInvoice extends Migrator
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
        $table = $this->table('invoice', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '发票表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('code', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '发票号码',])
			->addColumn('customer_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '关联客户ID',])
			->addColumn('contract_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '关联合同协议ID',])
			->addColumn('project_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '关联项目ID',])
			->addColumn('cash_type', 'boolean', ['null' => true,'signed' => false,'comment' => '付款方式：1现金 2转账 3微信支付 4支付宝 5信用卡 6支票 7其他',])
			->addColumn('is_cash', 'boolean', ['null' => true,'signed' => false,'comment' => '是否到账：0未到账 1部分到账 2全部到账',])
			->addColumn('amount', 'decimal', ['precision' => 15,'scale' => 2,'null' => true,'signed' => true,'comment' => '发票金额',])
			->addColumn('enter_amount', 'decimal', ['precision' => 15,'scale' => 2,'null' => true,'signed' => true,'comment' => '到账金额',])
			->addColumn('did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '开发票部门',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发票申请人',])
			->addColumn('check_admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发票审核人',])
			->addColumn('check_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '审核时间',])
			->addColumn('open_admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发票开具人',])
			->addColumn('open_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发票开具时间',])
			->addColumn('delivery', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '快递单号',])
			->addColumn('type', 'boolean', ['null' => true,'signed' => false,'comment' => '抬头类型：1企业2个人',])
			->addColumn('invoice_subject', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '关联发票主体ID',])
			->addColumn('invoice_type', 'boolean', ['null' => true,'signed' => false,'comment' => '发票类型：1增值税专用发票,2普通发票,3专用发票',])
			->addColumn('invoice_title', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '开票抬头',])
			->addColumn('invoice_phone', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '电话号码',])
			->addColumn('invoice_tax', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '纳税人识别号',])
			->addColumn('invoice_bank', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '开户银行',])
			->addColumn('invoice_account', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '银行账号',])
			->addColumn('invoice_banking', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '银行营业网点',])
			->addColumn('invoice_address', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '地址',])
			->addColumn('check_step_sort', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '当前审批步骤',])
			->addColumn('check_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '当前审批人ID，如:1,2,3',])
			->addColumn('flow_admin_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '历史审批人ID，如:1,2,3',])
			->addColumn('copy_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '抄送人ID，如:1,2,3',])
			->addColumn('file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '附件ID，如:1,2,3',])
			->addColumn('other_file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '其他附件ID，如:1,2,3',])
			->addColumn('check_status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '状态 0待审核,1审核中,2审核通过,3审核不通过,4已撤销,5已开具,10已作废',])
			->addColumn('last_admin_id', 'string', ['limit' => 200,'null' => false,'default' => 0,'signed' => true,'comment' => '上一审批人',])
			->addColumn('check_remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '撤销的理由',])
			->addColumn('remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '备注',])
			->addColumn('enter_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '最新到账时间',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
