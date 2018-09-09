<?php

/**
 * @return void
 */
function UploadFiles()
{
    global $gd;

    // Sponsor Page Banner
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload']['name']) {
        $_POST['pic_path'] = $_POST['pic_upload'];

        // 2) Was an external URL given?
    } elseif ($_POST['pic_path'] != 'http://' and $_POST['pic_path'] != '') {
        // 3) Was a code submitted?
    } elseif ($_POST['pic_code'] != '') {
        $_POST['pic_path'] = $_POST['pic_code'];
        if (substr($_POST['pic_path'], 0, 12) != 'html-code://') {
            $_POST['pic_path'] = 'html-code://'. $_POST['pic_path'];
        }
    }

    // Rotation Banner
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_banner']['name']) {
        $_POST['pic_path_banner'] = $_POST['pic_upload_banner'];

        // 2) Was an external URL given?
    } elseif ($_POST['pic_path_banner'] != 'http://' and $_POST['pic_path_banner'] != '') {
        // 3) Was a code submitted?
    } elseif ($_POST['pic_code_banner'] != '') {
        $_POST['pic_path_banner'] = $_POST['pic_code_banner'];
        if (substr($_POST['pic_path_banner'], 0, 12) != 'html-code://') {
            $_POST['pic_path_banner'] = 'html-code://'. $_POST['pic_path_banner'];
        }

        // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
        $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'], 468, 60);
        $_POST['pic_path_banner'] = 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'];
    }

    // Box Button
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_button']['name']) {
        $_POST['pic_path_button'] = $_POST['pic_upload_button'];

        // 2) Was an external URL given?
    } elseif ($_POST['pic_path_button'] != 'http://' and $_POST['pic_path_button'] != '') {
        // 3) Was a code submitted?
    } elseif ($_POST['pic_code_button'] != '') {
        $_POST['pic_path_button'] = $_POST['pic_code_button'];
        if (substr($_POST['pic_path_button'], 0, 12) != 'html-code://') {
            $_POST['pic_path_button'] = 'html-code://'. $_POST['pic_path_button'];
        }

        // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
        $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'], 468, 60);
        $_POST['pic_path_button'] = 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'];
    }
}
