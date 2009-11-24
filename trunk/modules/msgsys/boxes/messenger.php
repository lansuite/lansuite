<?php
/**
 * Generate Box for Messenger
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: messenger.php 2028 2009-11-23 15:23:32Z jochen.jung $
 */
 
// Check for valid login
if ($auth['login']) {

	// Buddylist
	$box->EngangedRow('<span class="copyright">-- Buddy List --</span>');

	$query = $db->qry("SELECT b.buddyid, u.username, a.login, a.lasthit
		FROM %prefix%buddys AS b
		LEFT JOIN %prefix%user AS u ON b.buddyid = u.userid
		LEFT JOIN %prefix%stats_auth AS a ON b.buddyid = a.userid
		WHERE b.userid = %int%
		GROUP BY b.buddyid
		ORDER BY u.username
		", $auth['userid']);

	while ($row = $db->fetch_array($query)) {

		// Is user online, or offline?
		$timeout = time() - 60*10;
		if ($row['login'] == 1 and $row['lasthit'] > $timeout) $class = "menu";
		else $class = "admin";

		// Chop username
		if (strlen($row["username"]) > 12) {
			$usertemp = substr( $row["username"], 0, 10) . "...";
			$username = "<span title=\"{$row["username"]}\" class = \"$class\">$usertemp</span>";
		} else $username = "<span title=\"{$row["username"]}\" class = \"$class\">{$row["username"]}</span>";

		// Session ID
		$msg_sid = "&" . session_name() . "=" . session_id();
		
		// New message available?
		$row_new_msg = $db->qry_first('SELECT senderid FROM %prefix%messages WHERE senderid = %int% AND receiverid = %int% AND new = \'1\'', $row['buddyid'], $auth["userid"]);
		if ($row_new_msg['senderid']){
			$item = "message_blink";
			if($cfg['msgsys_popup']){
				$caption = "<script type=\"text/javascript\" language=\"JavaScript\"> 
								var link = \"index.php?mod=msgsys&amp;action=query&amp;design=base&amp;queryid={$row["buddyid"]}$msg_sid\";
								var suche = /&amp;/;

								while(suche.exec(link)){
									link = link.replace(suche, \"&\");
								}
					   			window.open(link,'_blank','width=600,height=400,resizable=no');
							</script>";
			}else $caption = "";
		}else{
			$item = "message";
			$caption = "";
		}

		// Output
		$caption .= "<a href= \"#\" onclick=\"javascript:var w=window.open('index.php?mod=msgsys&amp;action=query&amp;design=base&amp;queryid={$row["buddyid"]}$msg_sid','_blank','width=600,height=400,resizable=no');\" class=\"$class\">". $username . "<span class=\"infobox\">". t('Messenger mit %1 starten', $row["username"]) ."</span></a> ". $dsp->FetchUserIcon($row["buddyid"]) ." <a href=\"index.php?mod=msgsys&amp;action=removebuddy&amp;queryid={$row["buddyid"]}\"><img src=\"design/{$auth["design"]}/images/arrows_delete.gif\" width=\"12\" height=\"13\" hspace=\"1\" vspace=\"0\" border=\"0\"><span class=\"infobox\">". t('Benutzer %1 aus Buddy-Liste entfernen', $row["username"]) ."</span></a>";
		$box->ItemRow($item,$caption);

		$buddycount++;
	}

	// No buddies
	if ($buddycount < 1) $box->DotRow("<i>". t('Noch keine Buddies') ."</i>");

	// Users not in buddylist
	$querynotinlist = $db->qry("SELECT m.senderid, u.username
		FROM %prefix%messages m
		LEFT JOIN %prefix%user u ON u.userid = m.senderid
		WHERE m.receiverid = %int% AND m.new = 1
		ORDER BY u.username
		", $auth['userid']);
	while ($row=$db->fetch_array($querynotinlist)) {
		// Session ID
		$msg_sid = "&" . session_name() . "=" . session_id();

		$querynobody = $db->qry('SELECT id FROM %prefix%buddys WHERE buddyid = %int% AND userid = %int%', $row['senderid'], $auth['userid']);

		if ($db->num_rows($query_id = $querynobody) < "1" AND $notinlist_peoples[$row["senderid"]] != 1) {

			// Topic not in list
			if ($notinlist != "1") {
				$notinlist = "1";
				$box->EmptyRow();
                $box->EngangedRow('<span class="copyright">-- Not in list --</span>');
			}

			// Is user online, or offline?
			$timeout = time() - 60*10;
			$row_login = $db->qry_first('SELECT userid FROM %prefix%stats_auth WHERE userid = %int% AND login = \'1\' AND lasthit > %int%', $row['senderid'], $timeout);
			if ($row_login['userid']) $class = "menu";
			else $class = "admin";

			// Chop username
			if (strlen($row["username"]) > 12) {
				$usertemp = substr( $row["username"], 0, 10) . "...";
				$username = "<span title=\"{$row["username"]}\" class = \"$class\">$usertemp</span>";
			} else $username = "<span class = \"$class\">{$row["username"]}</span>";

			// Output
			$box->ItemRow("message_blink",
				"<a href=\"#\" onclick=\"javascript:var w=window.open('index.php?mod=msgsys&amp;action=query&amp;design=base&amp;queryid={$row["senderid"]}$msg_sid','_blank','width=600,height=400,resizable=no');\" class=\"$class\">$username</a> ". $dsp->FetchUserIcon($row["senderid"]) ." <a href=\"index.php?mod=msgsys&amp;action=addbuddy&amp;step=2&amp;userid={$row["senderid"]}\"><img src=\"design/{$auth["design"]}/images/arrows_add.gif\" width=\"12\" height=\"13\" hspace=\"1\" vspace=\"0\" border=\"0\"><span class=\"infobox\">". t('Benutzer %1 in Buddy-Liste aufnehmen', $row["username"]) ."</span></a>"
				);

			$notinlist_peoples[$row["senderid"]] = 1;
		}
	}

	$box->EmptyRow();
	$box->ItemRow("add", t('Benutzer hinzufügen'), "index.php?mod=msgsys&amp;action=addbuddy", "", "menu");
}
?>
