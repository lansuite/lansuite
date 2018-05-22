<?php

/**
 * @return void
 */
function RewriteFields()
{
    if (substr($_POST['pic_path'], 0, 12) == 'html-code://') {
        $_POST['pic_code'] = substr($_POST['pic_path'], 12, strlen($_POST['pic_path']) - 12);
        $_POST['pic_path'] = '';
    }
    if (substr($_POST['pic_path_banner'], 0, 12) == 'html-code://') {
        $_POST['pic_code_banner'] = substr($_POST['pic_path_banner'], 12, strlen($_POST['pic_path_banner']) - 12);
        $_POST['pic_path_banner'] = '';
    }
    if (substr($_POST['pic_path_button'], 0, 12) == 'html-code://') {
        $_POST['pic_code_button'] = substr($_POST['pic_path_button'], 12, strlen($_POST['pic_path_button']) - 12);
        $_POST['pic_path_button'] = '';
    }
}
