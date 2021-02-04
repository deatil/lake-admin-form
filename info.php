<?php

return array(
    // 模块ID[必填]
    'module' => 'lform',
    // 模块名称[必填]
    'name' => '自定义表单',
    // 模块简介[选填]
    'introduce' => '自定义表单模块',
    // 模块作者[选填]
    'author' => 'deatil',
    // 作者地址[选填]
    'authorsite' => 'github.com/deatil',
    // 作者邮箱[选填]
    'authoremail' => 'deatil@github.com',
    // 版本号[必填]
    'version' => '2.0.3',
    // 适配最低系统版本[必填]
    'adaptation' => '2.3.27',
    
    // 模块地址
    'path' => __DIR__,
    
    // 依赖模块
    'need_module' => [],
    
    // 事件
    'event' => [],
    
    // 菜单
    'menus' => include __DIR__ . '/menu.php',
);
