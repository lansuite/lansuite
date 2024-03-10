<?php

/**
 * @param string $field
 * @param int $mode
 * @param string $error
 */
function Addr2Input($field, $mode, $error = ''): bool|string
{
    global $dsp;

    switch ($mode) {
        case \LanSuite\MasterForm::OUTPUT_PROC:
            $plzAndCityParameter = $_POST['plz|city'] ?? '';
            $plzParameter = $_POST['plz'] ?? '';
            $cityParameter = $_POST['city'] ?? '';
            if ($plzAndCityParameter == '' && $plzParameter && $_POST['city']) {
                $_POST['plz|city'] = $plzParameter .' '. $_POST['city'];
            }

            $plzAndCityParameter = $_POST['plz|city'] ?? '';
            $dsp->AddTextFieldRow('plz|city', t('PLZ und Ort'), $plzAndCityParameter, $error, '', Optional('city'));
            return false;
            break;

        case \LanSuite\MasterForm::CHECK_ERROR_PROC:
            $plzAndCityParameter = $_POST['plz|city'] ?? '';
            if (($plzAndCityParameter != '') || (FieldNeeded('city'))) {
                $pieces = explode(' ', $plzAndCityParameter);
                $_POST['plz'] = array_shift($pieces);
                $_POST['city'] = implode(' ', $pieces);

                if ($_POST['plz'] == 0 || $_POST['city'] == '') {
                    return t('Bitte gib Postleitzahl und Ort in folgendem Format ein: "12345 Stadt".');
                } elseif (strlen($_POST['plz']) < 4) {
                    return t('Die Postleitzahl muss aus 5 Ziffern bestehen.');
                }
            }
            // Means no error
            return false;
            break;
    }
}
