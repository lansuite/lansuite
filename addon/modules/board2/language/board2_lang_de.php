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

 $lang['board2']['language_name']	= "Deutsch";
 $lang['board2']['language_name_english']	= "German";
 $lang['board2']['version']	= "0.5";					// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['board2']['lastchange']	= "26. März 2006";	                // String, must not have a strict format
 $lang['board2']['translator']	= "Piri";			// String, the Realname of the translator
 $lang['board2']['headline']	= "Board";
 $lang['board2']['not_configured'] = "Das Board wurde noch nicht konfiguriert, bitte wenden sie sich an den Webmaster";
 $lang['board2']['noaccess'] = 'Sie haben hier keinen Zugriff';
 
 $lang['board2']['install']['subheadline'] = "Installation von phpBB";
 $lang['board2']['install']['text']	= "Beachte die folgenden Punkte bei der Installation von phpBB!:";
 $lang['board2']['install']['overwriteAdmin']	= "Wird mit den Daten des Admins von lansuite überschrieben.";
 $lang['board2']['install']['completeFormCorrectly']	= "Die restlichen Felder je nach Server korrekt ausfuellen.";
 $lang['board2']['install']['installphpBB']	= "Zum Installieren von phpBB hier klicken";
 $lang['board2']['install']['integration']	= "Wenn die Installation beendet ist müssen noch die Ordner <b>install/</b> und <b>contrib/</b> im Verzeichnis <b>@path</b> gelöscht werden.<br><br> Im nächsten Schritt wird phpBB in lansuite integriert.";
 $lang['board2']['install']['version']	= "Version von phpBB";

 $lang['board2']['index']['subheadline']	= "Board öffnen";

 $lang['board2']['installorinte']['subheadline']	= "Installieren oder Integrieren?";
 $lang['board2']['installorinte']['attention']	= "<b>ACHTUNG</b> Bitte erstellen sie auf jeden Fall von der Lansuite Datenbank und von der phpBB Datenbank(wenn sie ein bestehndes Forum integrieren) ein <b>Backup</b>, da im <b>Fehlerfall die Datenbank inkorrekt</b> werden kann!!";
 $lang['board2']['installorinte']['instOrInte']	= "Wollen Sie ein phpBB Forum neu Aufsetzen und in Lansuite integrieren oder wollen Sie ein bestehendes phpBB Forum in Lansuite integrieren?";
 $lang['board2']['installorinte']['install']	= "Aufsetzen eines neuen phpBB Forums";
 $lang['board2']['installorinte']['integrate']	= "Integrieren eines bestehenden phpBB Forums";
 
 $lang['board2']['test']['subheadline']	= "Möglichkeit testen";
 $lang['board2']['test']['doubleUser']	= "Die folgenden Usernamen kommen in phpBB doppelt vor, es darf jedoch kein Username doppelt vorkommen:";
 $lang['board2']['test']['noProblems']	= "Alles in Ordnung, phpBB kann integriert werden.";
 
 $lang['board2']['integration']['subheadline']	= "Integration von phpBB in lansuite";
 $lang['board2']['integration']['successfully']	= "phpBB wurde in lansuite integriert.";
 $lang['board2']['integration']['wrongVersion']	= "Für diese Version wurde noch keine Integration geschrieben. <br>Bei kleineren Updates kann u.U. eine Vorgänger- oder eine Nachfolgerversion verwendet werden.";
 $lang['board2']['integration']['bug']	= "Es ist ein unbekannter Fehler aufgetreten.";
 
 $lang['board2']['integrateonly']['subheadline']	= "Integrationseinstellungen";
 $lang['board2']['integrateonly']['prefix']	= "Prefix der Datenbanktabellen des phpBB Forum?";
 $lang['board2']['integrateonly']['path']	= "Pfad des phpBB Forums?";
 $lang['board2']['integrateonly']['user2user']	= "Sie können hier jedem <b>phpBB User einen Lansuite User zuordnen</b>, indem sie einfach für jeden phpBB User einen Lansuite User auswählen.<br>Sie können einen <b>phpBB User in Lansuite neu anlegen</b>, indem sie die Liste auf leer stellen";
 $lang['board2']['integrateonly']['boardRegister']	= "Sollen die Account Aktivierung am phpBB Board mittels Admin-Mail aktiviert werden?";
 $lang['board2']['integrateonly']['error']	= "Es wurde ein phpBB User einem Lansuite User doppelt zugewiesen!";
 
 $lang['board2']['disintegrate']['question'] = 'Wollen sie phpBB und Lansuite wirklich wieder trennen, wenn ja <b>Bitte Datenbank sichern!</br>';
 $lang['board2']['disintegrate']['successfully'] = 'phpBB wurde erfolgreich von lansuite getrennt.';
 
 $lang['board2']['update']['subheadline']	= "Updaten von phpBB";
 $lang['board2']['update']['follow'] = 'Wenn sie phpBB Updaten müssen bitte befolgen sie folgende Schritte:';
 $lang['board2']['update']['step1'] = '1. <b>deintetgrieren</b> sie phpBB von lansuite';
 $lang['board2']['update']['step2'] = '2. update phpBB';
 $lang['board2']['update']['step3'] = '3. <b>integrieren</b> sie phpBB wieder in lansuite';
 
 //
 // Please make a history at the end of file of your changes !!
 //

 /* HISTORY
 * 17. 2. 2006 : First adaption of the sample-module language file to the board2 language file.
 * 18. 2. 2005 : Some phrases where added.
 * 19. 2. 2005 : Some phrases where added.
 * 26. 3. 2005 : Some phrases where added.
 */
?>