<?php

use think\migration\Migrator;
use think\migration\db\Column;
use think\facade\Db;
use Phinx\Db\Adapter\MysqlAdapter;
class OaPcCate extends Migrator
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
    public function up()
    {
        $table = $this->table('pc_cate', ['engine' => 'InnoDB',  'comment' => 'PC管理情報のテーブル' ,'id' => 'management_number' ,'primary_key' => ['management_number']]);
        $table->addColumn('name', 'string', ['limit' => 255,'null' => true,'signed' => true,'comment' => 'PC名称',])
            ->addColumn('type', 'string', ['limit' => 50,'null' => true,'signed' => true,'comment' => '種類',])
            ->addColumn('status', 'boolean', ['null' => true,'signed' => true,'comment' => 'ステータス',])
            ->addColumn('specs', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR,'null' => true,'signed' => true,'comment' => 'スペック',])
            ->addColumn('manufacturer', 'string', ['limit' => 100,'null' => true,'signed' => true,'comment' => 'メーカー',])
            ->addColumn('serial_number', 'string', ['limit' => 50,'null' => true,'signed' => true,'comment' => 'シリアル番号',])
            ->addColumn('mac_address', 'string', ['limit' => 17,'null' => true,'signed' => true,'comment' => 'MACアドレス',])
            ->addColumn('ip_address', 'string', ['limit' => 15,'null' => true,'signed' => true,'comment' => 'IPアドレス',])
            ->addColumn('amount', 'string', ['limit' => 20,'null' => true,'signed' => true,'comment' => '金額',])
            ->addColumn('purchase_date', 'date', ['null' => true,'signed' => true,'comment' => '購入日',])
            ->addColumn('warranty_end_date', 'date', ['null' => true,'signed' => true,'comment' => '保証終了日',])
            ->addColumn('user_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR,'null' => true,'signed' => true,'comment' => '使用者ID',])
            ->addColumn('start_date', 'date', ['null' => true,'signed' => true,'comment' => '使用開始日',])
            ->addColumn('end_date', 'date', ['null' => true,'signed' => true,'comment' => '使用終了日',])
            ->addColumn('create_time', 'timestamp', ['null' => false,'default' => 'CURRENT_TIMESTAMP','signed' => true,'comment' => 'レコード作成日時',])
            ->addColumn('update_time', 'timestamp', ['null' => false,'default' => 'CURRENT_TIMESTAMP','signed' => true,'comment' => 'レコード更新日時',])
            ->create();
        $table =  Db::name('admin_rule');
        $rule =  $table->where(['name' =>'pc'])->find();
        if(!$rule){

            $table->insertAll([
                ['id' => 197, 'pid' => 4, 'src' => 'adm/pc/pc_cate', 'title' => 'pc管理', 'name' => 'pc', 'module' => 'adm', 'icon' => '', 'menu' => 1, 'sort' => 1, 'status' => 1, 'create_time' => 0, 'update_time' => 0],

                ['id' => 198, 'pid' => 197 , 'src' => 'adm/pc/add', 'title' => '新規/編集', 'name' => 'pc', 'module' => 'adm', 'icon' => '', 'menu' => 2, 'sort' => 1, 'status' => 1, 'create_time' => 0, 'update_time' => 0],

                ['id' => 199, 'pid' => 197 , 'src' => 'adm/pc/check', 'title' => '設置', 'name' => 'pc', 'module' => 'adm', 'icon' => '', 'menu' => 2, 'sort' => 1, 'status' => 1, 'create_time' => 0, 'update_time' => 0],]
            );
            $newIds = [197,198,199];

            // Update the rules column if the IDs do not exist
            Db::name('admin_group')
                ->where('id', 1)
                ->where(function ($query) use ($newIds) {
                    foreach ($newIds as $id) {
                        $query->whereNotLike('rules', "%,$id,%");
                    }
                })
                ->update([
                    'rules' => Db::raw("CONCAT(rules, '," . implode(',', $newIds) . "')")
                ]); 
        }

    }
    public function down()
    {
        // IDs to remove
        $idsToRemove = [197, 198, 199];

        // Fetch the existing rules column value
        $existingRules = Db::name('admin_group')
            ->where('id', 1)
            ->value('rules');

        // Convert the existing rules to an array
        $existingRulesArray = explode(',', $existingRules);

        // Remove the specified IDs from the array
        $filteredRulesArray = array_diff($existingRulesArray, $idsToRemove);

        // Implode the filtered rules array back to a string
        $filteredRules = implode(',', $filteredRulesArray);

        // Update the rules column
        Db::name('admin_group')
            ->where('id', 1)
            ->update(['rules' => $filteredRules]);
        Db::name('admin_rule')
            ->where(['name' =>'pc'])
            ->delete();
        $table = $this->table('pc_cate');
        $table->drop();
    }
}
