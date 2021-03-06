<?php

namespace app\lform\service;

use think\facade\Db;

/**
 * 数据库管理类
 *
 * @create 2019-11-3
 * @author deatil
 */
class Datatable 
{

    protected $table; /*数据库操作的表*/
    protected $fields             = array(); /*数据库操作字段*/
    protected $charset            = 'utf8mb4'; /*数据库操作字符集*/
    public $prefix                = ''; /*数据库操作表前缀*/
    protected $model_table_prefix = ''; /*模型默认创建的表前缀*/
    protected $engine_type        = 'MyISAM'; /*数据库引擎*/
    protected $key                = 'id'; /*数据库主键*/
    public $sql                   = ''; /*最后生成的sql语句*/
    protected $typeAlist          = array(
        "text"     => "VARCHAR",
        "string"   => "VARCHAR",
        "password" => "VARCHAR",
        "textarea" => "TEXT",
        "bool"     => "INT",
        "select"   => "INT",
        "num"      => "INT",
        "decimal"   => "DECIMAL",
        "tags"     => "VARCHAR",
        "datetime" => "INT",
        "date"     => "INT",
        "editor"   => "TEXT",
        "Ueditor"   => "TEXT",
        "bind"     => "INT",
        "image"    => "INT",
        "images"   => "VARCHAR",
        "attach"   => "VARCHAR",
    );

    /**
     * 初始化数据库信息
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function __construct($model_table_prefix = '', $prefix = '') 
    {
        $dbPrefix = app()->db->connect()->getConfig('prefix');
        $this->prefix = !empty($prefix) ? $prefix : $dbPrefix;
        $this->model_table_prefix = $model_table_prefix;
    }

    /**
     * 设置表名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function setTable($table = '') 
    {
        $this->table = $this->getTablename($table, true);
        return $this;
    }

    /**
     * 设置字段类型列表
     *
     * @create 2019-11-7
     * @author deatil
     */
    public function setTypeAlist($typeAlist = []) 
    {
        $this->typeAlist = $typeAlist;
        return $this;
    }

