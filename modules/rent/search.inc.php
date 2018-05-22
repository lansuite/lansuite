<?php

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('news');

$ms2->query['from'] = "%prefix%rentstuff AS s
  LEFT JOIN %prefix%user AS o ON s.ownerid = o.userid
  LEFT JOIN %prefix%rentuser AS u ON u.stuffid = s.stuffid";

$ms2->AddTextSearchField('Titel', array('s.caption' => 'like'));

$ms2->AddSelect('o.userid');
$ms2->AddSelect('COUNT(u.rentid) AS rented');
$ms2->AddResultField('Titel', 's.caption');
$ms2->AddResultField('Verliehen', 's.quantity', 'RentCount');
$ms2->AddResultField('Besitzer', 'o.username', 'UserNameAndIcon');

if ($auth['type'] >= 2) {
    $ms2->AddIconField('assign', 'index.php?mod=rent&action=show&step=10&stuffid=', t('Zuweisen'));
}

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=rent&action=add&stuffid=', t('Editieren'));
}

if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=rent&action=delete&stuffid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=rent&action=show', 's.stuffid');
