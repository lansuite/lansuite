<?php

// Forms
$lang['noc']['caption'] 		= "Device hinzuf&uuml;gen";
$lang['noc']['subcaption'] 	= "Um einen Device zum NOC hinzuzuf&uuml;gen, f&uuml;llen Sie bitte
				         		  das folgende Formular vollst&auml;ndig aus." . HTML_NEWLINE . "F&uuml;r das Feld Name
              					   stehen 30 Zeichen zur Verf&uuml;gung. ";
$lang['noc']['port_caption'] 	= "Port &auml;ndern";
$lang['noc']['port_subcaption']	= "Um den Status des Ports zu &auml;ndern auf &Auml;ndern dr&uuml;cken.";

$lang['noc']['device_caption'] 	= "Name";
$lang['noc']['device_ip'] 	= "IP-Adresse";
$lang['noc']['device_read'] 	= "Read-Community";
$lang['noc']['device_write'] 	= "Write-Community";
$lang['noc']['description'] 	= "Beschreibung";
$lang['noc']['contact']		= "Kontaktadresse";
$lang['noc']['uptime']		= "Laufzeit";
$lang['noc']['context']		= "Lengende";
$lang['noc']['location']		= "Standort";
$lang['noc']['no_ports']		= "Keine Ports ausgew&auml;hlt";
$lang['noc']['activate_ports']	= "Wollen Sie folgende Ports &auml;ndern?";
$lang['noc']['ports_caption']	= "Portstatus &auml;ndern";
$lang['noc']['ports_subcaption'] = "Geben sie bitte alle Ports an die Sie &auml;ndern wollen";


$lang['noc']['add_ok']		=	"Das Device wurde erfolgreich eingetragen.";
$lang['noc']['delete_ok']	=	"Das Device wurde erfolgreich gel&ouml;scht.";
$lang['noc']['change_ok']	=	"Das Device wurde erfolgreich ge&auml;ndert.";

$lang['noc']['port_active']	=	"Aktiv";
$lang['noc']['port_inactive']	=	"Inaktiv";
$lang['noc']['port_off']		=	"Ausgeschaltet";
$lang['noc']['portnr']		=	"Portnummer";
$lang['noc']['mac']		=   "MAC-Adresse";
$lang['noc']['ip']		=   "IP-Adresse";
$lang['noc']['linkstatus']	=	"Portstatus";
$lang['noc']['speed']		=	"Geschwindigkeit";
$lang['noc']['bytesIn']		=	"Empfangene Bytes";
$lang['noc']['bytesOut']		=	"Gesendete Bytes";


$lang['noc']['port_actived']	=	"aktiviert";
$lang['noc']['port_inactived']	=	"deaktiviert";
$lang['noc']['port_changed']	=	"Der Portstatus wurde ge&auml;ndert";

$lang['noc']['find_caption']	=	"User im Netzwerk finden";
$lang['noc']['find_subcaption']	=	"Mit diesem Formular k&ouml;nnen sie einen User im Netzwerk lokalisieren";
$lang['noc']['mac_address']	=	"MAC-Addresse";
$lang['noc']['device_and_ip']	=	"Device und IP";
$lang['noc']['ip_not_found']	=	"Die Adresse konnte nicht gefunden werden." . HTML_NEWLINE . "Die Adressen werden bei der Ansicht des Device aktuallisiert.";

// Question
$lang['noc']['device_delete']	= "Wollen Sie dieses Device wirklich l&ouml;schen?" . HTML_NEWLINE . "
				 				   Dadurch gehen alle (auch f&uuml;r die Statistik relevante) Informationen verloren";
$lang['noc']['change_port']	= "Sind Sie sicher, dass Sie den Status dieses Ports &auml;ndern wollen?";
$lang['noc']['update_question']  = "Dieser Vorgang kann einige Zeit dauern. Wollen sie wirklich alle Ports updaten.";

