<?php

/***************************************************************************
 *                            lang_admin.php [German]
 *                              -------------------
 *     begin                : Sun May 19 2002
 *     copyright            : (C) 2001 The phpBB Group
 *     email                : support@phpbb.com
 *
 *
 ****************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

/***************************************************************************
 * German translation by the translation team of phpBB.de:
 *   http://www.phpbb.de/groupcp.php?g=13086
 * Team Lead: Philipp Kordowich (PhilippK [at] phpbb.de)
 * Special Thanks to:
 *   Joel Ricardo Zick (Rici)
 *   Manfred Hoffmann, Ingo K�hler, Ingo Migliarina, Christian Wunsch
 * and all others for their comments and suggestions
 * 
 * Release date: 2006-04-08
 ***************************************************************************/

//
// Format is same as lang_main
//

//
// Modules, this replaces the keys used
// in the modules[][] arrays in each module file
//
$lang['General'] = 'Allgemeines';
$lang['Users'] = 'Benutzer';
$lang['Groups'] = 'Gruppen';
$lang['Forums'] = 'Forum';
$lang['Styles'] = 'Styles/Themes';

$lang['Configuration'] = 'Konfiguration';
$lang['Permissions'] = 'Befugnisse';
$lang['Manage'] = 'Einstellungen';
$lang['Disallow'] = 'Benutzernamen verbieten';
$lang['Prune'] = 'Autom. L�schen';
$lang['Mass_Email'] = 'Massen-E-Mail versenden';
$lang['Ranks'] = 'R�nge';
$lang['Smilies'] = 'Smilies';
$lang['Ban_Management'] = 'Bannen';
$lang['Word_Censor'] = 'Wortzensur';
$lang['Export'] = 'Exportieren';
$lang['Create_new'] = 'Erstellen';
$lang['Add_new'] = 'Hinzuf�gen';
$lang['Backup_DB'] = 'Datenbank-Backup';
$lang['Restore_DB'] = 'Datenbank wiederherstellen';


//
// Index
//
$lang['Admin'] = 'Administration';
$lang['Not_admin'] = 'Du hast keine Administrator-Rechte';
$lang['Welcome_phpBB'] = 'Willkommen bei phpBB';
$lang['Admin_intro'] = 'Danke, dass du dich f�r phpBB entschieden hast. Auf diesem Bildschirm erh�ltst du einen �berblick �ber die Statistiken deines Forums. Wenn du auf diese Seite zur�ckkehren m�chtest, klicke auf den <u>Admin Index</u>-Link im linken Bedienfeld. Um zu deinem Forum zur�ckzukehren, klicke auf das phpBB-Logo. Die anderen Links auf der linken Seite erlauben es dir, alle wichtigen Bereiche deines Forums zu kontrollieren. In jedem Bereich wird beschrieben, wie er richtig genutzt wird.';
$lang['Main_index'] = 'Forum Index';
$lang['Forum_stats'] = 'Forum Statistiken';
$lang['Admin_Index'] = 'Admin-Index';
$lang['Preview_forum'] = 'Forumsvorschau';

$lang['Click_return_admin_index'] = 'Klicke %shier%s, um zum Admin-Index zur�ckzukehren';

$lang['Statistic'] = 'Statistik';
$lang['Value'] = 'Wert';
$lang['Number_posts'] = 'Anzahl der Beitr�ge';
$lang['Posts_per_day'] = 'Beitr�ge pro Tag';
$lang['Number_topics'] = 'Anzahl der Themen';
$lang['Topics_per_day'] = 'Themen pro Tag';
$lang['Number_users'] = 'Anzahl der Benutzer';
$lang['Users_per_day'] = 'Benutzer pro Tag';
$lang['Board_started'] = 'Board startete am';
$lang['Avatar_dir_size'] = 'Gr��e des Avatarordners';
$lang['Database_size'] = 'Datenbankgr��e';
$lang['Gzip_compression'] ='GZip-Kompression';
$lang['Not_available'] = 'Nicht verf�gbar';

$lang['ON'] = 'Aktiv'; // This is for GZip compression
$lang['OFF'] = 'Inaktiv';


//
// DB Utils
//
$lang['Database_Utilities'] = 'Datenbankfunktionen';

$lang['Restore'] = 'Wiederherstellen';
$lang['Backup'] = 'Backup';
$lang['Restore_explain'] = 'Hiermit werden alle phpBB Tabellen aus einer Datei wiederhergestellt. Falls es dein Server unterst�tzt, kannst du auch einen GZip-komprimierten Text hochladen - er wird automatisch dekomprimiert! <b>ACHTUNG</b>: Es werden alle existierenden Daten �berschrieben. Der Vorgang wird einige Zeit dauern, bitte verlasse diese Seite nicht, bis er abgeschlossen wurde.';
$lang['Backup_explain'] = 'Hier kannst du alle phpBB-Tabellen abspeichern. Solltest du noch weitere, eigene Tabellen in derselben Datenbank wie die phpBB-Tabellen haben, die auch gespeichert werden sollen, gib ihre Namen in der \'Zus�tzliche Tabellen\'-Textbox an (getrennt mit Kommata). Sollte dein Server es unterst�tzen, kannst du die Datei(en) auch mit GZip komprimieren, bevor du sie runterl�dst.';

$lang['Backup_options'] = 'Backup-Optionen';
$lang['Start_backup'] = 'Backup beginnen';
$lang['Full_backup'] = 'Vollst�ndiges Backup';
$lang['Structure_backup'] = 'Nur-Struktur-Backup';
$lang['Data_backup'] = 'Nur-Daten-Backup';
$lang['Additional_tables'] = 'Zus�tzliche Tabellen';
$lang['Gzip_compress'] = 'GZip-Komprimierungs Datei';
$lang['Select_file'] = 'W�hle eine Datei';
$lang['Start_Restore'] = 'Wiederherstellung beginnen';

$lang['Restore_success'] = 'Die Datenbank wurde wieder hergestellt.<br /><br />Dein Board sollte jetzt wieder den Stand des Backups haben.';
$lang['Backup_download'] = 'Dein Download wird in K�rze beginnen - bitte etwas Geduld';
$lang['Backups_not_supported'] = 'Fehler: Dein Datenbanksystem unterst�tzt Datenbank-Backups nicht!';

$lang['Restore_Error_uploading'] = 'Fehler beim Hochladen der Backup-Datei';
$lang['Restore_Error_filename'] = 'Probleme mit dem Dateinamen, probiere einen anderen';
$lang['Restore_Error_decompress'] = 'Die GZip-Version kann nicht dekomprimiert werden, nutze bitte eine Nur-Text-Datei';
$lang['Restore_Error_no_file'] = 'Es wurde keine Datei hochgeladen';


//
// Auth pages
//
$lang['Select_a_User'] = 'W�hle einen Benutzer';
$lang['Select_a_Group'] = 'W�hle eine Gruppe';
$lang['Select_a_Forum'] = 'W�hle ein Forum';
$lang['Auth_Control_User'] = 'Benutzerbefugniskontrolle';
$lang['Auth_Control_Group'] = 'Gruppenbefugniskontrolle';
$lang['Auth_Control_Forum'] = 'Forenzugangskontrolle';
$lang['Look_up_User'] = 'Benutzer ausw�hlen';
$lang['Look_up_Group'] = 'Gruppe ausw�hlen';
$lang['Look_up_Forum'] = 'Forum ausw�hlen';

