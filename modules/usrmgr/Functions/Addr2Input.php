<?php

/**
 * @param string $field
 * @param int $mode
 * @param string $error
 * @return bool|string
 */
function Addr2Input($field, $mode, $error = '')
{
    global $dsp;

    switch ($mode) {
        case \LanSuite\MasterForm::OUTPUT_PROC:
            if ($_POST['plz|city'] == '' and $_POST['plz'] and $_POST['city']) {
                $_POST['plz|city'] = $_POST['plz'] .' '. $_POST['city'];
            }
            $dsp->AddTextFieldRow('plz|city', t('PLZ und Ort'), $_POST['plz|city'], $error, '', Optional('city'));
            return false;
            break;

        case \LanSuite\MasterForm::CHECK_ERROR_PROC:
            if (($_POST['plz|city'] != '') || (FieldNeeded('city'))) {
                $pieces = explode(' ', $_POST['plz|city']);
                $_POST['plz'] = array_shift($pieces);
                $_POST['city'] = implode(' ', $pieces);

                if ($_POST['plz'] == 0 or $_POST['city'] == '') {
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
