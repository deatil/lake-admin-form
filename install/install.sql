DROP TABLE IF EXISTS `pre__lform`;
CREATE TABLE `pre__lform` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '表单名称',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '表单标识',
  `description` char(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `list_grid` text COLLATE utf8mb4_unicode_ci COMMENT '列表定义',
  `is_check_token` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-token检测',
  `check_token` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '检测token',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自定义表单';

DROP TABLE IF EXISTS `pre__lform_attr`;
CREATE TABLE `pre__lform_attr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL COMMENT '所属模型',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段注释',
  `length` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段定义',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数据类型',
  `extra` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '参数',
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `show_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '显示类型，1-全部显示，2-添加显示，3-编辑显示，4-都不显示',
  `is_filter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '筛选字段',
  `is_must` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `is_list_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否列表显示',
  `is_detail_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否详情显示',
  `is_view` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否阅读量',
  `validate_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `validate_rule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `validate_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `error_info` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `auto_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `auto_rule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `auto_time` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模型属性表';


INSERT INTO `pre__lform` VALUES (1,'联系我们','contact','联系我们','id:ID\r\nname:姓名\r\ncontent:留言内容\r\n',1,'WYTfCylAWonfIaQ',1,1573053628,1579192287);
INSERT INTO `pre__lform_attr` VALUES (2,1,'status','数据状态','2','select','-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿','1','数据状态',1,0,1,0,0,0,0,'','',0,'','','',0,150,1,1573053628,1573053628),(3,1,'view','浏览数量','11','text','','0','浏览数量',1,0,1,0,0,0,1,'0','',0,'','','',0,150,1,1578980553,1573053628),(4,1,'update_time','更新时间','11','datetime','','0','更新时间',1,0,1,0,0,0,0,'','',0,'','','',0,100,1,1573053628,1573053628),(5,1,'create_time','添加时间','11','datetime','','0','添加时间',1,0,1,1,0,0,0,'0','',0,'','','',0,99,1,1573136993,1573053628),(6,1,'content','留言内容','500','textarea','','','留言内容',1,0,1,1,1,1,0,'0','',0,'','','',0,35,1,1579095167,1573133642),(10,1,'name','姓名','200','text','','','姓名',1,0,1,1,0,0,0,'regex','/^[\\x{4e00}-\\x{9fa5}0-9a-zA-Z\\_\\-]+$/u',0,'姓名不能为空','','',0,10,1,1579059006,1573135986),(11,1,'tel','电话','15','text','','','电话',1,0,1,1,1,1,0,'regex','/^(1)[0-9]{10}$/',0,'电话号码错误','','',0,20,1,1579094660,1578980372),(12,1,'email','邮箱','250','text','','','邮箱',1,1,0,1,1,1,0,'regex','/^[\\w\\-\\.]+@[\\w\\-\\.]+(\\.\\w+)+$/',0,'邮箱不能为空','','',0,25,1,1579061206,1578980426),(13,1,'adr','联系地址','500','textarea','','','联系地址',1,1,0,1,1,1,0,'0','',0,'','','',0,30,1,1579061216,1578980502);

DROP TABLE IF EXISTS `pre__lform_ext_contact`;
CREATE TABLE `pre__lform_ext_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `adr` varchar(500) DEFAULT NULL COMMENT '联系地址',
  `email` varchar(250) DEFAULT NULL COMMENT '邮箱',
  `tel` varchar(15) NOT NULL COMMENT '电话',
  `name` varchar(200) NOT NULL COMMENT '姓名',
  `content` varchar(500) NOT NULL COMMENT '留言内容',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `view` varchar(11) NOT NULL DEFAULT '0' COMMENT '浏览数量',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '数据状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='联系我们';

INSERT INTO `pre__lform_ext_contact` VALUES (8,'','','13345678911','编辑','留言内容',1579095185,1579095185,'0',1);