$lang['Group_auth_explain'] = 'Du kannst hier die Befugnisse und den Moderator-Status f�r jede Gruppe einstellen. Vergiss nicht, dass einzelne Benutzerbefugnisse immer noch g�ltig sind, wenn du die Gruppenbefugnisse �nderst (z. B. Zugang zu Foren u. �.). Sollte dies der Fall sein, wirst du dar�ber informiert.';
$lang['User_auth_explain'] = 'Du kannst hier die Befugnisse und den Moderator-Status f�r jeden einzelnen Benutzer einstellen. Vergiss nicht, dass Gruppenbefugnisse immer noch g�ltig sind, wenn du die Benutzerbefugnisse �nderst (z. B. Zugang zu Foren u. �.). Sollte dies der Fall sein, wirst du dar�ber informiert.';
$lang['Forum_auth_explain'] = 'Du kannst hier die Zugangsebenen f�r jedes Forum bestimmen. Es gibt eine einfache und eine fortgeschrittene Methode, dies zu tun. Bei der fortgeschrittenen Methode hast du eine bessere Kontrolle �ber jedes Forum. Bedenke, dass das �ndern der Zugangsebene beeinflusst, welche Benutzer welche Aktionen im Forum durchf�hren k�nnen.';

$lang['Simple_mode'] = 'Einfache Methode';
$lang['Advanced_mode'] = 'Fortgeschrittene Methode';
$lang['Moderator_status'] = 'Moderatorenstatus';

$lang['Allowed_Access'] = 'Zugang gestattet';
$lang['Disallowed_Access'] = 'Zugang verwehrt';
$lang['Is_Moderator'] = 'ist hier Moderator';
$lang['Not_Moderator'] = 'ist hier kein Moderator';

$lang['Conflict_warning'] = 'Warnung: Autorisationskonflikt';
$lang['Conflict_access_userauth'] = 'Der Benutzer hat auf Grund seiner Gruppenmitgliedschaft immer noch Rechte in diesem Forum. Vielleicht solltest du die Gruppenrechte �ndern oder den Benutzer komplett aus der Benutzergruppe entfernen. Die Gruppen mit Rechten (und die dazugeh�rigen Foren) stehen unten.';
$lang['Conflict_mod_userauth'] = 'Der Benutzer hat immer noch Moderatorenrechte in diesem Forum. Vielleicht solltest du die Gruppenrechte �ndern oder den Benutzer komplett aus der Benutzergruppe entfernen. Die Gruppen mit Rechten (und die dazugeh�rigen Foren) stehen unten.';

$lang['Conflict_access_groupauth'] = 'Der oder die folgenden Benutzer haben auf Grund ihrer Benutzereinstellungen immer noch das Zugangsrecht zu diesem Forum. Vielleicht solltest du diese Einstellungen �ndern, um dem Benutzer komplett den Zugang zu verweigern. Die Benutzer mit Rechten (und die dazugeh�rigen Foren) stehen unten.';
$lang['Conflict_mod_groupauth'] = 'Der oder die folgenden Benutzer haben auf Grund ihrer Benutzereinstellungen immer noch Moderatorenrechte in diesem Forum. Vielleicht solltest du diese Einstellungen �ndern, um dem Benutzer komplett die Rechte zu nehmen. Die Benutzer mit Rechten (und die dazugeh�rigen Foren) stehen unten.';

$lang['Public'] = '�ffentlich';
$lang['Private'] = 'Privat';
$lang['Registered'] = 'Registriert';
$lang['Administrators'] = 'Administratoren';
$lang['Hidden'] = 'Versteckt';

// These are displayed in the drop down boxes for advanced
// mode forum auth, try and keep them short!
$lang['Forum_ALL'] = 'Alle';
$lang['Forum_REG'] = 'Reg';
$lang['Forum_PRIVATE'] = 'Privat';
$lang['Forum_MOD'] = 'Mods';
$lang['Forum_ADMIN'] = 'Admin';

$lang['View'] = 'Ansicht';
$lang['Read'] = 'Lesen';
$lang['Post'] = 'Posten';
$lang['Reply'] = 'Antworten';
$lang['Edit'] = 'Editieren';
$lang['Delete'] = 'L�schen';
$lang['Sticky'] = 'Wichtig';
$lang['Announce'] = 'Ank�ndigung';
$lang['Vote'] = 'Umfrage';
$lang['Pollcreate'] = 'Umfrage erstellen';

$lang['Permissions'] = 'Befugnisse';
$lang['Simple_Permission'] = 'Einfache Befugnisse';

$lang['User_Level'] = 'Benutzerebene';
$lang['Auth_User'] = 'Benutzer';
$lang['Auth_Admin'] = 'Administrator';
$lang['Group_memberships'] = 'Benutzergruppenmitglieder';
$lang['Usergroup_members'] = 'Diese Gruppe hat die folgenden Mitglieder';

$lang['Forum_auth_updated'] = 'Forumsberechtigungen aktualisiert';
$lang['User_auth_updated'] = 'Benutzerberechtigungen aktualisiert';
$lang['Group_auth_updated'] = 'Gruppenberechtigungen aktualisiert';

$lang['Auth_updated'] = 'Befugnisse wurden aktualisiert';
$lang['Click_return_userauth'] = 'Klicke %shier%s, um zu den Benutzerrechten zur�ckzukehren';
$lang['Click_return_groupauth'] = 'Klicke %shier%s, um zu den Gruppenrechten zur�ckzukehren';
$lang['Click_return_forumauth'] = 'Klicke %shier%s, um zu den Forenberechtigungen zur�ckzukehren';


//
// Banning
//
$lang['Ban_control'] = 'Sperren';
$lang['Ban_explain'] = 'Hier kannst du Benutzer bannen. Du kannst entweder einen bestimmten Benutzer, eine IP-Adresse oder einen Hostnamen sperren. Durch diese Methode kann der Benutzer die Startseite des Forums nicht aufrufen. Um den Benutzer daran zu hindern, sich unter einem anderen Namen anzumelden, kannst du auch bestimmte E-Mail-Adressen sperren. Eine E-Mail-Sperre verhindert nur das Registrieren, nicht das Posten eines Benutzers!';
$lang['Ban_explain_warn'] = 'Bitte beachte, dass, wenn du mehrere IP-Adressen eingibst, alle Adressen zwischen dem Anfang und dem Ende der Sperrliste hinzugef�gt werden. Versuche die Anzahl der Adressen klein zu halten, indem du Wildcards einsetzt, wo es m�glich ist. Am besten ist es, eine konkrete IP-Adresse anzugeben.';

$lang['Select_username'] = 'W�hle einen Benutzernamen';
$lang['Select_ip'] = 'W�hle eine IP-Adresse';
$lang['Select_email'] = 'W�hle eine E-Mail-Adresse';

$lang['Ban_username'] = 'Einen oder mehrere Benutzer bannen';
$lang['Ban_username_explain'] = 'Wenn du mehrere Benutzer ausw�hlst, kannst du auch mehrere Benutzer auf einmal bannen';

