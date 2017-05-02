<?php
/**
 * Generate Signonstatus. Show Counter and Bar
 *
 * @package lansuite_core
 * @author knox
 * @version $Id: signonstatus.php 1956 2009-08-14 14:07:05Z johannes.pieringer@gmail.com $
 */

// mit oder ohne orgas
if ($cfg["guestlist_showorga"] == 0) {
    $querytype = "type = 1";
} else {
    $querytype = "type >= 1";
}

// Ermittle die Anzahl der registrierten Usern
$get_cur = $db->qry_first('SELECT count(userid) as n FROM %prefix%user AS user WHERE %plain%', $querytype);
$reg = $get_cur["n"];

// Ermittle die Anzahl der derzeit angemeldeten Usern
$get_cur = $db->qry_first('SELECT count(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE party_id=%int% AND (%plain%)', $party->party_id, $querytype);
$cur = $get_cur["n"];

// Wieviele davon haben bezahlt
$get_cur = $db->qry_first('SELECT count(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE (%plain%) AND (party.paid > 0) AND party_id=%int%', $querytype, $party->party_id);
$paid = $get_cur["n"];

// Anzahl der max. Teilnehmer
$max = $_SESSION['party_info']['max_guest'];

// Sicher ist sicher
if ($paid > $cur) {
    $paid = $cur;
}

// Max werden 112 Pixel(Bars) angezeigt
$max_bars = 112;

// 2 Pixel werden abgezogen da diese schon links und rechts vorhanden sind.
$max_bars = $max_bars - 2;

// Angemeldet länge ausrechnen.
if ($max * $cur == 0) {
    $curuser = 0;
} else {
    $curuser = round($max_bars / $max * $cur);
    if ($curuser > $max_bars) {
        $curuser = $max_bars;
    }
}

// Bezahlt länge ausrechnen.
if ($max * $paid == 0) {
    $gesamtpaid = 0;
} else {
    $gesamtpaid = round($max_bars / $max * $paid);
    if ($gesamtpaid > $max_bars) {
        $gesamtpaid = $max_bars;
    }
}

// Wirkliche Bildanzahl ausrechenn
$pixelges = $max_bars - $curuser;
$pixelcuruser = $curuser - $gesamtpaid;
$pixelpaid = $gesamtpaid;

// Bar erzeugen
if ($pixelpaid > 0) {
    $bar = '<ul class="BarOccupied infolink" style="width:'. $pixelpaid .'px;">&nbsp;<span class="infobox">'. t('Angemeldet und Bezahlt') .': '. $paid .'</span></ul>';
}
if ($pixelcuruser > 0) {
    $bar .= '<ul class="BarMarked infolink" style="width:'. $pixelcuruser .'px;">&nbsp;<span class="infobox">'. t('Nur Angemeldet') .': '. ($cur - $paid) .'</span></ul>';
}
if ($pixelges > 0) {
    $bar .= '<ul class="BarFree infolink" style="width:'. $pixelges .'px;">&nbsp;<span class="infobox">'. t('Frei') .': '. ($max - $cur) .'</span></ul>';
}
$bar .= '<ul class="BarClear">&nbsp;</ul>';

if ($cfg['sys_internet']) {
    #if (strlen($_SESSION['party_info']['name']) > 16) $party_name = substr($_SESSION['party_info']['name'], 0, 14) .'...';
  #else
    $options = '';
    $res = $db->qry('SELECT party_id, name FROM %prefix%partys');
    if ($db->num_rows($res) > 1 && $cfg['display_change_party']) {
        while ($row = $db->fetch_array($res)) {
            ($row['party_id'] == $party->party_id)? $selected = 'selected="selected"' : $selected = '';
            if (strlen($row['name']) > 20) {
                $row['name'] = substr($row['name'], 0, 18) .'...';
            }
            $options .= '<option '. $selected .' value="'. $row['party_id'] .'">'. $row['name'] .'</option>';
        }
        $box->ItemRow('data', '<form action=""><select name="set_party_id" class="form" >'. $options .'</select><br /><input type="submit" class="Button" value="Party wechseln" /></form>');
    } else {
        $box->ItemRow("data", '<b>'. $_SESSION['party_info']['name'] .'</b>');
    }
    $db->free_result($res);
  
    date_default_timezone_set($cfg['sys_timezone']);
    $box->EngangedRow(date("d.m.y", $_SESSION['party_info']['partybegin']) .' - '. date("d.m.y", $_SESSION['party_info']['partyend']));
}

$box->EngangedRow($bar);
$box->EngangedRow(t('Angemeldet').': '. $cur);
$box->EngangedRow(t('Bezahlt').': '. $paid);
$box->EngangedRow(t('Frei').': '. ($max - $paid));

if (!$cfg['sys_internet']) {
    $checkedin = $db->qry_first('SELECT COUNT(p.user_id) as n FROM %prefix%user AS u LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
    WHERE (%plain%) AND (p.checkin > 0) AND p.party_id = %int%', $querytype, $party->party_id);
    $box->EngangedRow(t('Eingecheckt').': '. ($checkedin['n']));

    $checkedout = $db->qry_first('SELECT COUNT(p.user_id) as n FROM %prefix%user AS u LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
    WHERE (%plain%) AND (p.checkout > 0) AND p.party_id = %int%', $querytype, $party->party_id);
    $box->EngangedRow(t('Ausgecheckt').': '. ($checkedout['n']));
}

## Counter
if ($cfg['sys_internet']) {
    $box->EmptyRow();
    $box->ItemRow("data", '<b>'. t('Counter') .'</b>');
  
    if ($_SESSION['party_info']['partyend'] < time()) {
        $box->EngangedRow(t('Diese Party ist bereits vorüber'));
    } else {
        $count = ceil(($_SESSION['party_info']['partybegin'] - time()) / 60);
        if ($count <= 1) {
            $count = t('Die Party läuft gerade!');
        } elseif ($count <= 120) {
            $count = t('Noch %1 Minuten.', array($count));
        } elseif ($count > 120 and $count <= 2880) {
            $count = t('Noch %1 Stunden.', array(floor($count/60)));
        } else {
            $count = t('Noch %1 Tage.', array(floor($count/1440)));
        }
  
        $box->EngangedRow($count);
  
        $checked = $db->qry_first("SELECT UNIX_TIMESTAMP(checked) AS n FROM %prefix%partys WHERE party_id = %int%", $party->party_id);
        $box->EmptyRow();
        $box->ItemRow("data", "<b>". t('Letzter Kontocheck') ."</b>");
        $box->EngangedRow($func->unixstamp2date($checked['n'], "datetime"));
    }
}
