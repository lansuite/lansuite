<?php

/**
 * @param string $field
 * @param int $mode
 * @param string $error
 * @return bool|string
 * @throws Exception
 * @throws SmartyException
 */
function PersoInput($field, $mode, $error = '')
{
    global $dsp, $usrmgr, $smarty;

    switch ($mode) {
        case \LanSuite\MasterForm::OUTPUT_PROC:
            $_POST[$field .'_1'] = substr($_POST[$field], 0, 11);
            $_POST[$field .'_2'] = substr($_POST[$field], 13, 7);
            $_POST[$field .'_3'] = substr($_POST[$field], 21, 7);
            $_POST[$field .'_4'] = substr($_POST[$field], 35, 1);

            if ($_POST[$field .'_1'] == '') {
                $_POST[$field .'_1'] = "aaaaaaaaaaD";
            }
            if ($_POST[$field .'_2'] == '') {
                $_POST[$field .'_2'] = "bbbbbbb";
            }
            if ($_POST[$field .'_3'] == '') {
                $_POST[$field .'_3'] = "ccccccc";
            }
            if ($_POST[$field .'_4'] == '') {
                $_POST[$field .'_4'] = "d";
            }

            $smarty->assign('name', $field);
            $smarty->assign('value1', $_POST[$field .'_1']);
            $smarty->assign('value2', $_POST[$field .'_2']);
            $smarty->assign('value3', $_POST[$field .'_3']);
            $smarty->assign('value4', $_POST[$field .'_4']);
            if ($error) {
                $smarty->assign('errortext', $dsp->errortext_prefix . $error . $dsp->errortext_suffix);
            }
            if (Optional("perso")) {
                $smarty->assign('optional', "_optional");
            }

            return $smarty->fetch('modules/usrmgr/templates/row_perso.htm');
            break;

        case \LanSuite\MasterForm::CHECK_ERROR_PROC:
            $_POST[$field] = $_POST["perso_1"] . "<<" . $_POST["perso_2"] . "<". $_POST["perso_3"] . "<<<<<<<" . $_POST["perso_4"];
            if ($_POST[$field] == "aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d") {
                $_POST[$field] = "";
            }
            if ($_POST[$field] == "<<<<<<<<<<") {
                $_POST[$field] = "";
            }
            if ($_POST[$field] != '') {
                $perso_res = $usrmgr->CheckPerso($_POST[$field]);
                switch ($perso_res) {
                    case 2:
                        return str_replace("<", "&lt;", t('Das Format der Personalausweisnummer ist falsch. Bitte nach folgendem Muster eingeben: \'aaaaaaaaaaD<<bbbbbbb<ccccccc<<<<<<<d\''));
                        break;
                    case 3:
                        return t('Prüfsummenfehler. Bitte überprüfen deine Angaben. Sehr wahrscheinlich hast du eine oder mehrere Zahlen falsch abgeschrieben.');
                        break;
                    case 4:
                        return t('Dieser Personalausweis ist leider bereits abgelaufen.');
                        break;
                }
            }
            return false; // -> Means no error
            break;
    }
}
