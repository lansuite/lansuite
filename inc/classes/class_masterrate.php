<?php

class masterrate {

	// Construktor
	function masterrate($mod, $id, $caption = '') {
	  global $auth, $db, $dsp, $framework, $smarty;
	  
	  $framework->add_js_path('ext_scripts/jquery.rating.js');
	  $framework->add_js_code("jQuery(function(){
  jQuery('form.rating').rating();
});");

	  $framework->add_css_code("
  .rating {
      cursor: pointer;
      clear: both;
      display: block;
      width: 100px;
  }
  .rating:after {
      content: '.';
      display: block;
      height: 0;
      width: 0;
      clear: both;
      visibility: hidden;
  }
  .cancel,
  .star {
      float: left;
      width: 17px;
      height: 15px;
      overflow: hidden;
      text-indent: -999em;
      cursor: pointer;
  }
  div.done, div.done a {
    cursor: default;
  }
  .star,
  .star a {
    background: url(design/images/masterrate.gif) no-repeat 0 0px;
  }
  .star a {
    display: block;
    width: 100%;
    height: 100%;
    background-position: 0 0px;
  }
  div.rating div.on a {
    background-position: 0 -16px;
  }
	div.rating div.hover a {
		background-position: 0 -32px;
	}");
/*
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
*/
  	$row = $db->qry_first('SELECT ROUND(AVG(score), 1) AS score FROM %prefix%ratings WHERE ref_name = %string% AND ref_id = %string% GROUP BY ref_name, ref_id',
      $mod, $id);
  	$smarty->assign('rating', $row['score']);
  	$smarty->assign('action', $framework->get_clean_url_query('base') .'&mr_step=2&design=base');
  	if ($caption == '') $caption = t('Bewertung');
  	$dsp->AddDoubleRow($caption, $smarty->fetch('design/templates/ls_masterrate_row.htm'));
  	
  	if ($_GET['mr_step'] == 2) {
      $db->qry('INSERT INTO %prefix%ratings SET score = %int%, ref_name = %string%, ref_id = %string%, date = NOW(), creatorid = %int%',
        $_POST['rating'], $mod, $id, $auth['userid']);
    }
	}
}
?>