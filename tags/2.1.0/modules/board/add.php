<?php

switch ($_GET["step"]) {
	case 2:		
		$forum_name 		= $_POST['forum_name'];
		$forum_description 	= $_POST['forum_desc'];
		$forum_usertype		= $_POST['forum_usertype'];

		if ($forum_name == "") {
			$forum_error['forum_name'] = $lang['board']['forum_no_name'];
			$_GET["step"] = "";
		} elseif ($_GET['var'] != "change") {
			$row =  $db->query_first("SELECT count(*) as number FROM {$config["tables"]["board_forums"]} WHERE name='$forum_name'");

			if ($row['number'] >= 1){
				$forum_error['forum_name'] = $lang['board']['forum_current_exist']; 		
				$_GET["step"] = "";
			}
		}	
		if ($forum_description == "") {
			$forum_error['forum_description'] = $lang['board']['forum_no_desc'];
			$_GET["step"] = "";
		}
	break;
}


switch($_GET["step"]) {
	default:
		$sec->unlock("board_add");

		// Change board
		if ($_GET['var'] == "change"){
			$dsp->NewContent($lang['board']['forum_add_caption'],$lang['board']['forum_add_subcaption']);
			$dsp->SetForm("index.php?mod=board&action=add&var=change&step=2&fid={$_GET['fid']}"); 

			$row = $db->query_first("SELECT name, description, need_type FROM {$config["tables"]["board_forums"]} WHERE fid='{$_GET['fid']}'");

			if (!isset($_POST['forum_name'])) $_POST['forum_name'] = $row['name'];
			if (!isset($_POST['fourm_desc'])) $_POST['forum_desc'] = $row['description'];
			if (!isset($_POST['forum_usertype'])) $_POST['forum_usertype'] = $row['need_type'];

		// Add board
		} else {
			$dsp->NewContent($lang['board']['forum_add_caption'],$lang['board']['forum_add_subcaption']);
			$dsp->SetForm("index.php?mod=board&action=add&step=2"); 
		}
		$dsp->AddTextFieldRow("forum_name",$lang['board']['forum_name'],$_POST['forum_name'],$forum_error['forum_name']);

		$usertype_data = array();
		foreach ($lang['board']['user_type'] as $key => $value){
			if ($_POST['forum_usertype'] == $key) $selected = "selected";
			else $selected = "";
			$usertype_data[] = "<option $selected value='$key'>$value</option>";
		}
		$dsp->AddDropDownFieldRow("forum_usertype",$lang['board']['usertype'],$usertype_data,$forum_error['forum_usertype']);

		$dsp->AddTextAreaPlusRow("forum_desc",$lang['board']['fourm_desc'],$_POST['forum_desc'],$forum_error['forum_description']);
		$dsp->AddFormSubmitRow("add");
		$dsp->AddContent();
	break;

	case 2:
		if (!$sec->locked("board_add")) {

			// Change
			if ($_GET['var'] == "change"){
				$db->query("UPDATE {$config["tables"]["board_forums"]} SET  
					name='$forum_name', 
					description='$forum_description',
					need_type = '$forum_usertype' WHERE fid='{$_GET['fid']}'
					");

				$func->confirmation($lang['board']['forum_change_ok'], "index.php?mod=board&action=change_forum");

			// Add
			} else {
				$db->query("INSERT INTO {$config["tables"]["board_forums"]} SET  
					name='$forum_name', 
					description='$forum_description',
					need_type = '$forum_usertype'
					");

				$func->confirmation($lang['board']['forum_add_ok'], "index.php?mod=board&action=add");
			}

			$sec->lock("board_add");
		} else $func->error($lang['board']['current_exist'], "index.php?mod=board&action=add");
	break;
}

?>
