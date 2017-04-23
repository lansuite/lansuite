<?php

$helplet['modul'] = 'Server';
$helplet['action'] = 'Hilfe';
$helplet['info'] = 'Hier können Organisatoren und Besucher Informationen über deine Server eintragen. Die übrigen Nutzer können sich so informieren, welche Server auf der LAN vorhanden sind, und wie diese erreicht werden können.';

$helplet['key'][1] = 'Erreichbarkeitstest';
$helplet['value'][1] = 'Ein Erreichbarkeistest ist integriert und informiert über den jeweiligen Status der Server. Hier wird zunächst per Ping die Erreichbarkeit der IP getestet (das hierfür verwendete Intervall liegt bei 60 Sekunden und lässt sich in der Modul Konfig definieren) und anschließend bei speziellen Services, wie FTP, HTTP, oder IRC versucht eine Verbindung zum Port aufzubauen. Entsprechend kann man aus der Liste auslesen, ob der komplette Serverrechner aus ist, oder nur der betreffende Service zur Zeit auf dem Server nicht läuft';
$helplet['key'][2] = 'FTP-Debug';
$helplet['value'][2] = 'Auf FTP-Servern erhält man weitere Informationen, falls der Erreichbarkeitstest fehlschlägt (z.B. ob es daran liegt, dass auf dem Server kein Home-Verzeichnis festgelegt ist, oder daran, dass das Downoad-Limit erreicht ist, etc.).';
