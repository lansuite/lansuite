<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		change_item.php
*	Module: 		FAQ
*	Main editor: 		Micheal@one-network.org
*	Last change: 		01.04.2003 13:53
*	Description: 		Changes FAQ items
*	Remarks:
*
**************************************************************************/


switch($_GET["step"]) {

	case 3:

	//  ERRORS
	$get_cat_names = $db->query("SELECT name FROM {$config["tables"]["faq_cat"]}");

		while($row=$db->fetch_array($get_cat_names)) {

			$name = $row["name"];

				if($name == $_POST["question_new_cat"] AND $_POST["question_new_cat"] != "" ) {

					$faq_error['question_cat'] = $lang['faq']['cat_exists'];
					$_GET["step"] = 2;

				}
		}

	$i = strlen($_POST["question_text"]);

		if($i > 5000) {

			$faq_error['question_text'] = $lang['faq']['text_to_long'];
			$_GET["step"] = 2;

		}


	if($_POST["question_new_cat"] == "" AND $_POST["question_cat"] == "new") {

		$faq_error['cat_name']	= $lang['faq']['no_cat_name'];
		$_GET["step"] = 2;
	}

	if($_POST["question_caption"] == "") {

		$faq_error['question_caption']	= $lang['faq']['no_quest_name'];
		$_GET["step"] = 2;
	}


	if($_POST["question_text"] == "") {
		$faq_error['question_text']	= $lang['faq']['no_quest_text'];
		$_GET["step"] = 2;
	}

	if($_POST["question_cat"] == 0 AND $_POST["question_new_cat"] == "") {

		$faq_error['question_cat']	= $lang['faq']['new_cat_error'];
		$_GET["step"] = 2;
	}

	if($_POST["question_cat"] != 0 AND $_POST["question_new_cat"] != "") {

		$faq_error['question_cat']	= $lang['faq']['choise_cat_error'];
		$_GET["step"] = 2;
	}

	break;

} // close switch



