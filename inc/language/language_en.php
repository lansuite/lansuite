<?php

	// Global
	$lang['sys']['yes'] = 'Yes';
	$lang['sys']['no'] = 'No';
	$lang['sys']['autor'] = 'Author';
	$lang['sys']['version'] = 'Version';
	$lang['sys']['online'] = 'Online';
	$lang['sys']['offline'] = 'Offline';
	$lang['sys']['compressed'] = 'compressed';
	$lang['sys']['uncompressed'] = 'uncompressed';
	$lang['sys']['seconds'] = 'seconds';
	$lang['sys']['language'] = 'Language';
	$lang['sys']['none'] = 'None';
	$lang['sys']['all'] = 'All';

	// Index Module
	$lang['index_module']['logout'] = 'You hav been logged out from the intranet successfully';
	$lang['index_module']['data_lost'] = 'Please consult the organisiation. And be nice and pacient :)';

	// Class Auth
	$lang['class_auth']['wrong_pw_inet'] = 'Have you forgotten your password?<br/><a href=\'index.php?mod=usrmgr&action=pwrecover\'>Here you can generate a new one</a>';
	$lang['class_auth']['wrong_pw_lan'] = 'If you have forgotten your password, please ask the organisators';
	$lang['class_auth']['wrong_pw'] = 'The password, you entered, is incorrect.';
	$lang['class_auth']['wrong_pw_log'] = 'loggin error for %EMAIL% (Password-Error)';
	$lang['class_auth']['closed'] = 'Your account is disabled. Please ask the organisators.';
	$lang['class_auth']['closed_log'] = 'loggin error for %EMAIL% (account disabled)';
	$lang['class_auth']['not_checkedin'] = 'You are not checked in yet. Please ask the organisators.';
	$lang['class_auth']['not_checkedin_log'] = 'loggin error for %EMAIL% (not checked in yet)';
	$lang['class_auth']['checkedout'] = 'You are allready checked out. Please ask the organisators.';
	$lang['class_auth']['checkedout_log'] = 'loggin error for %EMAIL% (checked out)';
	$lang['class_auth']['get_email_or_id'] = 'Please get your email or your Lansuite-id';
	$lang['class_auth']['get_pw'] = 'Please get your password';


	// Class db_mysql
	$lang['class_db_mysql']['no_mysql'] = 'The MySQL-PHP modul is not loaded. Please add the mysql.so extension to your php.ini and restart your webserver';
	$lang['class_db_mysql']['no_connection'] = 'The connection to the database has failed. Lansuite is aborting.';
	$lang['class_db_mysql']['no_db'] = 'The database \'%DB%\' could not be selected. Lansuite is aborting.';
	$lang['class_db_mysql']['sql_error'] = '(%LINE%) SQL-Failure. Database respondet: <font color=\'red\'><b>%ERROR%</b></font><br/> Your query was: <i>%QUERY%</i><br/><br/> Script: %SCRIPT%';
	$lang['class_db_mysql']['sql_error_log'] = 'SQL-Failure in PHP-Script \'%SCRIPT%\' (Referrer: \'%REFERRER%\')<br />SQL-Error-Message: %ERROR%<br />Query: %QUERY%';

	// Class Display
	# No Language

	// Class func
	$lang['class_func']['no_templ'] = 'The template <b>%TEMPL%</b> could not be opened';
	$lang['class_func']['seatinfo_priority'] = 'Function setainfo needs Priority defined as integer: 0 low (grey), 1 middle (green), 2 high (orange)';
	$lang['class_func']['sunday'] = 'Sunday';
	$lang['class_func']['monday'] = 'Monday';
	$lang['class_func']['tuesday'] = 'Tuesday';
	$lang['class_func']['wednesdey'] = 'Wednesday';
	$lang['class_func']['thursday'] = 'Thursday';
	$lang['class_func']['friday'] = 'Friday';
	$lang['class_func']['saturday'] = 'Saturday';
	$lang['class_func']['sunday_short'] = 'So';
	$lang['class_func']['monday_short'] = 'Mo';
	$lang['class_func']['tuesday_short'] = 'Tu';
	$lang['class_func']['wednesdey_short'] = 'We';
	$lang['class_func']['thursday_short'] = 'Th';
	$lang['class_func']['friday_short'] = 'Fr';
	$lang['class_func']['saturday_short'] = 'Sa';
	$lang['class_func']['january'] = 'January';
	$lang['class_func']['february'] = 'February';
	$lang['class_func']['march'] = 'March';
	$lang['class_func']['april'] = 'April';
	$lang['class_func']['may'] = 'May';
	$lang['class_func']['juni'] = 'Juni';
	$lang['class_func']['july'] = 'July';
	$lang['class_func']['august'] = 'August';
	$lang['class_func']['september'] = 'September';
	$lang['class_func']['october'] = 'Oktober';
	$lang['class_func']['november'] = 'November';
	$lang['class_func']['december'] = 'December';
	$lang['class_func']['error_access_denied'] = 'You don\'t have access to this area';
	$lang['class_func']['error_no_login'] = 'You are not logged in. Please log in to enter this area';
	$lang['class_func']['error_not_found'] = 'We\'re sorry this page could not be found on our server. To avoid errors, you shouldn\'t enter URLs manualy, but use the links. If you have entered this URL manualy, please check it.';
	$lang['class_func']['error_deactivated'] = 'This lansuite-modul has been deactivated, therefore it could not be used.';
	$lang['class_func']['error_no_refresh'] = 'You have repeated this task';
	$lang['class_func']['no_item_rlist'] = '%OBJECT% it\'s not exist';
	$lang['class_func']['no_item_search'] = 'No passed %OBJECT% found';	

	// Class GD
	$lang['class_gd']['error_imagecreate'] = 'Unable to initialize new GD image stream';
	$lang['class_gd']['error_write'] = 'Unable to write in directory \'%PATH%\'';

	// Class Graph

	// Class Party
	$lang['class_party']['drowpdown_name'] = 'Choose party';
	$lang['class_party']['drowpdown_price'] = 'Choose price';
	$lang['class_party']['drowpdown_user_group'] = 'Usergroup';
	$lang['class_party']['drowpdown_no_group'] = 'No group';
	$lang['class_party']['no_user_group'] = 'No usergroups available';

	// Class Sitetool
	$lang['class_sitetool']['footer_violation'] = 'The entry {footer} was not found in index.htm!';

	// Class XML
	# No Language

	// Class Barcode
	$lang['barcode']['barcode'] = 'Barcode';
	
	// Buttons
	$lang['button']['activate'] = 'Activate';
	$lang['button']['add'] = 'Add';
	$lang['button']['add_to_buddylist'] = 'Add to buddys';
	$lang['button']['addarticle'] = 'Add article';
	$lang['button']['back'] = 'Back';
	$lang['button']['bold'] = 'Bold';
	$lang['button']['checkin'] = 'Check in';
	$lang['button']['checkout'] = 'Check out';
	$lang['button']['close'] = 'Close';
	$lang['button']['code'] = 'Code';
	$lang['button']['comments'] = 'Comments';
	$lang['button']['deactivate'] = 'Deactivate';
	$lang['button']['delete'] = 'Delete';
	$lang['button']['details'] = 'Details';
	$lang['button']['download'] = 'Download';
	$lang['button']['edit'] = 'Edit';
	$lang['button']['fullscreen'] = 'Fullscreen';
	$lang['button']['games'] = 'Pairs';
	$lang['button']['generate'] = 'Generate';
	$lang['button']['join'] = 'Join';
	$lang['button']['kick'] = 'Kick';
	$lang['button']['kursiv'] = 'Italic';
	$lang['button']['login'] = 'Login';
	$lang['button']['new_calculate'] = 'Recalculate';
	$lang['button']['new_post'] = 'Reply';
	$lang['button']['new_thread'] = 'New thread';
	$lang['button']['newoption'] = 'New option';
	$lang['button']['newpassword'] = 'New password';
	$lang['button']['next'] = 'Next';
	$lang['button']['no'] = 'No';
	$lang['button']['ok'] = 'Okay';
	$lang['button']['open'] = 'Open';
	$lang['button']['order'] = 'Order';
	$lang['button']['paidchange'] = 'Change paid';
	$lang['button']['picture'] = 'Picture';
	$lang['button']['ports'] = 'Ports';
	$lang['button']['printview'] = 'Printview';
	$lang['button']['ranking'] = 'Ranking';
	$lang['button']['result'] = 'Result';
	$lang['button']['save'] = 'Save';
	$lang['button']['search'] = 'Search';
	$lang['button']['send'] = 'Send';
	$lang['button']['sendmail'] = 'Send mail';
	$lang['button']['skip'] = 'Skip';
	$lang['button']['thread_delete'] = 'Delete thread';
	$lang['button']['tree'] = 'Tree';
	$lang['button']['unblock'] = 'Unblock';
	$lang['button']['underline'] = 'Underline';
	$lang['button']['veto'] = 'Veto';
	$lang['button']['vote'] = 'Vote';
	$lang['button']['yes'] = 'Yes';
	$lang['button']['zitat'] = 'Quote';
	$lang['button']['bookmark'] = 'Bookmark';
	$lang['button']['change'] = 'Change';
	$lang['button']['undo_generate']	= 'Undo generating';
	$lang['button']['undo_close']	= 'Undo closed';
	$lang['button']['preview']	= 'Preview';
	$lang['button']['disqualify']	= 'Disqualify';
	$lang['button']['undisqualify']	= 'Qualify';
	$lang['button']['switch_user']	= 'Switch user';
	$lang['button']['day']	= 'Day overview';
	$lang['button']['month']	= 'Month overview';
	$lang['button']['year']	= 'Year overview';
	$lang['button']['finish']	= 'Finish';
	$lang['button']['print']	= 'Print';
	$lang['button']['changeclanpw'] = 'Change Clanpw';
	$lang['button']['register'] = 'Register';
	$lang['button']['lost_pw'] = 'Lost password?';

	// Missing-Fields dialog
	$lang['missing_fields']['caption'] = 'There are missing fields';
	$lang['missing_fields']['subcaption'] = 'Please enter your data first, to complete your signon to the system';
  $lang['missing_fields']['success'] = 'Thanks, for submitting your data';	
  
  // MS2
  $lang['ms2']['score'] = 'Score';
  $lang['ms2']['details'] = 'Details';
  $lang['ms2']['edit'] = 'Edit';
  $lang['ms2']['delete'] = 'Delete';
  $lang['ms2']['send_mail'] = 'Send mail';
  $lang['ms2']['change_pw'] = 'Change password';
  $lang['ms2']['switch_user'] = 'Switch user';  
  $lang['ms2']['game_tree'] = 'Game tree';  
  $lang['ms2']['game_pairs'] = 'Pairs';  
  $lang['ms2']['ranking'] = 'Ranking';  
  $lang['ms2']['assign'] = 'Assign';  
  $lang['ms2']['generate'] = 'Generate';  
?>