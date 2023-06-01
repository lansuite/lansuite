<?php

/**
 * @param string $val
 */
function check_no_space($val): bool|string
{
    if (str_contains($val, ' ')) {
        return t('Der Feldname darf kein Leerzeichen enthalten');
    } else {
        return false;
    }
}
