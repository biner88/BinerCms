/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50622
Source Host           : localhost:3306
Source Database       : binercms

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2016-05-25 17:37:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for v1_system_access
-- ----------------------------
DROP TABLE IF EXISTS `v1_system_access`;
CREATE TABLE `v1_system_access` (
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '该值跟随t字段变化',
  `m` varchar(100) NOT NULL COMMENT '模块',
  `c` varchar(100) NOT NULL COMMENT '模块',
  `a` varchar(100) NOT NULL COMMENT '模块',
  `t` tinyint(1) NOT NULL DEFAULT '0' COMMENT '权限类型,0:角色,1:用户',
  KEY `m` (`m`) USING BTREE,
  KEY `c` (`c`) USING BTREE,
  KEY `a` (`a`) USING BTREE,
  KEY `t` (`t`) USING BTREE,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of v1_system_access
-- ----------------------------
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'upload', 'uploadone', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'menu', 'delete', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'menu', 'edit', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'menu', 'add', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'menu', 'index', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'log', 'item', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'log', 'index', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'index', 'cacheclear', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'index', 'index', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'group', 'access', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'group', 'delete', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'group', 'edit', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'group', 'add', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'group', 'index', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'ajax', 'checkuser', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'ajax', 'getmenu', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'ajax', 'opensource', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'ajax', 'clearcache', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'index', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'item', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'add', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'edit', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'access', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'setdefaultaccess', '0');
INSERT INTO `v1_system_access` VALUES ('1', 'admin', 'user', 'delete', '0');

-- ----------------------------
-- Table structure for v1_system_group
-- ----------------------------
DROP TABLE IF EXISTS `v1_system_group`;
CREATE TABLE `v1_system_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '名称',
  `pid` smallint(5) NOT NULL DEFAULT '0' COMMENT '上级',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否审核',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '说明备注',
  `ename` varchar(20) NOT NULL DEFAULT '' COMMENT '英文名称',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`) USING BTREE,
  KEY `ename` (`ename`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='角色分组表';

-- ----------------------------
-- Records of v1_system_group
-- ----------------------------
INSERT INTO `v1_system_group` VALUES ('1', '管理员', '0', '1', '管理员', '', '1208784792', '1440991580');

-- ----------------------------
-- Table structure for v1_system_log
-- ----------------------------
DROP TABLE IF EXISTS `v1_system_log`;
CREATE TABLE `v1_system_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户编号',
  `m` varchar(40) NOT NULL DEFAULT '' COMMENT '分组名称',
  `c` varchar(40) NOT NULL COMMENT '模型名称',
  `a` varchar(40) NOT NULL COMMENT '操作名称',
  `ip` char(15) NOT NULL COMMENT 'IP地址',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型',
  `created_time` int(10) unsigned NOT NULL,
  `remark` text NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '权限说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统日志表';

-- ----------------------------
-- Records of v1_system_log
-- ----------------------------

-- ----------------------------
-- Table structure for v1_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `v1_system_menu`;
CREATE TABLE `v1_system_menu` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '菜单项 ID',
  `parent_id` int(8) NOT NULL DEFAULT '0' COMMENT '父菜单 ID',
  `name` varchar(255) NOT NULL COMMENT '菜单显示出来的名称',
  `display` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(255) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '方法',
  `query_string` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '若直接设定一个访问地址，则覆盖模块、控制器、方法的访问方式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 COMMENT='导航表';

-- ----------------------------
-- Records of v1_system_menu
-- ----------------------------
INSERT INTO `v1_system_menu` VALUES ('1', '0', '系统中心', '1', '', '', '', '', 'fa-sliders', '0', '');
INSERT INTO `v1_system_menu` VALUES ('4', '1', '后台用户', '1', 'Admin', 'User', 'index', '', 'fa-user', '1', '');
INSERT INTO `v1_system_menu` VALUES ('58', '4', '用户列表', '1', 'Admin', 'user', 'index', 'index', '', '2', '');
INSERT INTO `v1_system_menu` VALUES ('59', '4', '新增用户', '1', 'Admin', 'user', 'add', '', '', '2', '');
INSERT INTO `v1_system_menu` VALUES ('60', '1', '角色管理', '1', '', '', '', '', 'fa-users', '2', '');
INSERT INTO `v1_system_menu` VALUES ('61', '60', '角色列表', '1', 'Admin', 'Group', 'index', '', '', '1', '');
INSERT INTO `v1_system_menu` VALUES ('62', '60', '新增角色', '1', 'Admin', 'Group', 'add', '', '', '1', '');
INSERT INTO `v1_system_menu` VALUES ('63', '1', '菜单管理', '1', 'Admin', 'menu', 'index', '', 'fa-list', '3', '');
INSERT INTO `v1_system_menu` VALUES ('64', '1', '日志管理', '1', 'Admin', 'Log', 'index', '', '', '4', '');
INSERT INTO `v1_system_menu` VALUES ('66', '63', '菜单列表', '1', 'admin', 'menu', 'index', '', '', '1', '');

-- ----------------------------
-- Table structure for v1_system_user
-- ----------------------------
DROP TABLE IF EXISTS `v1_system_user`;
CREATE TABLE `v1_system_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '唯一标识符',
  `account` varchar(40) NOT NULL COMMENT '用户名',
  `pass` char(40) NOT NULL DEFAULT '' COMMENT '密码的hash值',
  `pass_salt` char(6) NOT NULL DEFAULT '' COMMENT '密码干扰码',
  `pass_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '密码加密类型',
  `nickname` varchar(40) NOT NULL DEFAULT '' COMMENT '昵称',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '修改时间',
  `login_count` int(10) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `last_login_time` int(10) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:保密,1:男,2:女',
  `telphone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `birthday` int(10) NOT NULL DEFAULT '0' COMMENT '生日',
  `address` varchar(255) NOT NULL DEFAULT '',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '软删除时间戳',
  `group_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户组',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户定义表';

-- ----------------------------
-- Records of v1_system_user
-- ----------------------------
INSERT INTO `v1_system_user` VALUES ('1', 'super_admin', '2b129b70f961ec1abb0aebf5d9879430', 'a7c360', '4', '', '超级管理员', '1', '1428905215', '0', '33', '127.0.0.1', '1464163753', '', '', '0', '', '0', '', '0', '1');
INSERT INTO `v1_system_user` VALUES ('2', 'admin', '2b129b70f961ec1abb0aebf5d9879430', 'a7c360', '4', 'biner', '管理员', '1', '1428905215', '0', '61', '61.54.46.33', '1463986101', '', '', '0', '', '0', '', '0', '1');
