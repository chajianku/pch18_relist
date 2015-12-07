<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
function callback_init() {
global $m;
	$m->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pch18_relist` (
  `id` int(10) NOT NULL,
  `lastdate` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
	cron::set('pch18_relist','plugins/pch18_relist/run.php',0,0,0);
	$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX."options` (`name`,`value`) VALUES  ('relist','1')");
}

function callback_inactive() {
	global $m;
	$m->query("DROP TABLE IF EXISTS `".DB_PREFIX."pch18_relist`");
	$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name`= 'relist'");
	cron::del('pch18_relist');
}
?>