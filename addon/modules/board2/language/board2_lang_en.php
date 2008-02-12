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
 


 $lang['board2']['language_name']	= "English";
 $lang['board2']['language_name_english']	= "English";
 $lang['board2']['version']	= "0.5";					// Integer !, Increment this Counter after you add, delete or rename of tags
 $lang['board2']['lastchange']	= "18. Februar 2006";	                // String, must not have a strict format
 $lang['board2']['translator']	= "Piri";			// String, the Realname of the translator
 $lang['board2']['headline']	= "Board";
 $lang['board2']['not_configured'] = "The Board hasn't been configured, please tell your webmaster'";
 
 $lang['board2']['index']['subheadline']	= "Board open";
 
 $lang['board2']['installorinte']['subheadline']	= "Installation or Integration?";
 $lang['board2']['installorinte']['attention']	= "<b>Attention</b> Please create a <b>Backup</b> of your Lansuite Database and of your phpBB Database (if you are integrating an existing phpBB Forum), because the whole <b>database my be inncorrect</b> if an error occures!!";
 $lang['board2']['installorinte']['instOrInte']	= "Do you want to setup a new phpBB Board and integrate it in lansuite or do you want to integrate an existing phpBB Board in lansuite?";
 $lang['board2']['installorinte']['install']	= "Setup a new phpBB Board";
 $lang['board2']['installorinte']['integrate']	= "Integrate an existing Board";
 
 $lang['board2']['test']['subheadline']	= "Test the posibility";
 $lang['board2']['test']['doubleUser']	= "The following usernames exists twice in phpBB, in Lansuite no username can exist twice:";
 $lang['board2']['test']['noProblems']	= "Everything fine, phpBB can be integrated.";
 
 $lang['board2']['install']['subheadline']	= "Installation of phpBB";
 $lang['board2']['install']['text']	= "Attentd to the following points at the installation of phpBB!:";
 $lang['board2']['install']['overwriteAdmin']	= "Will be overwritten with the data of lansuite.";
 $lang['board2']['install']['completeFormCorrectly']	= "The remaning fields of the form are depending on your server.";
 $lang['board2']['install']['installphpBB']	= "Click here to install phpBB.";
 $lang['board2']['install']['integration']	= "The Folders <b>install/</b> und <b>contrib/</b> in the dictionary <b>@path</b> must be deleted after the installation.<br> The next step is to integrate phpBB in lansuite.";
 $lang['board2']['install']['version']	= "Version of phpBB";
 
 $lang['board2']['integration']['subheadline']	= "Integration of phpBB in lansuite";
 $lang['board2']['integration']['successfully']	= "phpBB is now integrated in lansuite.";
 $lang['board2']['integration']['wrongVersion']	= "For this Version of phpBB is no integration available <br> Try the forerunner- or the followerversion.";
 $lang['board2']['integration']['bug']	= "An unknown bug occured.";
 
 $lang['board2']['integrateonly']['subheadline']	= "Integration Options";
 $lang['board2']['integrateonly']['prefix']	= "Prefix of the phpBB Board?";
 $lang['board2']['integrateonly']['path']	= "Path of the phpBB Board?";
 $lang['board2']['integrateonly']['username']	= "Username";
 $lang['board2']['integrateonly']['email']	= "Email";
 $lang['board2']['integrateonly']['new']	= "Add as new to lansuite";
 $lang['board2']['integrateonly']['boardRegister']	= "Should the Account Activation on the phpbb Board per Admin get activated?";
 $lang['board2']['integrateonly']['error']	= "A phpBB User double assigned to an Lansuite User!";
 $lang['board2']['integrateonly']['user2user']	= "Now you can <b>connect a phpBB User with an Lansuite User</b>, by selecting for an phpBB User an Lansuite User. <br>You also can create a phpBB User in Lansuite new</b>, by selecting the empty row in the list";

 //
 // Please make a history at the end of file of your changes !!
 //

 /* HISTORY
 * 17. 2. 2006 : First adaption of the sample-module language file to the board2 language file.
 * 18. 2. 2006 : Complete translation of all german phrases.
 * 19. 2. 2006 : Complete translation of all german phrases.
 */
 ?>
