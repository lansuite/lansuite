<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				join_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task joinen
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_show"], $lang["task"]["subhead_show"]);
	
	$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=200>Aufgabe:</td><td width=200>Detail:</td><td width=40>User:</td><td width=40>Status:</td></tr></table>";
	
	$dsp->AddSingleRow($display);
	
	$res = $db->query("SELECT * FROM {$config["tables"]["tasks"]} ORDER BY 'prio'");
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
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=200><a href=?mod=task&action=show_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=200>'."".$info."".'</td><td width=40><b><font color=red>0<b></td><td width=40>offen</td></tr></table>';
	
	$dsp->AddSingleRow($display);
	}
	
	$dsp->AddContent();
	break;
	
	

	case 2:


	$dsp->NewContent($lang["task"]["head_show_detail"], $lang["task"]["subhead_show_detail"]);
	$taskid=$_GET["taskid"];
	$task = $db->query_first_rows("SELECT * FROM {$config["tables"]["tasks"]} WHERE taskid = '$taskid' ");
	//$task_name 	= $task["task"];
	$dsp->AddSingleRow($task["task"]);
	$dsp->AddSingleRow($task["shortinfo"]);
	//abfrage ob eingeloggt
	if ($auth['login'] == 1)
	{
	$button = '<a href="%s"><img border="0" src="design/'.$_SESSION["auth"]["design"].'/images/%s.gif"></a> ';
	$buttons .= sprintf($button, "?mod=task&action=join&taskid=$taskid&step=2", "buttons_join");
	$dsp->AddDoubleRow($lang["task"]["show_taskjoin"], $buttons);
	}
	//zurückbutton nach übersicht
	$dsp->AddBackButton("?mod=task&action=show_task", ""); 
	$dsp->AddContent();
	break;
}
?>