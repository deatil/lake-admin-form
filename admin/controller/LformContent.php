<?php

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;

use app\lform\service\Datatable;

use app\lform\model\Form as FormModel;
use app\lform\model\FormAttr as FormAttrModel;

/**
 * 自定义表单
 *
 * @create 2019-11-3
 * @author deatil
 */
class LformContent extends LformBase 
{
    protected $module = 'lform';
    
    // 表单
    protected $Form;
    
    // 表单配置
    protected $FormAttr;
    
    // 表单创建表控制类
    protected $ExtTableDatatable;
    
    /**
     * 框架初始化
     */
    public function initialize() 
    {
        parent::initialize();
        
        $this->Form = new FormModel();
        $this->FormAttr = new FormAttrModel();
        $this->ExtTableDatatable = new Datatable('lform_ext_');
    }

    /**
     * 表单列表
     */
    public function index() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $order = "id DESC";
            $data = $this->Form
                ->where($map)
                ->order($order)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $this->Form
                ->where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {            
            return View::fetch();
        }
    }
    
    /**
     * 数据列表
     */
    public function list($form_id = '') 
    {
        $form = $this->Form->where([
            'id' => $form_id,
        ])->find();
        
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $order = "id DESC";
            
            $tablename = $this->ExtTableDatatable
                ->getTablename(strtolower($form['name']));
            
            $data = Db::name($tablename)
                ->where($map)
                ->order($order)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = Db::name($tablename)
                ->where($map)
                ->count();
                
            $form_attrs = $this->FormAttr
                ->where([
                    'form_id' => $form_id,
                ])
                ->order('sort ASC')
                ->column('name,type', 'name');
                
            if (!empty($data)) {
                foreach ($data as $k => $v) {
                    if (!empty($form_attrs)) {
                        foreach ($form_attrs as $form_attr_name => $form_attr) {
                            if (isset($v[$form_attr_name]) && $form_attr == 'datetime') {
                                $data[$k][$form_attr_name] = date('Y-m-d H:i:s', $v[$form_attr_name]);
                            }
                        }
                    }
                }
            }

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {            
            $form_attr = $this->FormAttr
                ->where([
                    'form_id' => $form_id,
                ])
                ->order('sort ASC')
                ->select()
                ->toArray();

            $form_attrs = [];
            if (!empty($form_attr)) {
                foreach ($form_attr as $val) {
                    if ($val['is_show'] == 1) {
                        $form_attrs[] = $val['name'];
                    }
                }
            }
            
            $grid_list = lform_get_grid_list($form['list_grid']);

            $data = [
                'form_id'  => $form_id,
                'form_attrs'  => $form_attrs,
                'grid'  => $grid_list,
            ];
            View::assign($data);

            return View::fetch();
        }
        
    }

    /**
     * 数据详情
     */
    public function detail($form_id = '', $id = '')
    {
        $form = $this->Form->where([
            'id' => $form_id,
        ])->find();
        
        $form_attrs = $this->FormAttr
            ->where([
                'form_id' => $form_id,
            ])
            ->order('sort ASC')
            ->column('*', 'name');
            
        $form_attr_names = [];
        if (!empty($form_attrs)) {
            foreach ($form_attrs as $val) {
                if ($val['is_show'] == 1) {
                    $form_attr_names[] = $val['name'];
                }
            }
        }
        
        $tablename = $this->ExtTableDatatable->getTablename(strtolower($form['name']));
        $info = Db::name($tablename)
            ->where('id', $id)
            ->find();

        $data = [
            'form_id' => $form_id,
            'form_attr_names' => $form_attr_names,
            'form_attrs' => $form_attrs,
            'info' => $info,
        ];
        View::assign($data);

        return View::fetch();
    }

    /**
     * 删除数据
     */
    public function delete($form_id = '', $id = '')
    {
        $form = $this->Form->where([
            'id' => $form_id,
        ])->find();

        $tablename = $this->ExtTableDatatable->getTablename(strtolower($form['name']));
        $result = Db::name($tablename)->where([
            'id' => $id,
        ])->delete();

        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        return $this->success('删除成功！');
    }
    
}