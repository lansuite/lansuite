<?php

/**
 * @param string $AvatarName
 */
function CheckAndResizeUploadPic($AvatarName): bool|string
{
    global $gd;

    if ($AvatarName == '') {
        return false;
    }
    $FileEnding = strtolower(substr($AvatarName, strrpos($AvatarName, '.'), 5));
    if ($FileEnding != '.png' and $FileEnding != '.gif' and $FileEnding != '.jpg' and $FileEnding != '.jpeg') {
        return t('Bitte eine Grafikdatei auswählen');
    }

    $gd->CreateThumb($AvatarName, $AvatarName, 100, 100);
    return false;
}
