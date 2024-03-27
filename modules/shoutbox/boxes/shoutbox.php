<?php
/**
 * Ajax Shoutbox
 */
$framework->add_js_path("ext_scripts/jquery-plugins/jquery.form.js");
if (!$auth['userid']) {
    $userid = 0;
} else {
    $userid = $auth['userid'];
}

$shoutDelay = $cfg['shout_delay'] ?? 20;
$shoutEntries = $cfg['shout_entries'] ?? 5;
$shoutLength = $cfg['shout_length'] ?? 160;

$framework->add_js_code('var shoutdelay = ' . intval($shoutDelay) . ';
                        var maxcount = ' . intval($shoutEntries) . ';');

$smarty->assign("shoutuserid", $auth['userid']);
$smarty->assign("shoutlength", intval($shoutLength));

$box->ItemRow('data', $smarty->fetch('modules/shoutbox/templates/box-template.htm'));
