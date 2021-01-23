<?php

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

use Lake\Admin\Model\FieldType as FieldTypeModel;

use app\lform\service\Datatable;

use app\lform\model\Form as FormModel;
use app\lform\model\FormAttr as FormAttrModel;

/**
 * 字段管理
 *
 * @create 2019-11-5
 * @author deatil
 */
class LformAttr extends LformBase 
{
    protected $module = 'lform';
    
    // 表单配置
    protected $FormAttr;
    
    // 表单创建表控制类
    protected $ExtTableDatatable;
    
    protected $validate_rule;
    
    protected $auto_type;
    
    protected $the_time;
    
    /**
     * 框架初始化
     */
    public function initialize() 
    {
        parent::initialize();
        
        $this->Form = new FormModel();
        $this->FormAttr = new FormAttrModel();
        $this->ExtTableDatatable = new Datatable('lform_ext_');
        
        $this->validate_rule = array(
            0            => '请选择',
            'regex'      => '正则验证',
            'function'   => '函数验证',
            'unique'     => '唯一验证',
            'length'     => '长度验证',
            'in'         => '验证在范围内',
            'notin'      => '验证不在范围内',
            'between'    => '区间验证',
            'notbetween' => '不在区间验证',
        );
        $this->auto_type = [
            0 => '请选择', 
            'function' => '函数', 
            'field' => '字段', 
            'string' => '字符串'
        ];
        $this->the_time  = [
            0 => '请选择', 
            '3' => '始 终', 
            '1' => '新 增', 
            '2' => '编 辑',
        ];
    }

    /**
     * 表单字段
     */
    public function index($form_id = '') 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $order = "sort ASC, id ASC";
            $data = $this->FormAttr
                ->where($map)
                ->order($order)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $this->FormAttr
                ->where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
            
        } else {
        
            // 表单详情
            $form = $this->Form->where([
                'id' => $form_id,
            ])->find();

            $data = [
                'form' => $form,
                'form_id' => $form_id,
            ];
            View::assign($data);
            return View::fetch();
            
        }
    }

    /**
     * 添加字段
     */
    public function add()
    {
        $form_id = request()->param('form_id', '');
        if (!$form_id) {
            return $this->error('非法操作！');
        }
        
        if (request()->isPost()) {
            $data = request()->post();
            $result = $this->FormAttr->save($data);
            if (false === $result) {
                return $this->error($this->FormAttr->getError());
            }
            
            return $this->success('添加成功！', url('admin/LformAttr/index?form_id='.$form_id));
        }else{
            $info = [
                'form_id'   => $form_id
            ];
            $fieldType = FieldTypeModel::order('listorder')
                ->column('name,title,default_define,ifoption,ifstring');
            $data = [
                'data'   => $info,
                'fieldType' => $fieldType,
                'validate_rule' => $this->validate_rule,
                'the_time' => $this->the_time,
            ];
            View::assign($data);
            return View::fetch();
        }
    }

    /**
     * 编辑表单字段
     */
    public function edit()
    {
        $param = $this->request->param();

        $form_id = isset($param['form_id']) ? $param['form_id'] : '';
        $id = isset($param['id']) ? $param['id'] : '';
        if (!$id) {
            return $this->error('非法操作！');
        }
        
        if (request()->isPost()) {
            $data = request()->post();
            
            $result = $this->FormAttr
                ->update($data, [
                    'id' => $data['id'],
                ]);
            if (false === $result) {
                return $this->error($this->FormAttr->getError());
            }
            
            return $this->success('修改成功！', url('admin/lformAttr/index?form_id='.$form_id));
        } else {
            $info = $this->FormAttr
                ->where([
                    'id' => $id,
                ])
                ->find();
            $fieldType = FieldTypeModel::order('listorder')
                ->column('name,title,default_define,ifoption,ifstring');
            $data = [
                'data' => $info,
                'fieldType' => $fieldType,
                'validate_rule' => $this->validate_rule,
                'the_time' => $this->the_time,
            ];
            View::assign($data);
            return View::fetch();
        }
    }

    /**
     * 删除表单字段
     */
    public function delete()
    {
        $id = request()->param('id', 0);
        if (!$id) {
            return $this->error('非法操作！');
        }
        
        // 字段信息
        $attr = $this->FormAttr
            ->where([
                'id' => $id,
            ])
            ->find();
        if (empty($attr)) {
            return $this->error('字段不存在！');
        }
        
        // 表单信息
        $form = $this->Form
            ->where([
                'id' => $attr['form_id'],
            ])
            ->find();
        if (empty($form)) {
            return $this->error('字段不存在！');
        }
        
        // 删除字段
        $result = $this->FormAttr
            ->where([
                'id' => $id,
            ])
            ->delete();
        if (false === $result) {
            return $this->error($this->FormAttr->getError());
        }
        
        // 删除字段
        $db = new Datatable('lform_ext_');
        if ($db->checkField($form['name'], $attr['name'])) {
            $db->delField($form['name'], $attr['name'])->query();
        }
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改字段状态
     */
    public function setStatus() 
    {
        $id = request()->param('id');
        $status = input('status', '0', 'trim,intval');

        if (!$id) {
            return $this->error("非法操作！");
        }

        $map['id'] = $id;
        $result = $this->FormAttr
            ->where($map)
            ->data([
                'status' => $status,
            ])
            ->update();
        if (false === $result) {
            return $this->error("设置失败！");
        }
        
        return $this->success("设置成功！");
    } 

    /**
     * 排序
     */
    public function listorder()
    {
        $id = $this->request->param('id/d', 0);
        $sort = $this->request->param('value/d', 0);
        $rs = $this->FormAttr
            ->where([
                'id' => $id, 
            ])
            ->update([
                'sort' => $sort,
            ]);
        if ($rs === false) {
            $this->error("字段排序失败！");
        }
        
        $this->success("字段排序成功！");
    }
    
}