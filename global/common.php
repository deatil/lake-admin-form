<?php

use think\facade\Db;
use think\facade\Session;

/**
 * 生成token
 *
 * @create 2020-1-17
 * @author deatil
 */
function lform_token($form_name = null) {
    if (empty($form_name)) {
        return '';
    }
    
    $form = Db::name('lform')->where([
        'name' => $form_name,
    ])->find();
    if (empty($form)) {
        return '';
    }
    
    if ($form['is_check_token'] != 1) {
        return '';
    }
    
    $csrf = token($form['check_token']);
    
    return $csrf;
}

/**
 * 检测
 *
 * @create 2020-1-17
 * @author deatil
 */
function lform_token_check($form_name = null) {
    if (empty($form_name)) {
        return false;
    }
    
    $form = Db::name('lform')->where([
        'name' => $form_name,
    ])->find();
    if (empty($form)) {
        return false;
    }
    
    if ($form['is_check_token'] != 1) {
        return true;
    }
    
    // 请求数据
    $token = request()->param($form['check_token']);
    
    // session记录数据
    $csrf_token = Session::get($form['check_token']);
    if ($csrf_token != $token) {
        return false;
    }
    
    return true;
}

function lform_get_grid_list($list_grids) {
    $grids = preg_split('/[;\r\n]+/s', trim($list_grids));
    foreach ($grids as &$value) {
        // 字段:标题:链接
        $val = explode(':', $value);
        // 支持多个字段显示
        $field = explode(',', $val[0]);
        $value = array('field' => $field, 'title' => $val[1]);
        if (isset($val[2])) {
            // 链接信息
            $value['href'] = $val[2];
            // 搜索链接信息中的字段信息
            preg_replace_callback('/\[([a-z_]+)\]/', function ($match) use (&$fields) {$fields[] = $match[1];}, $value['href']);
        }
        if (strpos($val[1], '|')) {
            // 显示格式定义
            list($value['title'], $value['format']) = explode('|', $val[1]);
        }
        foreach ($field as $val) {
            $array    = explode('|', $val);
            $fields[] = $array[0];
        }
    }
    $data = array('grids' => $grids, 'fields' => $fields);
    return $data;
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function lform_parse_field_attr($string) {
    if (0 === strpos($string, ':')) {
        // 采用函数定义
        return eval('return ' . substr($string, 1) . ';');
    } elseif (0 === strpos($string, '[')) {
        // 支持读取配置参数（必须是数组类型）
        return \think\Config::get(substr($string, 1, -1));
    }

    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

function lform_parse_field_bind($table, $selected = '', $model = 0) {
    $list = array();
    if ($table) {
        $res = db($table)->select()->toArray();
        foreach ($res as $key => $value) {
            if (($model 
                    && isset($value['model_id']) 
                    && $value['model_id'] == $model
                ) 
                || (isset($value['model_id']) 
                    && $value['model_id'] == 0
                )
            ) {
                $list[] = $value;
            } elseif(!$model) {
                $list[] = $value;
            }
        }
        if (!empty($list)) {
            $TTree = new \Lake\TTree();
            $listTree = $TTree->withData($list)->buildArray(0);
            $list = $TTree->buildFormatList($listTree, 'title');
        }
    }
    return $list;
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function lform_parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}
