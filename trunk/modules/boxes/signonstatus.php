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
if ($max * $cur == 0) $curuser = 0;
else {
  $curuser = round($max_bars / $max * $cur);
  if ($curuser > $max_bars) $curuser = $max_bars;
}

// Bezahlt länge ausrechnen.
if ($max * $paid == 0) $gesamtpaid = 0;
else {
  $gesamtpaid = round($max_bars / $max * $paid);
  if ($gesamtpaid > $max_bars) $gesamtpaid = $max_bars;
}

// Wirkliche Bildanzahl ausrechenn
$pixelges = $max_bars - $curuser;
$pixelcuruser = $curuser - $gesamtpaid;
$pixelpaid = $gesamtpaid;

// Bar erzeugen
if ($pixelpaid > 0) $bar = '<ul class="BarOccupied" style="width:'. $pixelpaid .'px;" onmouseover="return overlib(\''. t('Angemeldet und Bezahlt') .': '. $paid  .'\');" onmouseout="return nd();">&nbsp;</ul>';
if ($pixelcuruser > 0) $bar .= '<ul class="BarMarked" style="width:'. $pixelcuruser .'px;" onmouseover="return overlib(\''. t('Nur Angemeldet') .': '. ($cur - $paid) .'\');" onmouseout="return nd();">&nbsp;</ul>';
if ($pixelges > 0) $bar .= '<ul class="BarFree" style="width:'. $pixelges .'px;" onmouseover="return overlib(\''. t('Frei') .': '. ($max - $cur)  .'\');" onmouseout="return nd();">&nbsp;</ul>';
$bar .= '<ul class="BarClear">&nbsp;</ul>';

#if (strlen($_SESSION['party_info']['name']) > 16) $party_name = substr($_SESSION['party_info']['name'], 0, 14) .'...';
#else
$options = '';
$res = $db->qry('SELECT party_id, name FROM %prefix%partys');
if ($db->num_rows($res) > 1) {
  while ($row = $db->fetch_array($res)){
  	($row['party_id'] == $party->party_id)? $selected = 'selected="selected"' : $selected = '';
  	if (strlen($row['name']) > 20) $row['name'] = substr($row['name'], 0, 18) .'...';
  	$options .= '<option '. $selected .' value="'. $row['party_id'] .'">'. $row['name'] .'</option>';
  }
  $box->ItemRow('data', '<form action=""><select name="set_party_id">'. $options .'</select><br /><input type="submit" value="Party wechseln" /></form>');
} else {
  $box->ItemRow("data", '<b>'. $_SESSION['party_info']['name'] .'</b>');
}
$db->free_result($res);

$box->EngangedRow(date("d.m.y", $_SESSION['party_info']['partybegin']) .' - '. date("d.m.y", $_SESSION['party_info']['partyend']));

$box->EngangedRow($bar);
$box->EngangedRow(t('Angemeldet').': '. $cur);
$box->EngangedRow(t('Bezahlt').': '. $paid);
$box->EngangedRow(t('Frei').': '. ($max - $paid));

## Counter
$box->EmptyRow();
$box->ItemRow("data", '<b>'. t('Counter') .'</b>');

if ($_SESSION['party_info']['partyend'] < time()) $box->EngangedRow(t('Diese Party ist bereits vorüber'));
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
