<?php
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('news');

$ms2->query['from'] = "%prefix%sponsor AS s";

$ms2->AddResultField('Titel', 's.name');
$ms2->AddResultField('Autor', 's.url');

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=sponsor&amp;action=change&amp;sponsorid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=sponsor&amp;action=delete&amp;step=2&sponsorid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=sponsor&amp;action=change', 's.sponsorid');
