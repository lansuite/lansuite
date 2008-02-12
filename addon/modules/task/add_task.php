<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:			1.0
*	Filename: 				add_task.php
*	Module: 					Aufgaben
*	Main editor: 			saschakoths@arcor.de
*	Last change: 			20.03.2005
*	Description: 			Task add
*	Remarks: 		
*
**************************************************************************/

switch($_GET["step"]) {
	
	

	default:
	$dsp->NewContent($lang["task"]["head_add"], $lang["task"]["subhead_add"]);

	$dsp->SetForm("index.php?mod=task&action=add_task&step=2");
	$dsp->AddTextFieldRow("task", $lang["task"]["add_task"], "", "");
	$dsp->AddTextAreaRow("shortinfo", $lang["task"]["add_shortinfo"], "", "", "","",1);
	
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
	$dsp->AddDropDownFieldRow("prio", $lang["task"]["add_prio"], $t_array, "", $optional = NULL);
	
	$dsp->AddFormSubmitRow("add");$dsp->AddBackButton("?mod=task&action=show_task", ""); 
	$dsp->AddContent();
	break;

	case 2:


	if (strlen($_POST["task"])==0)
	{
		$dsp->AddSingleRow($lang["task"]["add_error"]);
		$dsp->AddBackButton("?mod=task&action=add_task", ""); 
		$dsp->AddContent();
	}else{

		$task  = $func->db2text($_POST["task"]);
		$shortinfo	= $func->db2text($_POST["shortinfo"]);
		if (strlen($_POST["shortinfo"])==0){$shortinfo=$lang["task"]["add_noinfo"];}
		$prio = rawurlencode($_POST["prio"]);
		

	$add_it = $db->query("INSERT INTO {$config["tables"]["tasks"]} SET
								task = '{$task}',
								shortinfo = '{$shortinfo}',
								prio = '{$prio}'
								");

		if($add_it == 1) { $func->confirmation($lang["task"]["add_ok"],"?mod=task&action=show_task");
		}
		else
		 $func->error("NO_REFRESH","");
	} 
	break;
	
}
?>