$lang['Ban_IP'] = 'Eine(n) oder mehrere IP-Adressen/Hostnamen bannen';
$lang['IP_hostname'] = 'IP-Adressen oder Hostname';
$lang['Ban_IP_explain'] = 'Um mehrere verschiedene IP-Adressen oder Hostnamen anzugeben, trenne sie mit Kommata voneinander. Um einen Bereich von IP-Adressen anzugeben, trenne den Anfang und das Ende mit einem Bindestrich (-), benutze * als Platzhalter';

$lang['Ban_email'] = 'Eine oder mehrere E-Mail Adressen bannen';
$lang['Ban_email_explain'] = 'Um mehrere verschiedene E-Mail Adressen anzugeben, trenne sie mit Kommata voneinander. F�r einen allgemeinen Benutzernamen benutze ein * (z.B. *@hotmail.de)';

$lang['Unban_username'] = 'Einen oder mehrere Benutzer entsperren';
$lang['Unban_username_explain'] = 'Wenn du mehrere Benutzer ausw�hlst, kannst du auch mehrere Benutzer auf einmal entsperren';

$lang['Unban_IP'] = 'Eine oder mehrere IP-Adressen entsperren';
$lang['Unban_IP_explain'] = 'Wenn du mehrere IP-Adressen ausw�hlst, kannst du auch mehrere IP-Adressen auf einmal entsperren';

$lang['Unban_email'] = 'Eine oder mehrere E-Mail Adressen entsperren';
$lang['Unban_email_explain'] = 'Wenn du mehrere E-Mail Adressen ausw�hlst, kannst du auch mehrere E-Mail Adressen auf einmal entsperren';

$lang['No_banned_users'] = 'Keine gesperrten Benutzernamen';
$lang['No_banned_ip'] = 'Keine gebannten IP-Adressen';
$lang['No_banned_email'] = 'Keine gebannten E-Mail Adressen';

$lang['Ban_update_sucessful'] = 'Die Banliste wurde aktualisiert';
$lang['Click_return_banadmin'] = 'Klicke %shier%s, um zur Sperr-Kontrolle zur�ckzukehren';


//
// Configuration
//
$lang['General_Config'] = 'Allgemeine Konfiguration';
$lang['Config_explain'] = 'Hier kannst du die allgemeinen Einstellungen deines Forums �ndern. F�r Benutzer- und Foreneinstellungen nutze bitte die Links auf der linken Seite.';

$lang['Click_return_config'] = 'Klicke %shier%s, um zur allgemeinen Konfiguration zur�ckzukehren';

$lang['General_settings'] = 'Allgemeine Boardeinstellungen';
$lang['Server_name'] = 'Domainname';
$lang['Server_name_explain'] = 'Der Name der Domain, auf der das Board l�uft';
$lang['Script_path'] = 'Scriptpfad';
$lang['Script_path_explain'] = 'Der Pfad zu phpBB2, relativ zum Domainnamen';
$lang['Server_port'] = 'Server Port';
$lang['Server_port_explain'] = 'Der Port, unter dem dein Server l�uft, normalerweise 80. �ndere dies nur, wenn es ein anderer Port ist';
$lang['Site_name'] = 'Name der Seite';
$lang['Site_desc'] = 'Beschreibung der Seite';
$lang['Board_disable'] = 'Board deaktivieren';
$lang['Board_disable_explain'] = 'Hiermit sperrst du das Forum f�r alle Benutzer. Administratoren k�nnen auf den Administrations-Bereich zugreifen, wenn das Forum gesperrt ist.';
$lang['Acct_activation'] = 'Account-Freischaltung aktivieren';
$lang['Acc_None'] = 'Keine'; // These three entries are the type of activation
$lang['Acc_User'] = 'Per E-Mail';
$lang['Acc_Admin'] = 'Durch den Admin';

$lang['Abilities_settings'] = 'Standard Benutzer- und Foreneinstellungen';
$lang['Max_poll_options'] = 'Maximale Anzahl der Umfrageoptionen';
$lang['Flood_Interval'] = 'Flood-Intervall';
$lang['Flood_Interval_explain'] = 'Anzahl der Sekunden, die ein Benutzer warten muss, bevor er einen neuen Beitrag schreiben kann';
$lang['Board_email_form'] = 'Benutzer E-Mails per Board';
$lang['Board_email_form_explain'] = 'Deine Benutzer k�nnen sich �ber das Board E-Mails schreiben';
$lang['Topics_per_page'] = 'Themen pro Seite';
$lang['Posts_per_page'] = 'Beitr�ge pro Seite';
$lang['Hot_threshold'] = 'Beitr�ge, die ein Thema braucht, um ein \'Hot Topic\' zu werden';
$lang['Default_style'] = 'Standard-Style';
$lang['Override_style'] = 'Style �berschreiben';
$lang['Override_style_explain'] = 'Vom Benutzer gew�hltes Style �berschreiben';
$lang['Default_language'] = 'Standard-Sprache';
$lang['Date_format'] = 'Datumsformat';
$lang['System_timezone'] = 'Zeitzone';
$lang['Enable_gzip'] = 'GZip-Komprimierung aktivieren';
$lang['Enable_prune'] = 'Automatisches l�schen aktivieren';
$lang['Allow_HTML'] = 'HTML erlauben';
$lang['Allow_BBCode'] = 'BBCode erlauben';
$lang['Allowed_tags'] = 'Erlaubte HTML-Tags';
$lang['Allowed_tags_explain'] = 'Trenne die Tags mit Kommata';
$lang['Allow_smilies'] = 'Smilies erlauben';
$lang['Smilies_path'] = 'Speicherort f�r Smilies';
$lang['Smilies_path_explain'] = 'Der Pfad in deinem phpBB-Verzeichnis, in dem die Smilies liegen (z. B. images/smiles)';
$lang['Allow_sig'] = 'Signaturen erlauben';
$lang['Max_sig_length'] = 'Maximale Signaturl�nge';
$lang['Max_sig_length_explain'] = 'Die maximale Anzahl an Zeichen, die ein Benutzer in seiner Signatur nutzen darf';
$lang['Allow_name_change'] = 'Namenswechsel erlauben';

$lang['Avatar_settings'] = 'Avatareinstellungen';
$lang['Allow_local'] = 'Galerieavatare erlauben';
$lang['Allow_remote'] = 'Avatarremote erlauben';
$lang['Allow_remote_explain'] = 'Avatare, die von einer anderen Site verlinkt werden';
$lang['Allow_upload'] = 'Hochladen von Avataren erlauben';
$lang['Max_filesize'] = 'Maximale Gr��e';
$lang['Max_filesize_explain'] = 'F�r hochgeladene Avatare';
$lang['Max_avatar_size'] = 'Maximale Abmessungen des Avatars';
$lang['Max_avatar_size_explain'] = '(H�he x Breite in Pixel)';
$lang['Avatar_storage_path'] = 'Avatar Speicherpfad';
$lang['Avatar_storage_path_explain'] = 'Der Pfad in deinem phpBB-Verzeichnis, in dem die Avatare liegen (z. B. images/avatars)';
$lang['Avatar_gallery_path'] = 'Avatar Galeriepfad';
$lang['Avatar_gallery_path_explain'] = 'Der Pfad in deinem phpBB-Verzeichnis, in dem die Galerie-Avatare liegen (z. B. images/avatars/gallery)';

