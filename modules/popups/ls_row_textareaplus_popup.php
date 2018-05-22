<?php

$dsp->NewContent('Zeichen anklicken, um es ins Textfeld einzufügen');

$smilie = $db->qry("SELECT shortcut, image FROM %prefix%smilies");
$out = '';
$z = 0;
while ($smilies = $db->fetch_array($smilie)) {
    if (file_exists('ext_inc/smilies/'. $smilies['image'])) {
        $out .= '<a href="#" onclick="InsertCode(opener.document.'. $_GET['form'] .'.'. $_GET['textarea'] .', \''. $smilies['shortcut'] .'\'); return false">
    <img src="ext_inc/smilies/'. $smilies['image'] .'" border="0" alt="'. $smilies['image'] .'" />
  </a>';
        $z++;
        if ($z % 12 == 0) {
            $out .= '<br />';
        }
    }
}
$dsp->AddSingleRow($out);
