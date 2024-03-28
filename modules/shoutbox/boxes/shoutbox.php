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
    
$framework->addJavaScriptCode('var shoutdelay = '.$cfg['shout_delay'].';
                        var maxcount = '.$cfg['shout_entries'].';');

$smarty->assign("shoutuserid", $auth['userid']);
$smarty->assign("shoutlength", $cfg['shout_length']);

$box->ItemRow('data', $smarty->fetch('modules/shoutbox/templates/box-template.htm'));
