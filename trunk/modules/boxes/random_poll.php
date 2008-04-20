<?php
$templ['box']['rows'] = "";

//Anpassungen funky
srand;
$sd_row = $db->qry_first("SELECT COUNT(p.pollid) AS activepolls FROM %prefix%polls AS p
                        WHERE p.endtime = 0 OR p.endtime > %int%", time());
						
if($sd_row["activepolls"] > 0) 
{						
	
	$sd_res2 = $db->qry("SELECT p.pollid, p.caption, p.multi, p.endtime FROM {$config["tables"]["polls"]} AS p 
					    WHERE p.endtime = 0 OR p.endtime > ". time() ."
					    ORDER BY p.changedate DESC");
	$sd_i = rand(1,$sd_row["activepolls"]);
	$sd_j = 0;
	$sd_pollid = -1;
	while($sd_row2 = $db->fetch_array($sd_res2)) 
	{
			$sd_j++;
			if($sd_i == $sd_j)
			{
			   $sd_pollid = $sd_row2["pollid"];
			}
			   
	}
}

if($sd_pollid != -1)
{
	$row = $db->qry_first("SELECT p.pollid, p.caption, p.multi, COUNT(v.pollid) AS votes FROM %prefix%polls AS p
	  LEFT JOIN %prefix%pollvotes AS v on p.pollid = v.pollid
	  WHERE p.pollid = %int% 
	  GROUP BY p.pollid
	  ", $sd_pollid);
}
else
{
//-Anpassungen funky

	$row = $db->qry_first("SELECT p.pollid, p.caption, p.multi, COUNT(v.pollid) AS votes FROM %prefix%polls AS p
	  LEFT JOIN %prefix%pollvotes AS v on p.pollid = v.pollid
	  GROUP BY p.pollid
	  ORDER BY p.changedate ASC
	  ");

//-Anpassungen funky
}
//+Anpassungen funky

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