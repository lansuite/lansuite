<?php
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue for Userdetails

// Show Editbutton for Signonstatus
if ($party->count > 0) {
    $ms2->AddIconField('signon', 'index.php?mod=usrmgr&action=party&user_id=', t('Partyanmeldung'));
}
