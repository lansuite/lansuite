<?php

$helplet['modul'] = 'Lansuite-Suchfunktion';
$helplet['action'] = 'Volltext Suche';
$helplet['info'] = 'In diesem Suchfeld wird mit Volltextsuche gesucht. Dies bedeutet, dass nach mehreren Worten gesucht werden kann, unabhängig von deren Reihenfolge im Text. Dabei können die folgenden Modifikatoren angewandt werden, um das Suchergebnis zu optimieren';

$helplet['key'][1] = '+';
$helplet['value'][1] = 'Das Wort unmittelbar hinter einem + MUSS im Ergebnis vorkommen';
$helplet['key'][2] = '-';
$helplet['value'][2] = 'Das Wort unmittelbar hinter einem - DARF NICHT im Ergebnis vorkommen';
$helplet['key'][3] = '>';
$helplet['value'][3] = 'Gewichtet das folgende Wort stärker';
$helplet['key'][4] = '<';
$helplet['value'][4] = 'Gewichtet das folgende Wort schwächer';
$helplet['key'][5] = '()';
$helplet['value'][5] = 'Über klammern lassen sich Ausdrückee gruppieren';
$helplet['key'][6] = '~';
$helplet['value'][6] = 'Negiert die gewichtung des folgenden Wortes (Im Gegensatz zu - wird also nicht komplett ausgeschlossen, sondern lediglich deutlich schlechter gewichtet)';
$helplet['key'][7] = '*';
$helplet['value'][7] = 'Steht anstelle von beliebigen Zeichen';
$helplet['key'][8] = '""';
$helplet['value'][8] = 'Mit den "-Zeichen kann man der Suche sagen, dass der Wert der durch sie eingeschlossen wird, exakt so vorkommen soll';
