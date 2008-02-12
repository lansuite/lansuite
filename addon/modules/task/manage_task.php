<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				manage_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task manage
*	Remarks: 		
*
**************************************************************************/

$headermenuitem	= $vars["headermenuitem"];
if ($headermenuitem == "") $headermenuitem = 1;
switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_manage"], $lang["task"]["subhead_manage"]);
	$menunames[] = $lang["task"]["menuname1"];
	$menunames[] = $lang["task"]["menuname2"];
	$dsp->AddHeaderMenu($menunames, "?mod=task&action=manage_task", $headermenuitem);
	
		switch ($headermenuitem) {
		case 1:
		$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=150>Aufgabe:</td><td width=180>Detail:</td><td width=100>User:</td><td width=40>Status:</td></tr></table>";
	
		$res = $db->query("SELECT * FROM {$config["tables"]["tasks"]} LEFT JOIN {$config["tables"]["taskuser"]} ON id_task=taskid LEFT JOIN {$config["tables"]["user"]} ON id_user=userid ORDER BY 'prio'");
	
		$dsp->AddSingleRow($display);
		while ($tasks = $db->fetch_array($res))
		{

		$i++;
		if($tasks["prio"]==1)
		{$prio="<img src=ext_inc/task_icons/hoch.gif>";}
		if($tasks["prio"]==2)
		{$prio="<img src=ext_inc/task_icons/mittel.gif>";}
		if($tasks["prio"]==3)
		{$prio="<img src=ext_inc/task_icons/niedrig.gif>";}
	
		if (strlen($tasks["shortinfo"])>20) 
      		{ 
        		$info= substr($tasks["shortinfo"],0,20)."...";
        	} else {
        		$info= $tasks["shortinfo"];
        	}
		
		$tid=$tasks["taskid"];
	$username=$tasks["username"];
	$status=$tasks["status"];
	if ($status=="0"){$status="<font color=red><b>offen</b></font>";}else{$status="geschlossen";}

	$logged = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["taskuser"]} WHERE ($tid=id_task) GROUP BY id_user");
	
	if ($username==""){$username="<font color=red><b>leer</b></font>";$status="<font color=red><b>offen</b></font>";}
	if($logged["anz"]<1){$logged["anz"]=0;};
	
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=150>'."".$tasks['task']."".'</td><td width=180>'."".$info."".'</td><td width=100>'."".$username."".'</td><td width=40>'."".$status."".'</td></tr></table>';
	
	$dsp->AddSingleRow($display);
	}
	
	$dsp->AddContent();
		
		
		
		
		
		
		
		break;
	
	
		case 2:
		$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=200>Aufgabe:</td><td width=200>Detail:</td><td width=40>User:</td><td width=40>Status:</td></tr></table>";
	
	
		$res = $db->query("SELECT * FROM {$config["tables"]["tasks"]} ORDER BY 'prio'");
		$dsp->AddSingleRow($display);
		while ($tasks = $db->fetch_array($res))
		{

		$i++;
		if($tasks["prio"]==1)
		{$prio="<img src=ext_inc/task_icons/hoch.gif>";}
		if($tasks["prio"]==2)
		{$prio="<img src=ext_inc/task_icons/mittel.gif>";}
		if($tasks["prio"]==3)
		{$prio="<img src=ext_inc/task_icons/niedrig.gif>";}
	
		if (strlen($tasks["shortinfo"])>20) 
      		{ 
        		$info= substr($tasks["shortinfo"],0,20)."...";
        	} else {
        		$info= $tasks["shortinfo"];
        	}
		$tid=$tasks["taskid"];
	
		
	
		$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=200><a href=?mod=task&action=show_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=200>'."".$info."".'</td><td width=40><b><font color=red>'."".$c."".'<b></td><td width=40>offen</td></tr></table>';
	
		$dsp->AddSingleRow($display);
		}
	
	$dsp->AddContent();
		break;
		}
}
?>