<?php
$LSCurFile = __FILE__;

include_once('modules/usrmgr/search_main.inc.php');

function PaidIconLink($paid){
  global $dsp, $templ, $line, $party;
  
  // Only link, if selected party = current party
  if ($_POST["search_dd_input"][1] == $party->party_id) $templ['ms2']['link'] = 'index.php?mod=usrmgr&action=changepaid&step=2&userid='. $line['userid'];
  else $templ['ms2']['link'] = '';
  
  if ($paid) {
    $templ['ms2']['icon_name'] = 'paid';
    $templ['ms2']['icon_title'] = t('Bezahlt');
  } else {
    $templ['ms2']['icon_name'] = 'not_paid';
    $templ['ms2']['icon_title'] = t('Nicht bezahlt');
  }
  $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_icon');
  if ($templ['ms2']['link']) $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
  return $templ['ms2']['link_item'];
}

function ClanURLLink($clan_name) {
  global $line;

  if ($clan_name != '' and $line['clanurl'] != '' and $line['clanurl'] != 'http://') {
    if (substr($line['clanurl'], 0, 7) != 'http://') $line['clanurl'] = 'http://'. $line['clanurl'];
    return '<a href="'. $line['clanurl'] .'" target="_blank">'. $clan_name .'</a>';
  } else return $clan_name;
}

function IfLowerUserlevel($userid) {
  global $line, $auth;
  
  if ($line['type'] < $auth['type']) return true;
  else return false;
}

function IfLowerOrEqualUserlevel($userid) {
  global $line, $auth;
  
  if ($line['type'] <= $auth['type']) return true;
  else return false;
}


$ms2->AddTextSearchField('NGL/WWCL/LGZ-ID', array('u.nglid' => 'exact', 'u.nglclanid' => 'exact', 'u.wwclid' => 'exact', 'u.wwclclanid' => 'exact', 'u.lgzid' => 'exact', 'u.lgzclanid' => 'exact',));

$ms2->AddTextSearchDropDown(t('Benutzertyp'), 'u.type', array('' => t('Alle'), '1' => t('Gast'), '!1' => t('Nicht Gast'), '<0' => t('Deaktiviert'), '2' => t('Admin'), '3' => t('Operator'), '2,3' => t('Admin, oder Operator')));
	
$party_list = array('' => 'Alle', 'NULL' => 'Zu keiner Party angemeldet');
$row = $db->query("SELECT party_id, name FROM {$config['tables']['partys']}");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);
$ms2->AddTextSearchDropDown('Party', 'p.party_id', $party_list);#, $party->party_id

$ms2->AddTextSearchDropDown(t('Zahlstatus'), 'p.paid', array('' => t('Alle'), '0' => t('Nicht bezahlt'), '>1' => t('Bezahlt'), '1' => t('Bezahlt per Vorverkauf'), '2' => t('Bezahlt per Abendkasse')));
$ms2->AddTextSearchDropDown(t('Eingecheckt'), 'p.checkin', array('' => t('Alle'), '0' => t('Nicht eingecheckt'), '>1' => t('Eingecheckt')));
$ms2->AddTextSearchDropDown(t('Ausgecheckt'), 'p.checkout', array('' => t('Alle'), '0' => t('Nicht ausgecheckt'), '>1' => t('Ausgecheckt')));
$ms2->AddTextSearchDropDown(t('Geschlecht'), 'u.sex', array('' => t('Alle'), '0' => t('Unbekannt'), '1' => t('Männlich'), '2' => t('Weblich')));
$ms2->AddTextSearchDropDown(t('Accounts'), 'u.locked', array('' => t('Alle'), '0' => t('Nur freigegebene'), '1' => t('Nur gesperrte')));

$ms2->AddSelect('c.url AS clanurl');
$ms2->AddSelect('u.type');
$ms2->AddResultField(t('Clan'), 'c.name AS clan', 'ClanURLLink');
// If Party selected
if (($_POST["search_dd_input"][1] != '' and $_POST["search_dd_input"][1] != 'NULL') or ($_GET["search_dd_input"][1] != '' and $_GET["search_dd_input"][1] != 'NULL')) {
  $ms2->AddResultField(t('Bezahlt'), 'p.paid', 'PaidIconLink');
  $ms2->AddResultField(t('In'), 'p.checkin', 'MS2GetDate');
  $ms2->AddResultField(t('Out'), 'p.checkout', 'MS2GetDate');
}

$ms2->AddIconField('details', 'index.php?mod=usrmgr&action=details&userid=', t('Details'));
if ($party->count > 0) $ms2->AddIconField('signon', 'index.php?mod=usrmgr&action=party&user_id=', t('Partyanmeldung'));
$ms2->AddIconField('send_mail', 'index.php?mod=mail&action=newmail&step=2&userID=', t('Mail senden'));
$ms2->AddIconField('change_pw', 'index.php?mod=usrmgr&action=newpwd&step=2&userid=', t('Passwort ändern'), 'IfLowerOrEqualUserlevel');
if ($auth['type'] >= 2) $ms2->AddIconField('assign', 'index.php?mod=usrmgr&action=switch_user&step=10&userid=', t('Benutzer wechseln'), 'IfLowerUserlevel');
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=usrmgr&action=change&step=1&userid=', t('Editieren'));
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=usrmgr&action=delete&step=2&userid=', t('Löschen'));


if ($auth['type'] >= 2) {
  $res = $db->query("SELECT * FROM {$config['tables']['party_usergroups']}");
  $ms2->AddMultiSelectAction("Gruppenzuordung aufheben", "index.php?mod=usrmgr&action=group&step=30&group_id=0", 0);
  while ($row = $db->fetch_array($res)) {
    $ms2->AddMultiSelectAction("Der Gruppe '{$row['group_name']}' zuordnen", "index.php?mod=usrmgr&action=group&step=30&group_id={$row['group_id']}", 0);
  }
  $db->free_result($res);
}
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Freigeben'), "index.php?mod=usrmgr&action=account_lock&step=11", 1);
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Sperren'), "index.php?mod=usrmgr&action=account_lock&step=10", 1);
if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Löschen'), "index.php?mod=usrmgr&action=delete&step=10", 1);

$ms2->PrintSearch('index.php?mod=usrmgr&action=search', 'u.userid');
?>
