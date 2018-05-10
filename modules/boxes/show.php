<?php
switch ($_GET['step']) {
    // Activate
    case 10:
        foreach ($_POST['action'] as $key => $val) {
            $db->qry("UPDATE %prefix%boxes SET active = 1 WHERE boxid = %int%", $key);
        }
        break;
  
    // Deactivate
    case 11:
        foreach ($_POST['action'] as $key => $val) {
            $db->qry("UPDATE %prefix%boxes SET active = 0 WHERE boxid = %int%", $key);
        }
        break;
  
    // Edit
    case 20:
        $mf = new \LanSuite\MasterForm();

        $mf->AddField(t('Titel'), 'name');
        $selections = array();
        $selections['0'] = t('Links');
        $selections['1'] = t('Rechts');
        $mf->AddField(t('Seite'), 'place', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
        $mf->AddField(t('Position'), 'pos');
        $mf->AddField(t('Aktiv'), 'active', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
        $selections = array();
        $selections['0'] = t('Egal');
        $selections['1'] = t('Nur in Intranet Version');
        $selections['2'] = t('Nur in Internet Verrsion');
        $mf->AddField(t('Nur online / offline'), 'internet', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
        $selections = array();
        $selections['0'] = t('Egal');
        $selections['1'] = t('Nur für ausgeloggte');
        $selections['2'] = t('Nur für eingeloggte');
        $selections['3'] = t('Nur für Admins + Superadmins');
        $selections['4'] = t('Nur für Superadmins');
        $mf->AddField(t('Login benötigt'), 'login', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);
        $mf->AddField(t('Modul benötigt'), 'module', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
        $mf->AddField(t('Quelldatei'), 'source');
        $mf->AddField(t('Callback'), 'callback', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

        $mf->SendForm('index.php?mod=boxes&amp;step=20', 'boxes', 'boxid', $_GET['boxid']);
        break;
  
    // Delete
    case 30:
        $md = new \LanSuite\MasterDelete();
        $md->Delete('boxes', 'boxid', $_GET['boxid']);
        break;
}

$dsp->NewContent(t('Box-Manager'), t('Hier kannst du die Anzeige und Position der Boxen verwalten'));

$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('');

$ms2->query['from'] = "%prefix%boxes AS b";
$ms2->query['default_order_by'] = 'place ASC, pos ASC, name ASC';

$ms2->AddTextSearchDropDown(t('Internet-Modus'), 'b.internet', array('' => t('Egal'), '1' => t('Nur im Intranet-Modus'), '2' => t('Nur im Internet-Modus')));
$ms2->AddTextSearchDropDown(t('Login'), 'b.login', array('' => t('Egal'), '1' => t('Nur für ausgeloggte'), '2' => t('Nur für eingeloggte'), '3' => t('Nur für Admins + Superadminen'), '4' => t('Nur für Superadminen')));

$ms2->AddResultField(t('Titel'), 'b.name');
$ms2->AddResultField(t('Boxid'), 'boxid');
$ms2->AddResultField(t('Seite'), 'b.place', 'PlaceName');
$ms2->AddResultField(t('Position'), 'b.pos');
$ms2->AddResultField(t('Aktiv'), 'b.active', 'TrueFalse');
$ms2->AddResultField(t('Quelldatei'), 'b.source');

if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=boxes&amp;step=20&amp;boxid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=boxes&amp;step=30&amp;boxid=', t('Löschen'));
}

$ms2->AddMultiSelectAction(t('Aktivieren'), 'index.php?mod=boxes&step=10');
$ms2->AddMultiSelectAction(t('Deaktivieren'), 'index.php?mod=boxes&step=11');

$ms2->PrintSearch('index.php?mod=boxes', 'b.boxid');
$dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=boxes&amp;step=20'));
