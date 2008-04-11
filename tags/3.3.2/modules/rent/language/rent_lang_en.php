<?php

$lang['rent']['structure_version']	= '1';
$lang['rent']['content_version']	= '1';
$lang['rent']['language_name'] = 'English';
$lang['rent']['lastchange'] = 'July 13th 2005';
$lang['rent']['translator'] = 'Jochen';


$lang['rent']['add_stuff_error'] 	= 'Enter a name for the equipment, please.';
$lang['rent']['add_stuff_quantity_error'] 	= 'Enter an amount of equipment, please.';
$lang['rent']['add_stuff_no_number_error'] 	= 'An integer value is needed.';
$lang['rent']['add_stuff_max_error'] 		= 'The amount of equipment may not exceede 999.';
$lang['rent']['add_stuff_length_error']		= 'The equipment name may not consist of more than 50 characters.';
$lang['rent']['add_stuff_length_info_error']= 'The remark may not consist of more than 100 characters.';

$lang['rent']['addstuff_eq_add'] 	= 'Add equipment';
$lang['rent']['addstuff_eq_add_info']= 'Enter the data for equipment.';
$lang['rent']['addstuff_eq_name'] 	= 'Equipment name';
$lang['rent']['addstuff_shortinfo']= 'Short description';
$lang['rent']['addstuff_eq_quantity'] 	= 'Available amount';
$lang['rent']['addstuff_ok'] 		= 'The equipment has been submitted successfully.';

$lang['rent']['show_stuff_search_eq'] 	= 'Search for rentable equipment';
$lang['rent']['show_stuff_search_eq_res'] 	= 'Overview of the rentable equipment (search results)';
$lang['rent']['show_stuff_question'] 	= 'Would you like to rent the selected article to an user (a selection will follow)';
$lang['rent']['show_stuff_not_rent'] 	= '\nAll of this article are rent, at the moment. No further rental is possible.';
$lang['rent']['show_stuff_choise_user']	= 'Select an user (leaser):';
$lang['rent']['show_stuff_search_result']	= 'Search results:';
$lang['rent']['show_stuff_rent_ok']		= 'Okay, this article was rent.';

$lang['rent']['show_out_print_form']	= 'PrintForm-Caption';
$lang['rent']['show_out_search_result']	= 'Overview of user renting this equipment:';
$lang['rent']['show_out_get_rent']		= 'Would you like to take back those selected articles?';
$lang['rent']['show_out_db_error']		= '\nError in data base.';

$lang['rent']['show_back_search_eq']	= 'Search the equipment taken back';
$lang['rent']['show_back_search_result']= 'Overview over the quipment taken back:';

$lang['rent']['del_stuff_search_result']= 'Edit / Delete equipment - Overview:';
$lang['rent']['del_stuff_question']		= 'You are now able to delete, or edit this entry. Deleting will imideatly take action! ' . HTML_NEWLINE . 'If this article is completely, or partly rent, all relations will be deleted, too!';
$lang['rent']['del_stuff_warning']		= 'Attention! This article is still rented %RENTED% times. ' . HTML_NEWLINE . HTML_NEWLINE . ' Are you sure, you would like to delete this article? ' . HTML_NEWLINE . HTML_NEWLINE . 'Notice: ' . HTML_NEWLINE . 'If the article is beeing deletd, also all relations to the lender were removed. After delting you are no longer able to find out, who has rent this article!';
$lang['rent']['del_stuff_edit']			= 'Edit equipment entry';
$lang['rent']['del_stuff_form_edit']	= 'By this form you can edit the selected equipment entry.';
$lang['rent']['del_stuff_edit_warning']	= 'Take note of the fact, that you can not edit the equipments name, as long as a part of it is still rent!';

$lang['rent']['del_stuff_choise_owner']	= 'Select an owner (Orga).';
$lang['rent']['del_stuff_my_stuff']		= 'Pick myselfe as owner.';
$lang['rent']['del_stuff_no_owner']		= 'Pick no owner / Take the current one.';
$lang['rent']['del_stuff_text_owner']	= 'You can select an owner for the equipment.';
$lang['rent']['del_stuff_edit_ok']		= 'Okay, the article has been edited.';
$lang['rent']['del_stuff_del_ok']		= 'Okay, the article has been deleted. Possibly existing releations are lost.';

$lang['rent']['equipment']	= 'Equipment';
$lang['rent']['rent_from']	= 'Rent from';
$lang['rent']['rent_on_user']	= 'Overview over the rented stuff for this user';
$lang['rent']['rent_info']	= 'To mark equipment as taken back, simply click the cross-icon in front of the equipment entry.';

$lang['rent']['user_no_rent']	= 'The user \'%NAME%\' has not rent (further) equipment.';
?>