switch($_GET["step"]) {

	default:


	$get_cat = $db->query("SELECT catid, name FROM {$config["tables"]["faq_cat"]}");

	$count_cat = $db->num_rows($get_cat);

	if($count_cat == 0) { $func->information($lang['faq']['no_itenm'],"index.php?mod=home"); }

	else {

		$dsp->NewContent($lang['faq']['change_caption'],$lang['faq']['show_subcaption']);
		if($_SESSION['menu_status']['faq'][$_GET['faqcatid']] == "open") {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "closed";
		}else {
			$_SESSION['menu_status']['faq'][$_GET['faqcatid']] = "open";
		}

		while($row=$db->fetch_array($get_cat)) {

			$templ["faq"]["overview"]["row"]["cat"]["titel"]	= $row["name"];
			$templ["faq"]["overview"]["row"]["cat"]["link"]	= "index.php?mod=faq&action=show&faqcatid={$row['catid']}";
			$templ['faq']['overview']['row']['question']['change']['change']['link']	= $dsp->FetchButton("index.php?mod=faq&object=cat&action=change_cat&catid={$row['catid']}&step=2","edit");

			$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_cat");

			if($_SESSION['menu_status']['faq'][$row['catid']] == "open") {

				$get_item = $db->query("SELECT caption,itemid FROM {$config["tables"]["faq_item"]}
													WHERE catid = '{$row['catid']}'");
				while($row=$db->fetch_array($get_item)) {

					$templ["faq"]["overview"]["row"]["question"]["title"]	= $func->text2html($row["caption"]);
					$templ["faq"]["overview"]["row"]["question"]["id"]	= $row["itemid"];
					$templ['faq']['overview']['row']['question']['change']['change']['link']	= $dsp->FetchButton("index.php?mod=faq&object=cat&action=change_item&itemid={$row['itemid']}&step=2","edit");
					$faq_content .= $dsp->FetchModTpl("faq","faq_overview_row_question");

				}//while
			}//if
		}//while

		$dsp->AddSingleRow($faq_content, "class='menu'");
		$dsp->AddContent();

	} // close else

	break;


	case 2:

	unset($_SESSION['change_blocker_faqitem']);

	$get_data = $db->query_first("SELECT caption, text, catid FROM {$config["tables"]["faq_item"]} WHERE itemid = '{$_GET["itemid"]}'");

	$question_caption 	= $get_data["caption"];

		if($question_caption != "") {

				if($_POST["question_caption"] == "") $_POST["question_caption"] = $get_data["caption"];
				if($_POST["question_text"] == "")    $_POST["question_text"] = $get_data["text"];


				$_POST["question_cat"]=($_POST["question_cat"] == "") ? $get_data["catid"] : $_POST["question_cat"];


				$dsp->NewContent($lang['faq']['change_cat_caption'],$lang['faq']['add_quest_subcaption']);
				$dsp->SetForm("index.php?mod=faq&object=item&came_from=$came_from&action=change_item&step=3&itemid=" .$_GET["itemid"]);

				$get_cats = $db->query("SELECT name,catid FROM {$config["tables"]["faq_cat"]}");

				$faq_cats[] = "<option value=\"0\"> ".$lang['faq']['choose_cat']." </option>";

				while($row=$db->fetch_array($get_cats)) {

					$selected=($row["catid"] == $_POST["question_cat"]) ? "selected" : "";

					$faq_cats[] .= "<option $selected value=" . $row["catid"] . "> " . $row["name"] . " </option>";
				}

				$dsp->AddTextFieldRow("question_caption",$lang['faq']['add_quest'],$_POST['question_caption'],$faq_error['question_caption']);
				$dsp->AddTextAreaPlusRow("question_text",$lang['faq']['add_text'],$_POST['question_text'],$faq_error['question_text']);
				$dsp->AddDropDownFieldRow("question_cat",$lang['faq']['choise_cat'],$faq_cats,"");
				$dsp->AddTextFieldRow("question_new_cat",$lang['faq']['new_cat'],$_POST['question_new_cat'],$faq_error['question_cat']);
				$dsp->AddFormSubmitRow("add");
				$dsp->AddContent();
		}

			else

				$func->error($lang['faq']['quest_not_exists'],"");

	break;


	case 3:

	$get_itemid = $db->query_first("SELECT caption FROM {$config["tables"]["faq_item"]} WHERE itemid = '{$_GET["itemid"]}'");
	$faqitem_caption_test = $get_itemid["caption"];

		if($faqitem_caption_test != "") {

			$courent_date = date("U");

				if($_POST["question_cat"] == 0 AND $_POST["question_new_cat"] != "" AND $_SESSION["change_blocker_faqitem"] != 1) {

					$update_it1 = $db->query("INSERT INTO {$config["tables"]["faq_cat"]} SET
												name = '{$_POST["question_new_cat"]}',
												poster = '{$_SESSION["auth"]["userid"]}',
												date = '$courent_date'
								 				");

					$get_catid = $db->query_first("SELECT catid FROM {$config["tables"]["faq_cat"]} WHERE name = '{$_POST["question_new_cat"]}'");
					$catid = $get_catid["catid"];

					$update_it2 = $db->query("UPDATE {$config["tables"]["faq_item"]} SET
										caption = '{$_POST["question_caption"]}',
										text = '{$_POST["question_text"]}',
										poster = '{$_SESSION["auth"]["userid"]}',
										date = '$courent_date',
										catid = '$catid'
										WHERE itemid = '{$_GET["itemid"]}'");

						if($update_it1 == 1 AND $update_it2 == 1) {

							$func->confirmation($lang['faq']['change_quest_ok']	,"");

							$_SESSION['change_blocker_faqitem'] = 1;


						}

							else {

								$func->error("NO_REFRESH","");
							}

				} // if

					else {

						if($_SESSION['change_blocker_faqitem'] != 1) {

							$add_it = $db->query("UPDATE {$config["tables"]["faq_item"]} SET
											caption = '{$_POST["question_caption"]}',
											text = '{$_POST["question_text"]}',
											poster = '{$_SESSION["auth"]["userid"]}',
											date = '$courent_date',
											catid = '{$_POST["question_cat"]}'
								 			WHERE itemid = '{$_GET["itemid"]}'");

								if($add_it == 1) {

									$func->confirmation($lang['faq']['change_onlyquest_ok'],"");
									$_SESSION['change_blocker_faqitem'] = 1;

								}
						}

						else {

							$func->error("NO_REFRESH","");

						}

					}
		}

			else {

				$func->error($lang['faq']['quest_not_exists'],"");

			}

	break; // BREAK CASE 2

}

?>
