<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaMessage extends Migrator
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
        $table = $this->table('message', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '消息表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('title', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '消息主题',])
			->addColumn('template', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '消息模板，用于前端拼接消息0私人消息,1公告,2办公审批,3报销审批,4发票审批,5合同审批',])
			->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '消息内容',])
			->addColumn('file_ids', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '消息附件',])
			->addColumn('from_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发送人id',])
			->addColumn('to_uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '接收人id',])
			->addColumn('type', 'boolean', ['null' => true,'signed' => false,'comment' => '阅览人类型：1 人员 2部门 3岗位 4全部',])
			->addColumn('type_user', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '人员ID或部门ID或角色ID，全员则为空',])
			->addColumn('send_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '发送日期',])
			->addColumn('read_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '阅读时间',])
			->addColumn('pid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '来源发件id',])
			->addColumn('fid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '转发或回复消息关联id',])
			->addColumn('status', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '状态：-1已删除消息 0垃圾消息 1正常消息',])
			->addColumn('is_draft', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '是否是草稿：1正常消息 2草稿消息',])
			->addColumn('delete_source', 'boolean', ['null' => true,'signed' => false,'comment' => '垃圾消息来源： 1已发消息 2草稿消息 3已收消息',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新时间',])
			->addColumn('module_name', 'string', ['limit' => 30,'null' => false,'default' => null,'signed' => true,'comment' => '模块',])
			->addColumn('controller_name', 'string', ['limit' => 30,'null' => false,'default' => null,'signed' => true,'comment' => '控制器',])
			->addColumn('action_name', 'string', ['limit' => 30,'null' => false,'default' => null,'signed' => true,'comment' => '方法',])
			->addColumn('action_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '操作模块数据的id（针对系统消息）',])
            ->create();
    }
}
