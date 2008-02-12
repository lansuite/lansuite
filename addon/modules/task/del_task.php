<?

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				del_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task delete
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	default:
	$dsp->NewContent($lang["task"]["head_del"], $lang["task"]["subhead_del"]);
	$dsp->AddSingleRow($lang["task"]["subhead_del2"]);
	$display = "<table class=tbl_0><tr><td width=30><center>Nr.:</center></td><td width=40><center>Prio:</center></td><td width=150>Aufgabe:</td><td width=180>Detail:</td><td width=100>User:</td><td width=40>Status:</td></tr></table>";
	
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
	
	$display = '<table class=tbl_0><tr><td width=30><center>'."".$i."".'.'."".'</center></td><td width=40><center>'."".$prio." ".'</center></td><td width=150><a href=?mod=task&action=del_task&step=2&taskid='."".$tid."".'>'."".$tasks['task']."".'</a></td><td width=180>'."".$info."".'</td><td width=100>'."".$logged["anz"]."".'</td><td width=40>'."".$status."".'</td></tr></table>';
	
	$dsp->AddSingleRow($display);
	}
	
	$dsp->AddContent();
	break;

	case 2:

	$tid=$_GET["taskid"];
	$db->query("DELETE FROM {$config["tables"]["tasks"]} WHERE taskid='$tid'"); 
	$db->query("DELETE FROM {$config["tables"]["taskuser"]} WHERE id_task='$tid'");	
	
	
	$func->confirmation($lang["task"]["del_confirmation"], "?mod=task&action=del_task"); 
	
	break;
	
}
?>