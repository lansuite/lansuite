<?php

// Forms
$lang['noc']['caption'] 		= 'Add device';
$lang['noc']['subcaption'] 		= 'To add a device to the NOC, please submit this form' . HTML_NEWLINE . 'There are 30 characters available for the field name';
$lang['noc']['port_caption'] 	= 'Change port';
$lang['noc']['port_subcaption']	= 'To change the state of this port click change.';

$lang['noc']['device_caption'] 	= 'Name';
$lang['noc']['device_ip'] 		= 'IP-Address';
$lang['noc']['device_read'] 	= 'Read-Community';
$lang['noc']['device_write'] 	= 'Write-Community';
$lang['noc']['description'] 	= 'Description';
$lang['noc']['contact']			= 'Contact address';
$lang['noc']['uptime']			= 'Uptime';
$lang['noc']['context']			= 'Context';
$lang['noc']['location']		= 'Location';
$lang['noc']['no_ports']		= 'No ports selected';
$lang['noc']['activate_ports']	= 'Would you like to change the following ports?';
$lang['noc']['ports_caption']	= 'Change port state';
$lang['noc']['ports_subcaption']  = 'Please provide all ports, you would like to change';


$lang['noc']['add_ok']		=	'The device was added successfully.';
$lang['noc']['delete_ok']	=	'The device was deleted successfully.';
$lang['noc']['change_ok']	=	'The device was changed successfully.';

$lang['noc']['port_active']		=	'Active';
$lang['noc']['port_inactive']	=	'Inactive';
$lang['noc']['port_off']		=	'Disabled';
$lang['noc']['portnr']			=	'Port number';
$lang['noc']['mac']				=   'MAC-Address';
$lang['noc']['ip']				=   'IP-Address';
$lang['noc']['linkstatus']		=	'Port state';
$lang['noc']['speed']			=	'Speed';
$lang['noc']['bytesIn']			=	'Received bytes';
$lang['noc']['bytesOut']		=	'Send bytes';

$lang['noc']['port_actived']	=	'activated';
$lang['noc']['port_inactived']	=	'deactivated';
$lang['noc']['port_changed']	=	'The port state has been changed';

$lang['noc']['find_caption']	=	'Find user in network';
$lang['noc']['find_subcaption']	=	'Using this form, you can locate a user in the network';
$lang['noc']['mac_address']		=	'MAC-Address';
$lang['noc']['device_and_ip']	=	'Device and IP';
$lang['noc']['ip_not_found']	=	'The address could not be found.' . HTML_NEWLINE . 'The devices could be refreshed in the device view.';

// Question
$lang['noc']['device_delete']	= 'Would you like to delete this device?' . HTML_NEWLINE . 'Doing this, all information (also relevant to the statistics) will be lost';
$lang['noc']['change_port']		= 'Are you sure, you would like to change the state of this port?';
$lang['noc']['update_question'] = 'This action may take some time. Would you realy like to update all ports?';

// Error
$lang['noc']['device_caption_error'] 	=	'Enter a device name, please';
$lang['noc']['device_ip_error']			=	'Enter an IP-Address for this device, please';
$lang['noc']['device_ipcheck_error']	=	'Enter a <em>valid</em> IP-Address for this device, please';
$lang['noc']['device_read_error']		=	'Enter a read-community for this device, please';
$lang['noc']['device_write_error']		=	'Enter a write-community for this device, please';
$lang['noc']['device_not_exist']		=	'The selected device does not exist';
$lang['noc']['port_not_exist']			=	'This port does not exist';

$lang['noc']['connect_error']	=   HTML_NEWLINE . 'The device is unreachable. This could be due to:' . HTML_NEWLINE .  HTML_NEWLINE . '
				      				- The device has no power' . HTML_NEWLINE . '
				      				- The device has no IP-Address set' . HTML_NEWLINE . '
				      				- The device does not support SNMP' . HTML_NEWLINE . '
				      				- You have entered the wrong Read-Community' . HTML_NEWLINE . '
				      				- You have entered the wrong IP-Address' . HTML_NEWLINE . '
				      				- You forgott to enamle SNMP at your device' . HTML_NEWLINE . '
				      				- This PHP does not support SNMP. Compile it with SNMP' . HTML_NEWLINE . '
				      				&nbsp; &nbsp;or download PHP compiled with SNMP from' . HTML_NEWLINE . '
				      				&nbsp; &nbsp;<a href="http://de.php.net">the german PHP-page</a>' . HTML_NEWLINE;
$lang['noc']['add_error']		=	'Device could not be writen to the data base.';
$lang['noc']['delete_error']	=	'The device could not be deleted.';
$lang['noc']['change_error']	=	'The device could not be changed.';
$lang['noc']['change_port_error']		=	'The port could not be changed.' . HTML_NEWLINE . 'Ferify the settings of the write community';
$lang['noc']['ping_error']		=	'Could not execute the command ping';
$lang['noc']['arp_error']		=	'Could not execute the command arp';

// Warning
$lang['noc']['write_warning']	=	HTML_NEWLINE . HTML_NEWLINE . '<big>Warning:</big> A by default set write-comuity ( \'private\' ) is a high security risk!';
$lang['noc']['read_warning']	=	HTML_NEWLINE . HTML_NEWLINE . '<big>Warning:</big> A by default set read-comuity ( \'public\' ) is a high security risk!';

$lang['noc']['ms_search']	=	'Devices: Search';
$lang['noc']['ms_result']	=	'Device selection';
$lang['noc']['err_nosnmp']	=	'There is no SNMP-support available in this PHP-Version. The NOC-Module needs SNMP to run. Please compile your PHP with SNMP-Support';

?>
