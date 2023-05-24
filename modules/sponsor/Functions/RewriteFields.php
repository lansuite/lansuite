<?php

/**
 * @return void
 */
function RewriteFields()
{
    if (str_starts_with($_POST['pic_path'], 'html-code://')) {
        $_POST['pic_code'] = substr($_POST['pic_path'], 12, strlen($_POST['pic_path']) - 12);
        $_POST['pic_path'] = '';
    }
    if (str_starts_with($_POST['pic_path_banner'], 'html-code://')) {
        $_POST['pic_code_banner'] = substr($_POST['pic_path_banner'], 12, strlen($_POST['pic_path_banner']) - 12);
        $_POST['pic_path_banner'] = '';
    }
    if (str_starts_with($_POST['pic_path_button'], 'html-code://')) {
        $_POST['pic_code_button'] = substr($_POST['pic_path_button'], 12, strlen($_POST['pic_path_button']) - 12);
        $_POST['pic_path_button'] = '';
    }
}
