<?php

$lang['rent']['structure_version']	= '1';
$lang['rent']['content_version']	= '2';
$lang['rent']['language_name'] = 'Deutsch';
$lang['rent']['lastchange'] = '16. Oktober 2005';
$lang['rent']['translator'] = 'Marco Müller (Genesis)';



$lang['rent']['add_stuff_error'] 	= 'Bitte geben Sie einen Namen f&uuml;r das Equipment ein.';
$lang['rent']['add_stuff_quantity_error'] 	= 'Bitte geben Sie eine Equipmentmenge an.';
$lang['rent']['add_stuff_no_number_error'] 	= 'Bitte geben Sie eine Zahl ein.';
$lang['rent']['add_stuff_max_error'] 		= 'Die Equipmentmenge darf nicht gr&ouml;ßer als 999 sein.';
$lang['rent']['add_stuff_length_error']		= 'Der Equipmentname darf nicht mehr als 50 Zeichen enthalten.';
$lang['rent']['add_stuff_length_info_error']= 'Die Anmerkung darf nicht mehr als 100 Zeichen enthalten.';

$lang['rent']['addstuff_eq_add'] 	= 'Equipment hinzuf&uuml;gen';
$lang['rent']['addstuff_eq_add_info']= 'Bitte geben sie die Daten f&uuml;r das Equipment ein.';
$lang['rent']['addstuff_eq_name'] 	= 'Equipmentname';
$lang['rent']['addstuff_shortinfo']= 'Kurzer Hinweis/Anmerkung';
$lang['rent']['addstuff_eq_quantity'] 	= 'Verf&uuml;gbare Menge';
$lang['rent']['addstuff_ok'] 		= 'Das Equipment wurde erfolgreich eingetragen.';

$lang['rent']['show_stuff_search_eq'] 	= 'Verleihbares Eqipment suchen';
$lang['rent']['show_stuff_search_eq_res'] 	= '&Uuml;bersicht des verleihbaren Eqipments (Suchergebnisse)';
$lang['rent']['show_stuff_question'] 	= 'Wollen Sie den ausgew&auml;hlte Artikel an einen User verleihen ? (Es folgt eine Auswahl)';
$lang['rent']['show_stuff_not_rent'] 	= '\nAlle diese Artikel wurden verliehen. Weiterer Verleih nicht m&ouml;glich.';
$lang['rent']['show_stuff_choise_user']	= 'W&auml;hlen Sie jetzt den User aus (Mieter):';
$lang['rent']['show_stuff_search_result']	= 'Suchergebnisse:';
$lang['rent']['show_stuff_rent_ok']		= 'OK, der Artikel wurde verliehen.';


$lang['rent']['show_out_print_form']	= 'PrintForm-Caption';
$lang['rent']['show_out_search_result']	= '&Uuml;bersicht der Benutzer die Equipment geliehen haben:';
$lang['rent']['show_out_get_rent']		= 'Wollen Sie den ausgew&auml;hlte Artikel zur&uuml;cknehmen ?';
$lang['rent']['show_out_db_error']		= '\nFehler in der der Datenbank.';


$lang['rent']['show_back_search_eq']	= 'Suche im zur&uuml;ckgenommenen Eqipment';
$lang['rent']['show_back_search_result']= '&Uuml;bersicht des zur&uuml;ckgenommenen Eqipments:';

$lang['rent']['del_stuff_search_result']= 'Equipment bearbeiten/l&ouml;schen - &Uuml;bersicht:';
$lang['rent']['del_stuff_question']		= 'Sie k&ouml;nnen jetzt den Eintrag l&ouml;schen oder bearbeiten. Das L&ouml;schen wird sofort ausgef&uuml;hrt! ' . HTML_NEWLINE . 'Sollte dieser Artikel ganz oder teilweise verliehen sein, werden die Zuordnungen ebenfalls gel&ouml;scht!';
$lang['rent']['del_stuff_warning']		= 'Achtung! Dieser Artikel ist noch %RENTED% mal verliehen. ' . HTML_NEWLINE .  HTML_NEWLINE . ' Sind sich sicher das Sie den Artikel l&ouml;schen wollen ? ' . HTML_NEWLINE . HTML_NEWLINE . ' Beachten Sie: ' . HTML_NEWLINE . 'Wenn der Artikel gel&ouml;scht wird, werden auch die Zuordnungen zu den Verleihern aufgehoben. Sie haben nach dem L&ouml;schen keine M&ouml;glichkeit mehr, festzustellen wer diesen Artikel ausgeliehen hat!';
$lang['rent']['del_stuff_edit']			= 'Equipmenteintrag bearbeiten';
$lang['rent']['del_stuff_form_edit']	= 'Mit diesem Formular k&ouml;nnen Sie den gew&auml;hlten Equipmenteintrag bearbeiten.';
$lang['rent']['del_stuff_edit_warning']	= 'Beachten Sie, dass Sie den Equipmentnamen nicht &auml;ndern k&ouml;nnen, solange ein Teil davon verliehen ist!';

$lang['rent']['del_stuff_choise_owner']	= 'Einen Besitzer (Orga) ausw&auml;hlen.';
$lang['rent']['del_stuff_my_stuff']		= 'Mich selbst als Besitzer festlegen.';
$lang['rent']['del_stuff_no_owner']		= 'Keinen Besitzer festlegen bzw. den bestehenden &uuml;bernehmen.';
$lang['rent']['del_stuff_text_owner']	= 'F&uuml;r das Equipment k&ouml;nnen Sie einen Besitzer festlegen.';
$lang['rent']['del_stuff_edit_ok']		= 'OK, der Artikel wurde ge&auml;ndert.';
$lang['rent']['del_stuff_del_ok']		= 'OK, der Artikel wurde gel&ouml;scht. Evt. bestehende Zuordnungen wurden aufgehoben.';


$lang['rent']['equipment']	= 'Equipment';
$lang['rent']['rent_from']	= 'Verliehen von';
$lang['rent']['rent_on_user']	= '&Uuml;bersicht der verliehenen Sachen f&uuml;r den Benutzer';
$lang['rent']['rent_info']	= 'Um Equipment als zur&uuml;ckgenommen zu verbuchen, klicken Sie einfach auf das gekreuzte Icon vor dem
			  Equipmenteintrag.';

$lang['rent']['user_no_rent']	= 'Der Benutzer \'%NAME%\' hat kein (weiteres) Equipment ausgeliehen.';
?>