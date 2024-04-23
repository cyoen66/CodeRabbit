<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaCustomer extends Migrator
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
        $table = $this->table('customer', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '客户表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户名称',])
			->addColumn('source_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '客户来源id',])
			->addColumn('grade_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '客户等级id',])
			->addColumn('industry_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '所属行业id',])
			->addColumn('services_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '客户意向id',])
			->addColumn('provinceid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '省份id',])
			->addColumn('cityid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '城市id',])
			->addColumn('distid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '区县id',])
			->addColumn('townid', 'integer', ['limit' => MysqlAdapter::INT_BIG,'null' => false,'default' => 0,'signed' => true,'comment' => '城镇id',])
			->addColumn('address', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '客户联系地址',])
			->addColumn('status', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '客户状态：0未设置,1新进客户,2跟进客户,3正式客户,4流失客户',])
			->addColumn('intent_status', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '意向状态：0未设置,1意向不明,2意向模糊,3意向一般,4意向强烈',])
			->addColumn('contact_first', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '第一联系人id',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '录入人',])
			->addColumn('belong_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '所属人',])
			->addColumn('belong_did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '所属部门',])
			->addColumn('belong_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '获取时间',])
			->addColumn('distribute_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '分配时间',])
			->addColumn('share_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '共享人员，如:1,2,3',])
			->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '客户描述',])
			->addColumn('market', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '主要经营业务',])
			->addColumn('remark', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '备注信息',])
			->addColumn('bank', 'string', ['limit' => 60,'null' => false,'default' => '','signed' => true,'comment' => '开户银行',])
			->addColumn('bank_sn', 'string', ['limit' => 60,'null' => false,'default' => '','signed' => true,'comment' => '银行帐号',])
			->addColumn('tax_num', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '纳税人识别号',])
			->addColumn('cperson_mobile', 'string', ['limit' => 20,'null' => false,'default' => '','signed' => true,'comment' => '开票电话',])
			->addColumn('cperson_address', 'string', ['limit' => 200,'null' => false,'default' => '','signed' => true,'comment' => '开票地址',])
			->addColumn('discard_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '废弃时间',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '添加时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '修改时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
