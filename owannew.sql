-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-08-23 17:19:59
-- 服务器版本： 5.6.44
-- PHP 版本： 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `owannew`
--

-- --------------------------------------------------------

--
-- 表的结构 `owan_auth_group`
--

CREATE TABLE `owan_auth_group` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `owan_auth_group`
--

INSERT INTO `owan_auth_group` (`id`, `title`, `status`, `rules`) VALUES
(1, '普通用户', 1, '1,2,3,16,21,23');

-- --------------------------------------------------------

--
-- 表的结构 `owan_auth_rule`
--

CREATE TABLE `owan_auth_rule` (
  `id` mediumint(8) UNSIGNED NOT NULL COMMENT 'ID',
  `pid` mediumint(8) UNSIGNED ZEROFILL NOT NULL COMMENT '父ID',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `type` tinyint(1) UNSIGNED ZEROFILL NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `controller` varchar(255) DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `condition` varchar(255) DEFAULT NULL,
  `href` varchar(255) DEFAULT NULL COMMENT '链接',
  `sort` int(5) UNSIGNED ZEROFILL NOT NULL COMMENT '排序'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `owan_auth_rule`
--

INSERT INTO `owan_auth_rule` (`id`, `pid`, `icon`, `title`, `type`, `status`, `controller`, `condition`, `href`, `sort`) VALUES
(1, 00000000, 'icon-xitong', '系统设置', 1, 1, '', '', 'system', 00000),
(2, 00000001, 'icon-ic_opt_feature', '权限管理', 1, 1, '', '', 'Jurisdiction', 00000),
(3, 00000002, 'icon-iconset0194', '菜单列表', 1, 1, 'admin/System/menu', NULL, '/admin.php/System/menu', 00001),
(5, 00000001, 'icon-wangzhan', '网站管理', 1, 1, '', NULL, 'website', 00000),
(6, 00000000, 'icon-neirong', '内容管理', 1, 1, '', NULL, 'content', 00000),
(7, 00000006, '&#xe620;', '文章管理', 1, 1, '', NULL, 'article', 00000),
(8, 00000007, '&#xe620;', '文章列表', 1, 1, 'admin/System/index', NULL, '/admin.php/System/index', 00000),
(9, 00000007, '&#xe620;', '图片列表', 1, 1, 'admin/System/index', NULL, '/admin.php/System/index', 00000),
(13, 00000005, 'icon-basic-information', '基本信息', 1, 1, '', NULL, 'infor', 00000),
(24, 00000023, '', '添加/修改', 2, 1, 'admin/System/useradd', NULL, '/admin.php/System/useradd', 00001),
(16, 00000002, 'icon-fenzu', '权限分组', 1, 1, 'admin/System/group', NULL, '/admin.php/System/group', 00003),
(17, 00000003, '', '添加/修改', 2, 1, 'admin/Ajax/menuadd', NULL, '/admin.php/Ajax/menuadd', 00001),
(18, 00000003, '', '删除菜单', 2, 1, 'admin/Ajax/menudel', NULL, '/admin.php/Ajax/menudel', 00002),
(22, 00000016, '', '启用/禁用', 2, 1, 'admin/Ajax/changeState', NULL, '/admin.php/Ajax/changeState', 00001),
(20, 00000016, '', '添加分组', 2, 1, 'admin/System/group', NULL, '/admin.php/System/group', 00002),
(21, 00000016, '', '修改权限', 2, 1, 'admin/System/group', NULL, '/admin.php/System/group', 00003),
(23, 00000002, 'icon-yonghuguanli', '用户管理', 1, 1, 'admin/System/userlist', NULL, '/admin.php/System/userlist', 00003),
(25, 00000023, '', '批量删除', 2, 1, 'admin/AjAx/delAll', NULL, '/admin.php/AjAx/delAll', 00002),
(26, 00000023, '', '启用/禁用', 2, 1, 'admin/Ajax/toStatue', NULL, '/admin.php/Ajax/toStatue', 00003);

-- --------------------------------------------------------

--
-- 表的结构 `owan_auth_user`
--

CREATE TABLE `owan_auth_user` (
  `userid` int(9) NOT NULL COMMENT 'id',
  `signname` varchar(255) NOT NULL COMMENT '登录名',
  `password` varchar(255) NOT NULL COMMENT '登录密码',
  `salt` varchar(255) DEFAULT NULL COMMENT '密码盐值',
  `nickname` varchar(255) DEFAULT NULL COMMENT '用户昵称',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `groupid` int(3) UNSIGNED ZEROFILL NOT NULL COMMENT '分组ID',
  `addtime` int(15) DEFAULT NULL COMMENT '账户添加时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `owan_auth_user`
--

INSERT INTO `owan_auth_user` (`userid`, `signname`, `password`, `salt`, `nickname`, `status`, `groupid`, `addtime`) VALUES
(1, 'admin', 'ec9f5dca9fbbc3d078f0b42ad3d69cc8', '3b8577', '超级管理员', 1, 001, 1564617600),
(2, 'encong', 'ec9f5dca9fbbc3d078f0b42ad3d69cc8', '3b8577', '恩从', 1, 001, 1564617600),
(3, 'encongs', 'aae9faa241b9619fc059c4ea39bcc693', '3b8577', '恩从', 1, 001, 1564617600);

-- --------------------------------------------------------

--
-- 表的结构 `owan_uploads`
--

CREATE TABLE `owan_uploads` (
  `id` int(9) NOT NULL COMMENT 'id',
  `uid` int(9) NOT NULL COMMENT '上传人',
  `source` varchar(255) NOT NULL COMMENT '来源',
  `path` varchar(255) NOT NULL COMMENT '文件地址',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名',
  `seo` varchar(255) DEFAULT NULL COMMENT '文件副标题',
  `type` varchar(255) NOT NULL COMMENT '文件类型',
  `relation` int(9) UNSIGNED ZEROFILL NOT NULL DEFAULT '000000000' COMMENT '是否关联数据，是：数据id，否：0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `owan_auth_group`
--
ALTER TABLE `owan_auth_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `owan_auth_rule`
--
ALTER TABLE `owan_auth_rule`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `owan_auth_user`
--
ALTER TABLE `owan_auth_user`
  ADD PRIMARY KEY (`userid`);

--
-- 表的索引 `owan_uploads`
--
ALTER TABLE `owan_uploads`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `owan_auth_group`
--
ALTER TABLE `owan_auth_group`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `owan_auth_rule`
--
ALTER TABLE `owan_auth_rule`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=27;

--
-- 使用表AUTO_INCREMENT `owan_auth_user`
--
ALTER TABLE `owan_auth_user`
  MODIFY `userid` int(9) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `owan_uploads`
--
ALTER TABLE `owan_uploads`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT COMMENT 'id';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
