<?php

namespace app\admin\validate\lform;

use think\Validate;

use app\lform\service\Datatable;

/**
 * 设置模型
 */
class Form extends Validate 
{
    protected $rule = array(
        'title'   => 'require',
        'name'   => 'require|checkTable|unique:lform|/^[a-zA-Z]\w{0,39}$/',
    );
    
    protected $message = array(
        'title.require'   => '字段标题不能为空！',
        'name.checkTable' => '数据库中有此表',
    );
    
    protected $scene = array(
        'add'   => 'title, name',
        'edit'   => 'title'
    );
    
    protected function requireIn($value, $rule, $data)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        } else {
            return true;
        }
        $field = array_shift($rule);
        $val = $this->getDataValue($data, $field);
        if (!in_array($val, $rule) && $value == '') {
            return false;
        } else {
            return true;
        }
    }

    protected function checkTable($value, $rule, $data)
    {
        $tablename = 'form_' . strtolower($value);
        $db = new Datatable();
        if (!$db->CheckTable($tablename)) {
            return true;
        }else{
            return false;
        }
    }
}