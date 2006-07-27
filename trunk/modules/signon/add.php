<?php

$user = $db->query_first("SELECT * FROM {$config["tables"]["party_user"]} WHERE user_id = '{$auth['userid']}' AND party_id = '{$party->party_id}'");

$currenttime = time();
if ($user["user_id"]) $func->information($lang['signon']['allready'], "index.php?mod=news");

// Signon started?
elseif ($_SESSION['party_info']['s_startdate'] >= $currenttime) { # and $_GET['signon'] != 0
	$func->information(HTML_NEWLINE . "{$lang['signon']['signon_start']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_startdate'], "daydatetime"). "</strong>", "");
	if($auth["login"] == 0) {
		$dsp->NewContent("");
		$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
		$dsp->AddContent();
	}

// Signon ended?
} elseif($_SESSION['party_info']['s_enddate'] <= $currenttime) { # and $_GET['signon'] != 0
	$func->information( HTML_NEWLINE . "{$lang['signon']['signon_closed']}:" . HTML_NEWLINE . HTML_NEWLINE . "<strong>". $func->unixstamp2date($_SESSION['party_info']['s_enddate'],"daydatetime"). "</strong>", "");
	if ($auth["login"] == 0){
		$dsp->NewContent("");
		$dsp->AddDoubleRow("", "<a href=\"index.php?mod=signon&action=add&step=2&signon=0\">". $lang["signon"]["add_not_registered_nosignup"] ."</a>");
		$dsp->AddContent();
	}
} else {

  // If user is logged in: Show only AGB and price selection
  if ($auth['login']) {
    $dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption2"]), str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_subcaption2"]));

    $DoSignon = 1;
    $_GET['userid'] = $auth['userid'];

  // Show form to insert data
  } else $dsp->NewContent(str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_caption"]), str_replace("%NAME%", $_SESSION['party_info']['name'], $lang["signon"]["add_subcaption"]));

  include_once ("modules/usrmgr/language/usrmgr_lang_de.php");
  if ($language == 'en') include_once ("modules/usrmgr/language/usrmgr_lang_en.php");
  include_once ("modules/usrmgr/add.php");

	$dsp->AddContent();
}
?>