    /**
     * 创建表
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function createTable(
        $table = '', 
        $comment = '', 
        $pk = 'id'
    ) {
        $this->setTable($table);

        $sql = $this->generateField($pk, 'int', 11, '', '主键', true);

        $primary = $pk ? "PRIMARY KEY (`" . $pk . "`)" : '';
        $generatesql = $sql . ',';

        $create = "CREATE TABLE IF NOT EXISTS `" . $this->table . "`("
        . $generatesql
        . $primary
        . ") ENGINE=" . $this->engine_type . " AUTO_INCREMENT=1 DEFAULT CHARSET=" . $this->charset . " ROW_FORMAT=DYNAMIC COMMENT='" . $comment . "';";
        $this->sql = $create;
        return $this;
    }

    /**
     * 快速创建ID字段
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function generateField(
        $key = '', 
        $type = '', 
        $length = 11, 
        $default = '', 
        $comment = '主键', 
        $is_auto_increment = false
    ) {
        if ($key && $type) {
            $auto_increment = $is_auto_increment ? 'AUTO_INCREMENT' : '';
            $field_type     = $length ? $type . '(' . $length . ')' : $type;
            $signed         = in_array($type, array('int', 'float', 'double')) ? 'signed' : '';
            $comment        = $comment ? "COMMENT '" . $comment . "'" : "";
            $default        = $default ? "DEFAULT '" . $default . "'" : "";
            $sql            = "`{$key}` {$field_type} {$signed} NOT NULL {$default} $auto_increment {$comment}";
        }
        return $sql;
    }

    /**
     * 追加字段
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function columField($table, $attr = []) 
    {
        $field_attr['table'] = $table ? $this->getTablename($table, true) : $this->table;
        $field_attr['name'] = $attr['name'];

        if (empty($field_attr['define'])) {
            $field_attr['type']  = $attr['type'] ? $this->typeAlist[$attr['type']] : 'varchar';
            if (intval($attr['length']) && $attr['length']) {
                $field_attr['length'] = "(" . $attr['length'] . ")";
            } else {
                $field_attr['length'] = "";
            }
            $field_attr['is_null'] = $attr['is_must'] ? 'NOT NULL' : 'NULL';
            $field_attr['default'] = $attr['value'] != '' ? 'DEFAULT "' . $attr['value'] . '"' : '';
            
            $field_attr['define'] = "{$field_attr['type']}{$field_attr['length']} {$field_attr['is_null']} {$field_attr['default']}";
        }

        $field_attr['comment'] = (isset($attr['remark']) && $attr['remark']) ? $attr['remark'] : $attr['title'];
        $field_attr['after']   = (isset($attr['after']) && $attr['after']) ? ' AFTER `' . $attr['after'] . '`' : ' AFTER `id`';
        $field_attr['action']  = (isset($attr['action']) && $attr['action']) ? $attr['action'] : 'ADD';
        
        // 执行方式
        if ($field_attr['action'] == 'ADD') {
        $this->sql = "ALTER TABLE `{$field_attr['table']}` ADD `{$field_attr['name']}` {$field_attr['define']} COMMENT '{$field_attr['comment']}' {$field_attr['after']}";
        } elseif ($field_attr['action'] == 'CHANGE') {
            $field_attr['oldname'] = (isset($attr['oldname']) && $attr['oldname']) ? $attr['oldname'] : '';

            $this->sql = "ALTER TABLE `{$field_attr['table']}` CHANGE `{$field_attr['oldname']}` `{$field_attr['name']}` {$field_attr['define']} COMMENT '{$field_attr['comment']}'";
        }
        return $this;
    }

    /**
     * 删除字段
     * @var $table 表名
     * @var $field 字段名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function delField($table, $field) 
    {
        $table = $table ? $this->getTablename($table, true) : $this->table;
        $this->sql = "ALTER TABLE `$table` DROP `$field`";
        return $this;
    }

    /**
     * 删除数据表
     * @var $table 表名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function delTable($table) 
    {
        $table = $table ? $this->getTablename($table, true) : $this->table;
        $this->sql = "DROP TABLE IF EXISTS `$table`";
        return $this;
    }

    /**
     * 更新数据表
     * @var $table 表名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function updateTableName($old_table = '', $new_table = '') 
    {
        if (!empty($old_table) && !empty($new_table)) {
            $old_table = $this->getTablename($old_table, true);
            $new_table = $this->getTablename($new_table, true);
            $this->sql = "RENAME TABLE  `".$old_table."` TO  `".$new_table."` ;";
        }
        
        return $this;
    }

    /**
     * 结束表
     * @var $engine_type 数据库引擎
     * @var $comment 表注释
     * @var $charset 数据库编码
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function endTable(
        $comment, 
        $engine_type = null, 
        $charset = null
    ) {
        if (null != $charset) {
            $this->charset = $charset;
        }
        if (null != $engine_type) {
            $this->engine_type = $engine_type;
        }
        $end = "ENGINE=" . $this->engine_type . " AUTO_INCREMENT=1 DEFAULT CHARSET=" . $this->charset . " ROW_FORMAT=DYNAMIC COMMENT='" . $comment . "';";
        $this->sql .= ")" . $end;
        return $this;
    }

    /**
     * create的别名
     * @return boolen 
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function query() 
    {
        if (empty($this->sql)) {
            return false;
        }
        
        $res = Db::execute($this->sql);
        return $res !== false;
    }

    /**
     * 创建动作
     * @return boolen 
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function create() 
    {
        return $this->query();
    }

    /**
     * 获取最后生成的sql语句
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function getLastSql() 
    {
        return $this->sql;
    }

    /**
     * 获取指定的表名
     * @var $table 要获取名字的表名
     * @var $prefix 获取表前缀, 默认为不获取 false
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function getTablename($table, $prefix = false) 
    {
        if (false == $prefix) {
            $this->table = $this->model_table_prefix . $table;
        } else {
            $this->table = $this->prefix . $this->model_table_prefix . $table;
        }
        return $this->table;
    }

    /**
     * 获取指定表名的所有字段及详细信息
     * @var $table 要获取名字的表名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function getFields($table) 
    {
        if (false == $table) {
            $table = $this->table; //为空调用当前table
        } else {
            $table = $table;
        }
        $patten = "/\./";
        if (!preg_match_all($patten, $table)) {
            //匹配_
            $patten = "/_+/";
            if (!preg_match_all($patten, $table)) {
                $table = $this->prefix . $this->model_table_prefix . $table;
            } else {
                //匹配是否包含表前缀，如果是 那么就是手动输入
                $patten = "/$this->prefix/";
                if (!preg_match_all($patten, $table)) {
                    $table = $this->prefix . $table;
                }
            }
        }
        $sql = "SHOW FULL FIELDS FROM $table";
        return Db::query($sql);
    }

    /**
     * 确认表是否存在
     * @var $table 表名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function checkTable($table) 
    {
        //获取表名
        $this->table = $this->getTablename($table, true);
        $result      = Db::execute("SHOW TABLES LIKE '%$this->table%'");
        return $result;
    }

    /**
     * 确认字段是否存在
     * @var $table 表名 
     * @var $field 字段名 要检查的字段名
     *
     * @create 2019-11-3
     * @author deatil
     */
    public function checkField($table, $field) 
    {
        // 检查字段是否存在
        $table = $this->getTablename($table, true);
        if (!Db::query("Describe $table $field")) {
            return false;
        } else {
            return true;
        }
    }
}