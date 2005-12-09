<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		add_item.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		29.03.2003 20:17
*	Description: 		Adds FAQ Items
*	Remarks:
*
**************************************************************************/

switch($_GET["step"]) {

	case 2:

	//  ERRORS
	$get_cat_names = $db->query("SELECT name FROM {$config["tables"]["faq_cat"]}");

		while($row=$db->fetch_array($get_cat_names)) {

			$name = $row["name"];

				if($name == $_POST["question_new_cat"] AND $_POST["question_new_cat"] != "" ) {

					$faq_error['question_cat'] = $lang['faq']['cat_exists'];
					$_GET["step"] = 1;

				}
		}

	$i = strlen($_POST["question_text"]);

		if($i > 5000) {

			$faq_error['question_text'] = $lang['faq']['text_to_long'];
			$_GET["step"] = 1;

		}


	if($_POST["question_new_cat"] == "" AND $_POST["question_cat"] == "new") {

		$faq_error['cat_name']	= $lang['faq']['no_cat_name'];
		$_GET["step"] = 1;
	}

	if($_POST["question_caption"] == "") {

		$faq_error['question_caption']	= $lang['faq']['no_quest_name'];
		$_GET["step"] = 1;
	}


	if($_POST["question_text"] == "") {
		$faq_error['question_text']	= $lang['faq']['no_quest_text'];
		$_GET["step"] = 1;
	}

	if($_POST["question_cat"] == 0 AND $_POST["question_new_cat"] == "") {

		$faq_error['question_cat']	= $lang['faq']['new_cat_error'];
		$_GET["step"] = 1;
	}

	if($_POST["question_cat"] != 0 AND $_POST["question_new_cat"] != "") {

		$faq_error['question_cat']	= $lang['faq']['choise_cat_error'];
		$_GET["step"] = 1;
	}

break;

} // close switch


switch($_GET["step"]) {

	default:

	unset($_SESSION['add_blocker_faqitem']);

	$dsp->NewContent($lang['faq']['add_quest_caption'],$lang['faq']['add_quest_subcaption']);
	$dsp->SetForm("index.php?mod=faq&object=item&action=add_item&step=2");

	$get_cats = $db->query("SELECT name,catid FROM {$config["tables"]["faq_cat"]}");

	$faq_cats[] = "<option selected value=\"0\"> ".$lang['faq']['choose_cat']." </option>";

	while($row=$db->fetch_array($get_cats)) {

		$faq_cats[] .= "<option value=" . $row["catid"] . "> " . $row["name"] . " </option>";
	}

	$dsp->AddTextFieldRow("question_caption",$lang['faq']['add_quest'],$_POST['question_caption'],$faq_error['question_caption']);
	$dsp->AddTextAreaPlusRow("question_text",$lang['faq']['add_text'],$_POST['question_text'],$faq_error['question_text']);
	$dsp->AddDropDownFieldRow("question_cat",$lang['faq']['choise_cat'],$faq_cats,"");
	$dsp->AddTextFieldRow("question_new_cat",$lang['faq']['new_cat'],$_POST['question_new_cat'],$faq_error['question_cat']);
	$dsp->AddFormSubmitRow("add");
	$dsp->AddContent();

	break; // BREAK DEFAULT

	case 2:

	$courent_date = date("U");

	if($_POST["question_cat"] == 0 AND $_POST["question_new_cat"] != "" AND $_SESSION['add_blocker_faqitem'] != 1) {

		$add_it = $db->query("INSERT INTO {$config["tables"]["faq_cat"]} SET
								name = '{$_POST["question_new_cat"]}',
								poster = '{$_SESSION["auth"]["userid"]}',
								date = '$courent_date'
								");

		$catid = $db->insert_id();


		$add_it = $db->query("INSERT INTO {$config["tables"]["faq_item"]} SET
								caption = '{$_POST["question_caption"]}',
								text = '{$_POST["question_text"]}',
								poster = '{$_SESSION["auth"]["userid"]}',
								date = '$courent_date',
								catid = '$catid'
								");

			if($add_it == 1) { $func->confirmation($lang['faq']['add_quest_ok'],"");

				$_SESSION['add_blocker_faqitem'] = 1;

			}

				else {

					$func->error("NO_REFRESH","");

				}

	}

		else {

			if($_SESSION["add_blocker_faqitem"] != 1) {

				$add_it = $db->query("INSERT INTO {$config["tables"]["faq_item"]} SET
								caption = '{$_POST["question_caption"]}',
								text = '{$_POST["question_text"]}',
								poster = '{$_SESSION["auth"]["userid"]}',
								date = '$courent_date',
								catid = '{$_POST["question_cat"]}'
								");

					if($add_it == 1) { $func->confirmation($lang['faq']['add_onlyquest_ok'],"");

						$add_blocker_faqitem = 1;

						$_SESSION['add_blocker_faqitem'] = 1;


					}
			}

				else

					$func->error("NO_REFRESH","");

				}

	break; // BREAK CASE 2

} // close switch step
?>
