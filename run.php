<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function cron_pch18_relist() {
	global $m;

	$query = $m->query("SELECT DISTINCT id,uid FROM `".DB_PREFIX."baiduid` ");
	while ($fetch = $m->fetch_array($query)) {
		$id = $fetch['id'];
		$uid = $fetch['uid'];
		$isqd_query = $m->query("SELECT * FROM `".DB_PREFIX."pch18_relist` where id=".$id." and lastdate='".date("Y-m-d")."'");
		$isqd_fetch = $m->fetch_array($isqd_query);
		$setqd = option::uget('pch18_relist_enable',$uid);	
		if (!empty($setqd)){//user表里面开启签到功能
			$r = misc::scanTiebaByPid($id);//更新列表函数
			$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$id);
		
		}

	}
}
?>