<?php

namespace app\lform\controller;

use think\facade\Db;
use think\facade\Hook;
use think\facade\Session;
use think\facade\View;

use app\lform\model\Form as FormModel;
use app\lform\model\FormAttr as FormAttrModel;

use app\lform\service\Datatable;

use Lake\Module\Controller\HomeBase;

/**
 * 表单
 *
 * @create 2019-11-7
 * @author deatil
 */
class Index extends HomeBase
{
    /**
     * 表单提交
     *
     * @create 2019-11-7
     * @author deatil
     */
    public function post()
    {
        if (!request()->isPost()) {
            return $this->error('非法请求！');
        }
        
        $data = request()->param();
        
        $form_name = request()->param('form');
        if (empty($form_name)) {
            return $this->error('非法请求！');
        }
        
        // 添加检测hook
        Hook::listen('lform_post_start');
        
        $csrf = lform_token_check($form_name);
        if (!$csrf) {
            return $this->error('非法请求！');
        }
        
        $form = (new FormModel())->where('name', $form_name)->find();
        if (empty($form)) {
            return $this->error('非法请求！');
        }
    
        if ($form['status'] != 1) {
            return $this->error('非法请求！');
        }
    
        $form_attrs = (new FormAttrModel())
            ->where([
                'form_id' => $form['id'],
                'is_show' => 1,
            ])
            ->order('sort ASC')
            ->column('name');
        if (empty($form_attrs)) {
            return $this->error('非法请求1！');
        }
        
        unset($data['id']);
        if (empty($data)) {
            return $this->error('请求数据不能为空！');
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
        
        $form_rule_attrs = (new FormAttrModel())
            ->where([
                'form_id' => $form['id'],
                'is_show' => 1,
            ])
            ->order('sort ASC')
            ->select()
            ->toArray();
            
        // 字段规则
        $fieldRule = Db::name('field_type')
            ->column('vrule,pattern', 'name');
    
        if (!empty($form_rule_attrs)) {
            foreach ($form_rule_attrs as $item) {
                $name = $item['name'];
                $type = $item['type'];
                $title = $item['title'];
                
                //查看是否赋值
                if (!isset($data[$name])) {
                    switch ($type) {
                        // 开关
                        case 'switch':
                            $data[$name] = 0;
                            break;
                        case 'checkbox':
                            $data[$name] = '';
                            break;
                    }
                } else {
                    // 如果值是数组则转换成字符串，适用于复选框等类型
                    if (is_array($data[$name])) {
                        $data[$name] = implode(',', $data[$name]);
                    }
                    switch ($type) {
                        // 开关
                        case 'switch':
                            $data[$name] = 1;
                            break;
                    }
                }

                // 数据格式验证
                if (isset($fieldRule[$type]['vrule']) 
                    && !empty($fieldRule[$type]['vrule'])
                    && !empty($data[$name]) 
                ) {
                    if (!empty($fieldRule[$type]['pattern'])) {
                        if (!call_user_func_array(['Validate', $fieldRule[$type]['vrule']], [
                            $data[$name],
                            $fieldRule[$type]['pattern']
                        ])) {
                            return $this->error("'" . $title . "'格式错误~");
                        }
                    } else {
                        if (!call_user_func_array(['Validate', $fieldRule[$type]['vrule']], [
                            $data[$name]
                        ])) {
                            return $this->error("'" . $title . "'格式错误~");
                        }
                    }
                }
                
                if (isset($data[$name])) {
                    if (isset($item['is_must']) 
                        && $item['is_must'] == 1
                    ) {
                        if (empty($data[$name])) {
                            return $this->error("'" . $title . "'为必填项~");
                        }
                        
                        // 数据格式验证
                        if (isset($item['validate_type']) 
                            && !empty($item['validate_type'])
                            && !empty($data[$name]) 
                        ) {
                            if (!empty($item['error_info'])) {
                                $errorInfo = $item['error_info'];
                            } else {
                                $errorInfo = "'" . $title . "'格式错误~";
                            }
                            
                            if (!empty($item['validate_rule'])) {
                                if (!call_user_func_array(['Validate', $item['validate_type']], [
                                    $data[$name],
                                    $item['validate_rule']
                                ])) {
                                    return $this->error($errorInfo);
                                }
                            } else {
                                if (!call_user_func_array(['Validate', $item['validate_type']], [
                                    $data[$name]
                                ])) {
                                    return $this->error($errorInfo);
                                }
                            }
                        }
                    }
                    
                }
                
            }
        }
        
        $tablename = (new Datatable('lform_ext_'))->getTablename(strtolower($form['name']));
        
        $result = db($tablename)->insert($data);
        if ($result === false) {
            return $this->error('提交失败，请稍后重试！');
        }
        
        return $this->success('提交成功！');
    }

}
