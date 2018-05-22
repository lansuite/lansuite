<?php

$dsp->NewContent(t('Admin Loginübersicht'), '');

// Einbinden der MasterSearch2 (Eigene Engine zum Suchen in Lansuite)
$ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('lastlogin');

// Normale Queryabfrage in MS2, prefix wird automatisch durch den tabellen-Vornamen ersetzt. Bei uns "lansuite_"
// Es wird also lansuite_user in MySQL aufgerufen
$ms2->query['from'] = "%prefix%user AS u";
$ms2->query['where'] = "u.type > 1";
$ms2->query['default_order_by'] ="u.lastlogin DESC";

// AddResultField fügt eine Ausgabespalte hinzu, hier mit dem Namen "Letzter Login". Der zweite Parameter ist die
// Tabellenspalte in der Datenbank, hier lastlogin. MS2GetDate ist eine Funktion die den Wert in ein normales deutsches
// Datum umwandelt
$ms2->AddResultField('Letzter Login', 'UNIX_TIMESTAMP(u.lastlogin) AS lastlogin', 'MS2GetDate');

// Ebenfalls ein Ausgabefeld mit Name Benutzer. Greift auf die Spalte username in der Tabelle zu. Der dritte Parameter ist
// wieder eine vorgegebene Funktion die den Benutzernamen mit Icon und Link zurück gibt.
$ms2->AddResultField(t('Benutzer'), 'u.username', 'UserNameAndIcon');

// PrintSearch gibt deine Suche aus. Der erste Parameter ist der aktuelle Link.
// Der zweite Parameter ist die ID an der sich die Suche orientiert. Hier suchen wir nach verschiedenen usern also gehts um die userid.
$ms2->PrintSearch('index.php?mod=stats&action=lastlogin', 'u.userid');
