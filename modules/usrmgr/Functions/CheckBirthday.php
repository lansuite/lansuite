<?php

/**
 * Check for optional birthday selection
 * If Date is (DateNow - 80 years) the Date is the present value.
 * From the display::AddDateTimeRow() function. Not the perfect way.
 *
 * @param string $date  From Inputfield like 2000-01-02
 * @return bool|string  Returns Message on error else false
 */
function check_birthday($date)
{
    global $cfg;

    if ($cfg["signon_show_birthday"] == 2) {
        $ref_date = (date("Y")-80)."-".date("n")."-".date("d");
        if ($date == $ref_date or ($date=="0000-00-00")) {
            return t("Bitte das korrekte Geburtsdatum eingeben.");
        } else {
            return false;
        }
    }
}
