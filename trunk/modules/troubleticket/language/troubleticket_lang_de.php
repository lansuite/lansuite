<?php

 //Default
 $lang['troubleticket']['language_name'] = 'Deutsch';
 $lang['troubleticket']['version']       = 2;
 $lang['troubleticket']['lastchange']    = '16. Oktober 2005';
 $lang['troubleticket']['translator']    = 'Cewigo (Jan)';
 $lang['troubleticket']['modulname']     = 'troubleticket';     //necessary

 //Contend
 $lang['troubleticket']['headline']       = 'Troubleticket hinzufügen';
 $lang['troubleticket']['headl_edit']     = 'Troubleticket bearbeiten';
 $lang['troubleticket']['show']           = 'Troubleticket anzeigen';
 $lang['troubleticket']['description']    = 'Beschreibung';
 $lang['troubleticket']['priority']       = 'Priorität';
 $lang['troubleticket']['head']           = 'Überschrift';
 $lang['troubleticket']['prob_descr']     = 'Problembeschreibung';
 $lang['troubleticket']['set_up_on']      = 'Eingetragen am/um';
 $lang['troubleticket']['from_user']      = 'Von Benutzer';
 $lang['troubleticket']['assign_orga']    = 'Bearbeitender Orga';
 $lang['troubleticket']['visible_4all']   = 'Sichtbar für Alle';
 $lang['troubleticket']['visible_4orga']  = 'Sichtbar nur für Orgas';
 $lang['troubleticket']['no_contend']     = 'Keine weiteren Informationen angegeben.';
 $lang['troubleticket']['add_confirm']    = 'Das Troubleticket wurde erfolgreich eingetragen';
 $lang['troubleticket']['change_confirm'] = 'Das Troubleticket wurde erfolgreich geändert';
 $lang['troubleticket']['unlink_confirm'] = 'Das ausgewählte Ticket wurde gelöscht.';
 $lang['troubleticket']['user_assign']    = 'Ihnen wurde das Troubleticket "<b>%TTCaption%</b>"zugewiesen. '; //endign with dot, followed by a space
 $lang['troubleticket']['assign_confirm'] = 'Das ausgewählte Ticket wurde dem Orga zugewiesen.';
 $lang['troubleticket']['show_info']      = 'Hier sehen Sie alle Informationen zu diesem Ticket';
 $lang['troubleticket']['no_hint']        = ' Kein Hinweis eingetragen';	//starts with a space
 $lang['troubleticket']['subline']        = ' Mit diesem Formular können Sie ein Troubleticket hinzufügen, falls Sie ein Problem haben'; //starts with a space

 //Status
 $lang['troubleticket']['status']         = 'Ticketstatus';
 $lang['troubleticket']['status_choose']  = 'Status auswählen';
 $lang['troubleticket']['st_default']     = 'default: Scriptfehler!';
 $lang['troubleticket']['st_new']         = 'Neu / Ungeprüft';
 $lang['troubleticket']['st_acc']         = 'Überprüft / Akzeptiert';
 $lang['troubleticket']['st_checked']     = 'Überprüft am/um';
 $lang['troubleticket']['st_in_work']       = 'In Arbeit';
 $lang['troubleticket']['st_in_work_since'] = 'In Bearbeitung seit';
 $lang['troubleticket']['st_finished']      = 'Abgeschlossen';
 $lang['troubleticket']['st_finish_since']  = 'Beendet am/um';
 $lang['troubleticket']['st_denied']        = 'Abgelehnt';
 $lang['troubleticket']['st_denied_since']  = 'Bearbeitung abgelehnt am/um';

 //Comment
 $lang['troubleticket']['com']			   = 'Kommentar';
 $lang['troubleticket']['com_4user']        = 'Kommentar für Benutzer';
 $lang['troubleticket']['com_4orga']        = 'Kommentar für Orgas';
 $lang['troubleticket']['com_fa4orga']      = 'Kommentar von und für Orgas';

 //Search
 $lang['troubleticket']['ms_search_ticket'] = 'Ticket suchen';
 $lang['troubleticket']['ms_ticket_result'] = 'Ticket: Ergebnis';
 $lang['troubleticket']['ms_search_user']   = 'Benutzer suchen';
 $lang['troubleticket']['ms_user_result']   = 'Benutzer: Ergebnis';

 //Change
 $lang['troubleticket']['option_nochange']  = ' Keine Änderung ';   // all options starts and ends with a space
 $lang['troubleticket']['option_back2poll'] = ' Problem nicht übernehmen und zurückgeben ';
 $lang['troubleticket']['option_startwork'] = ' Problem übernehmen und Bearbeitung beginnen ';
 $lang['troubleticket']['option_reopen']	   = ' Problem nochmal aufgreifen und bearbeiten ';  // this option is currently not in use
 $lang['troubleticket']['option_finished']  = ' Auf Erledigt setzen ';
 $lang['troubleticket']['option_refuse']    = ' Bearbeitung ablehnen ';

 //States
 $lang['troubleticket']['state_0'] = 'Niedrig';
 $lang['troubleticket']['state_1'] = 'Normal';
 $lang['troubleticket']['state_2'] = 'Hoch';
 $lang['troubleticket']['state_3'] = 'Kritisch';

 
 //Categories
 $lang['troubleticket']['no_cat'] 	= 'Bitte Auswählen';
 $lang['troubleticket']['cat'] 		= 'Kategorie';
 $lang['troubleticket']['cat_empty']= 'Keine Kategorien vorhanden';
 $lang['troubleticket']['cat_user'] = 'Zuständiger Admin';
 $lang['troubleticket']['cat_nouser'] = 'Kein zuständiger Admin';
 $lang['troubleticket']['cat_err_name'] = 'Name zu lang oder leer';
 $lang['troubleticket']['cat_ok']		= 'Kategorie erfolgreich hinzugefügt/geändert';
 $lang['troubleticket']['cat_err']		= 'Kategorie konnte nicht hinzugefügt/geändert werden';
 $lang['troubleticket']['cat_no_err']		= 'Sie haben keine Kategorie zum ändern ausgewählt';
 
 //Questions
 $lang['troubleticket']['q_unlink'] = 'Wollen Sie das ausgewählte Troubleticket wirklich löschen?';

 //Errors
 $lang['troubleticket']['err_max_size']  = 'Der Text darf nicht mehr als 5000 Zeichen enthalten';
 $lang['troubleticket']['err_no_head']   = 'Bitte geben Sie eine kurze Beschreibung / Überschrift ein';
 $lang['troubleticket']['err_assign']    = 'Das Troubleticket konnte nicht zugewiesen werden! Problem mit der Datenbank !';
 $lang['troubleticket']['err_no_reason'] = 'Bei einer direkten Ablehnung ist die Angabe eines Grundes notwendig.';
 $lang['troubleticket']['err_no_tt_id']  = 'Es wurde keine Troubleticket-ID übergeben. Aufruf inkorrekt.';
 $lang['troubleticket']['err_unlink']    = 'Das Troubleticket konnte nicht gelöscht werden! Problem mit der Datenbank!';
 $lang['troubleticket']['err_no_cat']    = 'Bitte wählen Sie eine Kategorie';
 
?>