$lang['COPPA_settings'] = 'COPPA Einstellungen';
$lang['COPPA_fax'] = 'COPPA Fax Nummer';
$lang['COPPA_mail'] = 'COPPA E-Mail Adresse';
$lang['COPPA_mail_explain'] = 'Zu dieser E-Mail Adresse schicken die Eltern die COPPA Einverst�ndniserkl�rung';

$lang['Email_settings'] = 'E-Mail Einstellungen';
$lang['Admin_email'] = 'E-Mail Adresse des Administrators';
$lang['Email_sig'] = 'E-Mail Signatur';
$lang['Email_sig_explain'] = 'Diese Signatur wird an alle E-Mails des Administrators angeh�ngt';
$lang['Use_SMTP'] = 'Nutze einen SMTP Server zum Mailen';
$lang['Use_SMTP_explain'] = 'W�hle \'Ja\', wenn du m�chtest, dass deine E-Mails �ber einen SMTP-Server gesendet werden';
$lang['SMTP_server'] = 'SMTP-Server Addresse';
$lang['SMTP_username'] = 'SMTP Benutzername';
$lang['SMTP_username_explain'] = 'Gib nur dann einen Benutzernamen an, wenn der SMTP-Server dies ben�tigt';
$lang['SMTP_password'] = 'SMTP Passwort';
$lang['SMTP_password_explain'] = 'Gib nur dann ein Passwort an, wenn der SMTP-Server dies ben�tigt';

$lang['Disable_privmsg'] = 'Private Nachrichten';
$lang['Inbox_limits'] = 'Maximale Nachrichten im Eingang';
$lang['Sentbox_limits'] = 'Maximale Nachrichten im Ausgang';
$lang['Savebox_limits'] = 'Maximale Anzahl gespeicherter Nachrichten';

$lang['Cookie_settings'] = 'Cookie Einstellungen';
$lang['Cookie_settings_explain'] = 'Hier kannst du einstellen, was f�r Cookies zum Browser gesendet werden. Meistens stimmen die Standardeinstellungen. Solltest du sie �ndern m�ssen, tue es mit Bedacht, ansonsten kann sich niemand mehr im Forum einloggen.';
$lang['Cookie_domain'] = 'Cookie-Domain';
$lang['Cookie_name'] = 'Cookie-Name';
$lang['Cookie_path'] = 'Cookie-Pfad';
$lang['Cookie_secure'] = 'Sicheres Cookie';
$lang['Cookie_secure_explain'] = 'Falls dein Server auf SSL l�uft, aktiviere diese Funktion, ansonsten lasse sie deaktiviert';
$lang['Session_length'] = 'Sessionl�nge [ Sekunden ]';

// Visual Confirmation
$lang['Visual_confirm'] = 'Aktiviere visuelle Best�tigung';
$lang['Visual_confirm_explain'] = 'Benutzer m�ssen bei der Registrierung einen durch ein Bild vorgegeben Schl�ssel eingeben.';

// Autologin Keys - added 2.0.18
$lang['Allow_autologin'] = 'Erlaube automatisches Einloggen';
$lang['Allow_autologin_explain'] = 'Legt fest, ob Benutzern das automatische Einloggen erm�glicht wird';
$lang['Autologin_time'] = 'Ablaufzeit des Schl�ssels f�r das automatische Einloggen';
$lang['Autologin_time_explain'] = 'Tage, die der Schl�ssel f�r das automatische Einloggen g�ltig ist, wenn das Board nicht besucht wird. Mit Null l�uft der Schl�ssel nicht ab.';

// Search Flood Control - added 2.0.20
$lang['Search_Flood_Interval'] = 'Flood-Intervall f�r Suche';
$lang['Search_Flood_Interval_explain'] = 'Anzahl der Sekunden, die ein Benutzer zwischen Suchanfragen warten muss';

//
// Forum Management
//
$lang['Forum_admin'] = 'Forum Administration';
$lang['Forum_admin_explain'] = 'Hier kannst du Kategorien und Foren hinzuf�gen, l�schen, bearbeiten und neu anordnen.';
$lang['Edit_forum'] = 'Forum bearbeiten';
$lang['Create_forum'] = 'Neues Forum erstellen';
$lang['Create_category'] = 'Neue Kategorie erstellen';
$lang['Remove'] = 'Entfernen';
$lang['Action'] = 'Aktion';
$lang['Update_order'] = 'Reihenfolge �ndern';
$lang['Config_updated'] = 'Forumskonfiguration ge�ndert';
$lang['Edit'] = 'Bearbeiten';
$lang['Delete'] = 'L�schen';
$lang['Move_up'] = 'Nach oben';
$lang['Move_down'] = 'Nach unten';
$lang['Resync'] = 'Resync';
$lang['No_mode'] = 'Kein Modus ausgew�hlt';
$lang['Forum_edit_delete_explain'] = 'Hier kannst du alle allgemeinen Boardeinstellungen anpassen. Zur Benutzer- und Forenkonfiguration benutze bitte die entsprechenden Links auf der linken Seite';

$lang['Move_contents'] = 'Alle Inhalte verschieben';
$lang['Forum_delete'] = 'Forum l�schen';
$lang['Forum_delete_explain'] = 'Hier kannst du ein Forum oder eine Kategorie l�schen und entscheiden, wohin die enthaltenen Themen oder Foren verschoben werden sollen.';

$lang['Status_locked'] = 'Gesperrt';
$lang['Status_unlocked'] = 'Entsperrt';
$lang['Forum_settings'] = 'Allgemeine Foreneinstellungen';
$lang['Forum_name'] = 'Forumsname';
$lang['Forum_desc'] = 'Beschreibung';
$lang['Forum_status'] = 'Forumsstatus';
$lang['Forum_pruning'] = 'Automatisches Pruning';

$lang['prune_freq'] = '�berpr�fe das Themenalter alle';
$lang['prune_days'] = 'Entferne Themen, in denen nichts mehr geschrieben wurde seit';
$lang['Set_prune_data'] = 'Du hast das automatische L�schen f�r dieses Forum aktiviert, aber weder ein Intervall noch eine Anzahl an Tagen angegeben.';

$lang['Move_and_Delete'] = 'Verschieben und L�schen';

$lang['Delete_all_posts'] = 'Alle Beitr�ge l�schen';
$lang['Nowhere_to_move'] = 'Kein Ziel zum Verschieben';

$lang['Edit_Category'] = 'Kategorie editieren';
$lang['Edit_Category_explain'] = 'Hier kannst du den Namen einer Kategorie �ndern';

$lang['Forums_updated'] = 'Forums- und Kategorieinformationen wurden ge�ndert';

$lang['Must_delete_forums'] = 'Du musst erst alle Foren l�schen, bevor du diese Kategorie l�schen kannst';

$lang['Click_return_forumadmin'] = 'Klicke %shier%s, um zur Forumsadministration zur�ckzukehren';


//
// Smiley Management
//
$lang['smiley_title'] = 'Smilie-Bearbeitung';
$lang['smile_desc'] = 'Hier kannst du die Smilies, die die Benutzer in ihren Beitr�gen und Privaten Nachrichten einf�gen k�nnen, hinzuf�gen, l�schen oder bearbeiten.';

