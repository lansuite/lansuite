<?
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('news');

$ms2->query['from'] = "{$config["tables"]["rentstuff"]} AS s
  LEFT JOIN {$config["tables"]["user"]} AS o ON s.ownerid = o.userid
  LEFT JOIN {$config["tables"]["rentuser"]} AS u ON u.stuffid = s.stuffid";

$ms2->AddTextSearchField('Titel', array('s.caption' => 'like'));

#$ms2->AddTextSearchDropDown('Verliehen', 's.userid', ('' => 'Verliehen'));

$ms2->AddSelect('o.userid');
$ms2->AddResultField('Titel', 's.caption');
$ms2->AddResultField('Verfügbar', 's.quantity');
$ms2->AddResultField('Verliehen', 's.rented');
$ms2->AddResultField('Anz. Mieter', 'COUNT(*) AS Renters');
$ms2->AddResultField('Besitzer', 'o.username', 'UserNameAndIcon');

$ms2->AddIconField('assign', 'index.php?mod=rent&action=show_stuff&step=2&itemid=', $lang['ms2']['assign']);
if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=rent&action=delete_stuff&step=10&itemid=', $lang['ms2']['edit']);
if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=rent&action=delete_stuff&step=3&itemid=', $lang['ms2']['delete']);

$ms2->PrintSearch('index.php?mod=rent&action=show_stuff', 's.stuffid');
?>