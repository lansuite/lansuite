<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				show_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Dispays tasks in main window
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_show"], $lang["task"]["subhead_show"]);
	
	$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=150>Aufgabe:</td><td width=180>Detail:</td><td width=100>User:</td><td width=40>Status:</td></tr></table>";
	
	$res = $db->query("SELECT * FROM {$config["tables"]["tasks"]} ORDER BY 'prio'");
	
	$dsp->AddSingleRow($display);
	while ($tasks = $db->fetch_array($res))
	{

				$i++;
				if($tasks["prio"]==1)
					{
						$prio="<img src=ext_inc/task_icons/hoch.gif>";
					}
				if($tasks["prio"]==2)
					{
						$prio="<img src=ext_inc/task_icons/mittel.gif>";
					}
				if($tasks["prio"]==3)
					{
						$prio="<img src=ext_inc/task_icons/niedrig.gif>";
					}
	
	if (strlen($tasks["shortinfo"])>20) 
      		{ 
        		$info= substr($tasks["shortinfo"],0,20)."...";
        	} else {
        		$info= $tasks["shortinfo"];
        	}
  
	$tid=$tasks["taskid"];
	$username=$tasks["username"];
	
	$status=$tasks["status"];
	if ($status=="0")
		{
			$status="<font color=red><b>offen</b></font>";}else{$status="geschlossen";
		}
	
	$logged = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["taskuser"]} WHERE ($tid=id_task) GROUP BY id_task");
	
	if ($username=="")
		{
			$username="<font color=red><b>leer</b></font>";
		}
	if($logged["anz"]<1)
		{
			$logged["anz"]="<font color=red><b>0</b></font>";
		};
	
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=150><a href=?mod=task&action=show_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=180>'."".$info."".'</td><td width=100>'."".$logged["anz"]."".'</td><td width=40>'."".$status."".'</td></tr></table>';
	
	$dsp->AddSingleRow($display);
	}
	
	$dsp->AddContent();
	break;
	
	
	case 2:

	$dsp->NewContent($lang["task"]["head_show_detail"], $lang["task"]["subhead_show_detail"]);
	$taskid=$_GET["taskid"];
	$userid = $auth['userid'];
	$task = $db->query_first_rows("SELECT * FROM {$config["tables"]["tasks"]} WHERE taskid = '$taskid' ");
	$dsp->AddSingleRow($task["task"]);
	$dsp->AddSingleRow($task["shortinfo"]);
	//abfrage ob eingeloggt
	if (($auth['login'] == 1)&&($task["status"]==0))
	{
	$button = '<a href="%s"><img border="0" src="design/'.$_SESSION["auth"]["design"].'/images/%s.gif"></a> ';
	$buttons .= sprintf($button, "?mod=task&action=show_task&taskid=$taskid&step=3", "buttons_join");
	$dsp->AddDoubleRow($lang["task"]["show_taskjoin"], $buttons);
	}
	if ($task["status"]==1)
	{
	$dsp->AddSingleRow("<font color=red>Status geschlossen: Anmeldung nicht mehr notwendig, es gibt genug Helfer !!!</font>");
	}
	if (($auth['login'] == 0)&&($task["status"]==0))
	{
	$dsp->AddSingleRow("<font color=red>Status offen: Bitte einloggen zum Anmelden!!!</font>");
	}
	//zurückbutton nach übersicht
	$dsp->AddBackButton("?mod=task&action=show_task", ""); 
	$dsp->AddContent();
	break;
	
	case 3:
	
	$taskid=$_GET["taskid"];
	$userid = $auth['userid'];

	$task = $db->query_first("SELECT * FROM {$config["tables"]["tasks"]},{$config["tables"]["taskuser"]} WHERE taskid = '$taskid' ");
	$tasks = $db->query_first("SELECT * FROM {$config["tables"]["taskuser"]} WHERE id_task = '$taskid' ");
	
	if (($tasks["id_task"]==$taskid)&&($tasks["id_user"]==$userid))
	{
	$dsp->AddSingleRow($lang["task"]["show_error"]);

	}else
	{
			
	$add_it = $db->query("INSERT INTO {$config["tables"]["taskuser"]} SET
								id_task= '{$taskid}',
								id_user='{$userid}'
								");

	if($add_it == 1) 
		{ 
			$func->confirmation($lang["task"]["subhead_join_ok"],"?mod=task&action=show_task");
		}
		else
		 $func->error("NO_REFRESH","");
	}
	$dsp->AddContent();
	
	break;
}
?>