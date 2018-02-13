<?php

$gd = new gd();

function RewriteFields()
{
    if (substr($_POST['pic_path'], 0, 12) == 'html-code://') {
        $_POST['pic_code'] = substr($_POST['pic_path'], 12, strlen($_POST['pic_path']) - 12);
        $_POST['pic_path'] = '';
    }
    if (substr($_POST['pic_path_banner'], 0, 12) == 'html-code://') {
        $_POST['pic_code_banner'] = substr($_POST['pic_path_banner'], 12, strlen($_POST['pic_path_banner']) - 12);
        $_POST['pic_path_banner'] = '';
    }
    if (substr($_POST['pic_path_button'], 0, 12) == 'html-code://') {
        $_POST['pic_code_button'] = substr($_POST['pic_path_button'], 12, strlen($_POST['pic_path_button']) - 12);
        $_POST['pic_path_button'] = '';
    }
}

function UploadFiles()
{
    global $func, $gd;
    // Check for errors
    if ($_POST['name'] == '') {
        $name_error = t('Bitte gib einen Namen ein');
        $_GET['step'] = 1;
    }
    if (strlen($_POST['text']) > 5000) {
        $text_error = t('Der Text darf nicht mehr als 5000 Zeichen enthalten');
        $_GET['step'] = 1;
    }

    // --- Sponsor Page Banner ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload']['name']) {
        $_POST['pic_path'] = $_POST['pic_upload'];

    // 2) Was an external URL given?
    } elseif ($_POST['pic_path'] != 'http://' and $_POST['pic_path'] != '') {
        $_POST['pic_path'] = $_POST['pic_path'];
    } // 3) Was a code submitted?
    elseif ($_POST['pic_code'] != '') {
        $_POST['pic_path'] = $_POST['pic_code'];
        if (substr($_POST['pic_path'], 0, 12) != 'html-code://') {
            $_POST['pic_path'] = 'html-code://'. $_POST['pic_path'];
        }
    }

    // --- Rotation Banner ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_banner']['name']) {
        $_POST['pic_path_banner'] = $_POST['pic_upload_banner'];

    // 2) Was an external URL given?
    } elseif ($_POST['pic_path_banner'] != 'http://' and $_POST['pic_path_banner'] != '') {
        $_POST['pic_path_banner'] = $_POST['pic_path_banner'];
    } // 3) Was a code submitted?
    elseif ($_POST['pic_code_banner'] != '') {
        $_POST['pic_path_banner'] = $_POST['pic_code_banner'];
        if (substr($_POST['pic_path_banner'], 0, 12) != 'html-code://') {
            $_POST['pic_path_banner'] = 'html-code://'. $_POST['pic_path_banner'];
        }

    // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
        $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'], 468, 60);
        $_POST['pic_path_banner'] = 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'];
    }

    // --- Box Button ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_button']['name']) {
        $_POST['pic_path_button'] = $_POST['pic_upload_button'];

    // 2) Was an external URL given?
    } elseif ($_POST['pic_path_button'] != 'http://' and $_POST['pic_path_button'] != '') {
        $_POST['pic_path_button'] = $_POST['pic_path_button'];
    } // 3) Was a code submitted?
    elseif ($_POST['pic_code_button'] != '') {
        $_POST['pic_path_button'] = $_POST['pic_code_button'];
        if (substr($_POST['pic_path_button'], 0, 12) != 'html-code://') {
            $_POST['pic_path_button'] = 'html-code://'. $_POST['pic_path_button'];
        }

    // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
        $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'], 468, 60);
        $_POST['pic_path_button'] = 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'];
    }
}


if ($_GET['action'] == 'change' and $_GET['sponsorid'] == '') {
    include_once('modules/sponsor/search.inc.php');
} else {
    $mf = new masterform();

    $mf->AddField(t('Name'), 'name');
    $mf->AddField(t('Ziel-URL'), 'url', '', '', FIELD_OPTIONAL);
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

    $mf->AddField(t('Auf Sponsorenseite').'|'.t('Der Banner wird auf der Sponsorenseite angezeigt'), 'sponsor', 'tinyint(1)', '', FIELD_OPTIONAL, '', 3);
    $mf->AddField(t('Bild-Upload'), 'pic_upload', IS_FILE_UPLOAD, 'ext_inc/banner/', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path', 'varchar(255)', '', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_banner, 'pic_code', 'text', '', FIELD_OPTIONAL);
    $mf->AddGroup('Sponsorenseite');

    $mf->AddField('', '', IS_TEXT_MESSAGE, t('Wenn du hier keine Datei angibst, wird der Banner/Button automatisch durch verkleinern der oben angegebenen Datei erzeugt (Funktioniert nur bei heraufgeladenen Dateien).'));
    $mf->AddGroup('');

    $mf->AddField(t('In Rotations-Banner').'|'.t('Der Banner wird oben in den Rotations-Banner aufgenommen'), 'rotation', 'tinyint(1)', '', FIELD_OPTIONAL, '', 4);
    $mf->AddField(t('Bild-Upload'), 'pic_upload_banner', IS_FILE_UPLOAD, 'ext_inc/banner/', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path_banner', 'varchar(255)', '', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_banner, 'pic_code_banner', 'text', '', FIELD_OPTIONAL);
    $mf->AddField(t('Bei HTTPS verstecken').'|'.t('Diese Option solltest du aktivieren, wenn du den Banner-Code von einer externen Webseite lädst und er dort nur als HTTP Version verfügbar ist, du deine Webseite aber als HTTPS ausliefern möchtest.'), 'ssl_hide_banner', '', '', FIELD_OPTIONAL);
    $mf->AddGroup('Rotation Banner');

    $mf->AddField(t('In Sponsoren-Box').'|'.t('Der Banner wird in der Sponsoren-Box angezeigt'), 'active', 'tinyint(1)', '', FIELD_OPTIONAL, '', 4);
    $mf->AddField(t('Bild-Upload'), 'pic_upload_button', IS_FILE_UPLOAD, 'ext_inc/banner/', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-URL'), 'pic_path_button', 'varchar(255)', '', FIELD_OPTIONAL);
    $mf->AddField(t('Oder: Bild-Code (z.B. Flash)') . $code_popup_link_box, 'pic_code_button', 'text', '', FIELD_OPTIONAL);
    $mf->AddField(t('Bei HTTPS verstecken').'|'.t('Diese Option solltest du aktivieren, wenn du den Banner-Code von einer externen Webseite lädst und er dort nur als HTTP Version verfügbar ist, du deine Webseite aber als HTTPS ausliefern möchtest.'), 'ssl_hide_button', '', '', FIELD_OPTIONAL);
    $mf->AddGroup('Sponsoren Box');

    if ($func->isModActive('tournament2')) {
        $mf->AddDropDownFromTable(t('Sponsor einem Turnier zuordnen'), 'tournamentid', 'tournamentid', 'name', 'tournament_tournaments', t('Keine'), 'party_id = '. (int)$party->party_id);
    }
    $mf->AddField(t('Position'), 'pos');
    $mf->AddField(t('Text'), 'text', '', HTML_ALLOWED, FIELD_OPTIONAL);
    $mf->AddGroup('Misc.');

    $mf->AdditionalDBAfterSelectFunction = 'RewriteFields';
    $mf->AdditionalDBPreUpdateFunction = 'UploadFiles';
    $mf->SendForm('index.php?mod=sponsor&amp;action='. $_GET['action'], 'sponsor', 'sponsorid', $_GET['sponsorid']);
}
