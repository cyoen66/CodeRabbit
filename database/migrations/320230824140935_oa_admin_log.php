<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaAdminLog extends Migrator
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
        $table = $this->table('admin_log', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '员工操作日志表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '用户ID',])
			->addColumn('type', 'string', ['limit' => 80,'null' => false,'default' => '','signed' => true,'comment' => '操作类型',])
			->addColumn('action', 'string', ['limit' => 80,'null' => false,'default' => '','signed' => true,'comment' => '操作动作',])
			->addColumn('subject', 'string', ['limit' => 80,'null' => false,'default' => '','signed' => true,'comment' => '操作主体',])
			->addColumn('module', 'string', ['limit' => 32,'null' => false,'default' => '','signed' => true,'comment' => '模块',])
			->addColumn('controller', 'string', ['limit' => 32,'null' => false,'default' => '','signed' => true,'comment' => '控制器',])
			->addColumn('function', 'string', ['limit' => 32,'null' => false,'default' => '','signed' => true,'comment' => '方法',])
			->addColumn('ip', 'string', ['limit' => 64,'null' => false,'default' => '','signed' => true,'comment' => '登录ip',])
			->addColumn('param_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '操作数据id',])
			->addColumn('param', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '参数json格式',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '创建时间',])
            ->create();
    }
}