$lang['smiley_config'] = 'Smilie-Konfiguration';
$lang['smiley_code'] = 'Smilie Code';
$lang['smiley_url'] = 'Smilie Bilddatei';
$lang['smiley_emot'] = 'Smilie Beschreibung';
$lang['smile_add'] = 'Einen neuen Smilie hinzuf�gen';
$lang['Smile'] = 'Smilie';
$lang['Emotion'] = 'Beschreibung';

$lang['Select_pak'] = 'W�hle Paketdatei (.pak)';
$lang['replace_existing'] = 'Aktuelle Smilies �berschreiben';
$lang['keep_existing'] = 'Aktuelle Smilies behalten';
$lang['smiley_import_inst'] = 'Du solltest das Smilie-Paket entzippen und alle Dateien ins jeweilige Smilie-Verzeichnis hochladen. W�hle dann die korrekten Angaben, um das Paket zu installieren.';
$lang['smiley_import'] = 'Smilie-Paketimport';
$lang['choose_smile_pak'] = 'W�hle ein Smilie-Paket (.pak)';
$lang['import'] = 'Smilies importieren';
$lang['smile_conflicts'] = ' Was tun, wenn Konflikte auftreten?';
$lang['del_existing_smileys'] = 'Aktuelle Smilies vor dem Import l�schen';
$lang['import_smile_pack'] = 'Smilie-Paket importieren';
$lang['export_smile_pack'] = 'Smilie-Paket erstellen';
$lang['export_smiles'] = 'Um aus deinen jetzigen Smilies ein Paket zu erstellen, klicke %shier%s, um das Paket herunterzuladen. Achte darauf, die .pak-Erweiterung am Ende beizubehalten. Erstelle dann eine Zip-Datei mit allen benutzten Smilies und der .pak-Datei.';

$lang['smiley_add_success'] = 'Der Smilie wurde hinzugef�gt';
$lang['smiley_edit_success'] = 'Der Smilie wurde ge�ndert';
$lang['smiley_import_success'] = 'Das Smilie-Paket wurde installiert';
$lang['smiley_del_success'] = 'Der Smilie wurde gel�scht';
$lang['Click_return_smileadmin'] = 'Klicke %shier%s, um zur Smilie-Verwaltung zur�ckzukehren';

$lang['Confirm_delete_smiley'] = 'Bist du sicher, dass dieser Smilie gel�scht werden soll?';


//
// User Management
//
$lang['User_admin'] = 'Benutzer-Administration';
$lang['User_admin_explain'] = 'Hier kannst du die Daten und Optionen eines Nutzers �ndern. Um die Befugnisse eines Benutzers zu �ndern, benutze bitte die Benutzer- und Gruppenkontrolle.';

$lang['Look_up_user'] = 'Benutzer ausw�hlen';

$lang['Admin_user_fail'] = 'Benutzerprofil konnte nicht ge�ndert werden';
$lang['Admin_user_updated'] = 'Benutzerprofil ge�ndert';
$lang['Click_return_useradmin'] = 'Klicke %shier%s, um zur Benutzeradministration zur�ckzukehren';

$lang['User_delete'] = 'Diesen Benutzer l�schen';
$lang['User_delete_explain'] = 'Klicke hier, um den Benutzer zu l�schen - diese Aktion kann nicht r�ckg�ngig gemacht werden.';
$lang['User_deleted'] = 'Benutzer wurde gel�scht';

$lang['User_status'] = 'Benutzer ist aktiv';
$lang['User_allowpm'] = 'Darf Private Nachrichten verschicken';
$lang['User_allowavatar'] = 'Darf einen Avatar benutzen';

$lang['Admin_avatar_explain'] = 'Hier kannst du den Avatar des Benutzers ansehen und l�schen';

$lang['User_special'] = 'Spezielle Optionen (nur f�r Administratoren)';
$lang['User_special_explain'] = 'Diese Optionen k�nnen nicht von den Benutzern ge�ndert werden. Du kannst hier den Status und andere Optionen der Benutzer festlegen, die den Benutzern selbst nicht zur Verf�gung stehen.';


//
// Group Management
//
$lang['Group_administration'] = 'Gruppenadministration';
$lang['Group_admin_explain'] = 'Hier kannst du die Benutzergruppen deines Forum �berwachen. Du kannst bestehende Gruppen l�schen oder editieren oder neue anlegen. Ebenso kannst du Gruppenleiter w�hlen, den Gruppenstatus auf offen/geschlossen �ndern und den Gruppennamen bzw. die Gruppenbeschreibung �ndern';
$lang['Error_updating_groups'] = 'Fehler beim Aktualisieren der Gruppe';
$lang['Updated_group'] = 'Die Gruppe wurde abge�ndert';
$lang['Added_new_group'] = 'Die Gruppe wurde hinzugef�gt';
$lang['Deleted_group'] = 'Die Gruppe wurde gel�scht';
$lang['New_group'] = 'Neue Gruppe erstellen';
$lang['Edit_group'] = 'Gruppe bearbeiten';
$lang['group_name'] = 'Gruppenname';
$lang['group_description'] = 'Gruppenbeschreibung';
$lang['group_moderator'] = 'Gruppenleiter';
$lang['group_status'] = 'Gruppenstatus';
$lang['group_open'] = 'Offene Gruppe';
$lang['group_closed'] = 'Geschlossene Gruppe';
$lang['group_hidden'] = 'Versteckte Gruppe';
$lang['group_delete'] = 'Gel�schte Gruppe';
$lang['group_delete_check'] = 'Diese Gruppe l�schen';
$lang['submit_group_changes'] = '�nderungen �bernehmen';
$lang['reset_group_changes'] = 'Reset';
$lang['No_group_name'] = 'Bitte gib einen Gruppennamen an';
$lang['No_group_moderator'] = 'Bitte gib einen Gruppenleiter an';
$lang['No_group_mode'] = 'Du musst einen Status f�r diese Gruppe angeben (offen/geschlossen)';
$lang['No_group_action'] = 'Es wurde keine Aktion ausgew�hlt';
$lang['delete_group_moderator'] = 'Alten Gruppenleiter entfernen?';
$lang['delete_moderator_explain'] = 'Wenn du den Gruppenleiter wechseln m�chtest, w�hle die entsprechende Option, um den alten Leiter zu entfernen. Ansonsten w�hle die Option nicht und der Benutzer wird ein regul�res Mitglied der Gruppe.';
$lang['Click_return_groupsadmin'] = 'Klicke %shier%s, um zur Gruppenadministration zur�ckzukehren.';
$lang['Select_group'] = 'Gruppe w�hlen';
$lang['Look_up_group'] = 'Gruppe finden';


//
// Prune Administration
//
$lang['Forum_Prune'] = 'Automatisches l�schen';
$lang['Forum_Prune_explain'] = 'Du kannst angeben, dass alle Themen, in denen seit einer gewissen Zeit nichts gepostet wurde, gel�scht werden. Solltest du keine Zahl angeben, werden alle Themen gel�scht. Laufende Umfragen und Ank�ndigungen sind davon nicht betroffen. Diese Themen m�ssen manuell entfernt werden.';
$lang['Do_Prune'] = 'L�schen starten';
$lang['All_Forums'] = 'Alle Foren';
$lang['Prune_topics_not_posted'] = 'Themen l�schen in denen es keine Antworten gab seit';
$lang['Topics_pruned'] = 'Gel�schte Themen';
$lang['Posts_pruned'] = 'Gel�schte Beitr�ge';
$lang['Prune_success'] = 'Das automatische L�schen des Forums wurde aktiviert';


