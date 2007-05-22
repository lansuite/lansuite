<?php

 if( !VALID_LS ) { die("Direct access not allowed!"); } // Direct-Call-Check

 // The next 4 tags must be in every modul-language file exist !

 // Specifications
 $lang['beamer']['language_name'] 	= "Deutsch";
 $lang['beamer']['version'] 		= 1;			// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['beamer']['lastchange'] 		= "15.05.2007";	// String, must not have a strict format
 $lang['beamer']['translator'] 		= "Denny Mleinek";		// String, the Realname of the translator

 $lang['beamer']['beamer']		= "Beamer&uuml;bersicht";
 $lang['beamer']['beamerstart']		= "Beamerinhalte pr&auml;sentieren";

 $lang['beamer']['viewcontent']		= "Beamerfenster ";
 $lang['beamer']['listcontent']		= "Auflistung der Inhalte";
 $lang['beamer']['newcontent']      = "Inhalte hinzuf&uuml;gen";
 $lang['beamer']['editcontent']     = "Inhalt bearbeiten";

 $lang['beamer']['viewModulMainPage_text'] = "Das Modul arbeitet derzeit nur mit dem Template 'simple' und 'beamer' zusammen. F&uuml;r eine schnelle L&ouml;sung erstellen Sie einen zus&auml;tzlichen Account der das Beamer-Template verwendet. Damit haben Sie die besten Ergebnisse im Fullscreen Mode. ".
 											 "<p/>Damit es mit jedem anderen Template funktioniert, m&uuml;ssen Sie in ihrem Template im Bereich der Meta-Angaben folgende Codezeilen hinzuf&uuml;gen:<p/> if( \$_GET['sitereload'] ) { echo ... (Restlichen Anweisungsblock bitte as der Design-index.php entnehmen.)  } ";

 
 $lang['beamer']['introtext']	= "Mit diesem Modul k&ouml;nnen Sie Texte und anderen Daten f&uuml;r eine Beamerpr&auml;sentation aufbereiten.";

 $lang['beamer']['activecontent'] = "Aktive Inhalte: ";
 $lang['beamer']['totalcontent'] =  "Inhalte gesamt: ";

 
 /* HISTORY
 *
 *
 */

?>
