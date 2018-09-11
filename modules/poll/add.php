<?php

$dsp->NewContent(t('Poll hinzufügen / ändern'), t('Um den Poll hinzuzufügen / zu ändern, fülle bitte das folgende Formular vollständig aus.'));

$mf = new \LanSuite\MasterForm();

$mf->AddField(t('Name'), 'caption');
$mf->AddField(t('Bemerkung'), 'comment', '', \LanSuite\MasterForm::LSCODE_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Anonym'), 'anonym', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Mehrfachauswahl möglich'), 'multi', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
$mf->AddField(t('Zeitlich begrenzen'), 'endtime', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

$mf->AddDropDownFromTable(t('Benutzergruppe'), 'group_id', 'group_id', 'group_name', 'party_usergroups', t('Keine bestimmte Gruppe'));
$mf->AddField(t('Nur eingeloggt?'), 'requirement', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL);

// Poll Options
if ($_POST['poll_option']) {
    foreach ($_POST['poll_option'] as $key => $val) {
        $_POST["poll_option[$key]"] = $val;
    }
} elseif ($_GET['pollid']) {
    $res = $db->qry('SELECT caption FROM %prefix%polloptions WHERE pollid = %int% ORDER BY polloptionid', $_GET['pollid']);
    for ($z = 1; $row = $db->fetch_array($res); $z++) {
        if (!$_POST["poll_option[$z]"]) {
            $_POST["poll_option[$z]"] = $row['caption'];
        }
    }
    $db->free_result($res);
}
if ($_GET['pollid']) {
    $mf->AddField(t('Polloptionen ändern') .'|'. t('Achtung: Dies führt dazu, dass die Abstimmung zurückgesetzt wird!'), 'poll_reset', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 20);
}
for ($z = 1; $z <= 20; $z++) {
    ($z <= 2)? $optional = 0 : $optional = \LanSuite\MasterForm::FIELD_OPTIONAL;
    $mf->AddField(t('Option') ." $z", "poll_option[$z]", 'varchar(80)', '', $optional);
}

$mf->AdditionalDBUpdateFunction = 'UpdatePoll';
$mf->SendForm('index.php?mod=poll&action=change&step=2&pollid='. $_GET['pollid'], 'polls', 'pollid', $_GET['pollid']);
