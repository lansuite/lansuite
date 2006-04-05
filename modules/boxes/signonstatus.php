<?php
$templ['box']['rows'] = "";


// Check Date for LAN
if(time() > $_SESSION['party_info']['partyend']){
	$party_data = $party->get_next_party();
	$party_next = true;
}else {
	$party_data = $_SESSION['party_info'];
	$party_data['party_id'] = $party->party_id;
	$party_next = false;
}

// mit oder ohne orgas
if($cfg["guestlist_showorga"] == 0) { $querytype = "type = 1"; } else { $querytype = "type >= 1"; }

// Ermittle die Anzahl der registrierten Usern
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user WHERE ($querytype)");
$reg = $get_cur["n"];


if (!$party_data) {
	$box->ItemRow("user", "<b>{$lang['boxes']['signonstatus_partyinfos']}</b>");

	$box->EmptyRow();
	$box->EngangedRow("{$lang['boxes']['signonstatus_reg_users']}: $reg");

	$box->EmptyRow();
	$box->EngangedRow($lang['boxes']['signonstatus_no_party_planed']);
} else {
	## SignOns w bar


    // Ermittle die Anzahl der derzeit angemeldeten Usern
	$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE party_id='{$party_data['party_id']}' AND ($querytype)");
	$cur = $get_cur["n"];

	// Wieviele davon haben bezahlt
	$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE ($querytype) AND (party.paid > 0) AND party_id='{$party_data['party_id']}'");
	$paid = $get_cur["n"];

	// Anzahl der max. Teilnehmer
	$max = $party_data['max_guest'];

	// Sicher ist sicher
	if ($paid > $cur){
		$paid = $cur;
	}

	// Max werden 100 Pixel(Bars) angezeigt
	$max_bars = 100;

	// 2 Pixel werden abgezogen da diese schon links und rechts vorhanden sind.
	$max_bars = $max_bars - 2;

	// Angemeldet länge ausrechnen.
	$curuser = round($max_bars / $max * $cur);
	if ($curuser > $max_bars){
		$curuser = $max_bars;
	}

	// Bezahlt länge ausrechnen.
	$gesamtpaid = round($max_bars / $max * $paid);
	if ($gesamtpaid > $max_bars){
		$gesamtpaid = $max_bars;
	}

	// Wirkliche Bildanzahl ausrechenn
	$pixelges = $max_bars - $curuser;
	$pixelcuruser = $curuser - $gesamtpaid;
	$pixelpaid = $gesamtpaid;

	// Bar erzeugen
	// links
	$bar = "<img src=\"design/{$auth['design']}/images/userbar_left.gif\" height=\"13\" border=\"0\">";

	// Bezahlt
	if ($pixelpaid > 0) $bar .= "<img src=\"design/{$auth['design']}/images/userbar_center_green.gif\" width=\"$pixelpaid\" height=\"13\" border=\"0\" title=\"{$lang['boxes']['signonstatus_paid']}\">";

	//Angemeldet
	if ($pixelcuruser > 0) $bar .= "<img src=\"design/{$auth['design']}/images/userbar_center_yellow.gif\" width=\"$pixelcuruser\" height=\"13\" border=\"0\" title=\"{$lang['boxes']['signonstatus_signed_on']}\">";

	//Gesamt
	if ($pixelges > 0) $bar .= "<img src=\"design/{$auth['design']}/images/userbar_center_bg.gif\" width=\"$pixelges\" height=\"13\" border=\"0\" title=\"{$lang['boxes']['signonstatus_free']}\">";

	// rechts
	$bar .= "<img src=\"design/{$auth['design']}/images/userbar_right.gif\" height=\"13\" border=\"0\">";

	if ($party_next) {
		$box->ItemRow("user", "<b>{$lang['boxes']['signonstatus_next_party']}<br />{$party_data['name']}</b>");
		$templ['box']['signonstatus']['case']['info']['party'] = "<b>{$lang['boxes']['signonstatus_next_party']}<br />". $party_data['name'] ."</b>";
	} else {
		$box->ItemRow("user", "{$lang['boxes']['signonstatus_sign_on_at']}<br /><b>{$party_data['name']}</b>");
		$templ['box']['signonstatus']['case']['info']['party'] = "{$lang['boxes']['signonstatus_sign_on_at']}<br /><b>". $party_data['name'] ."</b>";
	}

	$box->EngangedRow($bar);
	$box->EngangedRow("{$lang['boxes']['signonstatus_reg']}: $reg");
	$box->EngangedRow("<img src=\"design/{$auth["design"]}/images/userbox_yellow.gif\" width=\"5\" height=\"13\" border=\"0\"> {$lang['boxes']['signonstatus_signed_on']}: $cur");
	$box->EngangedRow("<img src=\"design/{$auth["design"]}/images/userbox_green.gif\" width=\"5\" height=\"13\" border=\"0\"> {$lang['boxes']['signonstatus_paid']}: $paid");
	$box->EngangedRow("{$lang['boxes']['signonstatus_free_places']}: ". ($max - $paid));

	## Counter

	$count = round(($party_data['partybegin'] - time()) / 86400);
	if ($count <= 0) $count = $lang['boxes']['signonstatus_party_running'];
	elseif ($count == 1) $count = str_replace("%DAYS%", $count, $lang['boxes']['signonstatus_days_left_sing']);
	else $count = str_replace("%DAYS%", $count, $lang['boxes']['signonstatus_days_left_plur']);

	$box->EmptyRow();
	$box->ItemRow("data", "<b>{$lang['boxes']['signonstatus_counter']}</b>");
	$box->EngangedRow($count);

	$box->EmptyRow();
	$box->ItemRow("data", "<b>". $party_data['name'] . "</b>");
	$box->EngangedRow($func->unixstamp2date($party_data['partybegin'],"datetime") . " - " . HTML_NEWLINE . $func->unixstamp2date($party_data['partyend'],"datetime"));
}

$boxes['signonstatus'] .= $box->CreateBox("signon_state",$lang['boxes']['userdata_signon_state']);
?>