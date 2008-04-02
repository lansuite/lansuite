<?php
$lang['install']['structure_version']	= '1';
$lang['install']['content_version']	= '1';
$lang['install']['language_name'] = 'Deutsch';
$lang['install']['lastchange'] = '24. August 2004';
$lang['install']['translator'] = 'Jochen';

$lang['install']['index_caption']	= 'Installation und Administration';
$lang['install']['index_subcaption']	= 'Auf diesen Seiten können Sie Lansuite installieren und verwalten';
$lang['install']['index_envcheck']	= 'Systemvoraussetzungen testen';
$lang['install']['index_dbmenu']	= 'Menueinträge neu schreiben';
$lang['install']['index_ls_conf']	= 'Grundeinstellungen (Datenbank-Zugangsdaten)';
$lang['install']['index_db']	= 'Datenbank updaten und verwalten';
$lang['install']['index_module']	= 'Modulmanager';
$lang['install']['index_settings']	= 'Allgemeine Einstellungen';
$lang['install']['index_import']	= 'Daten-Import';
$lang['install']['index_export']	= 'Daten-Export';
$lang['install']['index_pdfexport']	= 'PDF-Export';
$lang['install']['index_adminaccount']	= 'Administrator Account anlegen';
$lang['install']['index_feedback']	= 'Feedback senden';
$lang['install']['index_lansuite']	= 'Zum Lansuite Intranetsystem';
$lang['install']['db_caption']	= 'Datenbank-Initialisierung';
$lang['install']['db_subcaption']	= '<br><b>Ihre Datenbank-Struktur wurde soeben automatisch auf den neusten Stand gebracht</b>. Zusätzlich können Sie unterhalb einzelne Modul-Datenbanken zurücksetzen';
$lang['install']['db_createtables']	= 'Tabellen erstellen';
$lang['install']['db_insertdata']	= 'Daten in Tabellen schreiben';
$lang['install']['db_created']	= 'wurde angelegt';
$lang['install']['db_exists']	= 'Tabelle existiert bereits';
$lang['install']['db_ent_exists']	= 'Eintrag existiert bereits';
$lang['install']['db_rewrite']	= 'zurücksetzen';
$lang['install']['db_alltables']	= 'Alle Tabellen';
$lang['install']['db_configs']	= 'Konfigurationseinträge';
$lang['install']['db_modules']	= 'Moduleinträge';
$lang['install']['db_admin']	= 'Adminaccount';
$lang['install']['db_system']	= 'Systemaccount';
$lang['install']['db_create_success']	= 'Die Tabelle \'%NAME%\' wurde erfolgreich erstellt.';
$lang['install']['db_signonstatus']	= 'Anmeldestatus der Benutzer';
$lang['install']['db_signonstatus_rewritten']	= 'Status zurückgesetzt';
$lang['install']['db_insert_ok']	= 'Eintrag \'%NAME%\' eingefügt';
$lang['install']['db_rewrite_quest']	= 'Sind Sie sicher, dass Sie die Datenbank des Moduls <b>\'%NAME%\'</b> zurücksetzen möchten? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!';
$lang['install']['db_rewrite_all_quest']	= 'Sind Sie sicher, dass Sie <b>\'alle Tabellen\'</b> zurücksetzen möchten? Dies löscht unwiderruflich alle Datenbankeinträge und Lansuite wird komplett auf den Ausgangszustand zurückgesetzt!';
$lang['install']['db_rewrite_config_quest']	= 'Sind Sie sicher, dass Sie <b>\'alle Konfigurationen\'</b> zurücksetzen möchten? Damit gehen alle Ihre Moduleinstellungen verloren!';
$lang['install']['db_reset_user_quest']	= 'Sind Sie sicher, dass Sie den Status der Benutzer zurücksetzen möchten? Damit ist kein Benutzer mehr zur aktuellen Party angemeldet. Außerdem wird der Bezahltstatus aller Benutzer auf \'Nicht Bezahlt\' gesetzt.';
$lang['install']['db_rewrite_modules_quest']	= 'Sind Sie sicher, dass Sie die Modultabelle zurücksetzen möchten? Dadurch sind nur noch die Standardmodule aktiviert.';
$lang['install']['db_rewrite_this_module_quest']	= 'Sind Sie sicher, dass Sie die Datenbank dieses Moduls zurücksetzen möchten? Dies löscht unwiderruflich alle Daten, die in diesem Modul bereits geschrieben wurden!';
$lang['install']['admin_caption']	= 'Adminaccount anlegen';
$lang['install']['admin_subcaption']	= 'Hier legen Sie einen Adminaccount an, über welchen Sie Zugriff auf diese Admin-Seite erhalten. Wenn Sie bereits Benutzer-Daten importiert haben müssen Sie hier keinen weiteren Account anlegen.';
$lang['install']['admin_email']	= 'E-Mail';
$lang['install']['admin_pass2']	= 'Kennwort wiederholen';
$lang['install']['admin_success']	= 'Der Adminaccount wurde erfolgreich angelegt.';
$lang['install']['admin_err_noemail']	= 'Bitte geben Sie eine E-Mail-Adresse ein!';
$lang['install']['admin_err_nopw']	= 'Bitte geben Sie ein Kennwort ein!';
$lang['install']['admin_err_pwnotequal']	= 'Das Passwort und seine Verifizierung stimmen nicht überein!';
$lang['install']['admin_warning']	= 'ACHTUNG: Es wurde noch kein Adminaccount angelegt. Bitte legen Sie diesen unbedingt unterhalb an. Sobald dieser angelegt worden ist, ist diese Seite nur noch mit diesem Account erreichbar.';
$lang['install']['conf_caption']	= 'Grundeinstellungen';
$lang['install']['conf_subcaption']	= 'Bitte geben Sie nun die Zugangsdaten zur Datenbank an.';
$lang['install']['conf_dbdata']	= 'Datenbank-Zugangsdaten';
$lang['install']['conf_host']	= 'Host (Server-IP)';
$lang['install']['conf_user']	= 'Benutzername';
$lang['install']['conf_pass']	= 'Kennwort';
$lang['install']['conf_db']	= 'Datenbank';
$lang['install']['conf_prefix']	= 'Tabellen-Prefix';
$lang['install']['conf_display_debug_errors']	= 'MySQL-Fehler zeigen';
$lang['install']['conf_err_write']	= 'Datei \'config.php\' konnte <strong>nicht</strong> geschrieben werden.';
$lang['install']['conf_success']	= 'Datei \'config.php\' wurde erfolgreich geschrieben.';
$lang['install']['conf_design']	= 'Standard-Design';
$lang['install']['mod_caption']	= 'Modulverwaltung';
$lang['install']['mod_subcaption']	= 'Hier können Sie Module de-/aktivieren, sowie deren Einstellungen verändern.';
$lang['install']['mod_reset_quest']	= 'Sollen wirklich <b>\'alle Module\'</b> zurückgesetzt werden?' . HTML_NEWLINE . 'Dies wirkt sich <u>nicht</u> auf die Datenbankeinträge der Module aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu den Modulen getätigt worden sind. Außerdem sind danach nur noch die Standardmodule aktiviert.';
$lang['install']['mod_reset_mod_quest']	= 'Soll das Modul <b>\'%NAME%\'</b> wirklich zurückgesetzt werden?' . HTML_NEWLINE . 'Dies wirkt sich <u>nicht</u> auf die Datenbankeinträge des Modules aus, jedoch gehen alle Einstellungen und Menüänderungen verloren, die zu diesem Modul getätigt worden sind.';
$lang['install']['mod_set_caption']	= 'Moduleinstellungen';
$lang['install']['mod_set_subcaption']	= 'Hier können Sie Einstellungen zu diesem Modul vornehmen.';
$lang['install']['mod_set_err_nosettings']	= 'Zu diesem Modul gibt es keine Einstellungen';
$lang['install']['mod_menu_caption']	= 'Modul-Menüeinträge';
$lang['install']['mod_menu_subcaption']	= 'Hier können Sie die Navigationseinträge dieses Moduls ändern.';
$lang['install']['import_xml_caption']	= 'XML-Import - Dateiauswahl';
$lang['install']['import_xml_subcaption']	= 'Wählen Sie nun das XML-File aus, das Sie importieren möchten. Sollen die Userdaten und Sitzblöcke HINZUGEFÜGT werden, so müssen Sie die Option \'Datenbank löschen\' deaktivieren. Sie bekommen anschließend die Details des Importfiles angezeigt. Erst nach Bestätigung der Datei werden die Aktionen durchgeführt. Das Kommentarfeld können Sie benutzen, um jedem User, der importiert wird, den gleichen Kommentar zuzuweisen. Lassen Sie das Feld einfach leer, wenn Sie das nicht wünschen.';
$lang['install']['import_caption']	= 'Daten importieren';
$lang['install']['import_subcaption']	= 'Hier können Sie Benutzerdaten, die Sie aus einem anderen System exportiert haben, in Lansuite importieren.';
$lang['install']['import_import']	= 'Import (.xml, .csv, .tgz)';
$lang['install']['import_noseat']	= 'Sitzplan NICHT importieren';
$lang['install']['import_signon']	= 'Benutzer zur aktuellen Party anmelden';
$lang['install']['import_deldb']	= 'Alte Benutzerdaten löschen';
$lang['install']['import_replace']	= 'Vorhandene Einträge überschreiben';
$lang['install']['import_comment']	= 'Kommentar für alle setzen';
$lang['install']['import_warning']	= '<b>ACHTUNG:</b> Wird mit den importierten Daten auch ein Adminaccount importiert, werden Sie ab sofort aufgefordert sich mit diesem bei der Installation einzuloggen.';
$lang['install']['import_csv_report']	= 'Import wurde mit folgendem Ergebnis ausgeführt:' . HTML_NEWLINE . '<ul>Fehler: %ERROR%' . HTML_NEWLINE . 'Keine Aktion: %NOTHING%' . HTML_NEWLINE . 'Neue eingefügt: %INSERT%' . HTML_NEWLINE . 'Alte überschrieben: %REPLACE%</ul>';
$lang['install']['env_caption']	= 'Webserverkonfiguration und Systemvorraussetzungen überprüfen';
$lang['install']['env_subcaption']	= 'Hier können Sie testen, ob Lansuite auf ihrem System evtl. Probleme haben wird und bekommen entsprechende Lösungsvorschläge angezeigt.';
$lang['install']['env_valid']	= 'erfolgreich';
$lang['install']['env_invalid']	= 'fehlgeschlagen';
$lang['install']['env_warning']	= 'bedenkliche Einstellung';
$lang['install']['env_stats_info']	= 'Leider nicht möglich';
$lang['install']['env_stats_safemode']	= 'Statistiken funktionieren nicht, wenn PHP im Safe-Modus läuft.';
$lang['install']['env_stats']	= 'Auf ihrem System leider nicht möglich. Der Befehl oder die Datei ' . HTML_NEWLINE . '{FEHLER} wurde nicht gefunden. Evtl. sind nur die Berechtigungen der Datei nicht ausreichend gesetzt.';
$lang['install']['env_stats_os']	= 'Die Statistiken für Server funktionieren auf Ihrem System leider nicht.';
$lang['install']['env_phpversion']	= 'Auf Ihrem System wurde die PHP-Version ' . phpversion() . ' gefunden.  Lansuite benötigt mindestens PHP Version 4.3.0. Sie können zwar die Installation fortsetzen, allerdings kann keinerlei Garantie auf die ordnungsgemäße Funktionsweise gegeben werden. Laden und installieren Sie sich eine aktuellere Version von <a href=\'http://www.php.net\' target=\'_blank\'>www.php.net</a>.';
$lang['install']['env_no_mysql']	= 'Die MySQL-Erweiterung ist in PHP nicht geladen. Diese wird benötigt um auf die Datenbank zuzugreifen. Bevor keine Datenbank verfügbar ist, kann Lansuite nicht installiert werden. Den MySQL-Server gibt es unter <a href=\'http://www.mysql.com\' target=\'_blank\'>www.mysql.com</a> zum Download.';
$lang['install']['env_rg']	= 'Auf Ihrem System ist die PHP-Einstellung <b>register_globals</b> auf <b>On</b> gesetzt. Dies kann unter Umständen ein Sicherheitsrisiko darstellen, wenn auch kein großes (siehe dazu: <a href=\'http://www.php.net/manual/de/security.globals.php\' target=\'_blank\'>www.php.net</a>). Sie sollten in Ihrer <b>PHP.ini</b> die Option <b>register_globals</b> auf <b>Off</b> setzen! Bitte vergessen Sie nicht, Ihren Webserver nach dieser Änderung neu zu starten.';
$lang['install']['env_safe_mode']     = 'Auf Ihrem System ist die PHP-Einstellung <b>safe_mode</b> auf <b>On</b> gesetzt. safe_mode ist dazu gedacht, einige Systemfunktionen auf dem Server zu sperren um Angriffe zu verhindern (siehe dazu: <a href=\'http://de2.php.net/features.safe-mode\' target=\'_blank\'>www.php.net</a>). Doch leider benötigen einige Lansuite-Module (speziell: LansinTV, Serverstatistiken oder das Server-Modul) Zugriff auf genau diese Funktionen. Sie sollten daher in Ihrer <b>PHP.ini</b> die Option <b>safe_mode</b> auf <b>Off</b> setzen! <br /> Außer bei oben genannten Modulen, kann es bei aktiviertem safe_mode auch zu Problemen bei dem Generieren von Buttons, wie dem am Ende dieser Seite kommen.';
$lang['install']['env_mq']	= 'Auf Ihrem System ist die PHP-Einstellung <b>magic_quotes_gpc</b> auf <b>Off</b> gesetzt. Um mit Lansuite arbeiten zu können muss diese Option aktiviert sein. Ändern Sie bitte in Ihrer <b>PHP.ini</b> die Option <b>magic_quotes_gpc </b> auf <b> On </b>! Bitte vergessen Sie nicht, Ihren Webserver nach dieser Änderung neu zu starten.';;
$lang['install']['env_gd']	= 'Auf Ihrem System konnte das PHP-Modul <b>GD-Library</b> nicht gefunden werden. Durch diese Programmierbibliothek werden in Lansuite Grafiken, wie z.B. Turnierbäume generiert. Ab PHP Version 4.3.0 ist die GD bereits in PHP enthalten. Sollten Sie PHP 4.3.0 installiert haben und diese Meldung dennoch erhalten, überprüfen Sie, ob das GD-Modul evtl. deaktiviert ist. In PHP Version 4.2.3 ist die GD nicht enthalten. Wenn Sie diese Version benutzen muss GD 2.0 separat heruntergeladen, installiert und in PHP einkompiliert werden. Sollten Sie Windows und PHP 4.2.3 benutzen, empfehlen wir auf PHP 4.3.0 umzusteigen, da Sie sich auf diese Weise viel Arbeit sparen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie erhebliche Einschränkungen im Gebrauch machen müssen.';
$lang['install']['env_gd1']	= 'Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> nur in der Version GD1 ' . $GD . ' gefunden. Damit ist die Qualität der erzeugten Bilder wesentlich schlechter. Es wird deshalb empfohlen GD2 zu benutzen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.';
$lang['install']['env_gd2']	= 'Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> ohne Free-Type Support gefunden. Dadurch werden die Schriftarten in Grafiken (z.b. im Turnierbaum) nicht sehr schön dargestellt. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.';
$lang['install']['env_snmp']	= 'Auf Ihrem System konnte das PHP-Modul <b>SNMP-Library</b> nicht gefunden werden. SNMP ermöglicht es, auf Netzwerkdevices zuzugreifen, um detaillierte Informatioen über diese zu liefern. Ohne diese Bibliothek kann das Lansuite-Modul <b> NOC </b> (Netzwerküberwachung) nicht arbeiten. Das Modul NOC wird <b>automatisch deaktiviert</b>.';;
$lang['install']['env_ftp']	= 'Auf Ihrem System konnte das PHP-Modul <b>FTP-Library</b> nicht gefunden werden. Dies kann zur Folge haben, dass Module, die auf FTP-Server zugreifen (z.B. Downloadmodul, Servermodul), nicht korrekt funktionieren.';
$lang['install']['env_no_cfgfile']	= 'Die Datei <b>config.php</b> befindet sich <b>nicht</b> im Lansuite-Verzeichnis <b> inc/base/ </b>. Bitte überprüfen Sie die Datei auf korrekte Groß- und Kleinschreibung.';
$lang['install']['env_cfg_file']	= 'Die Datei <b>config.php</b> im Lansuite-Verzeichnis <b> inc/base/ </b> muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen.';
$lang['install']['env_cfg_file_key']	= 'Schreibrechte auf die Konfigurationsdatei';
$lang['install']['env_no_ext_inc']	= 'Der Ordner <b>ext_inc</b> existiert <b>nicht</b> im Lansuite-Verzeichnis. Bitte überprüfen Sie den Pfad auf korrekte Groß- und Kleinschreibung.';
$lang['install']['env_ext_inc']	= 'In den Ordner <b>ext_inc</b> und alle seine Unterordner muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen.';
$lang['install']['env_ext_inc_key']	= 'Schreibrechte im Ordner \'ext_inc\'';
$lang['install']['env_errreport']	= 'In Ihrer php.ini ist \'error_reporting\' so konfiguriert, dass auch unwichtige Fehlermeldungen angezeigt werden. Dies kann dazu führen, dass störende Fehlermeldungen in Lansuite auftauchen. Wir empfehlen diese Einstellung auf \'E_ALL ^ E_NOTICE\' zu ändern. In dieser Einstellung werden dann nur noch Fehler angezeigt, welche die Lauffähigkeit des Skriptes beeinträchtigen.';
$lang['install']['wizard_caption']	= 'Lansuite Installation und Administration';
$lang['install']['wizard_subcaption']	= 'Willkommen bei der Installation von Lansuite.' . HTML_NEWLINE . 'Im ersten Schritt wird die Konfiguration Ihres Webservers überprüft.' . HTML_NEWLINE . 'Sollte alles korrekt sein, so drücken Sie bitte am Ende der Seite auf <b>\'Weiter\'</b> um mit der Eingabe der Grundeinstellungen fortzufahren.';
$lang['install']['wizard_db_notavailable']	= 'Die Datenbank ist nicht erreichbar. Überprüfen Sie bitte die Angaben zur Datenbankverbindung.';
$lang['install']['wizard_db_exist']	= 'Die Datenbank \'%DB%\' existiert bereits und wurde daher nicht neu angelegt.';
$lang['install']['wizard_db_createfailed']	= 'Anlegen der Datenbank fehlgeschlagen. Überprüfen Sie bitte, ob der angegebene Benutzer über ausreichende Rechte verfügt um eine neue Datenbank anzulegen, bzw. überprüfen Sie, ob Sie den Namen der Datenbank korrekt angegeben haben.';
$lang['install']['wizard_db_createsuccess']	= 'Datenbank wurde erfolgreich angelegt.';
$lang['install']['wizard_db_createtable']	= '%CREATED% von %GES% Tabellen wurden angelegt. %EXIST% Tabellen existierten bereits.';
$lang['install']['wizard_db_createtable_fail']	= 'Das Erstellen der folgenden Tabellen ist fehlgeschlagen:';
$lang['install']['wizard_insertplz_success']	= 'Die Koordinaten der Postleitzahlen wurden erfolgreich in die Datenbank eingetragen.';
$lang['install']['wizard_insertplz_failed']	= 'Fehler beim Eintragen der Postleitzahlen in die Datenbank.';
$lang['install']['wizard_insertsettings_success']	= 'Die Standards der Einstellungen wurden erfolgreich in die Datenbank geschrieben.';
$lang['install']['wizard_createsys_success']	= 'Der Systemaccount wurde in die Datenbank eingetragen.';
$lang['install']['wizard_createsys_failed']	= 'Der Systemaccount existiert bereits und wurde daher nicht neu eingetragen.';
$lang['install']['wizard_loadwarning']	= '<b>ACHTUNG:</b><br>Der Aufruf der nächsten Seite kann bis zu eine Minute in Anspruch nehmen! Bitte in dieser Zeit den Ladevorgang nicht abbrechen!';
$lang['install']['wizard_db_caption']	= 'Datenbankgenerierung';
$lang['install']['wizard_db_subcaption']	= 'Das Setup versucht nun die Datenbank zu initialisieren.';
$lang['install']['wizard_import_caption']	= 'Datenimport';
$lang['install']['wizard_import_subcaption']	= 'Hier können Sie die XML- oder CSV-Datei mit den Benutzerdaten ihrer Gäste importieren. Diese erhalten Sie z.B. bei LanSurfer, oder über den Export-Link einer anderen Lansuite-Version oder von jedem anderen System, das das Lansuite XML-Benutzerformat unterstützt.' . HTML_NEWLINE . 'Sie können den Import auch überspringen (auf <b>\'Weiter\'</b> klicken). In diesem Fall sollten Sie im nächsten Schritt einen Adminaccount anlegen.';
$lang['install']['wizard_importupload_wrongformat']	= 'Bei der angegebenen Datei handelt es sich <b>nicht</b> um eine Lansuite-Import Datei';
$lang['install']['wizard_importupload_success']	= 'Datei-Import erfolgreich.';
$lang['install']['wizard_importupload_filetype']	= 'Dateityp';
$lang['install']['wizard_importupload_date']	= 'Exportiert am/um';
$lang['install']['wizard_importupload_source']	= 'Quelle';
$lang['install']['wizard_importupload_event']	= 'LanParty';
$lang['install']['wizard_importupload_version']	= 'Lansuite-Version';
$lang['install']['wizard_importupload_unsuportetfiletype']	= 'Der von Ihnen angegebene Dateityp wird nicht unterstützt. Bitte wählen Sie eine Datei vom Typ *.xml, oder *.csv aus oder überspringen Sie den Dateiimport.';
$lang['install']['wizard_admin_caption']	= 'Adminaccount anlegen';
$lang['install']['wizard_admin_subcaption']	= 'Hier können Sie einen Adminaccount anlegen. Falls dies bereits durch den Import geschehen ist, können Sie diesen Schritt auch überspringen (auf <b>\'Weiter\'</b> klicken).';
$lang['install']['wizard_final_caption']	= 'Installation abschließen';
$lang['install']['wizard_final_subcaption']	= 'Die Installation wurde erfolgreich beendet.';
$lang['install']['wizard_final_text']	= 'Die Installation ist nun beendet.' . HTML_NEWLINE . HTML_NEWLINE .'Mit einem Klick auf <b>Einloggen</b> unterhalb schließen Sie die Installation ab und gelangen auf die Adminseite. Dort können Sie weitere Konfigurationen vornehmen sowie bereits in der Installation getätigte ändern.' . HTML_NEWLINE . HTML_NEWLINE . 'Der Modulmanager ermöglicht es Ihnen dort Module zu de-/aktivieren.' . HTML_NEWLINE . HTML_NEWLINE . 'Über den Link \'Allgemeine Einstellungen\' stehen Ihnen eine Vielzahl an Konfigurationen in den einzelnen Modulen zur Verfügung.';
$lang['install']['wizard_warning_noadmin']	= '<b>Es wurde kein Admin-Account angelegt</b>' . HTML_NEWLINE . 'Solange kein Admin-Account existiert, ist die Admin-Seite für JEDEN im Netzwerk erreichbar.';
$lang['install']['export_caption']	= 'Daten exportieren';
$lang['install']['export_subcaption']	= 'Hier können Sie Benutzerdaten exportieren. Diese können Sie später wieder in Lansuite importieren.';
$lang['install']['settings_item_caption']	= 'Konfigurationseintrag bearbeiten';
$lang['install']['settings_item_subcaption']	= 'Hier können Sie den Wert dieses Eintrags editieren.';
$lang['install']['settings_key']	= 'Schlüssel';
$lang['install']['settings_description']	= 'Beschreibung';
$lang['install']['settings_value']	= 'Wert';
$lang['install']['settings_value_extended']	= 'Wert (Bei Boolean gilt 1 = ja, 0 = nein / Zeitwerte müssen noch als UNIX-timestamp eingegeben werden.)';
$lang['install']['settings_new_caption']	= 'Eingabe eines neuen Konfigurationschlüssels';
$lang['install']['settings_new_subcaption']	= 'Hier können Sie einen neuen Konfigurationschlüssel anlegen.';
$lang['install']['settings_type']	= 'Typ';
$lang['install']['settings_group']	= 'Existierende Gruppe';
$lang['install']['settings_newgroup']	= 'Oder: Neue Gruppe';
$lang['install']['update_db']   = 'Update der Datenbank';
$lang['install']['update_caption']	= 'Update der Datenbank';
$lang['install']['update_subcaption'] = 'Hier können Sie eine alte Datenbank von Lansuite auf die neue Version updaten.';
$lang['install']['update_install_version'] = 'Momentan installierte Version';
$lang['install']['update_update_file'] = 'Update auswählen';
$lang['install']['update_no_file'] = 'Es ist kein Update vorhanden. Sie haben die aktuellste Version.';
$lang['install']['update_ok'] = 'Update der Datenbank war erfolgreich.';
$lang['install']['update_fail'] = 'Update der Datenbank war nicht erfolgreich.' . HTML_NEWLINE . 'Die Datei konnte nicht gefunden werden.';
$lang['install']['wizard_table_exist'] = 'System-Tabelle existiert bereits. Bitte im Adminmenu ein Update ausführen.';
$lang['install']['export_xml_complete'] = 'XML: Komplette Datenbank Exportieren (Empfohlen)';
$lang['install']['export_xml_module'] = 'XML: Nur ausgewählte Module exportiern';
$lang['install']['export_xml_tables'] = 'XML: Nur ausgewählte Tabellen exportieren (für Experten)';
$lang['install']['export_csv_complete'] = 'CSV: Userdaten komplett (inkl. Sitzplatz und IP)';
$lang['install']['export_csv_sticker'] = 'CSV: Userdaten \'Aufkleber\' (Name, Username, Clan, Sitzplatz und IP)';
$lang['install']['export_csv_card'] = 'CSV: Sitzplatzkarten (Name, Username, Clan, Sitzplatz und IP)';
$lang['install']['export_data_ext_inc'] = 'DATA: Daten-Ordner herunterladen (Avatare, Bildergallerie, Banner, ...)';
$lang['install']['export_structure'] = 'Struktur exportieren';
$lang['install']['export_content'] = 'Inhalt exportieren';
$lang['install']['export_table'] = 'Diese Tabelle exportieren';
$lang['install']['export_csv_complete_save'] = 'Lansuite-CSV-Export speichern';
$lang['install']['export_csv_sticker_save'] = 'Lansuite-Aufkleber-Export speichern';
$lang['install']['export_csv_card_save'] = 'Lansuite-Sitzplatzkarten-Export speichern';
$lang['install']['export_ext_inc'] = 'Lansuite Daten-Ordner herunterladen';
$lang['install']['import_file'] = 'Zu importierende Datei';
$lang['install']['import_settings_new'] = 'Lansuite-XML-Export';
$lang['install']['import_settings_lansurfer'] = 'LanSurfer-XML-Export';
$lang['install']['import_settings_overwrite'] = 'Vorhandene Einträge ersetzen';
$lang['install']['import_success'] = 'Import erfolgreich.';
$lang['install']['import_err_tgz'] = 'Der Export des Ext-Inc Ordners kann aktuell leider nicht über Lansuite importiert werden. Bitte laden und entpacken Sie den Ordner manuell auf Ihren Webspace.';
$lang['install']['import_err_filetype'] = 'Dies scheint keine Lansuite-kompatible-XML-Datei zu sein. Bitte Überprüfen sie den Eintrag &lt;filetype&gt; am Anfang der XML-Datei (FileType: \'%FILETYPE%\')';
$lang['install']['import_csv_temp_unavailable'] = 'Der CSV-Import ist Aufgrund vieler Fehler vorübergehend aus dem System genommen worden.';
$lang['install']['menu_reset_navi_quest'] = 'Sind Sie sicher, dass Sie alle Navigationseinträge zurücksetzen möchten?';
$lang['install']['menu_group_change'] = 'Gruppe ändern';
$lang['install']['menu_group_change2'] = 'Hier können Sie diesen Navigationseintrag einer Gruppe zuweisen';
$lang['install']['menu_navi_caption'] = 'Navigationsmenü verwalten';
$lang['install']['menu_navi_subcaption'] = 'Hier können Sie das Navigationsmenü an Ihre Wünsche anpassen';
$lang['install']['menu_navi_reset'] = 'Navigation zurücksetzen';
$lang['install']['menu_navi_showactive'] = 'Nur Einträge von aktivierten Modulen anzeigen';
$lang['install']['menu_navi_showall'] = 'Alle Einträge anzeigen';
$lang['install']['del'] = 'entfernen';
$lang['install']['edit'] = 'editieren';
$lang['install']['hr'] = 'Trennzeile';
$lang['install']['group'] = 'Gruppe';
$lang['install']['pos'] = 'Pos';
$lang['install']['index_no_admin_warnig'] = '<b>ACHTUNG</b>: Es existiert noch kein Admin-Account. Daher hat JEDER Benutzer Admin-Rechte. Legen Sie unbedingt im Benutzermanager einen Superadmin an.';
$lang['install']['index_no_mod_admin_warnig'] = 'Die folgenden Module haben noch keinen Admin und sind daher für jeden Admin änderbar:';
$lang['install']['index_no_mod_admin_hint'] = 'Aktuell sind noch nicht alle Module so programmiert, dass sie eigene Admins haben können.';
$lang['install']['index_config_ls'] = 'Lansuite konfigurieren';
$lang['install']['index_update_repair_ls'] = 'Lansuite updaten / reparieren';
$lang['install']['index_data_management'] = 'Daten-Management';
$lang['install']['index_navigation'] = 'Navigationsmenü verwalten';
$lang['install']['wizard_overwrite'] = 'Datenbank überschreiben';
$lang['install']['wizard_overwrite2'] = '<b>ACHTUNG:</b> Eventuell vorhandene Daten in der oben angegeben Datenbank gehen verloren!';
$lang['install']['none'] = 'keine';
$lang['install']['everyone'] = 'Jeder';
$lang['install']['only_login'] = 'Nur Eingeloggte';
$lang['install']['only_admin'] = 'Nur Admins';
$lang['install']['only_op'] = 'Nur Superadminen';
$lang['install']['no_admin'] = 'Keine Admins';
$lang['install']['only_logout'] = 'Nur Ausgeloggte';
$lang['install']['modules_settings_success'] = 'Änderungen erfolgreich gespeichert.';
$lang['install']['modules_menu_start'] = 'Hauptmenüpunkt des Moduls / Modul-Startseite';
$lang['install']['modules_menu_sub'] = 'Untermenüpunkte';
$lang['install']['modules_menu_internal'] = 'Keine Menüpunkte - Interne Verweise';
$lang['install']['modules_menu_new'] = 'Neuen Menüeintrag hinzufügen';
$lang['install']['modules_del_success'] = 'Der Menü-Eintrag wurde erfolgreich gelöscht';
$lang['install']['modules_db_caption'] = 'Datenbank - Modul';
$lang['install']['modules_db_subcaption'] = 'Hier können Sie die Datenbankeinträge zu diesem Modul verwalten';
$lang['install']['modules_db_belong'] = 'Folgende Datenbank-Tabellen gehören zu diesem Modul';
$lang['install']['modules_actions'] = 'Aktionen';
$lang['install']['modules_reset_moddb'] = 'Modul-Datenbank zurücksetzen';
$lang['install']['modules_export_moddb'] = 'Modul-Datenbank exportieren';
$lang['install']['modules_rewritedb_success'] = 'Tabelle wurde erfolgreich neu geschrieben';
$lang['install']['modules_reset_modules'] = 'Alle Module zurücksetzen';
$lang['install']['modules_config'] = 'Konfig.';
$lang['install']['modules_menu'] = 'Menü';
$lang['install']['modules_db'] = 'DB';
$lang['install']['unknown'] = 'Unbekannt';
$lang['install']['menu_cap'] = 'Menu Einträge ersetzen';
$lang['install']['menu_rewrite'] = 'Alle Einträge ersetzen?';
$lang['install']['menu_write'] = 'Menu erfolgreich neu geschrieben';

