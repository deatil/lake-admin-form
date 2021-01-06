<?php

namespace app\lform\model;

use think\Model;
use think\facade\Db;

use Lake\Admin\Model\FieldType;

use app\lform\service\Datatable;

/**
* 设置模型
*/
class FormAttr extends Model
{
    protected $name = 'lform_attr';
    
    protected $autoWriteTimestamp = true;
    
    protected $insert = [
        'status' => 1,
    ];

    protected $type = array(
        'id'  => 'integer',
    );
    
    public static function onAfterInsert($data)
    {
        if ($data['form_id']) {
            $name = Db::name('lform')->where('id', $data['form_id'])->value('name');
            $db = new Datatable('lform_ext_');
            $attr = $data->toArray();
            $types = (new FieldType())->getFieldTypeList();
            return $db->setTypeAlist($types)->columField(strtolower($name), $attr)->query();
        }
    }
    
    public static function onBeforeUpdate($data)
    {
        $attr = $data->toArray();
        $attr['action'] = 'CHANGE';
        $attr['oldname'] = Db::name('lform_attr')->where('id', $attr['id'])->value('name');
        if ($attr['id']) {
            $name = Db::name('lform')->where('id', $attr['form_id'])->value('name');
            $db = new Datatable('lform_ext_');
            $types = (new FieldType())->getFieldTypeList();
            return $db->setTypeAlist($types)->columField(strtolower($name), $attr)->query();
        }else{
            return false;
        }
    }

    public function getFieldlist($map,$index='id'){
        $list = array();
        $row = $this->field('*,remark as help,type,extra as "option"')
            ->where($map)
            ->order('group_id asc, sort asc')
            ->select()
            ->toArray();
        foreach ($row as $key => $value) {
            if (in_array($value['type'],array('checkbox','radio','select','bool'))) {
                $value['option'] = lform_parse_field_attr($value['extra']);
            } elseif ($value['type'] == 'bind') {
                $extra = lform_parse_field_bind($value['extra']);
                $option = array();
                foreach ($extra as $k => $v) {
                    $option[$v['id']] = $v['title_show'];
                }
                $value['option'] = $option;
            }
            $list[$value['id']] = $value;
        }
        return $list;
    }

    public function del($id, $model_id)
    {
        $map['id'] = $id;
        $info = $this->find($id);
        $tablename = Db::name('Form')
            ->where([
                'id' => $model_id,
            ])
            ->value('name');

        //先删除字段表内的数据
        $result = $this->where($map)->delete();
        if ($result) {
            $tablename = strtolower($tablename);
            //删除模型表中字段
            $db = new Datatable('lform_ext_');
            if (!$db->CheckField($tablename, $info['name'])) {
                return true;
            }
            $result = $db->delField($tablename, $info['name'])->query();
            if ($result) {
                return true;
            }else{
                $this->error = "删除失败！";
                return false;
            }
        }else{
            $this->error = "删除失败！";
            return false;
        }
    }
}