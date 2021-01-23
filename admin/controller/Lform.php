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
 * @create 2019-11-5
 * @author deatil
 */
class Lform extends LformBase 
{    
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
     * 添加表单
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, 'lform/Form');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            if ($data['is_check_token'] == 1 
                && empty($data['check_token'])
            ) {
                $data['check_token'] = lake_get_random_string(15);
            }
            
            $result = $this->Form
                ->save($data);
            if (false === $result) {
                return $this->error($this->Form->getError());
            }
            
            return $this->success('添加成功！', url('admin/lform/index'));
        } else {
            return View::fetch();
        }
    }

    /**
     * 编辑表单
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, 'lform/Form');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = $this->Form->where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('表单不存在');
            }
            
            if ($info['name'] != $data['name']) {
                $this->ExtTableDatatable->updateTableName($info['name'], $data['name'])->query();
            }
            
            $data = request()->post();
            if ($data['is_check_token'] == 1 
                && empty($data['check_token'])
            ) {
                $data['check_token'] = lake_get_random_string(15);
            }
            
            $result = $this->Form
                ->where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error($this->Form->getError());
            }
            
            return $this->success('修改成功！', url('admin/lform/index'));
        } else {
            $info = $this->Form->where([
                'id' => request()->param('id'),
            ])->find();
            $data = array(
                'info'    => $info,
            );
            View::assign($data);
            return View::fetch();
        }
    }

    /**
     * 删除表单
     */
    public function delete($id = '') 
    {
        $form = $this->Form->where([
            'id' => $id,
        ])->find();
        if (empty($form)) {
            return $this->error('表单不存在！');
        }
        
        $tablename = $this->ExtTableDatatable->getTablename(strtolower($form['name']));
        
        $data_count = Db::name($tablename)->count();
        if ($data_count > 0) {
            return $this->error('表单有数据，不能进行删除！');
        }
        
        $this->ExtTableDatatable->delTable(strtolower($form['name']))->query();
        
        Db::name('lform_attr')->where([
            'form_id' => $id,
        ])->delete();
        
        $result = $this->Form->where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改状态
     */
    public function setstate() 
    {
        $id = request()->param('id');
        $status = input('status', '0', 'trim,intval');

        if (!$id) {
            return $this->error("非法操作！");
        }

        $map['id'] = $id;
        $result = $this->Form
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
    
}