$lang['install']['wizard_vars_caption'] = 'Wichtige Systemvariablen einstellen';
$lang['install']['wizard_vars_subcaption'] = 'Hier, in diesem letzten Schritt, werden die wichtigsten Konfigurationen in Lansuite eingestellt.';
$lang['install']['vars_country'] = 'Land, in dem die Party stattfindet';
$lang['install']['vars_url'] = 'URL der Webseite';
$lang['install']['vars_email'] = 'E-Mail des Webmasters';
$lang['install']['vars_system_mode'] = 'Internet- oder Lokaler-Modus?';
$lang['install']['vars_system_mode_internet'] = 'Internet-Seite. Vor der Party';
$lang['install']['vars_system_mode_intranet'] = 'Intranet-Seite. Auf der Party';

$lang['install']['warning_del_menuitem'] = 'Mit diesem Eintrag ist eine Zugriffsberechtigung verknüpft. Sie sollten diesen Eintrag daher nicht löschen, da sonst jeder Zugriff auf die betreffende Datei hat.'. HTML_NEWLINE .'Wenn Sie nur den Menülink entfernen möchten, löschen Sie die Felder Titel und Linkziel.'. HTML_NEWLINE .'Wenn Sie wirklich jedem Zugriff auf die Datei geben möchten, setzen Sie den Zugriff auf Jeder und löschen Sie dann den Eintrag.';
?>