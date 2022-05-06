<?php

$gd = new \LanSuite\GD();

if ($_GET['action'] == 'change' and $_GET['sponsorid'] == '') {
    include_once('modules/sponsor/search.inc.php');
} else {
    $mf = new \LanSuite\MasterForm();

    $mf->AddField(t('Name'), 'name');
    $mf->AddField(t('Ziel-URL'), 'url', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddGroup('General');

    $code_popup_link_banner = '<ul>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'ngl\');">NGL-Button</a></li>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'wwcl\');">WWCL-Banner</a></li>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'adsense\');">Google Anzeigen</a></li>
    </ul>';

    $code_popup_link_box = '<ul>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'ngl\');">NGL-Button</a></li>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'wwcl\');">WWCL-Banner</a></li>
    <li><a href="javascript:OpenHelplet(\'sponsor\', \'adsense_box\');">Google Anzeigen</a></li>
    </ul>';

    $mf->AddField(t('Auf Sponsorenseite').'|'.t('Der Banner wird auf der Sponsorenseite angezeigt'), 'sponsor', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 3);
    $mf->AddField(t('Bild-Upload'), 'pic_upload', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/banner/', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path', 'varchar(255)', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_banner, 'pic_code', 'text', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddGroup('Sponsorenseite');

    $mf->AddField('', '', \LanSuite\MasterForm::IS_TEXT_MESSAGE, t('Wenn du hier keine Datei angibst, wird der Banner/Button automatisch durch verkleinern der oben angegebenen Datei erzeugt (Funktioniert nur bei heraufgeladenen Dateien).'));
    $mf->AddGroup('');

    $mf->AddField(t('In Rotations-Banner').'|'.t('Der Banner wird oben in den Rotations-Banner aufgenommen'), 'rotation', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 4);
    $mf->AddField(t('Bild-Upload'), 'pic_upload_banner', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/banner/', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path_banner', 'varchar(255)', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_banner, 'pic_code_banner', 'text', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Bei HTTPS verstecken').'|'.t('Diese Option solltest du aktivieren, wenn du den Banner-Code von einer externen Webseite lädst und er dort nur als HTTP Version verfügbar ist, du deine Webseite aber als HTTPS ausliefern möchtest.'), 'ssl_hide_banner', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddGroup('Rotation Banner');

    $mf->AddField(t('In Sponsoren-Box').'|'.t('Der Banner wird in der Sponsoren-Box angezeigt'), 'active', 'tinyint(1)', '', \LanSuite\MasterForm::FIELD_OPTIONAL, '', 4);
    $mf->AddField(t('Bild-Upload'), 'pic_upload_button', \LanSuite\MasterForm::IS_FILE_UPLOAD, 'ext_inc/banner/', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path_button', 'varchar(255)', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_box, 'pic_code_button', 'text', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Bei HTTPS verstecken').'|'.t('Diese Option solltest du aktivieren, wenn du den Banner-Code von einer externen Webseite lädst und er dort nur als HTTP Version verfügbar ist, du deine Webseite aber als HTTPS ausliefern möchtest.'), 'ssl_hide_button', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddGroup('Sponsoren Box');

    if ($func->isModActive('tournament2')) {
        $mf->AddDropDownFromTable(t('Sponsor einem Turnier zuordnen'), 'tournamentid', 'tournamentid', 'name', 'tournament_tournaments', t('Keine'), 'party_id = '. (int)$party->party_id);
    }
    $mf->AddField(t('Position'), 'pos');
    $mf->AddField(t('Text'), 'text', '', \LanSuite\MasterForm::HTML_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddGroup('Misc.');

    $mf->AdditionalDBAfterSelectFunction = 'RewriteFields';
    $mf->AdditionalDBPreUpdateFunction = 'UploadFiles';
    $mf->SendForm('index.php?mod=sponsor&amp;action='. $_GET['action'], 'sponsor', 'sponsorid', $_GET['sponsorid']);
}