//
// Word censor
//
$lang['Words_title'] = 'Wortzensur';
$lang['Words_explain'] = 'Hier kannst du W�rter bestimmen, die in den Beitr�gen automatisch zensiert werden. Au�erdem kann kein Benutzer einen Namen w�hlen, in dem diese W�rter vorkommen. Du kannst ein * als Platzhalter im Wort-Feld verwenden. Beispiel: Fisch* entfernt W�rter wie Fischbesteck, Fischfang usw., *Fisch entfernt Backfisch, Stockfisch usw.';
$lang['Word'] = 'Wort';
$lang['Edit_word_censor'] = 'Wortzensur �ndern';
$lang['Replacement'] = 'Ersatz';
$lang['Add_new_word'] = 'Neues Wort hinzuf�gen';
$lang['Update_word'] = 'Zensur aktualisieren';

$lang['Must_enter_word'] = 'Ein Wort und ein entsprechender Ersatz sind n�tig';
$lang['No_word_selected'] = 'Kein Wort zum Editieren ausgew�hlt';

$lang['Word_updated'] = 'Die Wortzensur wurde aktualisiert';
$lang['Word_added'] = 'Die Wortzensur wurde eingerichtet';
$lang['Word_removed'] = 'Die Wortzensur wurde entfernt';

$lang['Click_return_wordadmin'] = 'Klicke %shier%s, um zur Wortzensur-Administration zur�ckzukehren';

$lang['Confirm_delete_word'] = 'Bist du sicher, dass diese Wortzensur gel�scht werden soll?';


//
// Mass Email
//
$lang['Mass_email_explain'] = 'Hier kannst du entweder allen registrierten Benutzern oder einer bestimmten Gruppe eine Nachricht schicken. Diese Nachricht wird an das Postfach des Administrators geschickt und anonym (BCC) an alle Empf�nger. Solltest du einer gro�en Gruppe eine E-Mail schicken, habe etwas Geduld und brich den Vorgang nicht ab. Es ist v�llig normal, dass der Vorgang l�nger dauert und du erh�ltst eine R�ckmeldung, wenn das Skript beendet ist';
$lang['Compose'] = 'Erstellen';

$lang['Recipients'] = 'Empf�nger';
$lang['All_users'] = 'Alle Benutzer';

$lang['Email_successfull'] = 'Die Nachricht wurde gesendet';
$lang['Click_return_massemail'] = 'Klicke %shier%s, um zur Massen E-Mail zur�ckzukehren';


//
// Ranks admin
//
$lang['Ranks_title'] = 'Rang-Administration';
$lang['Ranks_explain'] = 'Hier kannst du R�nge hinzuf�gen, editieren, anschauen und l�schen. Du kannst ebenfalls eigene R�nge erstellen, die du per Benutzeradministration an spezielle Benutzer vergibst.';

$lang['Add_new_rank'] = 'Neuen Rang anlegen';

$lang['Rank_title'] = 'Rangname';
$lang['Rank_special'] = 'Spezialrang';
$lang['Rank_minimum'] = 'Minimum-Beitr�ge';
$lang['Rank_maximum'] = 'Maximum-Beitr�ge';
$lang['Rank_image'] = 'Bild zum Rang (relativ zum Forenpfad)';
$lang['Rank_image_explain'] = 'Du kannst hier ein Bild bestimmen, dass dem jeweiligen Rang zugeordnet ist';

$lang['Must_select_rank'] = 'W�hle einen Rang aus';
$lang['No_assigned_rank'] = 'Kein Spezialrang vergeben';

$lang['Rank_updated'] = 'Die Ranginformationen wurden aktualisiert';
$lang['Rank_added'] = 'Der Rang wurde hinzugef�gt';
$lang['Rank_removed'] = 'Der Rang wurde gel�scht';
$lang['No_update_ranks'] = 'Der Rang wurde erfolgreich gel�scht. Allerdings wurden Benutzer, denen dieser Rang zugeordnet war, nicht aktualisiert. Du musst den Rang bei diesen Benutzern manuell aktualisieren';

$lang['Click_return_rankadmin'] = 'Klicke %shier%s, um zur Rang-Administration zur�ckzukehren';

$lang['Confirm_delete_rank'] = 'Bist du sicher, dass dieser Rang gel�scht werden soll?';


//
// Disallow Username Admin
//
$lang['Disallow_control'] = 'Verbot von Benutzernamen';
$lang['Disallow_explain'] = 'Hier kannst du Benutzernamen �berwachen, die nicht genutzt werden d�rfen. Du kannst einen Stern (*) als Platzhalter setzen. Beachte, dass du den jeweiligen Benutzer zuerst l�schen musst, wenn du einen bereits vergebenen Benutzernamen w�hlst.';

$lang['Delete_disallow'] = 'L�schen';
$lang['Delete_disallow_title'] = 'Einen verbotenen Namen entfernen';
$lang['Delete_disallow_explain'] = 'Du kannst einen verbotenen Namen entfernen, indem du den Namen aus der Liste ausw�hlst und Abschicken anklickst';

$lang['Add_disallow'] = 'Hinzuf�gen';
$lang['Add_disallow_title'] = 'Einen verbotenen Namen hinzuf�gen';
$lang['Add_disallow_explain'] = 'Du kannst ein * benutzen, um jegliche Benutzernamen zu verbieten';

$lang['No_disallowed'] = 'Keine verbotenen Benutzernamen';

$lang['Disallowed_deleted'] = 'Der verbotene Benutzername ist nun wieder gestattet';
$lang['Disallow_successful'] = 'Der verbotene Benutzername wurde hinzugef�gt';
$lang['Disallowed_already'] = 'Der angegebene Benutzername kann nicht verboten werden. Er existiert entweder schon oder stimmt mit einem existierenden �berein.';

$lang['Click_return_disallowadmin'] = 'Klicke %shier%s, um zum Verbot der Benutzernamen zur�ckzukehren';


//
// Styles Admin
//
$lang['Styles_admin'] = 'Styles Administration';
$lang['Styles_explain'] = 'Hier kannst du Styles (Templates und Themes) hinzuf�gen, l�schen und �berwachen.';
$lang['Styles_addnew_explain'] = 'In der folgenden Liste sind alle f�r dieses Template verf�gbaren Themes aufgef�hrt. Die in der Liste aufgef�hrten Objekte wurden der Datenbank noch nicht zugef�gt. Um ein Theme zu installieren, klicke einfach auf den Installieren-Link neben einem Eintrag';

$lang['Select_template'] = 'W�hle ein Template';

$lang['Style'] = 'Style';
$lang['Template'] = 'Template';
$lang['Install'] = 'Installieren';
$lang['Download'] = 'Runterladen';

$lang['Edit_theme'] = 'Theme editieren';
$lang['Edit_theme_explain'] = 'Hier kannst du die Einstellungen f�r das gew�hlte Theme �ndern';

$lang['Create_theme'] = 'Theme erstellen';
$lang['Create_theme_explain'] = 'Hier kannst du ein neues Theme f�r das gew�hlte Template erstellen. Wenn du Farben eingibst (f�r die du Hexdezimalzahlen nutzen solltest), darfst du das # nicht mit angeben - CCCCCC ist z. B. korrekt, #CCCCCC nicht';

