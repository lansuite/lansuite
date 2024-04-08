<?php

$helplet['modul'] = 'Shoutbox';
$helplet['action'] = 'Hilfe';
$helplet['info'] = 'Ein kleiner Minicat auf Ajax Basis.';

$helplet['key'][1] = 'Allgemeines';
$helplet['value'][1] = 'Die Shoutbox aktualisiert sich selbständig im Hintergrund.';

$shoutEntries = $cfg['shout_entries'] ?? 5;
$helplet['key'][2] = 'Einträge';
$helplet['value'][2] = 'Es werde derzeit immer die letzten ' . $shoutEntries . ' Einträge angezeigt. Der Wert kann in der Konfiguration geändert werden.';
