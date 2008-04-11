<?php

switch($_GET['step']) {
	case 2:
		$POLL = $db->qry_first('SELECT multi FROM %prefix%polls WHERE	pollid = %int%', $_GET['pollid']);
		if ($POLL['multi']) {
			if ($_POST['option'] == '') {
				$func->information($lang["poll"]["vote_err_nooption"], "");
				$_GET['step'] = "1";
			}
		} else {
			if($_POST['option'] == "") {
				$func->information($lang["poll"]["vote_err_nooption"], "");
				$_GET['step'] = "1";
			}	
		}
	break;	
}

switch ($_GET['step']) {
	default:
		$POLL = $db->qry_first('SELECT pollid, caption, endtime, multi FROM %prefix%polls WHERE	pollid = %int%', $_GET['pollid']);
		$VOTE = $db->qry_first('SELECT pollvoteid FROM %prefix%pollvotes WHERE pollid = %int% AND userid = %int%', $_GET['pollid'], $auth['userid']);

		if ((isset($POLL['pollid'])) AND ($poll['endtime'] > time() OR ($poll['endtime'] == "0" OR $poll['endtime'] == "")) AND (!$VOTE['pollvoteid'])) {
			$OPTIONS = $db->qry('SELECT	polloptionid, caption FROM %prefix%polloptions WHERE pollid = %int%', $_GET['pollid']);

			$dsp->NewContent(str_replace("%NAME%", $POLL["caption"], $lang["poll"]["vote_caption"]), $lang["poll"]["vote_subcaption"]);
			$dsp->SetForm("index.php?mod=poll&action=vote&step=2&pollid=". $_GET['pollid']);

			while($OPTIONS = $db->fetch_array($query)) {
				if ($POLL['multi']) $dsp->AddCheckBoxRow("option[]", $OPTIONS['caption'], '', '', '', '', '', $OPTIONS['polloptionid']);
				else $dsp->AddRadioRow("option", $OPTIONS["caption"], $OPTIONS["polloptionid"]);
			}

			$dsp->AddFormSubmitRow("vote");
			$dsp->AddBackButton("index.php?mod=poll", "poll/vote");
			$dsp->AddContent();

		} else $func->information($lang["poll"]["vote_err_common"], "index.php?mod=poll&action=show");
	break;

	case 2:
		$POLL = $db->qry_first('SELECT multi FROM %prefix%polls WHERE	pollid = %int%', $_GET['pollid']);
		$VOTE = $db->qry_first('SELECT pollvoteid FROM %prefix%pollvotes WHERE pollid = %int% AND userid = %int%', $_GET['pollid'], $auth['userid']);
		if (!$VOTE[pollvoteid]) {
			if ($POLL[multi]) foreach($_POST['option'] as $option) $db->qry('INSERT INTO %prefix%pollvotes SET pollid = %int%, userid = %int%, polloptionid = %int%', $_GET['pollid'], $auth['userid'], $option);
			else $db->qry('INSERT INTO %prefix%pollvotes SET pollid = %int%, userid = %int%, polloptionid = %int%', $_GET['pollid'], $auth['userid'], $_POST['option']);
			$func->confirmation($lang["poll"]["vote_success"], 'index.php?mod=poll&action=show&step=2&pollid='. $_GET['pollid']);
		} else $func->information($lang["poll"]["vote_err_allready"], "");
	break;
}
?>