<?php

namespace app\lform\service;

use app\lform\model\Form as FormModel;
use app\lform\model\FormAttr as FormAttrModel;

/**
 * 表单提交
 *
 * @create 2019-11-8
 * @author deatil
 */
class Content
{

    /**
     * 内容列表
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function lists($data = [])
    {
        $page = isset($data['page']) ? $data['page'] : 1;
        $pageSize = isset($data['limit']) ? $data['limit'] : 25;
        
        $form_name = isset($data['form']) ? $data['form'] : '';
        if (empty($form_name)) {
            return false;
        }
        
        $form = (new FormModel())->where('name', $form_name)->find();
        if (empty($form)) {
            return false;
        }
        
        $attributes = array('id');
        $attribute_list = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_list_show' => 1,
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
        $attr_types = array();
        if (!empty($attribute_list)) {
            foreach ($attribute_list as $val) {
                $attributes[] = $val['name'];
                $attr_types[$val['name']] = $val['type'];
            }
        }
            
        $map = array();
        $map['status'] = 1;
        
        $sort = request()->param('sort', 'DESC');
        $sort = strtoupper($sort);
        if (!in_array($sort, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }
        
        if (isset($attr_types['is_top'])) {
            $order = 'is_top ' . $sort . ', id ' . $sort;
        } else {
            $order = 'id ' . $sort;
        }

        $data = db($model)
            ->field($attributes)
            ->where($map)
            ->order($order)
            ->paginate([
                'list_rows' => $pageSize, 
                'page' => $page,
            ])
            ->toArray();

        $list = array();
        if (!empty($data['data'])) {
            foreach($data['data'] as $key => $value){
                if (!empty($attr_types)) {
                    foreach ($attr_types as $attr_type_key => $attr_type) {
                        if (isset($value[$attr_type_key]) && $attr_type == 'image') {
                            $value[$attr_type_key] = lake_get_attachment_path($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'images') {
                            $value[$attr_type_key] = lake_get_attachment_list($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'file') {
                            $value[$attr_type_key] = lake_get_attachment_path($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'files') {
                            $value[$attr_type_key] = lake_get_attachment_list($value[$attr_type_key], true);
                        }
                    }
                }
                
                $list[] = $value;
            }
        }

        $data['list'] = $list;
        unset($data['data']);
        return $data;
    }

    /**
     * 内容详情
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function detail($data = [])
    {
        $model = isset($data['model']) ? $data['model'] : 'Article';
        if (empty($model)) {
            return false;
        }
        
        $model_info = (new FormModel())->where([
            'name' => $model,
        ])->find();
        if (empty($model_info)) {
            return false;
        }
        
        $attributes = array('id');
        $attribute_list = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_detail_show' => 1,
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
        $attr_types = array();
        if (!empty($attribute_list)) {
            foreach ($attribute_list as $val) {
                $attributes[] = $val['name'];
                $attr_types[$val['name']] = $val['type'];
            }
        }
        
        if (!isset($data['id']) || empty($data['id'])) {
            return false;
        }
        
        $id = $data['id'];
        
        $info = db($model)
            ->field($attributes)
            ->where([
                'id' => $id,
                'status' => 1,
            ])
            ->find();
        if (empty($info)) {
            return false;
        }    
        
        // 增加阅读量
        $attr_view = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_view' => 1,
            ])
            ->order('sort ASC, id DESC')
            ->find();
        if (!empty($attr_view)) {
            db($model)->where([
                'id' => $id,
            ])->setInc($attr_view['name'], 1);
        }

        if (isset($info['content'])) {
            $info['content'] = str_replace(
                '/uploads/', 
                request()->domain() . '/uploads/', 
                $info['content']
            );
        }        
        
        if (!empty($attr_types)) {
            foreach ($attr_types as $attr_type_key => $attr_type) {
                if (isset($info[$attr_type_key]) && $attr_type == 'image') {
                    $info[$attr_type_key] = lake_get_attachment_path($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'images') {
                    $info[$attr_type_key] = lake_get_attachment_list($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'file') {
                    $info[$attr_type_key] = lake_get_attachment_path($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'files') {
                    $info[$attr_type_key] = lake_get_attachment_list($info[$attr_type_key], true);
                }
            }
        }
        
        return $info;
    }

    /**
     * 取列表单独一天
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function info($data = [])
    {
        $model = isset($data['model']) ? $data['model'] : 'Article';
        if (empty($model)) {
            return false;
        }
        
        $model_info = (new FormModel())->where([
            'name' => $model,
        ])->find();
        if (empty($model_info)) {
            return false;
        }
        
        $attributes = array('id');
        $attribute_list = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_detail_show' => 1,
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
        $attr_types = array();
        if (!empty($attribute_list)) {
            foreach ($attribute_list as $val) {
                $attributes[] = $val['name'];
                $attr_types[$val['name']] = $val['type'];
            }
        }
        
        if (isset($attr_types['is_top'])) {
            $order = 'is_top DESC, id DESC';
        } else {
            $order = 'id DESC';
        }
        
        $info = db($model)
            ->field($attributes)
            ->order($order)
            ->where([
                'status' => 1,
            ])
            ->find();
        if (empty($info)) {
            return false;
        }    
        
        // 增加阅读量
        $attr_view = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_view' => 1,
            ])
            ->order('sort ASC, id DESC')
            ->find();
        if (!empty($attr_view)) {
            db($model)->where([
                'id' => $info['id'],
            ])->setInc($attr_view['name'], 1);
        }

        if (isset($info['content'])) {
            $info['content'] = str_replace(
                '/uploads/', 
                request()->domain() . '/uploads/', 
                $info['content']
            );
        }        
        
        if (!empty($attr_types)) {
            foreach ($attr_types as $attr_type_key => $attr_type) {
                if (isset($info[$attr_type_key]) && $attr_type == 'image') {
                    $info[$attr_type_key] = lake_get_attachment_path($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'images') {
                    $info[$attr_type_key] = lake_get_attachment_list($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'file') {
                    $info[$attr_type_key] = lake_get_attachment_path($info[$attr_type_key], true);
                }
                if (isset($info[$attr_type_key]) && $attr_type == 'files') {
                    $info[$attr_type_key] = lake_get_attachment_list($info[$attr_type_key], true);
                }
            }
        }
        
        return $info;
    }
    
    /**
     * 上一页
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function prev($data = [])
    {
        if (empty($data['id'])) {
            return false;
        }
        $id = $data['id'];
        
        if (empty($data['model'])) {
            return false;
        }
        $model = $data['model'];
    
        $where = 'id < '.$id;
        $order = 'id DESC';
        $this->data['data'] = $this->pre_next($model, $id, $where, $order);
        return $this->data;
    }    
    
    /**
     * 下一页
     *
     * @create 2019-11-8
     * @author deatil
     */
    public function next($data = [])
    {
        if (empty($data['id'])) {
            return false;
        }
        $id = $data['id'];
        
        if (empty($data['model'])) {
            return false;
        }
        $model = $data['model'];

        $where = 'id > '.$id;
        $order = 'id ASC';
        $this->data['data'] = $this->pre_next($model, $id, $where, $order);
        return $this->data;
    }
    
