<?php

 //
 // the structure from an (english) language file of a module
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


 $lang['sample']['language_name'] = 'English';
 $lang['sample']['version'] = 1;					// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['sample']['lastchange'] = '14. June 2005';	// String, must not have a strict format
 $lang['sample']['translator'] = 'Jochen';			// String, the Realname of the translator


 $lang['sample']['headline']	= 'Headline';
 $lang['sample']['subheadline'] = 'Additional headline';
 $lang['sample']['single_row']	= 'A plain row';
 $lang['sample']['name']	= 'Textinput';
 $lang['sample']['user_insg']	= 'Overall users';


 //
 // Please make a history at the end of file of your changes !!
 //



 /* HISTORY
 *
 *
 */

?>