$lang['Export_themes'] = 'Theme exportieren';
$lang['Export_explain'] = 'Hier kannst du die Themedaten f�r ein bestimmtes Template exportieren. W�hle das Template aus der unteren Liste und das Script wird die Themekonfigurationsdatei erstellen und versuchen, sie in den Templatesordner zu speichern. Falls es die Datei nicht selbst speichern kann, kannst du sie runterladen. Um dem Skript das Schreiben der Datei zu erm�glichen, musst du dem gew�hlten Templateordner Schreibrechte gew�hren. F�r weitere Informationen siehe den phpBB2 Benutzerguide.';

$lang['Theme_installed'] = 'Das gew�hlte Theme wurde installiert';
$lang['Style_removed'] = 'Der gew�hlte Style wurde aus der Datenbank entfernt. Um den Style v�llig vom System zu entfernen, musst du es aus deinem Templates-Ordner l�schen.';
$lang['Theme_info_saved'] = 'Die Themeinformationen f�r das gew�hlte Template wurden gespeichert. Du solltest jetzt die Dateizugriffsrechte der theme_info.cfg (und eventueller Verzeichnisse) auf Nur-Lesen zur�ck stellen';
$lang['Theme_updated'] = 'Das gew�hlte Theme wurde aktualisiert. Du solltest die neuen Themeeinstellungen jetzt exportieren.';
$lang['Theme_created'] = 'Theme erstellt. Du solltest das Theme jetzt in die Themekonfiguration exportieren, damit es nicht verloren geht oder du es an anderer Stelle einsetzen kannst.';

$lang['Confirm_delete_style'] = 'Diesen Style wirklich l�schen?';

$lang['Download_theme_cfg'] = 'Der Exporter konnte nicht in die Themeinformationsdatei schreiben. Klicke auf den unteren Knopf, um die Datei per Browser runterzuladen. Hast du sie runtergeladen, kannst du die Datei in deinen Ordner mit den Templatedateien kopieren. Schlie�lich kannst du die Dateien zu einem Paket zusammenschlie�en.';
$lang['No_themes'] = 'Das gew�hlte Template hat keine verf�gbaren Themes. Um ein neues Theme zu erstellen, klicke auf den Theme-erstellen-Link auf der linken Seite';
$lang['No_template_dir'] = 'Konnte das Template-Verzeichnis nicht �ffnen. Es ist eventuell nicht lesbar oder existiert nicht (mehr).';
$lang['Cannot_remove_style'] = 'Du kannst den gew�hlten Style nicht entfernen, da er zum Forumsstandard geh�rt. Du kannst jedoch einen anderen Forumsstandard w�hlen und es erneut versuchen.';
$lang['Style_exists'] = 'Der gew�hlte Stylename existiert bereits, bitte gehe zur�ck und w�hle einen anderen Namen.';

$lang['Click_return_styleadmin'] = 'Klicke %shier%s, um zur Styles Administration zur�ckzukehren';

$lang['Theme_settings'] = 'Theme Einstellungen';
$lang['Theme_element'] = 'Theme Element';
$lang['Simple_name'] = 'Einfacher Name';
$lang['Value'] = 'Wert';
$lang['Save_Settings'] = 'Einstellungen �bernehmen';

$lang['Stylesheet'] = 'CSS-Stylesheet';
$lang['Stylesheet_explain'] = 'Dateiname des CSS-Stylesheets f�r dieses Theme.';
$lang['Background_image'] = 'Hintergrundbild';
$lang['Background_color'] = 'Hintergrundfarbe';
$lang['Theme_name'] = 'Themename';
$lang['Text_color'] = 'Textfarbe';
$lang['Link_color'] = 'Linkfarbe';
$lang['VLink_color'] = 'Farbe f�r gesehener Link';
$lang['ALink_color'] = 'Farbe f�r aktiver Link';
$lang['HLink_color'] = 'Farbe f�r gew�hlter Link';
$lang['Tr_color1'] = 'Farbe f�r Tabellenreihe 1';
$lang['Tr_color2'] = 'Farbe f�r Tabellenreihe 2';
$lang['Tr_color3'] = 'Farbe f�r Tabellenreihe 3';
$lang['Tr_class1'] = 'Tabellenreihe Klasse 1';
$lang['Tr_class2'] = 'Tabellenreihe Klasse 2';
$lang['Tr_class3'] = 'Tabellenreihe Klasse 3';
$lang['Th_color1'] = 'Farbe f�r Tabellenkopf 1';
$lang['Th_color2'] = 'Farbe f�r Tabellenkopf 2';
$lang['Th_color3'] = 'Farbe f�r Tabellenkopf 3';
$lang['Th_class1'] = 'Tabellenkopf Klasse 1';
$lang['Th_class2'] = 'Tabellenkopf Klasse 2';
$lang['Th_class3'] = 'Tabellenkopf Klasse 3';
$lang['Td_color1'] = 'Farbe f�r Tabellenzelle 1';
$lang['Td_color2'] = 'Farbe f�r Tabellenzelle 2';
$lang['Td_color3'] = 'Farbe f�r Tabellenzelle 3';
$lang['Td_class1'] = 'Tabellenzelle Klasse 1';
$lang['Td_class2'] = 'Tabellenzelle Klasse 2';
$lang['Td_class3'] = 'Tabellenzelle Klasse 3';
$lang['fontface1'] = 'Schriftart 1';
$lang['fontface2'] = 'Schriftart 2';
$lang['fontface3'] = 'Schriftart 3';
$lang['fontsize1'] = 'Schriftgr��e 1';
$lang['fontsize2'] = 'Schriftgr��e 2';
$lang['fontsize3'] = 'Schriftgr��e 3';
$lang['fontcolor1'] = 'Schriftfarbe 1';
$lang['fontcolor2'] = 'Schriftfarbe 2';
$lang['fontcolor3'] = 'Schriftfarbe 3';
$lang['span_class1'] = 'Abstand Klasse 1';
$lang['span_class2'] = 'Abstand Klasse 2';
$lang['span_class3'] = 'Abstand Klasse 3';
$lang['img_poll_size'] = 'Umfragen-Symbolgr��e [px]';
$lang['img_pm_size'] = 'Private Nachrichten-Statussymbolgr��e [px]';


//
// Install Process
//
$lang['Welcome_install'] = 'Willkommen bei der phpBB2-Installation';
$lang['Initial_config'] = 'Grundeinstellungen';
$lang['DB_config'] = 'Datenbankkonfiguration';
$lang['Admin_config'] = 'Administratorkonfiguration';
$lang['continue_upgrade'] = 'Sobald du die Konfigurationsdatei auf deinen Rechner heruntergeladen hast, kannst du die Schaltfl�che \'Upgrade fortsetzen\' bet�tigen, um mit dem Upgrade-Prozess fortzufahren. Bitte warte mit dem Hochladen der Konfigurationsdatei, bis der Upgrade-Prozess beendet ist.';
$lang['upgrade_submit'] = 'Upgrade fortsetzen';

$lang['Installer_Error'] = 'W�hrend der Installation trat ein Fehler auf';
$lang['Previous_Install'] = 'Eine vorherige Installation wurde entdeckt';
$lang['Install_db_error'] = 'Beim Update der Datenbank trat ein Fehler auf';

