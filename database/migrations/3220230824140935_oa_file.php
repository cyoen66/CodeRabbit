<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaFile extends Migrator
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
        $table = $this->table('file', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '文件表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('module', 'string', ['limit' => 15,'null' => false,'default' => '','signed' => true,'comment' => '所属模块',])
			->addColumn('sha1', 'string', ['limit' => 60,'null' => false,'default' => null,'signed' => true,'comment' => 'sha1',])
			->addColumn('md5', 'string', ['limit' => 60,'null' => false,'default' => null,'signed' => true,'comment' => 'md5',])
			->addColumn('name', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '原始文件名',])
			->addColumn('filename', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '文件名',])
			->addColumn('filepath', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '文件路径+文件名',])
			->addColumn('filesize', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '文件大小',])
			->addColumn('fileext', 'string', ['limit' => 10,'null' => false,'default' => '','signed' => true,'comment' => '文件后缀',])
			->addColumn('mimetype', 'string', ['limit' => 100,'null' => false,'default' => '','signed' => true,'comment' => '文件类型',])
			->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => false,'comment' => '上传会员ID',])
			->addColumn('uploadip', 'string', ['limit' => 15,'null' => false,'default' => '','signed' => true,'comment' => '上传IP',])
			->addColumn('status', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '0未审核1已审核-1不通过',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '',])
			->addColumn('admin_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => null,'signed' => true,'comment' => '审核者id',])
			->addColumn('audit_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '审核时间',])
			->addColumn('action', 'string', ['limit' => 50,'null' => false,'default' => '','signed' => true,'comment' => '来源模块功能',])
			->addColumn('use', 'string', ['limit' => 255,'null' => true,'signed' => true,'comment' => '用处',])
			->addColumn('download', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '下载量',])
            ->create();
    }
}
