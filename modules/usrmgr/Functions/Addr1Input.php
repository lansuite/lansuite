<?php

/**
 * @param string $field
 * @param int $mode
 * @param string $error
 */
function Addr1Input($field, $mode, $error = ''): bool|string
{
    global $dsp;

    switch ($mode) {
        case \LanSuite\MasterForm::OUTPUT_PROC:
            $streetAndNoParameter = $_POST['street|hnr'] ?? '';
            $streetParameter = $_POST['street'] ?? '';
            $streetNoParameter = $_POST['hnr'] ?? '';

            if ($streetAndNoParameter == '' && $streetParameter  && $streetNoParameter) {
                $_POST['street|hnr'] = $streetParameter  . ' ' . $streetNoParameter;
            }

            $streetAndNoParameter = $_POST['street|hnr'] ?? '';
            $dsp->AddTextFieldRow('street|hnr', t('Straße und Hausnummer'), $streetAndNoParameter, $error, '', Optional('street'));
            return false;
            break;

        case \LanSuite\MasterForm::CHECK_ERROR_PROC:
            if ($_POST['street|hnr'] != '' or FieldNeeded('street')) {
                $pieces = explode(' ', $_POST['street|hnr']);
                $_POST['hnr'] = array_pop($pieces);
                $_POST['street'] = implode(' ', $pieces);

                if ($_POST['street'] == '' or $_POST['hnr'] == '') {
                    return t('Bitte gib Straße und Hausnummer in folgendem Format ein: "Straßenname 12".');
                }
            }
            // Means no error
            return false;
            break;
    }
}
