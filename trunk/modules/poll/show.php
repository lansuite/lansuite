<?php

include_once('modules/poll/class_poll.php');
$poll = new poll;

if ($_GET['step'] >= 2) {
	$pollrow = $db->qry_first('SELECT caption, comment, UNIX_TIMESTAMP(endtime) AS endtime, multi, anonym FROM %prefix%polls
    WHERE	pollid = %int% AND (!group_id OR group_id = %int%)', $_GET['pollid'], $auth['group_id']);
  $dsp->NewContent(t('Poll') .': '. $pollrow["caption"], $func->text2html($pollrow['comment']));

	$voted = $db->qry_first('SELECT 1 AS found FROM %prefix%polloptions AS o
    INNER JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
    WHERE o.pollid = %int% AND v.userid = %int%', $_GET['pollid'], $auth['userid']);
	if (!$pollrow['caption']) {
    $func->error(t('Dieser Poll existiert nicht, oder Sie haben keine Berechtigung ihn zu sehen'), NO_LINK);
  	$_GET['step'] = 1;
  }

  if ($_GET['step'] >= 3) {
    if ($pollrow['endtime'] and $pollrow['endtime'] < time()) {
      $func->information(t('Dieser Poll ist bereits beendet'));
      $_GET['step'] = 2;
    } elseif ($voted['found']) {
      $func->information(t('Sie haben bereits gevoted'));
      $_GET['step'] = 2;
    }
  }
}

switch ($_GET['step']) {
  default:
    include_once('modules/poll/search.inc.php');
  break;

  case 2:
    // Has voted? -> Show results
    if ($voted['found'] or ($pollrow['endtime'] and $pollrow['endtime'] < time())) $poll->ShowResult($_GET['pollid'], $pollrow['anonym']);

    // Has not voted? -> Show form
    else {
  		$dsp->SetForm('index.php?mod=poll&action=show&step=3&pollid='. $_GET['pollid']);
  
      $res = $db->qry('SELECT polloptionid, caption FROM %prefix%polloptions WHERE pollid = %int% ORDER BY polloptionid', $_GET['pollid']);
      while ($row = $db->fetch_array($res)) {
        if ($pollrow['multi']) $dsp->AddCheckBoxRow('option[]', $row['caption'], '', '', '', '', '', $row['polloptionid']);
        else $dsp->AddRadioRow("option", $row['caption'], $row['polloptionid']);
      }
      $db->free_result($res);
  
  		$dsp->AddFormSubmitRow("vote");
  	}
		$dsp->AddBackButton("index.php?mod=poll", "poll/vote");
  break;

  case 3:
		if ($pollrow['multi']) foreach($_POST['option'] as $option) $db->qry('INSERT INTO %prefix%pollvotes SET userid = %int%, polloptionid = %int%', $auth['userid'], $option);
		else $db->qry('INSERT INTO %prefix%pollvotes SET userid = %int%, polloptionid = %int%', $auth['userid'], $_POST['option']);
		$func->confirmation(t('Ihre Stimme wurde gezählt'), 'index.php?mod=poll&action=show&step=2&pollid='. $_GET['pollid']);
  break;
}

/*
switch($_GET['action']) {
  default:

    switch($_GET['step']) {
        default:
          include_once('modules/poll/search.inc.php');
        break;

        case 2:
            $CHECK["poll"]  = $db->qry('SELECT pollid FROM %prefix%polls WHERE pollid = %int%', $_GET['pollid']);
            $CHECK["uservoted"] = $db->qry('SELECT pollvoteid FROM %prefix%pollvotes WHERE pollid   = %int% AND userid = %int%', $_GET['pollid'], $auth['userid']);
            if ($db->num_rows($query_id = $CHECK["poll"]) == "1") {
                $POLL = $db->qry_first('SELECT caption, comment, anonym, multi, UNIX_TIMESTAMP(endtime) AS endtime, group_id FROM %prefix%polls WHERE pollid = %int%', $_GET['pollid']);
                $CHECK["totalvotes"] = $db->qry('SELECT pollvoteid FROM %prefix%pollvotes WHERE pollid = %int%', $_GET['pollid']);
                $POLL_OPTION_QUERY = $db->qry('SELECT caption, polloptionid FROM %prefix%polloptions WHERE pollid = %int%', $_GET['pollid']);

                $dsp->NewContent(t('Poll anzeigen: %1', $POLL["caption"]), "");
                $dsp->AddDoubleRow(t('Bemerkung'), $func->text2html($POLL["comment"]));

                $array_index = "0";
                while($POLL_OPTION = $db->fetch_array($query_id = $POLL_OPTION_QUERY)) {
                    $POLL_VOTES_QUERY = $db->qry('SELECT userid FROM %prefix%pollvotes WHERE polloptionid = %int%', $POLL_OPTION['polloptionid']);

                    if ($POLL["anonym"] == FALSE) {
                        unset($first_entry);
                        $templ['poll']['show']['details']['case']['control']['javascript'] .= "votes[$array_index] = '";

                        while($POLL_VOTES = $db->fetch_array($query_id = $POLL_VOTES_QUERY)) {
                            if($first_entry == FALSE) {
                                $templ['poll']['show']['details']['case']['control']['javascript'] .= t('Folgende Benutzer haben für diese Option gevotet') .": ";

                                $USER = $db->qry_first('SELECT username, name, firstname FROM %prefix%user WHERE userid = %int%', $POLL_VOTES['userid']);
                                $templ['poll']['show']['details']['case']['control']['javascript'] .= " " . addslashes($USER["username"]);

                                $first_entry = TRUE;
                            } else {
                                $USER = $db->qry_first('SELECT username, name, firstname FROM %prefix%user WHERE userid = %int%', $POLL_VOTES['userid']);
                                $templ['poll']['show']['details']['case']['control']['javascript'] .= ", " . addslashes($USER["username"]);
                            }
                        }
                        if ($first_entry == FALSE) $templ['poll']['show']['details']['case']['control']['javascript'] .= t('Für diese Option hat niemand gevotet');
                        $templ['poll']['show']['details']['case']['control']['javascript'] .= "';";
                        $templ['poll']['show']['details']['case']['control']['javascript_title']    = t('Bewegen Sie den Mauszeiger über einen der Balken, um zu sehen wer für diese Option abgestimmt hat.');
                    }

                    $totalvotes = $db->num_rows($query_id = $CHECK["totalvotes"]);
                    $optionvotes    = $db->num_rows($query_id = $POLL_VOTES_QUERY);
                    if ($optionvotes == "") $optionvotes=0;

                    if($optionvotes > 0) {
                        $templ['poll']['show']['details']['row']['control']['percent']      = floor($optionvotes / $totalvotes * 100);
                        $templ['poll']['show']['details']['row']['info']['percent']     = number_format($optionvotes / $totalvotes * 100, 0, ",", ".");
                    } else $templ['poll']['show']['details']['row']['info']['percent']      = "0";
                    $templ['poll']['show']['details']['row']['info']['votes']           = $optionvotes;

                    if ($POLL["anonym"] == FALSE) $templ['poll']['show']['details']['row']['control']['link']       = " onMouseOver=\"showvotes($array_index)\" onMouseOut=\"remove()\" ";

            if ($templ['poll']['show']['details']['row']['control']['percent'] == '') $templ['poll']['show']['details']['row']['control']['percent'] = 0;
            $bar = '<ul class="BarOccupied" style="width:'. $templ['poll']['show']['details']['row']['control']['percent'] .'%;" '. $templ['poll']['show']['details']['row']['control']['link'] .'>'. $templ['poll']['show']['details']['row']['info']['votes'] .' ('. $templ['poll']['show']['details']['row']['info']['percent'] .'%) </ul>';
            $bar .= '<ul class="BarClear">&nbsp;</ul>';

                    $dsp->AddDoubleRow($POLL_OPTION["caption"], $bar);
                    unset($templ['poll']['show']['details']['row']['control']['percent']);

                    $array_index++;
                }

                if($POLL["anonym"] == FALSE) $dsp->AddDoubleRow("", $dsp->FetchModTpl("poll", "show_voters"));
                ($POLL["endtime"] < "1" OR $POLL["endtime"] > time())? $endtime = t('Offen') : $endtime = t('Beendet');
                ($POLL["anonym"] == "1")? $anonym = t('Ja') : $anonym = t('Nein');
                ($POLL["multi"] == "1")? $multi = t('Ja') : $multi = t('Nein');

                $dsp->AddDoubleRow('', $db->num_rows($query_id = $CHECK["totalvotes"]) .' '. t('Stimmen gesamt') .'<br />'.
                t('Status') .': '. $endtime .'<br />'.
                t('Anonym') .': '. $anonym .'<br />'.
                t('Mehrfachauswahl') .': '. $multi
          );

                if($auth["type"] > 1) {
                    $buttons .= $dsp->FetchButton("index.php?mod=poll&action=change&step=2&pollid={$_GET['pollid']}", "edit");
                    $buttons .= $dsp->FetchButton("index.php?mod=poll&action=delete&step=2&pollid={$_GET['pollid']}", "delete");
                }

                if((($POLL["endtime"] == 0) OR ($POLL["endtime"] > time())) AND ($db->num_rows($query_id = $CHECK["uservoted"]) < 1) AND ($auth["login"] == "1") AND ($POLL['group_id'] == 0 OR $POLL['group_id'] == $auth['group_id'])) {
                    $buttons .= $dsp->FetchButton("index.php?mod=poll&action=vote&pollid={$_GET['pollid']}", "vote");
                }
                $dsp->AddDoubleRow("", $buttons);
                $dsp->AddBackButton("index.php?mod=poll", "poll/show");
                $dsp->AddContent();

                // Including comment-engine
                if($auth["login"] == 1) {
                    include('inc/classes/class_mastercomment.php');
                    new Mastercomment('Poll', $_GET['pollid']);
                }
                //End comment-engine

            } // Pollcheck
            else $func->error(t('Dieser Poll existiert nicht'),"index.php?mod=poll");
        break;
    } // switch step
  break;

  case 'search':
    include_once('modules/poll/search.inc.php');
  break;
}
*/
?>