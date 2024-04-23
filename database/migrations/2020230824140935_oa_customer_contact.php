<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaCustomerContact extends Migrator
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
        $table = $this->table('customer_contact', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '客户联系人表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('cid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '客户ID',])
			->addColumn('is_default', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '是否是第一联系人',])
			->addColumn('name', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '姓名',])
			->addColumn('sex', 'boolean', ['null' => false,'default' => 0,'signed' => false,'comment' => '用户性别:0未知,1男,2女',])
			->addColumn('mobile', 'string', ['limit' => 20,'null' => false,'default' => '','signed' => true,'comment' => '手机号码',])
			->addColumn('qq', 'string', ['limit' => 20,'null' => false,'default' => '','signed' => true,'comment' => 'QQ号',])
			->addColumn('wechat', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '微信号',])
			->addColumn('email', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '邮件地址',])
			->addColumn('nickname', 'string', ['limit' => 50,'null' => false,'default' => '','signed' => true,'comment' => '称谓',])
			->addColumn('department', 'string', ['limit' => 50,'null' => false,'default' => '','signed' => true,'comment' => '部门',])
			->addColumn('position', 'string', ['limit' => 50,'null' => false,'default' => '','signed' => true,'comment' => '职务',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '创建人',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '删除时间',])
            ->create();
    }
}
