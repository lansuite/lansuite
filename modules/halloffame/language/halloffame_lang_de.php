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


 $lang['halloffame']['language_name'] = 'Deutsch';
 $lang['halloffame']['version'] = 1;					// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['halloffame']['lastchange'] = '18. Mai 2004';	                // String, must not have a strict format
 $lang['halloffame']['translator'] = 'Denny';			// String, the Realname of the translator


 $lang['halloffame']['headline']	= '&Uuml;berschrift';
 $lang['halloffame']['subheadline'] = 'Erweiterte &Uuml;berschrift';
 $lang['halloffame']['single_row']	= 'Eine einfache Zeile';
 $lang['halloffame']['name']	= 'Texteingabe';
 $lang['halloffame']['user_insg']	= 'Benutzer insgesamt';


 //
 // Please make a history at the end of file of your changes !!
 //



 /* HISTORY
 *
 *
 */

?>