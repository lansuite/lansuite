<?php
$templ['box']['rows'] = "";

// mit oder ohne orgas
if($cfg["guestlist_showorga"] == 0) { $querytype = "type = 1"; } else { $querytype = "type >= 1"; }

// Ermittle die Anzahl der registrierten Usern
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user WHERE ($querytype)");
$reg = $get_cur["n"];



// Ermittle die Anzahl der derzeit angemeldeten Usern
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE party_id='{$party->party_id}' AND ($querytype)");
$cur = $get_cur["n"];

// Wieviele davon haben bezahlt
$get_cur = $db->query_first("SELECT count(userid) as n FROM {$config["tables"]["user"]} AS user LEFT JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id WHERE ($querytype) AND (party.paid > 0) AND party_id='{$party->party_id}'");
$paid = $get_cur["n"];

// Anzahl der max. Teilnehmer
$max = $_SESSION['party_info']['max_guest'];

// Sicher ist sicher
if ($paid > $cur){
	$paid = $cur;
}

// Max werden 112 Pixel(Bars) angezeigt
$max_bars = 112;

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
$bar = "<img src=\"design/{$auth['design']}/images/userbar_left.gif\" height=\"13\" border=\"0\" alt =\"\" />";

// Bezahlt
if ($pixelpaid > 0) $bar .= '<img src="design/'. $auth['design'] .'/images/userbar_center_green.gif" width="'. $pixelpaid .'" height="13" border="0" onmouseover="return overlib(\''. t('Bezahlt') .': '. $paid  .'\');" onmouseout="return nd();" alt="'. t('Bezahlt') .'" />';

//Angemeldet
if ($pixelcuruser > 0) $bar .= '<img src="design/'. $auth['design'] .'/images/userbar_center_yellow.gif" width="'. $pixelcuruser .'" height="13" border="0" onmouseover="return overlib(\''. t('Angemeldet') .': '. $cur  .'\');" onmouseout="return nd();" alt="'. t('Angemeldet') .'" />';

//Gesamt
if ($pixelges > 0) $bar .= '<img src="design/'. $auth['design'] .'/images/userbar_center_bg.gif" width="'. $pixelges .'" height="13" border="0" onmouseover="return overlib(\''. t('Frei') .': '. ($max - $paid)  .'\');" onmouseout="return nd();" alt="'. t('Frei') .'" />';

// rechts
$bar .= "<img src=\"design/{$auth['design']}/images/userbar_right.gif\" height=\"13\" border=\"0\" alt =\"\" />";

$box->ItemRow("user", '<b>'. $_SESSION['party_info']['name'] .'</b>');
$box->EngangedRow(date("d.m.y", $_SESSION['party_info']['partybegin']) .' - '. date("d.m.y", $_SESSION['party_info']['partyend']));

$box->EngangedRow($bar);
$box->EngangedRow(t('Angemeldet').': '. $cur);
$box->EngangedRow(t('Bezahlt').': '. $paid);
$box->EngangedRow(t('Frei').': '. ($max - $paid));

## Counter
$box->EmptyRow();
$box->ItemRow("data", '<b>'. t('Counter') .'</b>');

if ($_SESSION['party_info']['partyend'] < time()) $box->EngangedRow(t('Diese Party ist bereits vorrüber'));
else {
  $count = ceil(($_SESSION['party_info']['partybegin'] - time()) / 60);
  if ($count <= 1) $count = t('Die Party läuft gerade!');
  elseif ($count <= 120) $count = t('Noch %1 Minuten.', array($count));
  elseif ($count > 120 AND $count <= 2880) $count = t('Noch %1 Stunden.', array(floor($count/60)));
  else $count = t('Noch %1 Tage.', array(floor($count/1440)));

  $box->EngangedRow($count);

  $checked = $db->query_first("SELECT checked as n FROM {$config["tables"]["partys"]} WHERE party_id = ".(int)$party->party_id);
  $box->EmptyRow();
  $box->ItemRow("data", "<b>". t('Letzter Kontocheck') ."</b>" );
  $box->EngangedRow($func->unixstamp2date($checked['n'],"datetime" ));
}
?>