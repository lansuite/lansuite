<?php
$smarty->assign('caption', t('Neue Server'));
$content = "";

if (!$cfg['server_sortmethod']) $cfg['server_sortmethod'] = 'changedate';
$query = $db->qry("SELECT serverid, caption, type, UNIX_TIMESTAMP(changedate) AS changedate FROM %prefix%server ORDER BY %string% DESC LIMIT 0, %plain%", $cfg['server_sortmethod'], $cfg['home_item_count']);
if($db->num_rows($query) > 0) {
	while($row = $db->fetch_array($query)) {

			$serverid 	= $row["serverid"];
			$caption	= $row["caption"];
			$type		= $row["type"];
			
			$templ['home']['show']['row']['control']['link']	= "index.php?mod=server&action=show_details&serverid=$serverid";
			$templ['home']['show']['row']['info']['text']		= $func->CutString($caption, 40);
			$templ['home']['show']['row']['info']['text2']		= "(".$type.")";


    if ($func->CheckNewPosts($row['changedate'], 'server', $row['serverid'])) $content	.= $dsp->FetchModTpl('home', 'show_row_new');
    else $content	.= $dsp->FetchModTpl('home', 'show_row');

		$templ['home']['show']['row']['info']['text2']		= "";	// set var to NULL
	}
}
else $content = "<i>". t('Keine Server vorhanden') ."</i>";
?>
