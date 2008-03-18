<?php

switch($_GET['step']) {
	case 2:
		$POLL = $db->qry_first('SELECT multi FROM %prefix%polls WHERE	pollid = %int%', $_GET['pollid']);
		if ($POLL['multi']) {
			if ($_POST['option'] == '') {
				$func->information(t('Sie m&uuml;ssen eine Option ausw&auml;hlen'), "");
				$_GET['step'] = "1";
			}
		} else {
			if($_POST['option'] == "") {
				$func->information(t('Sie m&uuml;ssen eine Option ausw&auml;hlen'), "");
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

			$dsp->NewContent(t('F&uuml;r Poll <b>%1</b> voten', $POLL["caption"]), t('Um f&uuml;r einen Poll zu voten, klicken Sie bitte das Feld der Option an f&uuml;r die Sie stimmen wollen. Wenn die Mehrfachauswahl f&uuml;r diesen Poll aktiviert ist, k&ouml;nnen Sie auch f&uuml;r mehrere Optionen stimmen.'));
			$dsp->SetForm("index.php?mod=poll&action=vote&step=2&pollid=". $_GET['pollid']);

			while($OPTIONS = $db->fetch_array()) {
				if ($POLL['multi']) $dsp->AddCheckBoxRow("option[]", $OPTIONS['caption'], '', '', '', '', '', $OPTIONS['polloptionid']);
				else $dsp->AddRadioRow("option", $OPTIONS["caption"], $OPTIONS["polloptionid"]);
			}

			$dsp->AddFormSubmitRow("vote");
			$dsp->AddBackButton("index.php?mod=poll", "poll/vote");
			$dsp->AddContent();

		} else $func->information(t('Dieser Poll kann nicht angezeigt werden. Dies kann folgende Ursachen haben:<li>Der Poll existiert nicht<li>Der Poll ist bereits beendet<li>Sie haben bereits f&uuml;r diesen Poll gevotet'), "index.php?mod=poll&action=show");
	break;

	case 2:
		$POLL = $db->qry_first('SELECT multi FROM %prefix%polls WHERE	pollid = %int%', $_GET['pollid']);
		$VOTE = $db->qry_first('SELECT pollvoteid FROM %prefix%pollvotes WHERE pollid = %int% AND userid = %int%', $_GET['pollid'], $auth['userid']);
		if (!$VOTE[pollvoteid]) {
			if ($POLL[multi]) foreach($_POST['option'] as $option) $db->qry('INSERT INTO %prefix%pollvotes SET pollid = %int%, userid = %int%, polloptionid = %int%', $_GET['pollid'], $auth['userid'], $option);
			else $db->qry('INSERT INTO %prefix%pollvotes SET pollid = %int%, userid = %int%, polloptionid = %int%', $_GET['pollid'], $auth['userid'], $_POST['option']);
			$func->confirmation(t('Ihre Stimme wurde gez&auml;hlt'), 'index.php?mod=poll&action=show&step=2&pollid='. $_GET['pollid']);
		} else $func->information(t('Sie haben bereits gevotet'), "");
	break;
}
?>