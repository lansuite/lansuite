<?php

$file = "modules/{$_GET['module']}/docu/{$language}_{$_GET['helpletid']}.php";
if (!file_exists($file)) {
    $func->information(t('Zu diesem Modul steht leider derzeit keine Hilfe zur Verfügung'), NO_LINK);
} else {
    include($file);

    $dsp->NewContent($helplet['modul'] .' ('. $helplet['action'] .')', $helplet['info']);
    $dsp->AddHruleRow();

    if ($helplet['key']) {
        foreach ($helplet['key'] as $key) {
            $value = array_shift($helplet['value']);
            if ($key) {
                $dsp->AddDoubleRow($key, $value);
            }
        }
    }
}
