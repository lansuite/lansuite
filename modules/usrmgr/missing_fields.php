<?php

// Check, if all required user data fields, are known and force user to add them, if not.
$auth['lastname'] = $auth['name'];
$auth['gender'] = $auth['sex'];
$auth['wwcl_id'] = $auth['wwclid'];
$auth['ngl_id'] = $auth['nglid'];
foreach ($auth as $key => $val) {
    if (!is_int($key) and Needed($key) and ($val == '' or $val == '1927-01-01')) {
        $missing_fields++;
    }
}

if ($missing_fields) {
    $dsp->NewContent(t('Unvollständiges Benutzerprofil'), t('Es gibt noch unausgefüllte Pflichtfelder in deinem Benutzerprofil. Bitte pflege diese nach'));
    $_GET['userid'] = $auth['userid'];

    include_once('modules/usrmgr/add.php');
}
