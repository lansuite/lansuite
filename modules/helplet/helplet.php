<?php
$module = $request->query->get('module');
$helpletId = $request->query->getInt('helpletid');

$fileCollection = new \LanSuite\FileCollection();
$fileCollection->setRelativePath('modules/');
$helpFilePath = $module . '/docu/' . $language . '_' . $helpletId . '.php';
$fileHandle = $fileCollection->getFileHandle($helpFilePath);

if (!$fileHandle->exists()) {
    $func->information(t('Zu diesem Modul steht leider derzeit keine Hilfe zur Verfügung'), NO_LINK);

} else {
    $fileHandle->includeCode();
    $dsp->NewContent($helplet['modul'] .' ('. $helplet['action'] .')', $helplet['info']);
    $dsp->AddHruleRow();

    if (array_key_exists('key', $helplet) && $helplet['key']) {
        foreach ($helplet['key'] as $key) {
            $value = array_shift($helplet['value']);
            if ($key) {
                $dsp->AddDoubleRow($key, $value);
            }
        }
    }
}
