<?php

 //Default
 $lang['troubleticket']['language_name'] = 'English';
 $lang['troubleticket']['version']       = 1;
 $lang['troubleticket']['lastchange']    = '08. Mai 2005';
 $lang['troubleticket']['translator']    = 'Cewigo (Jan)';
 $lang['troubleticket']['modulname']     = 'troubleticket';     //necessary

 //Contend
 $lang['troubleticket']['headline']       = 'Add a troubleticket';
 $lang['troubleticket']['headl_edit']     = 'Revise troubleticket';
 $lang['troubleticket']['show']           = 'Show troubleticket';
 $lang['troubleticket']['description']    = 'Description';
 $lang['troubleticket']['priority']       = 'Priority';
 $lang['troubleticket']['head']           = 'Headline';
 $lang['troubleticket']['prob_descr']     = 'Troubleticket description';
 $lang['troubleticket']['set_up_on']      = 'Added on';
 $lang['troubleticket']['from_user']      = 'By user';
 $lang['troubleticket']['assign_orga']    = 'assigned orga';
 $lang['troubleticket']['visible_4all']   = 'Visible for all';
 $lang['troubleticket']['visible_4orga']  = 'Visible for orgas only';
 $lang['troubleticket']['no_contend']     = 'No more informations';
 $lang['troubleticket']['add_confirm']    = 'The troubleticket has been added';
 $lang['troubleticket']['change_confirm'] = 'The troubleticket has been changed';
 $lang['troubleticket']['unlink_confirm'] = 'The choosen ticket has been deleted.';
 //$lang['troubleticket']['user_assign']    = 'Ihnen wurde das Troubleticket "<b>%TTCaption%</b>" zugewiesen. '; //endign with dot and space
 $lang['troubleticket']['assign_confirm'] = 'The choosen ticket were assigned';
 $lang['troubleticket']['show_info']      = 'Here you can find all informations on this ticket';
 $lang['troubleticket']['no_hint']        = ' No hint';	//beginning with a space
 $lang['troubleticket']['subline']        = ' Whit this formular your are abel to add a troubleticket'; //beginnig with a space

 //Status
 $lang['troubleticket']['status']         = 'Ticketstatus';
 $lang['troubleticket']['status_choose']  = 'choose status';
 $lang['troubleticket']['st_default']     = 'default: scripterror!';
 $lang['troubleticket']['st_new']         = 'New / not verified';
 $lang['troubleticket']['st_acc']         = 'Verified / accepted';
 $lang['troubleticket']['st_checked']     = 'Verified on';
 $lang['troubleticket']['st_in_work']       = 'In work';
 $lang['troubleticket']['st_in_work_since'] = 'In progress since';
 $lang['troubleticket']['st_finished']      = 'Finished';
 $lang['troubleticket']['st_finish_since']  = 'Finished on';
 $lang['troubleticket']['st_denied']        = 'Denied';
 $lang['troubleticket']['st_denied_since']  = 'Denied on';

 //Comment
 $lang['troubleticket']['com']			   = 'Comment';
 $lang['troubleticket']['com_4user']        = 'Comment for users';
 $lang['troubleticket']['com_4orga']        = 'Comment for orgas';
 $lang['troubleticket']['com_fa4orga']      = 'Comment for all';

 //Search
 $lang['troubleticket']['ms_search_ticket'] = 'Ticket suchen';
 $lang['troubleticket']['ms_ticket_result'] = 'Ticket: Ergebnis';
 $lang['troubleticket']['ms_search_user']   = 'Benutzer suchen';
 $lang['troubleticket']['ms_user_result']   = 'Benutzer: Ergebnis';

 //Change
 $lang['troubleticket']['option_nochange']  = ' No Changes ';   // all options starts and ends with a space
 $lang['troubleticket']['option_back2poll'] = ' Back to poll / ticket denied ';
 $lang['troubleticket']['option_startwork'] = ' Ticket accepted / in progress ';
 $lang['troubleticket']['option_reopen']	   = ' Problem nochmal aufgreifen und bearbeiten ';  // this option is currently not in use
 $lang['troubleticket']['option_finished']  = ' Finished ';
 $lang['troubleticket']['option_refuse']    = ' Ticket refused ';

 //States
 $lang['troubleticket']['state_0'] = 'Low';
 $lang['troubleticket']['state_1'] = 'Normal';
 $lang['troubleticket']['state_2'] = 'High';
 $lang['troubleticket']['state_3'] = 'Critical';

 //Categories
 $lang['troubleticket']['no_cat'] 	= 'Please choise';
 $lang['troubleticket']['cat']		= 'Categorie';
 
 //Questions
 $lang['troubleticket']['q_unlink'] = 'Would you really delete this ticket?';

 //Errors
 $lang['troubleticket']['err_max_size']  = 'This Text should be less than 5000 chars';
 $lang['troubleticket']['err_no_head']   = 'Please add a short headline';
 $lang['troubleticket']['err_assign']    = 'The troubleticket could not be assigned! Possible database problems!';
 $lang['troubleticket']['err_no_reason'] = 'Please give a short reason by refusing a ticket.';
 $lang['troubleticket']['err_no_tt_id']  = 'There where no ticket-id.';
 $lang['troubleticket']['err_unlink']    = 'The troubleticket could not be deleted! Possible database problems!';
 $lang['troubleticket']['err_no_cat']    = 'Please choise a Categorie';
?>
