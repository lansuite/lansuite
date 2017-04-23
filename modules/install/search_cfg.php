<?php
// Searchform for all Configsettings

function get_modullink($modul)
{
    return "<a href=\"index.php?mod=install&action=mod_cfg&step=10&module=".$modul."\">".$modul."</a>";
}

$dsp->NewContent('Suche Configschl&uuml;ssel', 'Zum bearbeiten auf den Modullink klicken');

// Init Mastersearch
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();
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
$ms2->AddResultField(t('Modul'), 'l.cfg_module', 'get_modullink');
$ms2->AddResultField(t('Value'), 'l.cfg_value');
$ms2->AddResultField(t('Beschreibung'), 'l.cfg_desc');

// Functionbuttons
// Einzeledit muss noch integriert werden.
//if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=install&action=mod_cfg&step=10&cfg_key=', t('Editieren'));

// Final Output
$ms2->PrintSearch('index.php?mod=install&action=search_cfg', 'l.cfg_key');
