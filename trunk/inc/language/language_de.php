<?php

	// Global
	$lang["sys"]["yes"] = "Ja";
	$lang["sys"]["no"] = "Nein";
	$lang["sys"]["autor"] = "Autor";
	$lang["sys"]["version"] = "Version";
	$lang["sys"]["online"] = "Online";
	$lang["sys"]["offline"] = "Offline";
	$lang["sys"]["compressed"] = "kompimiert";
	$lang["sys"]["uncompressed"] = "unkomprimiert";
	$lang["sys"]["seconds"] = "Sekunden";
	$lang["sys"]["language"] = "Sprache";

	// Index Module
	$lang['index_module']['logout'] = "Sie wurden erfolgreich aus dem Intranet ausgeloggt";
	$lang['index_module']['data_lost'] = "Bitte wenden Sie sich an die Organisatoren. Und seien Sie nett und geduldig :)";

	// Class Auth
	$lang['class_auth']['wrong_pw_inet'] = "Haben Sie ihr Passwort vergessen?<br/><a href=\"index.php?mod=usrmgr&action=pwrecover\">Hier können Sie sich ein neues Passwort generieren</a>";
	$lang['class_auth']['wrong_pw_lan'] = "Sollten Sie ihr Passwort vergessen haben wenden Sie sich bitte an die Organisation";
	$lang['class_auth']['wrong_pw'] = "Das von Ihnen eingegebene Passwort ist falsch.";
	$lang['class_auth']['wrong_pw_log'] = "Login für %EMAIL% fehlgeschlagen (Passwort-Fehler)";
	$lang['class_auth']['closed'] = "Ihr Account ist gesperrt. Melden Sie sich bitte bei der Organisation.";
	$lang['class_auth']['closed_log'] = "Login für %EMAIL% fehlgeschlagen (Account gesperrt)";
	$lang['class_auth']['not_checkedin'] = "Sie sind nicht eingecheckt. Melden Sie sich bitte bei der Organisation.";
	$lang['class_auth']['not_checkedin_log'] = "Login für %EMAIL% fehlgeschlagen (Account nicht eingecheckt)";
	$lang['class_auth']['checkedout'] = "Sie sind bereits ausgecheckt. Melden Sie sich bitte bei der Organisation.";
	$lang['class_auth']['checkedout_log'] = "Login für %EMAIL% fehlgeschlagen (Account ausgecheckt)";
	$lang['class_auth']['get_email_or_id'] = "Bitte geben Sie Ihre Emailadresse, oder Ihre LanSuite-ID ein";
	$lang['class_auth']['get_pw'] = "Bitte geben Sie Ihr Kennwort ein";


	// Class db_mysql
	$lang['class_db_mysql']['no_mysql'] = "Das MySQL-PHP Modul ist nich geladen. Bitte f&uuml;gen sie die mysql.so Extension zur php.ini hinzu und restarten sie Apache";
	$lang['class_db_mysql']['no_connection'] = "Die Verbindung zur Datenbank ist fehlgeschlagen. Lansuite wird abgebrochen";
	$lang['class_db_mysql']['no_db'] = "Die Datenbank '%DB%' konnte nicht ausgewählt werden. Lansuite wird abgebrochen";
	$lang['class_db_mysql']['sql_error'] = "(%LINE%) SQL-Failure. Database respondet: <font color=\"red\"><b>%ERROR%</b></font><br/> Your query was: <i>%QUERY%</i><br/><br/> Script: %SCRIPT%";
	$lang['class_db_mysql']['sql_error_log'] = "SQL-Fehler in PHP-Skript '%SCRIPT%' (Referrer: '%REFERRER%')<br />SQL-Fehler-Meldung: %ERROR%<br />Query: %QUERY%";

	// Class Display
	# No Language

	// Class func
	$lang['class_func']['no_templ'] = "Das Template <b>%TEMPL%</b> konnte nicht ge&ouml;ffnet werden";
	$lang['class_func']['seatinfo_priority'] = "Function setainfo needs Priority defined as Integer: 0 low (grey), 1 middle (green), 2 high (orange)";
	$lang['class_func']['sunday'] = "Sonntag";
	$lang['class_func']['monday'] = "Montag";
	$lang['class_func']['tuesday'] = "Dienstag";
	$lang['class_func']['wednesdey'] = "Mittwoch";
	$lang['class_func']['thursday'] = "Donnerstag";
	$lang['class_func']['friday'] = "Freitag";
	$lang['class_func']['saturday'] = "Samstag";
	$lang['class_func']['sunday_short'] = "So";
	$lang['class_func']['monday_short'] = "Mo";
	$lang['class_func']['tuesday_short'] = "Di";
	$lang['class_func']['wednesdey_short'] = "Mi";
	$lang['class_func']['thursday_short'] = "Do";
	$lang['class_func']['friday_short'] = "Fr";
	$lang['class_func']['saturday_short'] = "Sa";
	$lang['class_func']['january'] = "Januar";
	$lang['class_func']['february'] = "Februar";
	$lang['class_func']['march'] = "März";
	$lang['class_func']['april'] = "April";
	$lang['class_func']['may'] = "Mai";
	$lang['class_func']['juni'] = "Juni";
	$lang['class_func']['july'] = "Juli";
	$lang['class_func']['august'] = "August";
	$lang['class_func']['september'] = "September";
	$lang['class_func']['october'] = "Oktober";
	$lang['class_func']['november'] = "November";
	$lang['class_func']['december'] = "Dezember";
	$lang['class_func']['error_access_denied'] = "Sie haben keine Zugriffsrechte f&uuml;r diesen Bereich";
	$lang['class_func']['error_no_login'] = "Sie sind nicht eingelogt. Bitte loggen Sie sich erst ein, bevor Sie diesen Bereich betreten";
	$lang['class_func']['error_not_found'] = "Leider ist die von Ihnen aufgerufene Seite auf diesem Server nicht vorhanden.<br/>Um Fehler zu vermeiden, sollten Sie die URL nicht manuell &auml;ndern, sondern die Links benutzen. Wenn Sie die Adresse manuell eingegeben haben überprüfen Sie bitte die URL.";
	$lang['class_func']['error_deactivated'] = "Dieses lansuite Modul wurde deaktiviert, und steht somit nicht zur Verf&uuml;gung";
	$lang['class_func']['error_no_refresh'] = "Sie haben diese Anfrage wiederholt ausgef&uuml;hrt.";
	$lang['class_func']['no_item_rlist'] = "Es sind keine %OBJECT% vorhanden";
	$lang['class_func']['no_item_search'] = "Es wurden keine passenden %OBJECT% gefunden";


	// Class GD
	$lang['class_gd']['error_imagecreate'] = "Unable to Initialize new GD image stream";
	$lang['class_gd']['error_write'] = "Unable to write in directory '%PATH%'";

	// Class Graph

         // Class Seat
         $lang['class_seat']['not_shown'] = "nicht angezeigt";
         $lang['class_seat']['u18_block'] = "&lt;Unter18&gt; Block";

         // Class Seatadmin
	$lang['class_seatadmin']['search_new_seat'] = "neuen Platz suchen";
        	$lang['class_seatadmin']['swap_seat'] = "Platz von <strong>\"%CURRENTUSERNAME%\"</strong> mit dem von <strong>\"%USERNAME%\" tauschen</strong>";
         $lang['class_seatadmin']['set_filled_seat'] = "<strong>\"%CURRENTUSERNAME%\"</strong> Platz zuweisen und <strong>\"%USERNAME%\"</strong> <strong>keinen</strong> Platz vergeben";
         $lang['class_seatadmin']['set_filled_seat2'] = "<strong>\"%CURRENTUSERNAME%\"</strong> Platz zuweisen und <strong>\"%USERNAME%\"</strong> neuen Platz suchen";
         $lang['class_seatadmin']['cancel_changes'] = "Sitzplan&auml;nderung verwerfen / Zur&uuml;ck";
         $lang['class_seatadmin']['nothing_to_change'] = "Keine Daten zum &Auml;ndern vorhanden.";
         $lang['class_seatadmin']['seat_changed'] = "Der Platz wurde bereits ge&auml;ndert.";
         $lang['class_seatadmin']['user_has_seat'] = "Dem Benutzer wurde bereits ein Platz zugewiesen.";
         $lang['class_seatadmin']['change_confirm'] = "Die Sitzpl&auml;tze wurden erfolgreich ge&auml;ndert";

	// Class Party
	$lang['class_party']['drowpdown_name'] = "Party auswählen";
	$lang['class_party']['drowpdown_price'] = "Preis auswählen";
	$lang['class_party']['drowpdown_user_group'] = "Benutzergruppe";
	$lang['class_party']['drowpdown_no_group'] = "Ohne Gruppe";
	$lang['class_party']['no_user_group'] = "Keine Benutzergruppe vorhanden";
	$lang['class_party']['logevent'] = "Die Anmeldung von %ID% bei der Party %PARTY% wurde geändert. Neu: Bezahlt = %PAID%, Checkin = %CHECKIN%, Checkout = %CHECKOUT%, Pfand = %SEATCONTROL%, Preisid = %PIRCEID%";

	// Class Sitetool
	$lang['class_sitetool']['footer_violation'] = "Der Eintrag {footer} wurde unerlaubt aus der index.htm entfernt!";

	// Class XML
	# No Language

	// Class Barcode
	$lang['barcode']['barcode'] = "Strichcode";
	
	// Buttons
	$lang['button']['activate'] = "Aktivieren";
	$lang['button']['add'] = "Hinzufügen";
	$lang['button']['add_to_buddylist'] = "In Buddy-Liste";
	$lang['button']['addarticle'] = "Artikel hinzufg.";
	$lang['button']['back'] = "Zurück";
	$lang['button']['bold'] = "Fett";
	$lang['button']['checkin'] = "Einchecken";
	$lang['button']['checkout'] = "Auschecken";
	$lang['button']['close'] = "Schließen";
	$lang['button']['code'] = "Code";
	$lang['button']['comments'] = "Kommentare";
	$lang['button']['deactivate'] = "Deactivieren";
	$lang['button']['delete'] = "Löschen";
	$lang['button']['details'] = "Details";
	$lang['button']['download'] = "Download";
	$lang['button']['edit'] = "Editieren";
	$lang['button']['fullscreen'] = "Vollbild";
	$lang['button']['games'] = "Paarungen";
	$lang['button']['generate'] = "Generieren";
	$lang['button']['join'] = "Beitreten";
	$lang['button']['kick'] = "Kick";
	$lang['button']['kursiv'] = "Kursiv";
	$lang['button']['login'] = "Einloggen";
	$lang['button']['new_calculate'] = "Neu berechnen";
	$lang['button']['new_post'] = "Antworten";
	$lang['button']['new_thread'] = "Neues Thema";
	$lang['button']['newoption'] = "Neue Option";
	$lang['button']['newpassword'] = "Neues Passwort";
	$lang['button']['next'] = "Weiter";
	$lang['button']['no'] = "Nein";
	$lang['button']['ok'] = "Okay";
	$lang['button']['open'] = "Öffnen";
	$lang['button']['order'] = "Bestellen";
	$lang['button']['paidchange'] = "Zahlst. ändern";
	$lang['button']['picture'] = "Bild";
	$lang['button']['ports'] = "Ports";
	$lang['button']['printview'] = "Druckansicht";
	$lang['button']['ranking'] = "Rangliste";
	$lang['button']['result'] = "Ergebnis";
	$lang['button']['save'] = "Speichern";
	$lang['button']['search'] = "Suchen";
	$lang['button']['send'] = "Senden";
	$lang['button']['sendmail'] = "Mail senden";
	$lang['button']['skip'] = "Überspringen";
	$lang['button']['thread_delete'] = "Thema löschen";
	$lang['button']['tree'] = "Spielbaum";
	$lang['button']['unblock'] = "Freigeben";
	$lang['button']['underline'] = "Unterstreichen";
	$lang['button']['veto'] = "Veto";
	$lang['button']['vote'] = "Abstimmen";
	$lang['button']['yes'] = "Ja";
	$lang['button']['zitat'] = "Zitat";
	$lang['button']['bookmark'] = "Lesezeichen";
	$lang['button']['change'] = "Ändern";
	$lang["button"]["undo_generate"]	= "Generieren rückgängig";
	$lang["button"]["undo_close"]	= "Beenden rückgängig";
	$lang["button"]["preview"]	= "Vorschau";
	$lang["button"]["disqualify"]	= "Disqualifizieren";
	$lang["button"]["undisqualify"]	= "Qualifizieren";
	$lang["button"]["switch_user"]	= "Benutzer wechseln";
	$lang["button"]["day"]	= "Tages Übersicht";
	$lang["button"]["month"]	= "Monats Übersicht";
	$lang["button"]["year"]	= "Jahres Übersicht";
	$lang["button"]["finish"]	= "Beenden";
	$lang["button"]["print"]	= "Drucken";
	$lang["button"]["changeclanpw"] = "Clanpw ändern";
	
?>