// Error
$lang['noc']['device_caption_error'] 	=	"Bitte geben Sie einen Namen f&uuml;r das Device ein";
$lang['noc']['device_ip_error']		=	"Bitte geben Sie eine IP-Adresse f&uuml;r das Device ein";
$lang['noc']['device_ipcheck_error']	=	"Bitte geben Sie eine <em>g&uuml;ltige</em> IP-Adresse f&uuml;r das Device ein";
$lang['noc']['device_read_error']	=	"Bitte geben Sie eine Read-Community f&uuml;r das Device an.";
$lang['noc']['device_write_error']	=	"Bitte geben Sie eine Write-Community f&uuml;r das Device an.";
$lang['noc']['device_not_exist']		=	"Das gew&auml;hlte Device existiert nicht";
$lang['noc']['port_not_exist']		=	"Dieser Port existiert nicht";

$lang['noc']['connect_error']	=  HTML_NEWLINE . "Das Device konnte nicht erreicht werden. M&ouml;gl. Ursachen:" . HTML_NEWLINE . HTML_NEWLINE . "
				      				- Das Device hat keinen Strom" . HTML_NEWLINE . "
				      				- Das Device hat noch keine IP-Adresse" . HTML_NEWLINE . "
				      				- Das Device unterst&uuml;tzt kein SNMP" . HTML_NEWLINE . "
				      				- Sie haben eine falsche Read-Community angegeben" . HTML_NEWLINE . "
				      				- Sie haben eine falsche IP-Adresse angegeben" . HTML_NEWLINE . "
				      				- Sie haben vergessen, SNMP am device einzuschalten" . HTML_NEWLINE . "
				      				- Dieses PHP unterst&uuml;tzt kein SNMP, kompilieren sie es mit SNMP" . HTML_NEWLINE . "
				      				&nbsp; &nbsp;oder laden sie sich ein vorkompiliertes PHP mit SNMP von" . HTML_NEWLINE . "
				      				&nbsp; &nbsp;<a href=\"de.php.net\">Der Deutschen PHP Seite</a> herunter" . HTML_NEWLINE . "', ";
$lang['noc']['add_error']	=	"Device konnte nicht in die Datenbank eingetragen werden.";
$lang['noc']['delete_error']	=	"Das Device konnte nicht gel&ouml;scht werden.";
$lang['noc']['change_error']	=	"Das Device konnte nicht ge&auml;ndert werden.";
$lang['noc']['change_port_error']=	"Der Port auf konnte nicht ge&auml;ndert werden." . HTML_NEWLINE . "
											 Pr&uuml;fen sie die Einstellung der Write-Community";
$lang['noc']['ping_error']	=	"Kann den Befehl ping nicht ausf&uuml;hren";
$lang['noc']['arp_error']	=	"Kann den Befehl arp nicht ausf&uuml;hren";

// Warning
$lang['noc']['write_warning']	=	HTML_NEWLINE . HTML_NEWLINE . "<big>Warnung:</big> Eine Standartm&auml;ßig eingestellte Write-Community ( \"private\" ) beinhaltet ein hohes Sicherheitsrisiko!";
$lang['noc']['read_warning']	=	HTML_NEWLINE . HTML_NEWLINE . "<big>Warnung:</big> Eine Standartm&auml;ßig eingestellte Read-Community ( \"public\" ) beinhaltet ein hohes Sicherheitsrisiko!";

$lang['noc']['ms_search']	=	"Device&uuml;bersicht: Suche";
$lang['noc']['ms_result']	=	"Deviceauswahl: Ergebnis";
$lang['noc']['err_nosnmp']	=	"Es ist keine SNMP Unterst&uuml;tzung in dieser PHP Version vorhanden.\n\nNOC ben&ouml;tigt SNMP. &Uuml;berpr&uuml;fen Sie, ob sie bei der Kompilierung von PHP SNMP eingebunden haben";

?>