$lang['Re_install'] = 'Deine vorherige Installation ist noch aktiv.<br /><br />Falls du phpBB2 neu installieren m�chtest, aktiviere den unten stehenden Ja-Knopf. Beachte jedoch, dass dieser Vorgang s�mtliche existierenden Daten zerst�ren wird und keine Sicherungen vorgenommen werden. Der Administrator-Benutzername und das Passwort, das du benutzt hast, um dich im Board einzuloggen, werden nach der Neuinstallation erneut erstellt. Es bleiben sonst keine Einstellungen zur�ck.<br /><br />�berlege es dir gut, bevor du auf Ja klickst.';

$lang['Inst_Step_0'] = 'Danke, dass du dich f�r phpBB 2 entschieden hast. Um die Installation abzuschlie�en, gib bitte die unten geforderten Daten ein. Beachte, dass die Datenbank, in welche du installierst, bereits vorhanden sein sollte. Solltest du in eine Datenbank installieren, die ODBC nutzt, z. B. MS Access, solltest du erst einen DSN f�r sie erstellen, bevor du fortf�hrst.';

$lang['Start_Install'] = 'Installation beginnen';
$lang['Finish_Install'] = 'Installation abschlie�en';

$lang['Default_lang'] = 'Standardsprache';
$lang['DB_Host'] = 'Datenbank: Host / DSN';
$lang['DB_Name'] = 'Name der Datenbank';
$lang['DB_Username'] = 'Datenbank Benutzername';
$lang['DB_Password'] = 'Datenbank Passwort';
$lang['Database'] = 'Deine Datenbank';
$lang['Install_lang'] = 'W�hle Sprache f�r die Installation';
$lang['dbms'] = 'Datenbanktyp';
$lang['Table_Prefix'] = 'Prefix f�r die Tabellen in der Datenbank';
$lang['Admin_Username'] = 'Administrator Benutzername';
$lang['Admin_Password'] = 'Administrator Passwort';
$lang['Admin_Password_confirm'] = 'Administrator Passwort [ Best�tigung ]';

$lang['Inst_Step_2'] = 'Dein Administrator-Benutzername wurde erstellt. Deine Installation ist nun komplett. Du wirst jetzt auf eine Seite gef�hrt, wo du dein neues Board deinen Bed�rfnissen anpassen kannst. �berpr�fe am besten gleich die allgemeine Konfiguration und nimm eventuell n�tige �nderungen vor. Danke, dass du dich f�r phpBB 2 entschieden hast.';

$lang['Unwriteable_config'] = 'Momentan ist deine Konfigurationsdatei nicht beschreibbar. Du kannst dir eine Kopie der Datei runterladen, wenn du auf den entsprechenden Link unten klickst. Du solltest diese Datei ins selbe Verzeichnis wie phpBB2 hochladen. Wenn dies getan ist, solltest du dich mit deinem Administrator-Benutzernamen und Passwort, die du auf der letzten Seite angegeben hast, einloggen und den Administrationsbereich betreten, um die allgemeinen Einstellungen zu pr�fen. Einen entsprechenden Link findest du am Ende jeder Seite deines Forums. Danke, dass du dich f�r phpBB 2 entschieden hast.';
$lang['Download_config'] = 'Konfigurationsdatei herunterladen';

$lang['ftp_choose'] = 'W�hle Downloadmethode';
$lang['ftp_option'] = '<br />Da FTP Erweiterungen in dieser Version von PHP aktiviert sind, k�nntest du die M�glichkeit haben, die Konfigurationsdatei automatisch per FTP vor Ort zu �ndern.';
$lang['ftp_instructs'] = 'Du hast dich dazu entschieden, die Datei automatisch und vor Ort zu �ndern. Bitte gib die unten geforderten Informationen an, um den Prozess zu starten. Beachte, dass der FTP-Pfad der exakte Pfad zu deinem phpBB2-Ordner sein muss.';
$lang['ftp_info'] = 'Eingabe der FTP Informationen';
$lang['Attempt_ftp'] = 'Die Konfigurationsdatei vor Ort umschreiben';
$lang['Send_file'] = 'Ich m�chte, dass mir die Datei geschickt wird. Ich werde sie manuell hochladen.';
$lang['ftp_path'] = 'FTP-Pfad zum phpBB2';
$lang['ftp_username'] = 'Dein FTP Benutzername';
$lang['ftp_password'] = 'Dein FTP Passwort';
$lang['Transfer_config'] = 'Transfer beginnen';
$lang['NoFTP_config'] = 'Der Versuch, die Konfigurationsdatei vor Ort umzuschreiben, scheiterte. Bitte lade die Datei runter und lade sie manuell hoch.';

$lang['Install'] = 'Installation';
$lang['Upgrade'] = 'Upgrade';


$lang['Install_Method'] = 'W�hle eine Installations-Methode aus';

$lang['Install_No_Ext'] = 'Die PHP-Konfiguration auf deinem Server unterst�tzt nicht den gew�hlten Datenbank-Typ';

$lang['Install_No_PCRE'] = 'phpBB 2 ben�tigt das Perl-Compatible Regular Expressions Module f�r PHP, was von deiner PHP-Konfiguration anscheinend nicht unterst�tzt wird.';

//
// Version Check
//
$lang['Version_up_to_date'] = 'Dein Forum ist auf dem neuesten Stand; es sind keine Updates f�r deine phpBB-Version verf�gbar.';
$lang['Version_not_up_to_date'] = 'Dein Forum ist wahrscheinlich <b>nicht</b> auf dem neuesten Stand. Es sind Updates f�r deine phpBB-Version verf�gbar, bitte besuche <a href="http://www.phpbb.com/downloads.php" target="_new">http://www.phpbb.com/downloads.php</a>, um die aktuellste Version zu erhalten.';
$lang['Latest_version_info'] = 'Die neueste verf�gbare Version ist <b>phpBB %s</b>.';
$lang['Current_version_info'] = 'Du verwendest <b>phpBB %s</b>.';
$lang['Connect_socket_error'] = 'Die Verbindung zum phpBB-Server konnte nicht aufgebaut werden. Es trat folgender Fehler auf:<br />%s';
$lang['Socket_functions_disabled'] = 'Die Socket-Funktionen konnten nicht benutzt werden.';
$lang['Mailing_list_subscribe_reminder'] = 'Um immer die neuesten Informationen zu Updates von phpBB zu erhalten, solltest du dich <a href="http://www.phpbb.com/support/" target="_new">beim phpBB Newsletter anmelden</a>.';
$lang['Version_information'] = 'Versions-Information';

//
// Login attempts configuration
//
$lang['Max_login_attempts'] = 'Erlaubte Login-Versuche';
$lang['Max_login_attempts_explain'] = 'Anzahl der erlaubten Login-Versuche, bevor ein Benutzerkonto f�r eine festgelegte Zeit gesperrt wird.';
$lang['Login_reset_time'] = 'Login-Sperrzeit';
$lang['Login_reset_time_explain'] = 'Minuten, die ein Benutzer nach einer �berschreitung der erlaubten Login-Versuche warten muss, bis er sich wieder anmelden kann.';

// That's all Folks!
// Na Gott sei Dank!
// -------------------------------------------------

?>