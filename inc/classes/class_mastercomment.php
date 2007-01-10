<?php

function FetchDataRow($username) {
  global $func, $dsp, $line;

  $html_image= '<img src="ext_inc/avatare/%s" alt="%s" border="0">';
	$avatar = ($line['avatar_path'] != '' and $line['avatar_path'] != 'none') ? sprintf($html_image, $line['avatar_path'], t('Avatar')) : '';

  $ret = '<b>'. $username .'</b> '. $dsp->FetchUserIcon($line['userid']) . HTML_NEWLINE;
  $ret .= $func->unixstamp2date($line['date'], datetime) . HTML_NEWLINE;
  $ret .= $avatar . HTML_NEWLINE;
  return $ret;
}

function FetchPostRow($text) {
  global $func, $line;

  $ret = $func->text2html($text);
  if ($line['signature']) {
    $ret .= '<hr size="1" width="100%" color="cccccc">';
    $ret .= $func->text2html($line['signature']);
  }
  return $ret;
}


class Mastercomment{

	// Construktor
	function Mastercomment($mod, $id) {
		global $CurentURLBase, $dsp, $config, $auth;

    $dsp->AddFieldsetStart(t('Kommentare'));

    // Delete comments
    if ($_GET['mc_step'] == 10) {
      include_once('inc/classes/class_masterdelete.php');
      $md = new masterdelete();
      $md->LogID = $id;
      $md->Delete('comments', 'commentid', $_GET['commentid']);
    }


    // List current comments
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('bugtracker');

    $ms2->query['from'] = "{$config["tables"]["comments"]} AS c
      LEFT JOIN {$config["tables"]["user"]} AS u ON c.creatorid = u.userid
      LEFT JOIN {$config["tables"]["usersettings"]} AS s ON c.creatorid = s.userid
      ";
    $ms2->query['where'] = "c.relatedto_item = '$mod' AND c.relatedto_id = '$id'";
    $ms2->icon_field[0]['link'] = ''; // Do not link first line

    $ms2->AddSelect('UNIX_TIMESTAMP(c.date) AS date');
    $ms2->AddSelect('s.avatar_path');
    $ms2->AddSelect('s.signature');
    $ms2->AddSelect('u.userid');
    $ms2->AddResultField('', 'u.username', 'FetchDataRow');
    $ms2->AddResultField('', 'c.text', 'FetchPostRow');
    if ($auth['type'] >= 2) $ms2->AddIconField('edit', $CurentURLBase.'&commentid=', t('Editieren'));
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', $CurentURLBase.'&mc_step=10&commentid=', t('Löschen'));

    $ms2->PrintSearch($CurentURLBase, 'c.commentid');


    // Add new comments
    include_once('inc/classes/class_masterform.php');
    $mf = new masterform();
    $mf->LogID = $id;

    $mf->AddField(t('Kommentar'), 'text', '', LSCODE_BIG);
    $mf->AddFix('relatedto_item', $mod);
    $mf->AddFix('relatedto_id', $id);
    $mf->AddFix('date', time());
    $mf->AddFix('creatorid', $auth['userid']);
    $mf->SendForm('', 'comments', 'commentid', $_GET['commentid']);

    $dsp->AddFieldsetEnd(t('Kommentare'));
	}
}
?>