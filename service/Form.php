<?php

namespace app\lform\service;

use app\lform\model\Form as FormModel;
use app\lform\model\FormAttr as FormAttrModel;

use app\lform\service\Datatable;

/**
 * 表单提交
 *
 * @create 2019-11-8
 * @author deatil
 */
class Form
{

    /**
     * 表单数据提交
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function post($data = [])
    {
        if (empty($data)) {
            return false;
        }
        
        if (!isset($data['form']) || empty($data['form'])) {
            return false;
        }
        
        $form_name = $data['form'];
        
        $form = (new FormModel())->where('name', $form_name)->find();
        if (empty($form)) {
            return false;
        }
    
        $form_attrs = (new FormAttrModel())
            ->where([
                'form_id' => $form['id'],
                'is_show' => 1,
            ])
            ->order('sort ASC')
            ->column('name');
        if (empty($form_attrs)) {
            return false;
        }
        
        unset($data['id']);
        if (empty($data)) {
            return false;
        }
        
        foreach ($data as $key => $val) {
            if (!in_array($key, $form_attrs)) {
                unset($data[$key]);
            }
        }
    
        $default_data = [
            'view' => 0,
            'status' => 1,
            'create_time' => time(),
            'update_time' => time(),
        ];
        $data = array_merge($default_data, $data);
        
        $tablename = (new Datatable('lform_ext_'))->getTablename(strtolower($form['name']));
        
        $result = db($tablename)->insert($data);
        if ($result === false) {
            return false;
        }
        
        return true;
    }
}