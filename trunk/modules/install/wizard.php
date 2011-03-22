<?php

if ($_POST["resetdb"]) $db->success = 0;
include_once('modules/install/class_install.php');
$install = new Install();

$_SESSION['auth']['design'] = 'simple';

// Error-Switch
switch ($_GET["step"]){
  case 7:
    if ($_POST["email"] == "") $func->error(t('Bitte geben Sie eine E-Mail-Adresse ein!'), "index.php?mod=install&action=wizard&step=6");
    elseif ($_POST["password"] == "") $func->error(t('Bitte geben Sie ein Kennwort ein!'), "index.php?mod=install&action=wizard&step=6");
    elseif ($_POST["password"] != $_POST["password2"]) $func->error(t('Das Passwort und seine Verifizierung stimmen nicht überein!'), "index.php?mod=install&action=wizard&step=6");
    else {
      // Check for existing Admin-Account.
      $row = $db->qry_first("SELECT email FROM %prefix%user WHERE email=%string%", $_POST["email"]);

      // If found, update password
      if ($row['email']) $db->qry("UPDATE %prefix%user SET password = %string%, type = '3' WHERE email=%string%",
  md5($_POST["password"]), $_POST["email"]);

      // If not found, insert
      else {
        $db->qry("INSERT INTO %prefix%user SET username = 'ADMIN', firstname = 'ADMIN', name = 'ADMIN', email=%string%, password = %string%, type = '3'",
  $_POST["email"], md5($_POST["password"]));
        $userid = $db->insert_id();
      }
      
      include_once("inc/classes/class_auth.php");
      $authentication = new auth();
      $authentication->login($_POST["email"], $_POST["password"]);
    }
  // No break!

  case 8:
    if (!$func->admin_exists()) {
      $func->information(t('Sie müssen einen Admin-Account anlegen, um fortfahren zu können'));
      $_GET['step'] = 6;
    }
  break;
}

