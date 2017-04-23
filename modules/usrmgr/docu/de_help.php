<?php

$helplet['modul'] = 'Benutzermanager';
$helplet['action'] = 'Hilfe';
$helplet['info'] = 'Hier verwaltest du die Benutzer des Systems. Es lassen sich neue Benutzer hinzufügen und vorhandene editieren. Hier können die Benutzer zur aktuellen Party angemeldet werden und auch auf Bezahlt gesetzt werden. Auch die Admin-Rechte werden hier den verschiedenen Benutzern vergeben.';

$helplet['key'][1] = 'Lösch-Problematik';
$helplet['value'][1] = 'Bei dem Löschen ist zu beachten, dass Benutzer dabei nur im System versteckt werden. In der Datenbank sind sie weiterhin vorhanden und lassen sich nicht löschen. Das hat den Grund darin, dass andere Modulen eventuell noch auf den Benutzer referenzieren und es durch die Löschung dann in diesen Modulen zu Fehlern kommen könnte';
$helplet['key'][2] = 'Einlass-Assistent';
$helplet['value'][2] = 'Für einen reibungslosen und schnellen Einlass auf der Party sorgt der Einlass-Assistent. Hier klickt man Schritt für Schritt die einzelnen Fragen durch, die man einem neuen Gast beim Ankommen an der Party stellen muss. z.B.: Hast du schon einen Account? Hast du schon bezahlt? ... Durch beantworten dieser Dialoge und das Ausfüllen der daraufhin erscheinenden Formulare wird der Benutzer im System eingecheckt. Um den Einlass weiter zu beschleunigen hat man im Einlass-Assistenten beim Anlegen eines neuen Benutzers die Möglichkeit hier nur einen Benutzernamen, sowie den Bezahlstatus anzugeben. Alle weiteren Daten müssen vom Benutzer dann beim ersten Einloggen im System angegeben werden. Das erspart einige Zeit am Einlass.';
$helplet['key'][3] = 'Einlass beschleunigen';
$helplet['value'][3] = 'Um noch weniger beim Einlass eintippen zu müssen, lässt sich die Option "Passwort automatisch generieren" in den Moduleinstellungen aktivieren. Nach dem Benutzeranlegen, bekommt man dann eine 5-stellige Zahl angezeigt, die das vorläufige Benutzerkennwort darstellt. Dieses sollte vom Benutzer nach dem Einloggen geändert werden.';
$helplet['key'][4] = 'Barcode System';
$helplet['value'][4] = 'Eine weitere Funktion um den Einlass bei bereits angemeldeten Benutzern zu beschleunigen ist das Barcode-System. Ist es im PDF-Modul aktiviert, kann sich jeder Benutzer über den Link rechts in der Benutzerdaten-Box seine Eintrittskarte im PDF-Format mit Barcode anzeigen und ausdrucken lassen. Mit Hilfe dieses Ausdrucks und einem Scanner, reicht ein einfaches einlesen des Barcodes auf der Party um den Benutzer einchecken zu können.';
$helplet['key'][5] = 'Benutzergruppen';
$helplet['value'][5] = 'Es lassen sich über den Benutzermanager verschiedene Benutzergruppen anlegen, in die Benutzer später beim anlegen, oder ändern eingeordnet werden können. Es ist sogar möglich diese Zuordnung automatisch geschehen zu lassen. Dazu stehen die vorgefertigten Filter für das Alter (U-/Ü18 (16)), das Geschlecht und die Ortschaft bereit';
