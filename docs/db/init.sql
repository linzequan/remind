--
-- 数据库: `remind`
--
set names utf8;
create database `remind`;
use `remind`;

-- --------------------------------------------------------

--
-- 表的结构 `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `email_sys_queue`
--

CREATE TABLE IF NOT EXISTS `email_sys_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一自增标识',
  `content` text NOT NULL COMMENT '邮件内容',
  `priority` int(4) NOT NULL COMMENT '优先级，默认5，数字越小优先级越高',
  `status` int(4) NOT NULL COMMENT '状态 0未发送 1已发送 -1发送失败',
  `create_time` int(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统通知邮件队列表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sys_menu`
--

CREATE TABLE IF NOT EXISTS `sys_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '应用菜单ID',
  `pid` int(11) DEFAULT '0' COMMENT '上级菜单ID',
  `title` varchar(40) DEFAULT '' COMMENT '菜单标题',
  `ctrl_name` varchar(100) DEFAULT '' COMMENT '菜单访问控制器',
  `sort` tinyint(4) DEFAULT '0' COMMENT '菜单排序',
  `create_uname` varchar(40) DEFAULT '' COMMENT '创建者账号',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用菜单表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sys_user`
--

CREATE TABLE IF NOT EXISTS `sys_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统用户ID',
  `user_name` varchar(40) DEFAULT '' COMMENT '系统用户账号',
  `true_name` varchar(40) DEFAULT NULL COMMENT '真实姓名',
  `email` varchar(255) DEFAULT '' COMMENT '邮箱',
  `pwd` varchar(32) DEFAULT '' COMMENT '用户密码MD5',
  `is_admin` tinyint(4) DEFAULT '0' COMMENT '是否管理员0:非管理员,1:管理员',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '0:有效账户,1:无效账户',
  `create_uname` varchar(40) DEFAULT '' COMMENT '创建者账号',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `key_uname` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户表' ;

--
-- 转存表中的数据 `sys_user`
--

INSERT INTO `sys_user` (`user_id`, `user_name`, `true_name`, `email`, `pwd`, `is_admin`, `is_del`, `create_uname`, `create_time`) VALUES
(1, 'admin', '超级管理员', '', '9e3763ebc7a147d2a27222b763d7bd37', 1, 0, 'admin', '2016-07-04 14:18:07');

-- --------------------------------------------------------

--
-- 表的结构 `sys_user_pms`
--

CREATE TABLE IF NOT EXISTS `sys_user_pms` (
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '系统用户ID',
  `menu_id` int(11) NOT NULL DEFAULT '0' COMMENT '应用菜单ID',
  `pms` char(9) DEFAULT '' COMMENT '用户权限设置1:查询2:增加3:删除4:修改5:特殊',
  PRIMARY KEY (`user_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限表';
