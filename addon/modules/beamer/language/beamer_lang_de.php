<?php

 //
 // the structure from a (german) language file of a module
 //
 // strict name rule: <modulname>_lang_<language-tag>.php
 //
 // this file will be load in index_modules.php, later from a ModulManager
 // curently add a include_once bevor the modindex_???.php is included
 //
 //
 // the global language array is named: $lang
 // 
 // at the first comes the modulname (dir name = modulname !!)
 // at second comes the induvidual tag for a translation

 // The next 4 tags must be in every modul-language file exist !

 // Specifications
 $lang['beamer']['language_name'] 	= "Deutsch";
 $lang['beamer']['version'] 		= 1;			// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['beamer']['lastchange'] 		= "18. Mai 2004";	// String, must not have a strict format
 $lang['beamer']['translator'] 		= "MeisterM";		// String, the Realname of the translator

 $lang['beamer']['beamer']		= "Beamer";
 $lang['beamer']['usertxt']		= "Text auf die Leinwand";

 $lang['beamer']['usermsg']		= "Deine Nachricht";
 $lang['beamer']['msgtyp']		= "Nachrichtentyp";

 $lang['beamer']['sub']['admin']		= "Beamer verwalten";
 $lang['beamer']['sub']['start']		= "Beamer starten";
 $lang['beamer']['sub']['inhalt']		= "Inhalt bearbeiten";
 $lang['beamer']['sub']['inhalt_sort']		= "Inhalt sortieren";
 $lang['beamer']['sub']['usertxt']		= "Hier könnt ihr einen eigenen Text auf die Leinwand werfen";

 $lang['beamer']['descr']['admin']		= "Hier können die vorhandenen Beamer in der Halle verwaltet werden.";
 $lang['beamer']['descr']['start']		= "Wählen Sie einen Beamer aus der Liste und klicken Sie auf 'Öffnen' um ihn zu starten.";
 $lang['beamer']['descr']['inhalt']		= "Bearbeiten Sie hier die Inhalte die auf Ihrem Beamer angezeigt werden sollen.";
 $lang['beamer']['descr']['delmsg']		= "Löschen Sie hier Beamer-Nachrichten von den Usern.";
 $lang['beamer']['descr']['sort']		= "Hier können Sie die Reihenfolge der Inhalte festsetzen.";

 $lang['beamer']['user_msg']		= "Hier kann deine Nachricht stehen - Wie? Das steht im Intranet";

 $lang['beamer']['search']['active'] = "Aktiv";
 $lang['beamer']['search']['yes'] = "Ja";
 $lang['beamer']['search']['no'] = "Nein";
 $lang['beamer']['search']['loop'] = "Endlos";
 $lang['beamer']['search']['once'] = "Einmalig";
 $lang['beamer']['search']['all'] = "Alle";
 $lang['beamer']['search']['loop'] = "Endlos";
 $lang['beamer']['search']['beamer'] = "Beamer";
 $lang['beamer']['search']['caption'] = "Bezeichnung";
 $lang['beamer']['search']['sortkey'] = "SortKey";
 $lang['beamer']['search']['ersteller'] = "Ersteller";
 $lang['beamer']['search']['aktiv'] = "Aktiv";
 $lang['beamer']['search']['wiederholungen'] = "Wdh";
 $lang['beamer']['search']['text'] = "Text";
 $lang['beamer']['search']['typ'] = "Typ";
 $lang['beamer']['search']['user'] = "Benutzer";
 $lang['beamer']['search']['uhrzeit'] = "Uhrzeit";

$lang['beamer']['error']['not_active'] = "Diese Funktion ist für diesen Beamer nicht aktiviert.";
$lang['beamer']['error']['n_a'] = "Es sind keine Beamer verfügbar.";
$lang['beamer']['error']['blacklist'] = "Du bist auf der Blacklist und darfst daher keine Nachrichten schreiben.";
$lang['beamer']['conf']['usermsg'] = "Deine Nachricht wurde in die Warteschlange gestellt und wird bald auf dem Beamer verfügbar sein";
$lang['beamer']['conf']['usermsg_del'] = "Die Nachricht wurde gelöscht";
$lang['beamer']['conf']['blacklist_add'] = "Der Benutzer wurde auf die Blacklist gesetzt - Er kann nun keine Nachrichten mehr schreiben!";
$lang['beamer']['conf']['blacklist_already'] = "Dieser Benutzer ist bereits auf der Blacklist";
$lang['beamer']['conf']['blacklist_no'] = "Dieser Benutzer befindet sich nicht auf der Blacklist";
$lang['beamer']['conf']['blacklist_del'] = "Der Benutzer wurde von der Blacklist gelöscht";
 /* HISTORY
 *
 *
 */

?>
