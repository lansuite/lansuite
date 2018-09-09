<?php

/**
 * @param string $username
 * @return string
 */
function FetchDataRow($username)
{
    global $func, $dsp, $line;

    $html_image= '<img src="%s" alt="%s" border="0">';
    $avatar = ($func->chk_img_path($line['avatar_path'])) ? sprintf($html_image, $line['avatar_path'], t('Avatar')) : '';

    if ($line['userid']) {
        $ret = $dsp->FetchUserIcon($line['userid'], $username);
    } else {
        $ret = '<i>'. t('Gast') .'</i>';
    }

    $ret .= HTML_NEWLINE;
    $ret .= $func->unixstamp2date($line['date'], "datetime") . HTML_NEWLINE;
    if ($avatar) {
        $ret .= $avatar . HTML_NEWLINE;
    }

    return $ret;
}
