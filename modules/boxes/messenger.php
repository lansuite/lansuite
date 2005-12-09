<?php
$templ['box']['rows'] = "";

// Check for valid login
if ($auth['login']) {

	// Buddylist
	$box->EngangedRow("<img src=\"design/{$auth["design"]}/images/messenger_topic_buddylist.gif\" border=\"0\"/>");

	$query = $db->query("SELECT b.buddyid, u.username
		FROM {$config["tables"]["buddys"]} b
		LEFT JOIN {$config["tables"]["user"]} u ON u.userid = b.buddyid
		WHERE b.userid = '{$auth['userid']}'
		ORDER BY u.username
		");
	while ($row = $db->fetch_array($query)) {

		// Is user online, or offline?
		$timeout = time() - 60*10;
		$row_login = $db->query_first("SELECT userid FROM {$config['tables']['stats_auth']} WHERE userid = '{$row['buddyid']}' AND login = '1' AND lasthit > $timeout");
		if ($row_login['userid']) $class = "menu";
		else $class = "admin";

		// Chop username
		if (strlen($row["username"]) > 12) {
			$usertemp = substr( $row["username"], 0, 10) . "...";
			$username = "<span title=\"{$row["username"]}\" class = \"$class\">$usertemp</span>";
		} else $username = "<span title=\"{$row["username"]}\" class = \"$class\">{$row["username"]}</span>";

		// New message available?
		$row_new_msg = $db->query_first("SELECT senderid FROM {$config['tables']['messages']} WHERE senderid = '$row[buddyid]' AND receiverid = '{$auth["userid"]}' AND	new = '1'");
		if ($row_new_msg['senderid']) $item = "message_blink";
		else $item = "message";

		// Session ID
		$msg_sid = "&" . session_name() . "=" . session_id();

		// Output
		$box->ItemRow($item,
			"<a href=\"#\" onclick=\"javascript:var w=window.open('base.php?mod=query&amp;queryid={$row["buddyid"]}$msg_sid','_blank','width=600,height=400,resizable=no');\" class=\"$class\">". $username . "</a> ". $dsp->FetchUserIcon($row["buddyid"]) ." <a href=\"index.php?mod=msgsys&amp;action=removebuddy&amp;queryid={$row["buddyid"]}\"><img src=\"design/{$auth["design"]}/images/arrows_delete.gif\" width=\"12\" height=\"13\" hspace=\"1\" vspace=\"0\" border=\"0\"></a>"
			);

		$buddycount++;
	}

	// No buddies
	if ($buddycount < 1) $box->DotRow("<i>{$lang['boxes']['messenger_no_buddies']}</i>");

	// Users not in buddylist
	$querynotinlist = $db->query("SELECT m.senderid, u.username
		FROM {$config['tables']['messages']} m
		LEFT JOIN {$config["tables"]["user"]} u ON u.userid = m.senderid
		WHERE m.receiverid = '{$auth['userid']}'
		ORDER BY u.username
		");
	while ($row=$db->fetch_array($querynotinlist)) {
		// Session ID
		$msg_sid = "&" . session_name() . "=" . session_id();

		$querynobody = $db->query("SELECT id FROM {$config['tables']['buddys']} WHERE buddyid = '{$row['senderid']}' AND userid = '{$auth['userid']}'");

		if ($db->num_rows($query_id = $querynobody) < "1" AND $notinlist_peoples[$row["senderid"]] != 1) {

			// Topic not in list
			if ($notinlist != "1") {
				$notinlist = "1";
				$box->EmptyRow();
				$box->EngangedRow("<img src=\"design/{$auth["design"]}/images/messenger_topic_notinlist.gif\" border=\"0\">");
			}

			// Is user online, or offline?
			$timeout = time() - 60*10;
			$row_login = $db->query_first("SELECT userid FROM {$config['tables']['stats_auth']} WHERE userid = '{$row['senderid']}' AND login = '1' AND lasthit > $timeout");
			if ($row_login['userid']) $class = "menu";
			else $class = "admin";

			// Chop username
			if (strlen($row["username"]) > 12) {
				$usertemp = substr( $row["username"], 0, 10) . "...";
				$username = "<span title=\"{$row["username"]}\" class = \"$class\">$usertemp</span>";
			} else $username = "<span title=\"{$row["username"]}\" class = \"$class\">{$row["username"]}</span>";

			// Output
			$box->ItemRow("message_blink",
				"<a href=\"#\" onclick=\"javascript:var w=window.open('base.php?mod=query&amp;queryid={$row["senderid"]}$msg_sid','_blank','width=600,height=400,resizable=no');\" class=\"$class\">$username</a> ". $dsp->FetchUserIcon($row["senderid"]) ." <a href=\"index.php?mod=msgsys&amp;action=addbuddy&amp;step=2&amp;checkbox[]={$row["senderid"]}\"><img src=\"design/{$auth["design"]}/images/arrows_add.gif\" width=\"12\" height=\"13\" hspace=\"1\" vspace=\"0\" border=\"0\"></a>"
				);

			$notinlist_peoples[$row["senderid"]] = 1;
		}
	}

	$box->EmptyRow();
	$box->ItemRow("add", $lang['boxes']['messenger_add_buddy'], "index.php?mod=msgsys&amp;action=addbuddy", "", "menu");

	$boxes['messenger'] .= $box->CreateBox("messenger",$lang['boxes']['messenger']);
}
?>