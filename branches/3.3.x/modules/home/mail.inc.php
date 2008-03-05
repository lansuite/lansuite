<?php

$templ['home']['show']['item']['info']['caption'] = t('Neue Mails');
$templ['home']['show']['item']['control']['row'] = "";

$query = $db->qry('SELECT m.des_Status, m.mailID, m.subject, u.username FROM %prefix%mail_messages AS m LEFT JOIN %prefix%user AS u ON m.FromUserID = u.userid
  WHERE m.toUserID = %int% AND m.mail_status = \'active\'
  ORDER BY m.tx_date DESC
  LIMIT 0, %int%',
  $auth['userid'], $cfg['home_item_count']);

if ($db->num_rows($query) > 0) {
	while ($row = $db->fetch_array($query)) {
   	 $templ['home']['show']['row']['control']['link']	= 'index.php?mod=mail&action=showmail&ref=in&mailID='. $row['mailID'];
   	 $templ['home']['show']['row']['info']['text']		= $row['subject'].' ['.$row['username'].']';
   	
	if($row['des_Status'] == 'new')
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row_new");
	else
		$templ['home']['show']['item']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");

	 $templ['home']['show']['row']['info']['text'] = '';
	 $templ['home']['show']['row']['info']['text2'] = '';
	}
}
else $templ['home']['show']['item']['control']['row'] = "<i>". t('Keine Mails bisher vorhanden') ."</i>";
?>