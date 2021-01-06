<?php

namespace app\lform\model;

use think\Model;

use app\lform\service\Datatable;

/**
 * 表单
 */
class Form extends Model
{
    protected $name = 'lform';
    
    protected $autoWriteTimestamp = true;

    protected $auto = [
        'update_time',
    ];
    protected $insert = [
        'name', 
        'create_time', 
        'status' => 1,
    ];
    
    protected $type = [
        'id'             => 'integer',
        'create_time'    => 'integer',
        'update_time'    => 'integer',
    ];
    
    protected static $fields = [
        'status' => array('name' => 'status', 'title' => '数据状态', 'type' => 'select', 'length' => 2, 'extra' => "-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿", 'remark' => '数据状态', 'is_must' => 1, 'value'=>'1'),
        'view' => array('name' => 'view', 'title' => '浏览数量', 'type' => 'text', 'length' => 11, 'extra' => '', 'remark' => '浏览数量', 'is_must' => 1, 'value'=>'0'),
        'update_time' => array('name' => 'update_time', 'title' => '更新时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '更新时间', 'is_must' => 1, 'value'=>'0'),
        'create_time' => array('name' => 'create_time', 'title' => '添加时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '添加时间', 'is_must' => 1, 'value'=>'0'),
    ];
    
    public static function onBeforeInsert($event)
    {
        $fields = self::$fields;
        $data = $event->toArray();
        $tablename = strtolower($data['name']);
        //实例化一个数据库操作类
        $db = new Datatable('lform_ext_');
        //检查表是否存在并创建
        if (!$db->CheckTable($tablename)) {
            //创建新表
            return $db->createTable($tablename, $data['title'], 'id')->query();
        }else{
            return false;
        }
    }
    
    public static function onAfterInsert($event)
    {
        $data = $event->toArray();
        
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                if (in_array($key, array('uid', 'status', 'view', 'create_time', 'update_time'))) {
                    $fields[$key]['form_id'] = $data['id'];
                }else{
                    unset($fields[$key]);
                }
            }
            (new FormAttr())->saveAll($fields);
        }
    }

    public function getStatusTextAttr($value, $data) 
    {
        $status = array(
            0 => '禁用',
            1 => '启用',
        );
        return $status[$data['status']];
    }

}