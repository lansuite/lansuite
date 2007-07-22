<?php
$templ['box']['rows'] = "";

$row = $db->query_first("SELECT p.pollid, p.caption, p.multi, COUNT(v.pollid) AS votes FROM {$config["tables"]["polls"]} AS p
  LEFT JOIN {$config["tables"]["pollvotes"]} AS v on p.pollid = v.pollid
  GROUP BY p.pollid
  ORDER BY p.changedate ASC
  ");

$box->DotRow('<b>'. $row['caption'] .'</b>');
$box->EngangedRow(t('Rückmeldungen') .': '. $row['votes'], '', '', 'admin', 0);

$res2 = $db->qry('SELECT polloptionid, caption FROM %prefix%polloptions WHERE pollid = %int%', $row['pollid']);
$out = '<form id="dsp_form2" name="dsp_form2" method="post" action="index.php?mod=poll&action=vote&step=2&pollid='. $row['pollid'] .'" >';
while($row2 = $db->fetch_array($res2)) {
	if ($row['multi']) $out .= '<input name="option[]" type="checkbox" class="form" value="'. $row2["polloptionid"] .'" /> <label for="option[]">'. $row2['caption'] .'</label><br />';
	else $out .= '<input name="option" type="radio" class="form" value="'. $row2["polloptionid"] .'" /> <label for="option">'. $row2['caption'] .'</label><br />';
}
$out .= '<input type="submit" class="Button" name="imageField" value="Abstimmen" /></form>';

$templ['box']['rows'] .= '<li>'. $out . "<br /><br /></li>";
?>