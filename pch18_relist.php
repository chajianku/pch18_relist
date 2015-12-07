<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
/*
Plugin Name: 自动刷新贴吧列表
Version: 2.5
Plugin URL: http://t8qd.cn
Description: 管理员可手动为成员刷新列表
Author: pch18
Author URL: http://t8qd.cn
For: V3.0+
*/

function pch18_relist_setting() {

	?>
	<tr><td>自动刷新贴吧列表</td>
	<td>
	<input type="radio" name="pch18_relist_enable" value="1" <?php if (option::uget('pch18_relist_enable') == 1) { echo 'checked'; } ?> > 是&nbsp;
	<input type="radio" name="pch18_relist_enable" value="0" <?php if (option::uget('pch18_relist_enable') != 1) { echo 'checked'; } ?> > 否
	</td> 
    
	<?php
}
 
function pch18_relist_person_setting() {
	global $PostArray;
	if (!empty($PostArray)) {
		$PostArray[] = 'pch18_relist_enable';
	}
}


function pch18_relist_admin() {
	echo '<li ';
	if(isset($_GET['plug']) && $_GET['plug'] == 'pch18_relist') { echo 'class="active"'; }
	echo '><a href="index.php?mod=admin:setplug&plug=pch18_relist"><span class="glyphicon glyphicon-retweet"></span> 刷新贴吧列表</a></li>';
	}	
	

function pch18_relist_redate() {
global $m,$i;
if (isset($_GET['ref'])){
	$uidtoid = $m->query("SELECT id,uid FROM `".DB_PREFIX."baiduid` where uid=".UID);
	while ($iid = $m->fetch_array($uidtoid)) {
		$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$iid['id']);
	}
}
}

function pch18_relist_bind() {
	Redirect("setting.php?mod=showtb&ref");
}


addAction('baiduid_set','pch18_relist_bind');
addAction('set_save1','pch18_relist_person_setting');
addAction('showtb_set','pch18_relist_redate');
addAction('set_2','pch18_relist_setting');
addAction('navi_2','pch18_relist_admin');
addAction('navi_8','pch18_relist_admin');
?>