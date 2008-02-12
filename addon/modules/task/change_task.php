<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				change_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task change
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_change"], $lang["task"]["subhead_change"]);
	
	$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=150>Aufgabe:</td><td width=180>Detail:</td><td width=40>User:</td><td width=40>Status:</td></tr></table>";
	
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
	$username=$tasks["username"];
	
	
	$status=$tasks["status"];
	if ($status=="0"){$status="<font color=red><b>offen</b></font>";}else{$status="geschlossen";}
	
	$logged = $db->query_first("SELECT COUNT(*) AS anz FROM {$config["tables"]["taskuser"]} WHERE ($tid=id_task) GROUP BY id_task");
	
	if ($username==""){$username="<font color=red><b>leer</b></font>";}
	if($logged["anz"]<1){$logged["anz"]="<font color=red><b>0</b></font>";};
	
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=150><a href=?mod=task&action=change_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=180>'."".$info."".'</td><td width=100>'."".$logged["anz"]."".'</td><td width=40>'."".$status."".'</td></tr></table>';
	
	$dsp->AddSingleRow($display);
	}
	
	$dsp->AddContent();
	break;
	
	

	case 2:


	$dsp->NewContent($lang["task"]["head_change"], $lang["task"]["subhead_change"]);
	$taskid=$_GET["taskid"];
	$dsp->SetForm("index.php?mod=task&action=change_task&step=3&taskid=$taskid");
	
	$tasks = $db->query_first_rows("SELECT * FROM {$config["tables"]["tasks"]} WHERE taskid = '$taskid' ");
	
	$dsp->AddTextFieldRow("task", $lang["task"]["add_task"], $tasks["task"], "");
	$dsp->AddTextAreaRow("shortinfo", $lang["task"]["add_shortinfo"], $tasks["shortinfo"], "", "","",1);
	
	$option1_array= array("1" => "hoch",
			"2" => "mittel",
			"3" => "niedrig"
			);
		$t_array = array();
		reset ($option1_array);
		while (list ($key, $val) = each ($option1_array)) {
			($option1 == $key) ? $selected = "selected" : $selected = "";
			array_push ($t_array, "<option $selected value=\"$key\">$val</option>");
		}
	$status=$tasks["status"];
	$dsp->AddDropDownFieldRow("prio", $lang["task"]["add_prio"], $t_array, "", $optional = NULL);
	$dsp->AddCheckBoxRow("status", "Status geschlossen?", "", "", "", $status);
	$dsp->AddFormSubmitRow("add");$dsp->AddBackButton("?mod=task&action=change_task", ""); 
	$dsp->AddContent();
	break;
	
	case 3:
	
	
	if (strlen($_POST["task"])==0)
	{
		$dsp->AddSingleRow($lang["task"]["add_error"]);
		if ($_POST["status"]==""){$status=0;}else{$status=1;}
		$dsp->AddSingleRow($status);
		$dsp->AddBackButton("?mod=task&action=change_task", ""); 
		$dsp->AddContent();
	}else{

		$task  = $func->db2text($_POST["task"]);
		$shortinfo	= $func->db2text($_POST["shortinfo"]);
		if (strlen($_POST["shortinfo"])==0){$shortinfo=$lang["task"]["add_noinfo"];}
		$prio = rawurlencode($_POST["prio"]);
		if ($_POST["status"]==""){$status=0;}else{$status=1;}
		
	$taskid=$_GET["taskid"];
	$add_it = $db->query("UPDATE {$config["tables"]["tasks"]} SET
								task = '{$task}',
								shortinfo = '{$shortinfo}',
								prio = '{$prio}',
								status= '{$status}'
								WHERE taskid='$taskid'
								");

		if($add_it == 1) { $func->confirmation($lang["task"]["add_ok"],"?mod=task&action=change_task");
		}
		else
		 $func->error("NO_REFRESH","");
	} 
	break;
}
?>