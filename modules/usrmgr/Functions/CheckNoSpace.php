<?php

/**
 * @param string $val
 * @return bool|string
 */
function check_no_space($val)
{
    if (strpos($val, ' ') !== false) {
        return t('Der Feldname darf kein Leerzeichen enthalten');
    } else {
        return false;
    }
}
