-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.5.27 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  8.0.0.4482
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 credit.site 结构
CREATE TABLE IF NOT EXISTS `site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '站点id',
  `name` varchar(128) NOT NULL COMMENT '站点名',
  `signin_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '登入入口',
  `signin_data` varchar(1024) NOT NULL DEFAULT '' COMMENT '登入数据',
  `signin_check` varchar(1024) NOT NULL DEFAULT '' COMMENT '登入验证',
  `credit_url` varchar(1024) NOT NULL DEFAULT '' COMMENT '积分查询入口',
  `credit_check` varchar(1024) DEFAULT '' COMMENT '积分检查',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='站点信息表';

-- 正在导出表  credit.site 的数据：~1 rows (大约)
/*!40000 ALTER TABLE `site` DISABLE KEYS */;
INSERT INTO `site` (`id`, `name`, `signin_url`, `signin_data`, `signin_check`, `credit_url`, `credit_check`, `create_time`) VALUES
	(1, 'Saraba1st', 'http://bbs.saraba1st.com/2b/member.php?mod=logging&action=login&loginsubmit=yes&infloat=yes&lssubmit=yes', 'fastloginfield=username&username=%account%&cookietime=2592000&password=%password%', 'auth', 'http://bbs.saraba1st.com/2b/home.php?mod=spacecp&ac=credit&showcredit=1', '//*[@id="ct"]/div[1]/div/ul[2]', '2013-11-04 17:59:19');
/*!40000 ALTER TABLE `site` ENABLE KEYS */;


-- 导出  表 credit.user 结构
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `account` varchar(30) NOT NULL COMMENT '用户账号',
  `name` varchar(30) NOT NULL COMMENT '用户名',
  `password` varchar(128) NOT NULL COMMENT '密码',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '权限',
  `enabled` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户信息表';

-- 正在导出表  credit.user 的数据：~1 rows (大约)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `account`, `name`, `password`, `email`, `level`, `enabled`, `create_time`) VALUES
	(1, 'admin', 'Admin', '$1$KW3.Lk/.$GUlMIJmN8QLpLsNixYQVg.', 'a@a.com', 65535, 1, '2013-11-04 17:55:23');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;


-- 导出  表 credit.user_account 结构
CREATE TABLE IF NOT EXISTS `user_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '账号id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `account` varchar(30) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(30) NOT NULL COMMENT '密码',
  `site_id` int(11) unsigned NOT NULL COMMENT '站点id',
  `enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_account_site_id` (`user_id`,`account`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户相关的账户信息表';

-- 正在导出表  credit.user_account 的数据：~0 rows (大约)
/*!40000 ALTER TABLE `user_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_account` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
