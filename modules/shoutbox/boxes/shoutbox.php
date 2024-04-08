<?php
/**
 * Ajax Shoutbox
 */
$framework->addJavaScriptFile("ext_scripts/jquery-plugins/jquery.form.js");
if (!$auth['userid']) {
    $userid = 0;
} else {
    $userid = $auth['userid'];
}

$shoutDelay = $cfg['shout_delay'] ?? 20;
$shoutEntries = $cfg['shout_entries'] ?? 5;
$shoutLength = $cfg['shout_length'] ?? 160;

$framework->addJavaScriptCode('var shoutdelay = ' . intval($shoutDelay) . ';
                        var maxcount = ' . intval($shoutEntries) . ';');

$smarty->assign("shoutuserid", $auth['userid']);
$smarty->assign("shoutlength", intval($shoutLength));

$box->ItemRow('data', $smarty->fetch('modules/shoutbox/templates/box-template.htm'));
