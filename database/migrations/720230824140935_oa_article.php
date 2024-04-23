<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class OaArticle extends Migrator
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
        $table = $this->table('article', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_0900_ai_ci', 'comment' => '知识文章表' ,'id' => 'id','signed' => true ,'primary_key' => ['id']]);
        $table->addColumn('title', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '知识文章标题',])
			->addColumn('cate_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '关联分类id',])
			->addColumn('keywords', 'string', ['limit' => 255,'null' => true,'signed' => true,'comment' => '关键字',])
			->addColumn('desc', 'string', ['limit' => 1000,'null' => true,'signed' => true,'comment' => '摘要',])
			->addColumn('thumb', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '缩略图id',])
			->addColumn('uid', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '作者',])
			->addColumn('did', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '部门',])
			->addColumn('origin_url', 'string', ['limit' => 255,'null' => false,'default' => '','signed' => true,'comment' => '来源地址',])
			->addColumn('file_ids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '相关附件',])
			->addColumn('is_share', 'boolean', ['null' => false,'default' => 1,'signed' => true,'comment' => '分享，0私有,1所有人,2部门,3人员',])
			->addColumn('share_dids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '分享部门',])
			->addColumn('share_uids', 'string', ['limit' => 500,'null' => false,'default' => '','signed' => true,'comment' => '分享用户',])
			->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => false,'signed' => true,'comment' => '文章内容',])
			->addColumn('read', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '阅读量',])
			->addColumn('type', 'boolean', ['null' => false,'default' => 0,'signed' => true,'comment' => '属性：1精华 2热门 3推荐',])
			->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 1,'signed' => true,'comment' => '状态:1正常-1下架',])
			->addColumn('sort', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '排序',])
			->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '',])
			->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '',])
			->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => false,'default' => 0,'signed' => true,'comment' => '',])
            ->create();
    }
}
