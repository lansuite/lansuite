<?php

$_GET['mf_id'] = '1';

$nextVersionId = 0;
$row = $db->qry_first('SELECT 1 AS found, MAX(versionid) AS versionid FROM %prefix%wiki_versions WHERE postid = %int% GROUP BY postid', $_GET['postid']);
if ($row['found']) {
    $nextVersionId = $row['versionid'] + 1;
    $row = $db->qry_first('SELECT text FROM %prefix%wiki_versions WHERE postid = %int% AND versionid = %int%', $_GET['postid'], $row['versionid']);
    if ($_GET['mf_step'] != 2) {
        $_POST['text'] = $row['text'];
    }
}

$jscode = "UrlAuswahl = new Array();\n";
$define_url_options = '';
$i = 0;
$res = $db->qry('SELECT postid, name FROM %prefix%wiki ORDER BY name');
while ($row = $db->fetch_array($res)) {
    $jscode .= "UrlAuswahl[$i] = new Object(); UrlAuswahl[$i]['url'] = 'index.php?mod=wiki&action=show&postid={$row['postid']}'; UrlAuswahl[$i]['name'] = '{$row['name']}';\n";
    $define_url_options .= '<option value="'. $i .'">'. $row['name'] .'</option>';
    $i++;
}
$db->free_result($res);

$framework->add_js_code($jscode);
$smarty->assign('define_url_options', $define_url_options);
$dsp->AddDoubleRow('', $smarty->fetch('modules/wiki/templates/add_page_link.htm'));

$mf = new masterform();

$mf->AddField(t('Text'), 'text', '', HTML_ALLOWED);
$mf->AddFix('userid', $auth['userid']);

$mf->AddFix('postid', $_GET['postid']);
$mf->AddFix('versionid', $nextVersionId);

$mf->SendForm('index.php?mod=wiki&amp;action='. $_GET['action'] .'&postid='. $_GET['postid'], 'wiki_versions');
if ($_GET['mf_step'] == '2') {
    $_GET['action'] = 'show';
    include_once('modules/wiki/show.php');
}
