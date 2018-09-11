<?php

/**
 * Check for optional gender selection
 *
 * @param int $gender   From Inputfield 0=None, 1=Male, 2=Female
 * @return bool|string  Returns Message on error else false
 */
function check_opt_gender($gender)
{
    global $cfg;

    if ($cfg["signon_show_gender"] == 2) {
        if ($gender == 0) {
            return t("Bitte wählen sie ein Geschlecht aus.");
        } else {
            return false;
        }
    }
}
