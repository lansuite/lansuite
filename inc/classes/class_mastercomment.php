<?php

function FetchDataRow($username) {
  global $func, $dsp, $line;

  $html_image= '<img src="%s" alt="%s" border="0">';
	$avatar = ($line['avatar_path'] != '' and $line['avatar_path'] != 'none') ? sprintf($html_image, $line['avatar_path'], t('Avatar')) : '';

  if (!$username) $username = '<i>'. t('Gast') .'</i>';
  $ret = '<b>'. $username .'</b> ';
  if ($line['userid']) $ret .= $dsp->FetchUserIcon($line['userid']);
  $ret .= HTML_NEWLINE;
  $ret .= $func->unixstamp2date($line['date'], datetime) . HTML_NEWLINE;
  if ($avatar) $ret .= $avatar . HTML_NEWLINE;
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

function EditAllowed() {
  global $line, $auth;

  if ($line['creatorid'] == $auth['userid'] or $auth['type'] >= 2) return true;
  else return false;
}


class Mastercomment{

	// Construktor
	function Mastercomment($mod, $id) {
		global $CurentURLBase, $dsp, $config, $auth, $db, $config, $func;

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
    $ms2->AddSelect('c.creatorid');
    $ms2->AddSelect('s.avatar_path');
    $ms2->AddSelect('s.signature');
    $ms2->AddSelect('u.userid');
    $ms2->AddResultField('', 'u.username', 'FetchDataRow');
    $ms2->AddResultField('', 'c.text', 'FetchPostRow');
#   $ms2->AddIconField('quote', "javascript:InsertCode(document.dsp_form1.text, '[quote]". str_replace("\n", "\\n", addslashes(str_replace('"', '', $row["text"]))) ."[/quote]')", t('Zitieren'), 'EditAllowed');
    $ms2->AddIconField('edit', $CurentURLBase.'&commentid=', t('Editieren'), 'EditAllowed');
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', $CurentURLBase.'&mc_step=10&commentid=', t('Löschen'));

    $ms2->PrintSearch($CurentURLBase, 'c.commentid');


    // Add new comments
    if ($_GET['commentid']) $row = $db->query_first("SELECT creatorid FROM {$config['tables']['comments']} WHERE commentid = ".(int)$_GET['commentid']);
    if (!$_GET['commentid'] or ($row['creatorid'] and $row['creatorid'] == $auth['userid']) or $auth['type'] >= 2) {
      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();
      $mf->LogID = $id;

      $mf->AddField(t('Kommentar'), 'text', '', LSCODE_BIG);
      if (!$auth['login']) $mf->AddField('', 'captcha', IS_CAPTCHA);
      $mf->AddFix('relatedto_item', $mod);
      $mf->AddFix('relatedto_id', $id);
      $mf->AddFix('date', time());
      $mf->AddFix('creatorid', $auth['userid']);
      $mf->SendForm('', 'comments', 'commentid', $_GET['commentid']);

      $dsp->AddFieldsetEnd(t('Kommentare'));
    } else $func->error(t('Sie sind nicht berechtigt, diesen Kommentar zu editieren'));
	}
}
?>