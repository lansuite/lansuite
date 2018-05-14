<?php
// Searchform for all Config settings

$dsp->NewContent('Suche Configschl&uuml;ssel', 'Zum bearbeiten auf den Modullink klicken');

// Init Mastersearch
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2();
$ms2->query['from'] = "%prefix%config l";

// Add Searchfields
$ms2->AddSelect('l.cfg_key');
$ms2->AddSelect('l.cfg_value');
$ms2->AddSelect('l.cfg_desc');
$ms2->AddTextSearchField(t('Alle Felder'), array('l.cfg_key' => 'like', 'l.cfg_value' => 'like', 'l.cfg_desc' => 'like', 'l.cfg_module' => 'like'));
$list = array('' => 'Alle');
$row = $db->qry('SELECT cfg_module FROM %prefix%config WHERE cfg_module != "" GROUP BY cfg_module ORDER BY cfg_module');
while ($res = $db->fetch_array($row)) {
    $list[$res['cfg_module']] = $res['cfg_module'];
}
$db->free_result($row);
$ms2->AddTextSearchDropDown('Modul', 'l.cfg_module', $list);

// Which columns should be displayed?
$ms2->AddResultField(t('Key'), 'l.cfg_key');
$ms2->AddResultField(t('Modul'), 'l.cfg_module', 'GetModulLink');
$ms2->AddResultField(t('Value'), 'l.cfg_value');
$ms2->AddResultField(t('Beschreibung'), 'l.cfg_desc');

// Final Output
$ms2->PrintSearch('index.php?mod=install&action=search_cfg', 'l.cfg_key');
