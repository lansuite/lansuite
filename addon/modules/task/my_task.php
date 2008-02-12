<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				my_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_my"], $lang["task"]["subhead_my"]);
	
	$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=150>Aufgabe:</td><td width=300>Detail:</td></tr></table>";
	
	$dsp->AddSingleRow($display);
	$userid = $auth['userid'];
	$res = $db->query("SELECT * FROM {$config["tables"]["tasks"]},{$config["tables"]["taskuser"]} WHERE taskid=id_task AND id_user=$userid ORDER BY 'prio'");
	while ($tasks = $db->fetch_array($res)){
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
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=150><a href=?mod=task&action=my_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=300>'."".$info."".'</td><td width=50><a href=?mod=task&action=my_task&step=3&taskid='."".$tid."".'>'."".'abmelden</td></tr></table>';
	$dsp->AddSingleRow($display);
	}
	$dsp->AddBackButton("?mod=task&action=show_task", "");
	$dsp->AddContent();
	break;
	
	
	case 2:


	$dsp->NewContent($lang["task"]["head_show_detail"], $lang["task"]["subhead_show_detail"]);
	$taskid=$_GET["taskid"];
	$userid = $auth['userid'];
	$task = $db->query_first_rows("SELECT * FROM {$config["tables"]["tasks"]} WHERE taskid = '$taskid' ");
	$dsp->AddSingleRow($task["task"]);
	$dsp->AddSingleRow($task["shortinfo"]);


	//zurückbutton nach übersicht
	$dsp->AddBackButton("?mod=task&action=my_task", ""); 
	$dsp->AddContent();
	break;
	
	case 3:


	$tid=$_GET["taskid"];
	$userid = $auth['userid'];
	
	$db->query("DELETE FROM {$config["tables"]["taskuser"]} WHERE id_task='$tid' and id_user='$userid'");	
	
	$func->confirmation($lang["task"]["del_confirmation"], "?mod=task&action=my_task"); 
	break;
}
?>