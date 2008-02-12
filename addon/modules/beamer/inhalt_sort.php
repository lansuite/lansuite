<?php
if (($auth["type"] >= 2) or (($auth["userid"] == $_GET["userid"]) && $cfg['user_self_details_change'])) {

if($_GET['direction']=="up") {
$res = $db->query("SELECT sortkey, beamerID, cID FROM {$config["tables"]["beamer_content"]} WHERE cID='{$_GET['inhalt']}'");
$cnt = $db->fetch_array($res);

if($cnt['sortkey']!=0) {
$sortkey = $cnt['sortkey'] - 1;
$sql = "UPDATE {$config["tables"]["beamer_content"]} SET sortkey=sortkey+1 WHERE beamerID='{$cnt['beamerID']}' AND sortkey='$sortkey'";
$db->query($sql);

$sql = "UPDATE {$config["tables"]["beamer_content"]} SET sortkey=sortkey-1 WHERE cID='{$cnt['cID']}'";
$db->query($sql);
}
}

if($_GET['direction']=="down") {
$res = $db->query("SELECT sortkey, beamerID, cID FROM {$config["tables"]["beamer_content"]} WHERE cID='{$_GET['inhalt']}'");
$cnt = $db->fetch_array($res);

$sortkey = $cnt['sortkey'] + 1;
$sql = "UPDATE {$config["tables"]["beamer_content"]} SET sortkey=sortkey-1 WHERE beamerID='{$cnt['beamerID']}' AND sortkey='$sortkey'";
$db->query($sql);

$sql = "UPDATE {$config["tables"]["beamer_content"]} SET sortkey=sortkey+1 WHERE cID='{$cnt['cID']}'";
$db->query($sql);

}

$dsp->NewContent($lang["beamer"]["beamer"]." / ".$lang['beamer']['sub']['inhalt'], $lang['beamer']['descr']['sort']);
$dsp->SetForm("index.php?mod=beamer&action=inhalt_sort");

$res = $db->query("SELECT bID, bezeichnung FROM {$config["tables"]["beamer_beamer"]}");
while($beamer = $db->fetch_array($res)) {
$inhalt_b[] = '<option value="'.$beamer['bID'].'">'.$beamer['bezeichnung'].'</option>';
}


$dsp->AddDropDownFieldRow("bID", "Beamer:", $inhalt_b, '', 0);
$dsp->AddFormSubmitRow('ok');
$dsp->AddContent();


$templ['index']['info']['content'] .= "";

if(isset($_POST['bID'])) {
$beamer = $_POST['bID'];
} else {
$beamer = $_GET['bID'];
}

$templ['index']['info']['content'] .= '<tr><td class="tbl_6" colspan="2" height="30"><table class="tbl_6" cellpadding="3" cellspacing="0" width="100%"><tbody>';

$res = $db->query("SELECT cID, caption, active, sortkey FROM {$config["tables"]["beamer_content"]} WHERE beamerID='$beamer' ORDER BY sortkey");
while($cnt = $db->fetch_array($res)) {

if($cnt['active']) {
$aktiv = $lang['beamer']['search']['yes'];
} else {
$aktiv = $lang['beamer']['search']['no'];
}

$templ['index']['info']['content'] .= '<tr><td witdh="5%"></td>

			 <td valign="top" width="55%"><strong>'.$cnt['caption'].'</strong><br></td>
			 <td width="10%">'.$aktiv.'</td>
			 <td width="10%">'.$cnt['sortkey'].'</td>
			<td width="10%">
<a href="?mod=beamer&action=inhalt_sort&bID='.$beamer.'&inhalt='.$cnt['cID'].'&direction=up"><img src="design/'.$auth['design'].'/images/arrows_orderby_desc.gif" alt="" title="" border="0"></a>
<a href="?mod=beamer&action=inhalt_sort&bID='.$beamer.'&inhalt='.$cnt['cID'].'&direction=down"><img src="design/'.$auth['design'].'/images/arrows_orderby_asc.gif" alt="" title="" border="0"></a>
</a></td>
			<td witdh="5%"></td>
		  </tr>';
}

$templ['index']['info']['content'] .= '</tbody></table></td></tr>';


} else $func->error("ACCESS_DENIED", "");
?>