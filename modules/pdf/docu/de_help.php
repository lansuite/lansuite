<?php

$helplet['modul'] = 'PDF-Export';
$helplet['action'] = 'Hilfe';
$helplet['info'] = 'Mit dem PDF-Export können Besucherkarten, Sitzplatzkarten, Besucherlisten und sogar Urkunden erzeugt werden.';

$helplet['key'][1] = 'Vorlagen';
$helplet['value'][1] = 'Im PDF-Export können für jede Ausdrucksart Vorlagen erstellt werden.
Wenn eine neue Vorlage erzeugt wird, muss man zuerst einen sinnvollen Namen geben, das Format des Papiers wählen (A5/A4/A3), den Rand festlegen und Hoch- oder Querformat einstellen.';

$helplet['key'][2] = 'Felder';
$helplet['value'][2] = 'Danach kann man die Vorlage bearbeiten es stehen folgende Möglichkeiten zur Verfügung:<ul><li><b>Rechteck:</b> Gefüllt oder nur den Rahmen</li><li><b>Linie:</b>  Linie in beliebiger Farbe</li><li><b>Text:</b>  Einen festen Text</li><li><b>Bild:</b>  Ein Bild das in etc_inc/pdf_templates abgelegt ist</li><li><b>Daten:</b>  Dynamische Daten wie Benutzername oder Sitzplatz</li></ul>';

$helplet['key'][3] = 'Objekt';
$helplet['value'][3] = 'Je nach Objekt stehen einem verschiedene Auswahlen zur Verfügung, die Angaben werden in Millimeter gemacht:<ul><li><b>X<sub>0</sub>/Y<sub>0</sub>:</b> Wo startet das Element</li><li><b>X/Y:</b> Wo endet die Linie</li><li><b>Breite/Höhe:</b> Wie breit/hoch ist das Rechteck oder das Bild (Bilder werden in diese Angaben eingepasst)</li><li><b>Text:</b> Der auszugebende Text</li><li><b>Rechtsbündig:</b> Soll der Text rechtbündig am Startpunkt sein</li><li><b>Rahmen:</b> Zeichnet eine Rahmen um das Objekt</li><li><b>R/G/B:</b> Die Farbe des Elementes ind Rot/Grün/Blau anteilen</li><li><b>Sichtbar:</b> Wenn dieses Feld deaktiviert ist, wird das Element nicht gedruckt</li></ul>';

$helplet['key'][4] = 'Besucherkarten';
$helplet['value'][4] = 'Sobald eine Vorlage angelegt ist, können Besucherkarten gedruckt werden.<br>Eine Standardvorlage wird mitgeliefert die ist aber nicht wirklich brauchbar smilie_wink.gif.<br>Ihr könnt die Besucher nach gewissen Kriterien auswählen oder direkt einen Benutzer auwählen.';

$helplet['key'][5] = 'Sitzplatzkarten';
$helplet['value'][5] = 'Bei den Sitzplatzkarten gilt das gleiche mit den Vorlagen.<br>Dann kann man einen Sitzblock auswählen oder alle Drucken lassen.';

$helplet['key'][6] = 'Userlisten';
$helplet['value'][6] = 'Bei den Besucherlisten könnt ihr auch wieder wählen welche Besucher in welcher Reihenfolge auf die Liste sollen. Die Liste kann dann z.B. zum Abhaken bei Ankunft sein oder eine einfache Getränkestrichliste';

$helplet['key'][7] = 'Urkunden';
$helplet['value'][7] = 'Ihr wählt den Platz und den Benutzer einfach aus und schon wird die passende Urkunde generiert.<br>Baut euch für jedes Turnier einfach eine neue Urkunde.<br> Eine Vorlage ist vorhanden';