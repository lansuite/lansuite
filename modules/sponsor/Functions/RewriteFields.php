<?php

/**
 * @return void
 */
function RewriteFields()
{
    $picPathParameter = $_POST['pic_path'] ?? '';
    if (str_starts_with($picPathParameter, 'html-code://')) {
        $_POST['pic_code'] = substr($picPathParameter, 12, strlen($picPathParameter) - 12);
        $_POST['pic_path'] = '';
    }

    $picPathBannerParameter = $_POST['pic_path_banner'] ?? '';
    if (str_starts_with($picPathBannerParameter, 'html-code://')) {
        $_POST['pic_code_banner'] = substr($picPathBannerParameter, 12, strlen($picPathBannerParameter) - 12);
        $_POST['pic_path_banner'] = '';
    }

    $picPathButton = $_POST['pic_path_button'] ?? '';
    if (str_starts_with($picPathButton, 'html-code://')) {
        $_POST['pic_code_button'] = substr($picPathButton, 12, strlen($picPathButton) - 12);
        $_POST['pic_path_button'] = '';
    }
}