    /**
     * 上一页
     *
     * @create 2019-11-8
     * @author deatil
     */
    protected function pre_next($model = null, $id = null, $where, $order)
    {    
        $model_info = (new FormModel())->where([
            'name' => $model,
        ])->find();
        if (empty($model_info)) {
            return [];
        }
        
        $attributes = array('id');
        $attribute_list = (new FormAttrModel())
            ->where([
                'model_id' => $model_info['id'],
                'is_list_show' => 1,
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
        $attr_types = array();
        if (!empty($attribute_list)) {
            foreach ($attribute_list as $val) {
                $attributes[] = $val['name'];
                $attr_types[$val['name']] = $val['type'];
            }
        }
            
        $map = array();
        $map['status'] = 1;
        
        $category_id = request()->param('catid', 0);
        if ($category_id) {
            $map['category_id'] = $category_id;
        }

        $data = db($model)
            ->field($attributes)
            ->where($where)
            ->where($map)
            ->order($order)
            ->paginate([
                'list_rows' => 1, 
                'page' => 1,
            ])
            ->toArray();

        $list = array();
        if (!empty($data['data'])) {
            foreach($data['data'] as $key => $value){
                if (!empty($attr_types)) {
                    foreach ($attr_types as $attr_type_key => $attr_type) {
                        if (isset($value[$attr_type_key]) && $attr_type == 'image') {
                            $value[$attr_type_key] = lake_get_attachment_path($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'images') {
                            $value[$attr_type_key] = lake_get_attachment_list($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'file') {
                            $value[$attr_type_key] = lake_get_attachment_path($value[$attr_type_key], true);
                        }
                        if (isset($value[$attr_type_key]) && $attr_type == 'files') {
                            $value[$attr_type_key] = lake_get_attachment_list($value[$attr_type_key], true);
                        }
                    }
                }
                
                $list[] = $value;
            }
        }

        if (!empty($list[0])) {
            $info = $list[0];
        } else {
            $info = '';
        }
        
        return $info;
    }    
}
