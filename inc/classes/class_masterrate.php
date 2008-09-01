<?php

class masterrate {

	// Construktor
	function masterrate($mod, $id) {
	  global $auth, $db, $dsp;
	  
	  include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    $mf->LogID = $id;

    $selections = array();
    $selections[''] = t('Bitte auswählen');
    $selections['1'] = '1 '. t('Sehr schlecht');
    $selections['2'] = '2';
    $selections['3'] = '3';
    $selections['4'] = '4';
    $selections['5'] = '5';
    $selections['6'] = '6';
    $selections['7'] = '7';
    $selections['8'] = '8';
    $selections['9'] = '9';
    $selections['10'] = '10 '. t('Sehr gut');
    $mf->AddField(t('Bewertung'), 'score', IS_SELECTION, $selections);

    if (!$auth['login']) $mf->AddField('', 'captcha', IS_CAPTCHA);
    $mf->AddFix('ref_name', $mod);
    $mf->AddFix('ref_id', $id);
    $mf->AddFix('date', 'NOW()');
    $mf->AddFix('creatorid', $auth['userid']);
    $mf->SendForm('', 'ratings', 'ratingid', $_GET['ratingid']);

  	$row = $db->qry_first('SELECT AVG(score) AS score FROM %prefix%ratings WHERE ref_name = %string% AND ref_id = %string% GROUP BY ref_name, ref_id', $mod, $id);
  	$dsp->AddDoubleRow('Current Rating:', $row['score']);
	}
}
?>