switch ($_GET["step"]){
    // Check Environment
    default:
    $dsp->NewContent(t('Lansuite Installation und Administration'), t('Willkommen bei der Installation von Lansuite.<br />Im ersten Schritt wird die Konfiguration Ihres Webservers überprüft.<br />Sollte alles korrekt sein, so drücken Sie bitte am Ende der Seite auf <b>Weiter</b> um mit der Eingabe der Grundeinstellungen fortzufahren.'));

        $dsp->SetForm("index.php?mod=install&action=wizard");
        $lang_array = array();
        if ($language == "de") $selected = 'selected'; else $selected = '';
        array_push ($lang_array, "<option $selected value=\"de\">Deutsch</option>");
        if ($language == "en") $selected = 'selected'; else $selected = '';
        array_push ($lang_array, "<option $selected value=\"en\">English</option>");
        $dsp->AddDropDownFieldRow("language", t('Sprache'), $lang_array, "");
        $dsp->AddFormSubmitRow(t('Ändern'));

        $continue = $install->envcheck();

        if ($continue) $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=2"));
        $dsp->AddContent();
    break;


    // Setting up ls_conf
    case 2:
        // Reset DB-Config, for when reinstalling in new DB, the next step would connect to existing, old tables
        $config["database"]["database"] = '';
        $install->WriteConfig();

        $dsp->NewContent(t('Grundeinstellungen'), t('Bitte geben Sie nun die Zugangsdaten zur Datenbank an.'));
        $dsp->SetForm("index.php?mod=install&action=wizard&step=3");

        // Set default settings from Config-File
        if ($_POST["host"] == "") $_POST["host"] = $config['database']['server'];
        if ($_POST["user"] == "") $_POST["user"] = $config['database']['user'];
#        if ($_POST["database"] == "") $_POST["database"] = $config['database']['database'];
        if ($_POST["prefix"] == "") $_POST["prefix"] = $config['database']['prefix'];

        #### Database Access
        $dsp->AddSingleRow("<b>". t('Datenbank-Zugangsdaten') ."</b>");
        $dsp->AddTextFieldRow("host", t('Host (Server-IP)'), $_POST["host"], "");
        $dsp->AddTextFieldRow("user", t('Benutzername'), $_POST["user"], "");
        $dsp->AddPasswordRow("pass", t('Kennwort'), $_POST["pass"], "");
        $dsp->AddTextFieldRow("database", t('Datenbank'), $_POST["database"], "");
        $dsp->AddTextFieldRow("prefix", t('Tabellen-Prefix'), $_POST["prefix"], "");

        #### Default Design
        // Open the design-dir
        $design_dir = opendir("design/");

        include_once("inc/classes/class_xml.php");
        $xml = new xml;
        
        // Check all Subdirs of $design_dir fpr valid design-xml-files
        $t_array = array();
        while ($akt_design = readdir($design_dir)) if ($akt_design != "." AND $akt_design != ".." AND $akt_design != ".svn" AND $akt_design != "templates") {

            $file = "design/$akt_design/design.xml";
            if (file_exists($file)) {

                // Read Names from design.xml
                $xml_file = fopen($file, "r");
                $xml_content = fread($xml_file, filesize($file));
                if ($xml_content != "") {
                    ($config['lansuite']['default_design'] == $akt_design) ? $selected = "selected" : $selected = "";
                    array_push ($t_array, "<option $selected value=\"$akt_design\">". $xml->get_tag_content("name", $xml_content) ."</option>");
                }
                fclose($xml_file);
            }
        }
        $dsp->AddDropDownFieldRow("design", t('Standard-Design'), $t_array, "");

        $dsp->AddCheckBoxRow("resetdb", t('Datenbank überschreiben'), t('ACHTUNG: Eventuell vorhandene Daten in der oben angegeben Datenbank gehen verloren!'), "", 0, "");
        $func->information(t('ACHTUNG: Der Aufruf der nächsten Seite kann bis zu einer Minute in Anspruch nehmen! Bitte in dieser Zeit den Ladevorgang nicht abbrechen!'),NO_LINK);

        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=1", "install/ls_conf");
        $dsp->AddContent();
    break;


    // Writing ls_conf & try to create DB-Strukture
    case 3:
        $continue = 1;

        $fail_leadin = "<font color=\"#ff0000\">";
        $leadout = "</font>";
        $output = "";

        // Set new $config-Vars
        $config["database"]["server"] = $_POST["host"];
        $config["database"]["user"] = $_POST["user"];
        $config["database"]["passwd"] = $_POST["pass"];
        $config["database"]["database"] = $_POST["database"];
        $config["database"]["prefix"] = $_POST["prefix"];
        $config["lansuite"]["default_design"] = $_POST["design"];

        // Write new $config-Vars to config.php-File
        if (!$install->WriteConfig()) {
            $continue = 0;
            $output .= $fail_leadin . t('Datei \'config.php\' konnte <strong>nicht</strong> geschrieben werden.') . $leadout . HTML_NEWLINE . HTML_NEWLINE;
        } else {
            $output .= t('Datei \'config.php\' wurde erfolgreich geschrieben.') .HTML_NEWLINE . HTML_NEWLINE;

            $res = $install->TryCreateDB($_POST["resetdb"]);
            switch ($res){
                case 0: $output .= $fail_leadin . t('Die Datenbank ist nicht erreichbar. Überprüfen Sie bitte die Angaben zur Datenbankverbindung.') . $leadout; break;
                case 1: $output .= t('Die Datenbank \'%1\' existiert bereits und wurde daher nicht neu angelegt.', $config["database"]["database"]); break;
                case 2: $output .= $fail_leadin . t('Anlegen der Datenbank fehlgeschlagen. Überprüfen Sie bitte, ob der angegebene Benutzer über ausreichende Rechte verfügt um eine neue Datenbank anzulegen, bzw. überprüfen Sie, ob Sie den Namen der Datenbank korrekt angegeben haben.') . $leadout; break;
                case 3: $output .= t('Datenbank wurde erfolgreich angelegt.'); break;
                case 4: $output .= $fail_leadin . t('Verbdindung ok aber keinen Datenbanknamen angegeben.') . $leadout; break;
                case 5: $output .= t('Datenbank wurde erfolgreich Überschrieben.'); break;
            }
            $output .= HTML_NEWLINE . HTML_NEWLINE;

            if ($res == 1 or $res == 3 or $res == 5){
                $db->connect();

                // Check for Updates
#               if($res == 1){
#                   $install->check_updates();
#               }
                // Scan the modules-dir for mod_settings/db.xml-File, read data, compare with db and create/update DB, if neccessary
                $install->CreateNewTables(0);
                $output .= t('Die Tabellenstruktur wurde erfolgreich angepasst'). HTML_NEWLINE . HTML_NEWLINE;
                // Insert translations of DB-items
                //$install->InsertTranslations();
            }
        }

        $dsp->NewContent(t('Datenbankgenerierung'), t('Das Setup versucht nun die Datenbank zu initialisieren.'));
        $dsp->AddSingleRow($output);

        if ($continue) $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=4"));
        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=2", "install/db");
        $dsp->AddContent();
    break;


    // Display import form
    case 4:

        $dsp->NewContent(t('Datenimport'), t('Hier können Sie die XML- oder CSV-Datei mit den Benutzerdaten ihrer Gäste importieren. Diese erhalten Sie z.B. bei LanSurfer, oder über den Export-Link einer anderen Lansuite-Version oder von jedem anderen System, das das Lansuite XML-Benutzerformat unterstützt.<br />Sie können den Import auch überspringen (auf <b>\'Weiter\'</b> klicken). In diesem Fall sollten Sie im nächsten Schritt einen Adminaccount anlegen.'));

        $dsp->SetForm("index.php?mod=install&action=wizard&step=5", "", "", "multipart/form-data");

        $dsp->AddSingleRow("<b>".t('Zu importierende Datei')."</b>");
        $dsp->AddFileSelectRow("importdata", t('Import (.xml, .csv, .tgz)'), "");
        $dsp->AddHRuleRow();
        $dsp->AddSingleRow("<b>".t('Lansuite-XML-Export')."</b>");
        $dsp->AddCheckBoxRow("rewrite", t('Vorhandene Einträge ersetzen'), "", "", 1, 1);
        $dsp->AddHRuleRow();
        $dsp->AddSingleRow("<b>".t('Importsettings')."</b>");
        $dsp->AddTextFieldRow("comment", t('Kommentar für alle setzen'), "", "", "", 1);
        $dsp->AddCheckBoxRow("deldb", t('Alte Benutzerdaten löschen'), "", "", 1, 1);
        $dsp->AddCheckBoxRow("replace", t('Vorhandene Einträge überschreiben'), "", "", 1, 1);
        $dsp->AddCheckBoxRow("signon", t('Benutzer zur aktuellen Party anmelden'), "", "", 1, 1);
        $dsp->AddHRuleRow();
        $dsp->AddSingleRow("<b>".t('LanSurfer-XML-Export')."</b>");
        $dsp->AddCheckBoxRow("noseat", t('Sitzplan NICHT importieren'), "", "", 1, "");

        $dsp->AddSingleRow(t('ACHTUNG: Wird mit den importierten Daten auch ein Adminaccount importiert, werden Sie ab sofort aufgefordert sich mit diesem bei der Installation einzuloggen.'));
        $dsp->AddFormSubmitRow(t('Hinzufügen'));

        $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=6"));
        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=3", "install/import");
        $dsp->AddContent();
    break;


    // Import uploaded file
    case 5:
        switch ($import->GetUploadFileType($_FILES['importdata']['name'])){
            case "xml":
                $header = $import->GetImportHeader($_FILES['importdata']['tmp_name']);
                $dsp->NewContent(t('wizard_importupload_caption'), t('wizard_importupload_subcaption')); // FIXME

                switch ($header["filetype"]) {
                    case "LANsurfer_export":
                    case "lansuite_import":
                        $import->ImportLanSurfer($_POST["deldb"], $_POST["replace"], $_POST["noseat"], $_POST["signon"], $_POST["comment"]);

                        $dsp->AddSingleRow(t('Datei-Import erfolgreich.'));
                        $dsp->AddDoubleRow(t('Dateityp'), $header["filetype"]);
                        $dsp->AddDoubleRow(t('Exportiert am/um'), $header["date"]);
                        $dsp->AddDoubleRow(t('Quelle'), $header["source"]);
                        $dsp->AddDoubleRow(t('LanParty'), $header["event"]);
                        $dsp->AddDoubleRow(t('Lansuite-Version'), $header["version"]);
                    break;

                    case "LanSuite":
                        $import->ImportXML($_POST["rewrite"]);
                        $dsp->AddSingleRow("Import erfolgreich");
                    break;

                    default:
                        $func->Information(t('Dies scheint keine Lansuite-kompatible-XML-Datei zu sein. Bitte Überprüfen sie den Eintrag &lt;filetype&gt; am Anfang der XML-Datei (FileType: \'%1\')', $header["filetype"]), "index.php?mod=install&action=wizard&step=4");
                    break;
                }

                $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=6"));
                $dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/import");
                $dsp->AddContent();
            break;

            case "csv":
                $check = $import->ImportCSV($_FILES['importdata']['tmp_name'], $_POST["deldb"], $_POST["replace"], $_POST["signon"], $_POST["comment"]);

                $dsp->NewContent(t('wizard_importupload_caption'), t('wizard_importupload_subcaption'));  // FIXME
                $dsp->AddSingleRow(t('Import wurde mit folgendem Ergebnis ausgeführt:<br /><ul>Fehler: %1<br />Keine Aktion: %1<br />Neue eingefügt: %1<br />Alte überschrieben: %1</ul>', $check["error"], $check["nothing"], $check["insert"], $check["replace"]));

                $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=6"));
                $dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/import");
                $dsp->AddContent();
            break;

            default:
                $func->information(t('Der von Ihnen angegebene Dateityp wird nicht unterstützt. Bitte wählen Sie eine Datei vom Typ *.xml, oder *.csv aus oder überspringen Sie den Dateiimport.'), "index.php?mod=install&action=wizard&step=4");
            break;
        }
    break;


    // Display form to create Adminaccount
    case 6:
        $dsp->NewContent(t('Adminaccount anlegen'), t('Hier können Sie einen Adminaccount anlegen. Falls dies bereits durch den Import geschehen ist, können Sie diesen Schritt auch überspringen (auf <b>\'Weiter\'</b> klicken).'));
        $dsp->SetForm("index.php?mod=install&action=wizard&step=7");
        if ($func->admin_exists()) $dsp->AddDoubleRow(t('Info'), t('Es existiert bereits ein Adminaccount'));
        
        $dsp->AddTextFieldRow("email", t('E-Mail'), 'admin@example.com', '');
        $dsp->AddPasswordRow("password", t('Kennwort'), '', '', '', '', "onkeyup=\"CheckPasswordSecurity(this.value, document.images.seclevel1)\"");
        $dsp->AddPasswordRow("password2", t('Kennwort wiederholen'), '', '');
        $smarty->assign('pw_security_id', '1');
        $dsp->AddDoubleRow('', $smarty->fetch('design/templates/ls_row_pw_security.htm'));
        $dsp->AddFormSubmitRow(t('Hinzufügen'));

        $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Weiter'), "index.php?mod=install&action=wizard&step=8"));
        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=4", "install/admin");
        $dsp->AddContent();
    break;


    // Create Adminaccount
    case 7:
    // No break!


    // Load modules
    case 8:
        $dsp->NewContent(t('Module aktivieren'), t('Hier können Sie festlegen, welche Module aktiv sein sollen'));
        $dsp->SetForm("index.php?mod=install&action=wizard&step=9");
        $res = $db->qry("SELECT * FROM %prefix%modules ORDER BY changeable DESC, caption");
        while ($row = $db->fetch_array($res)) $dsp->AddContentLine($install->getModConfigLine($row, 0));
        $db->free_result($res);
        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=6", "install/admin");
        $dsp->AddContent();
    break;

    // Set main config-variables
    case 9:
        // Update modules
        $res = $db->qry("SELECT name, reqphp, reqmysql FROM %prefix%modules WHERE changeable");
        while ($row = $db->fetch_array($res)){
            if ($_POST[$row["name"]]) {
                if ($row['reqphp'] and version_compare(phpversion(), $row['reqphp']) < 0) $func->information(t('Das Modul %1 kann nicht aktiviert werden, da die PHP Version %2 benötigt wird', $row["name"], $row['reqphp']), NO_LINK);
                else $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = %string%", $row["name"]);
            } elseif (count($_POST)) $db->qry_first("UPDATE %prefix%modules SET active = 0 WHERE name = %string%", $row["name"]);
        }
        $db->free_result($res);

        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'settings'");
        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'banner'");
        $db->qry_first("UPDATE %prefix%modules SET active = 1 WHERE name = 'about'");

        $func->getActiveModules();
        $install->CreateNewTables(0);


        $dsp->NewContent(t('Wichtige Systemvariablen einstellen'), t('Hier, in diesem letzten Schritt, werden die wichtigsten Konfigurationen in Lansuite eingestellt.'));

        $dsp->SetForm("index.php?mod=install&action=wizard&step=10");

        // Country
        // Get Selections
        $get_cfg_selection = $db->qry("SELECT cfg_display, cfg_value FROM %prefix%config_selections WHERE cfg_key = 'country'");
        $country_array = array();
        while ($selection = $db->fetch_array($get_cfg_selection)){
            ($language == $selection["cfg_value"]) ? $selected = "selected" : $selected = "";
            array_push ($country_array, "<option $selected value=\"{$selection["cfg_value"]}\">". t($selection["cfg_display"]) ."</option>");
        }
        $dsp->AddDropDownFieldRow("country", t('Land, in dem die Party stattfindet'), $country_array, "");

        // URL & Admin-Mail
        $dsp->AddHRuleRow();
        $dsp->AddTextFieldRow("url", t('URL der Webseite'), 'http://'. $_SERVER['HTTP_HOST'], "");
        $dsp->AddTextFieldRow("email", t('E-Mail des Webmasters'), 'webmaster@'. $_SERVER['HTTP_HOST'], "");

        // Online, or offline mode?
        $dsp->AddHRuleRow();
        $mode_array = array();
        if ($_SERVER['HTTP_HOST'] == 'localhost' or $_SERVER['HTTP_HOST'] == '127.0.0.1') $selected = ""; else $selected = "selected";
        array_push ($mode_array, '<option $selected value="1">'. t('Internet-Seite. Vor der Party') .'</option>');
        if ($_SERVER['HTTP_HOST'] == 'localhost' or $_SERVER['HTTP_HOST'] == '127.0.0.1') $selected = "selected"; else $selected = "";
        array_push ($mode_array, '<option $selected value="0">'. t('Intranet-Seite. Auf der Party') .'</option>');
        $dsp->AddDropDownFieldRow("mode", t('Internet- oder Lokaler-Modus?'), $mode_array, "");

        $dsp->AddFormSubmitRow(t('Weiter'));

        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=8", "install/vars");
        $dsp->AddContent();
    break;


    // Display final hints
    case 10:
        // Set variables
        $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = 'sys_language'", $language);
        $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = 'sys_country'", $_POST['country']);
        $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = 'sys_partyurl'", $_POST['url']);
        $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = 'sys_party_mail'", $_POST['email']);
        $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = 'sys_internet'", $_POST['mode']);

        unset($_SESSION['language']);

        $dsp->NewContent(t('Installation abschließen'), t('Die Installation wurde erfolgreich beendet.'));
            
        $dsp->AddSingleRow(t('Die Installation ist nun beendet.<br /><br />Mit einem Klick auf <b>Einloggen</b> unterhalb schließen Sie die Installation ab und gelangen auf die Adminseite. Dort können Sie weitere Konfigurationen vornehmen sowie bereits in der Installation getätigte ändern.<br /><br />Der Modulmanager ermöglicht es Ihnen dort Module zu de-/aktivieren.<br /><br />Über den Link \'Allgemeine Einstellungen\' stehen Ihnen eine Vielzahl an Konfigurationen in den einzelnen Modulen zur Verfügung.'));
        if (!func::admin_exists()) $dsp->AddSingleRow("<font color=red>". t('<b>Es wurde kein Admin-Account angelegt</b><br />Solange kein Admin-Account existiert, ist die Admin-Seite für JEDEN im Netzwerk erreichbar.') ."</font>");

        $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Login'), "index.php?mod=install"));
        $dsp->AddBackButton("index.php?mod=install&action=wizard&step=9", "install/admin");
        $dsp->AddContent();
        
        $config["environment"]["configured"] = 1;
        $install->WriteConfig($cfg_set);
    break;
}

?>
