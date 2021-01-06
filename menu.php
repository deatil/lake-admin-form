<?php
return [
    [
        //地址，[模块/]控制器/方法
        "route" => "admin/Lform/index",
        // 请求方式，大写
        "method" => "GET",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 2,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "is_menu" => 1,
        //名称
        "title" => "表单管理",
        //图标
        "icon" => "icon-shiyongwendang",
        //备注
        "tip" => "",
        //排序
        "listorder" => 115,
        //子菜单列表
        "child" => [
            [
                "route" => "admin/Lform/index",
                "method" => "GET",
                "type" => 1,
                "is_menu" => 1,
                "title" => "表单管理",
                "icon" => "icon-shiyongwendang",
                "listorder" => 5,
                "child" => [
                    [
                        "route" => "admin/Lform/add",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "添加表单",
                        "child" => [
                            [
                                "route" => "admin/Lform/add",
                                "method" => "POST",
                                "type" => 1,
                                "is_menu" => 0,
                                "title" => "添加表单",
                            ],
                        ],
                    ],
                    [
                        "route" => "admin/Lform/edit",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "编辑表单",
                        "child" => [
                            [
                                "route" => "admin/Lform/edit",
                                "method" => "POST",
                                "type" => 1,
                                "is_menu" => 0,
                                "title" => "编辑表单",
                            ],
                        ],
                    ],
                    [
                        "route" => "admin/Lform/delete",
                        "method" => "POST",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "删除表单",
                    ],
                    [
                        "route" => "admin/Lform/status",
                        "method" => "POST",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "表单状态",
                    ],
                ],
            ],
            [
                "route" => "admin/LformContent/index",
                "method" => "GET",
                "type" => 1,
                "is_menu" => 1,
                "title" => "表单数据",
                "icon" => "icon-shiyongwendang",
                "listorder" => 10,
                "child" => [
                    [
                        "route" => "admin/LformContent/list",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "表单数据列表",
                    ],
                    [
                        "route" => "admin/LformContent/detail",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "添加表单数据",
                    ],
                    [
                        "route" => "admin/LformContent/delete",
                        "method" => "POST",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "删除表单数据",
                    ],
                ],
            ],
            [
                "route" => "admin/LformAttr/index",
                "method" => "GET",
                "type" => 1,
                "is_menu" => 0,
                "title" => "字段列表",
                "icon" => "icon-shiyongwendang",
                "listorder" => 15,
                "child" => [
                    [
                        "route" => "admin/LformAttr/index",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "字段列表",
                    ],
                    [
                        "route" => "admin/LformAttr/add",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "添加字段",
                        "child" => [
                            [
                                "route" => "admin/LformAttr/add",
                                "method" => "POST",
                                "type" => 1,
                                "is_menu" => 0,
                                "title" => "添加字段",
                            ],
                        ],
                    ],
                    [
                        "route" => "admin/LformAttr/edit",
                        "method" => "GET",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "编辑字段",
                        "child" => [
                            [
                                "route" => "admin/LformAttr/edit",
                                "method" => "POST",
                                "type" => 1,
                                "is_menu" => 0,
                                "title" => "编辑字段",
                            ],
                        ],
                    ],
                    [
                        "route" => "admin/LformAttr/delete",
                        "method" => "POST",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "删除字段",
                    ],
                    [
                        "route" => "admin/LformAttr/setStatus",
                        "method" => "POST",
                        "type" => 1,
                        "is_menu" => 0,
                        "title" => "字段状态",
                    ],
                ],
            ],
        ],
    ],
];
