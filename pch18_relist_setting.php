<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); }?>
<h3>自动刷新贴吧列表</h3>
<table class="table table-striped">
	<thead>
		<th>PID</th>
		<th>用户名</th>
		<th>贴吧数</th>
		<th>状态</th>
		<th>上一次</th>
		<th>操作</th>
	</thead>
	<tbody>
<?php 
        global $m;
		$baid_ft = $m->query("SELECT id,uid FROM `".DB_PREFIX."baiduid`");
		$num=0;
		while ($baid = $m->fetch_array($baid_ft)) {
			$uxs = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users` where id=".$baid['uid']);
            if ($uxs['t']=='') continue;
			$uxsm = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX.$uxs['t']."` WHERE `pid` = ".$baid['id']);
			//显示pid+user
			echo '<tr><td>'.$baid['id'].'</td><td>'.$uxs['name'].'</td>';
			//tieba
			if ($uxsm['c']==0)
			$uxsm['c']='<font color="red"><B>[ '.$uxsm['c'].' ]</B></font>';
			echo '<td>'.$uxsm['c'].'</td>';
			//显示是否开启自动签到
			if (option::uget('pch18_relist_enable',$baid['uid'])==1){
			echo '<td><font color="Green"><b>开启</b></font></td>';
			}else{
			echo '<td><font color="Darkorange"><b>关闭</b></font></td>';
			}
			//lastdo
			$lastdate = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."pch18_relist` where id=".$baid['id']);
			if ($lastdate['lastdate']==date("Y-m-d"))
				$lastdate['lastdate']='<font color="DarkTurquoise">今日</font>';
			if ($lastdate['lastdate']=='')
				$lastdate['lastdate']='<font color="black"></b>无记录<b></font>';
			echo '<td><font color="DeepPink"><b>'.$lastdate['lastdate'].'</b></font></td>';
			//setting
			echo '<td><a href="index.php?mod=admin:setplug&plug=pch18_relist&ref='.$baid['id'].'" onclick="$(\'#tb_num_isok\').html(\'正在刷新该用户贴吧列表，可能需要较长时间，请耐心等待...\')">刷新贴吧列表</a></td>';
		$num++;
		}
		
			if (isset($_GET['isok'])) {
				echo '</br><div class="alert alert-success" id="tb_num_isok">'.$_GET['isok'].'</div>';
			}else{
				echo '</br><div class="alert alert-success" id="tb_num_isok">当前已列出 '.$num.' 个百度ID账户</div>';
			}
			if($num == 0){
				echo '<div class="alert alert-info" id="tb_num">您的站点内没有用户需要刷新贴吧列表，刷新功能暂不可用！</div>';
				}
				else{
			echo '<div class="alert alert-info" id="tb_num">';
			echo '<a href="index.php?mod=admin:setplug&plug=pch18_relist&ref=all" onclick="$(\'#tb_num_isok\').html(\'正在刷新 所有用户 贴吧列表，可能需要较长时间，请耐心等待...\')">刷新所有用户贴吧列表</a>';
			echo ' | <a href="index.php?mod=admin:setplug&plug=pch18_relist&ref=kq" onclick="$(\'#tb_num_isok\').html(\'正在刷新 所有刷新所有(跳过没开自动刷新)用户 贴吧列表，可能需要较长时间，请耐心等待...\')">刷新开启用户贴吧列表</a>';
			echo ' | <a href="index.php?mod=admin:setplug&plug=pch18_relist&ref=wkq" onclick="$(\'#tb_num_isok\').html(\'正在刷新 所有刷新所有(没开自动刷新)用户 贴吧列表，可能需要较长时间，请耐心等待...\')">刷新未开启用户贴吧列表</a>';
			echo ' | <a href="index.php?mod=admin:setplug&plug=pch18_relist&ref=kong" onclick="$(\'#tb_num_isok\').html(\'正在刷新 所有空账户 贴吧列表，可能需要较长时间，请耐心等待...\')">刷新所有空账户贴吧列表</a>';
			echo '</div>';
			}

//=====================================以下刷新贴吧代码=========================
if (isset($_GET['ref'])) {
switch ($_GET['ref']){


case 'all':
//刷新全部贴吧
		$query = $m->query("SELECT DISTINCT id,uid FROM `".DB_PREFIX."baiduid` ");
		while ($fetch = $m->fetch_array($query)) {
			$id = $fetch['id'];
			$uid = $fetch['uid'];
			$r = misc::scanTiebaByPid($id);//更新列表函数
			$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$id);

		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=pch18_relist&isok=已刷新完所有用户贴吧列表');
		die();
		
		
case 'kq';
//刷新开启用户全部贴吧
		$query = $m->query("SELECT DISTINCT id,uid FROM `".DB_PREFIX."baiduid` ");
		while ($fetch = $m->fetch_array($query)) {
			$id = $fetch['id'];
			$uid = $fetch['uid'];
			$setqd = option::uget('pch18_relist_enable',$uid);
			if (!empty($setqd)){    //user表里面开启签到功能
				$r = misc::scanTiebaByPid($id);     //更新列表函数
				$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$id);
			}
		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=pch18_relist&isok=已刷新完所有状态为开启的用户贴吧列表');
		die();
		

case 'wkq';
//刷新未开启用户全部贴吧
		$query = $m->query("SELECT DISTINCT id,uid FROM `".DB_PREFIX."baiduid` ");
		while ($fetch = $m->fetch_array($query)) {
			$id = $fetch['id'];
			$uid = $fetch['uid'];
			$setqd = option::uget('pch18_relist_enable',$uid);
			if ($setqd != 1){    //user表里面开启签到功能
				$r = misc::scanTiebaByPid($id);     //更新列表函数
				$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$id);
			}
		}
		Redirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=pch18_relist&isok=已刷新完所有状态为未开启用户贴吧列表');
		die();
		
			
case 'kong':
//刷新空账户
		$baid_ft = $m->query("SELECT id,uid FROM `".DB_PREFIX."baiduid`");
		while ($baid = $m->fetch_array($baid_ft)) {
			$uxs = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."users` where id=".$baid['uid']);
			if ($uxs['t']=='') continue;
			$uxsm = $m->once_fetch_array("SELECT COUNT(*) AS `c` FROM `".DB_PREFIX.$uxs['t']."` WHERE `pid` = ".$baid['id']);
			if ($uxsm['c']==0){
			$r = misc::scanTiebaByPid($baid['id']);//更新列表函数
			$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$baid['id']);
			}
		}
	    Redirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=pch18_relist&isok=已刷新完所有空账户');
		die();
		
		
default:
		if ($_GET['ref']>0){
		$r = misc::scanTiebaByPid($_GET['ref']);
		$m->query("REPLACE INTO `".DB_NAME."`.`".DB_PREFIX."pch18_relist` SET `lastdate` = '".date("Y-m-d")."', id = ".$_GET['ref']);
		Redirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=pch18_relist&isok=已刷新完PID='.$_GET['ref'].'的用户的贴吧列表');
		}
}
}

?>
	</tbody>
</table>