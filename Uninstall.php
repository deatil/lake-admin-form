<?php

namespace app\lform;

use think\facade\Db;

use app\lform\service\Datatable;

/**
 * 卸载脚本
 *
 * @create 2019-11-3
 * @author deatil
 */
class Uninstall
{
    //固定相关表
    private $modelTabList = [
        'lform',
        'lform_attr',
    ];

    // 卸载
    public function run()
    {
        if (request()->param('clear') == 1) {
            // 删除模型中建的表
            $table_list = Db::name('lform')
                ->field('name')
                ->select()
                ->toArray();
            if (!empty($table_list)) {
                $Datatable = new Datatable('lform_ext_');
                foreach ($table_list as $val) {
                    $Datatable->delTable($val['name'])->query();
                }
            }
            
            // 删除固定表
            if (!empty($this->modelTabList)) {
                $dbPrefix = app()->db->connect()->getConfig('prefix');
                foreach ($this->modelTabList as $tablename) {
                    if (!empty($tablename)) {
                        $tablename = $dbPrefix . $tablename;
                        Db::execute("DROP TABLE IF EXISTS `{$tablename}`;");
                    }
                }
            }
        }

        return true;
    }

}
