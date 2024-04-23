<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaAdmin extends Migrator
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
        $table = $this->table('admin', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '员工表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('username', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '登录用户名',])
			->addColumn('pwd', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '登录密码',])
			->addColumn('salt', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '密码盐',])
			->addColumn('reg_pwd', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '初始密码',])
			->addColumn('name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '员工姓名',])
			->addColumn('email', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '电子邮箱',])
			->addColumn('mobile', 'integer', ['limit' => MysqlAdapter::INT_BIG,'null' => false,'default' => 0,'signed' => true,'comment' => '手机号码',])
			->addColumn('sex', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '性别1男,2女',])
			->addColumn('nickname', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '昵称',])
			->addColumn('thumb', 'string', ['limit' => 255,'null' => false,'default' => null,'signed' => true,'comment' => '头像',])
			->addColumn('theme', 'string', ['limit' => 255,'null' => false,'default' => 'black','signed' => true,'comment' => '系统主题',])
			->addColumn('did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '部门id',])
			->addColumn('position_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '职位id',])
			->addColumn('type', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '员工类型：0未设置,1正式,2试用,3实习',])
			->addColumn('age', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '年龄',])
			->addColumn('native_place', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '籍贯',])
			->addColumn('idcard', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '身份证',])
			->addColumn('education', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '学历',])
			->addColumn('bank_account', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '银行账号',])
			->addColumn('bank_info', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '银行卡信息',])
			->addColumn('desc', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => '员工个人简介',])
			->addColumn('entry_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '员工入职日期',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '注册时间',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '更新信息时间',])
			->addColumn('last_login_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '最后登录时间',])
			->addColumn('login_num', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '登录次数',])
			->addColumn('last_login_ip', 'string', ['limit' => 64,'null' => false,'default' => '','signed' => true,'comment' => '最后登录IP',])
			->addColumn('is_lock', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '是否锁屏:1是0否',])
			->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 1,'signed' => true,'comment' => '状态：-1删除,0禁止登录,1正常,2离职',])
			->addIndex(['id'], ['unique' => true,'name' => 'id'])
            ->create();
    }
}
