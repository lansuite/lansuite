<?php

function FetchDataRow($username) {
  global $func, $dsp, $line;

  $html_image= '<img src="%s" alt="%s" border="0">';
    $avatar = (func::chk_img_path($line['avatar_path'])) ? sprintf($html_image, $line['avatar_path'], t('Avatar')) : '';

  if ($line['userid']) $ret .= $dsp->FetchUserIcon($line['userid'], $username);
  else $ret = '<i>'. t('Gast') .'</i>';
  $ret .= HTML_NEWLINE;
  $ret .= $func->unixstamp2date($line['date'], datetime) . HTML_NEWLINE;
  if ($avatar) $ret .= $avatar . HTML_NEWLINE;
  return $ret;
}

function FetchPostRow($text) {
  global $func, $line;

  $ret = '<span id="post'. $line['commentid'] .'">'. $func->text2html($text) .'</span>';
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
	function Mastercomment($mod, $id, $update_table = array()) {
		global $framework, $dsp, $auth, $db, $func, $cfg;

    #echo '<ul class="Line">';
    $dsp->AddFieldsetStart(t('Kommentare'));

    // Delete comments
    if ($_GET['mc_step'] == 10) {
      include_once('inc/classes/class_masterdelete.php');
      $md = new masterdelete();
      $md->LogID = $id;
      $md->Delete('comments', 'commentid', $_GET['commentid']);
      unset($_GET['commentid']);
    }
	
	  $CurentURLBase = $framework->get_clean_url_query('base');
    $CurentURLBase = str_replace('&mc_step=10', '', $CurentURLBase);
    $CurentURLBase = str_replace('&mf_step=2', '', $CurentURLBase);
    $CurentURLBase = preg_replace('#&mf_id=[0-9]*#si', '', $CurentURLBase);
    $CurentURLBase = preg_replace('#&commentid=[0-9]*#si', '', $CurentURLBase);

    // No Order by in this MS, for it collidates with possible other MS on this page
    $order_by_tmp = $_GET['order_by'];
    $_GET['order_by'] = '';    

    // List current comments
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('bugtracker');

    $ms2->query['from'] = "%prefix%comments AS c
      LEFT JOIN %prefix%user AS u ON c.creatorid = u.userid
      ";
    $ms2->query['where'] = "c.relatedto_item = '$mod' AND c.relatedto_id = '$id'";
    $ms2->config['dont_link_first_line'] = 1;

    $ms2->AddSelect('UNIX_TIMESTAMP(c.date) AS date');
    $ms2->AddSelect('c.creatorid');
    $ms2->AddSelect('u.avatar_path');
    $ms2->AddSelect('u.signature');
    $ms2->AddSelect('u.userid');

    $ms2->AddResultField('', 'u.username', 'FetchDataRow', '', 180);
    $ms2->AddResultField('', 'c.text', 'FetchPostRow');
    $ms2->AddIconField('quote', 'javascript:document.getElementById(\'text\').value += \'[quote]\' + document.getElementById(\'post%id%\').innerHTML + \'[/quote]\'', t('Zitieren'));
    $ms2->AddIconField('edit', $CurentURLBase.'&commentid=%id%#dsp_form2', t('Editieren'), 'EditAllowed');
    if ($auth['type'] >= 3) $ms2->AddIconField('delete', $CurentURLBase.'&mc_step=10&commentid=', t('Löschen'));

    $ms2->PrintSearch($CurentURLBase, 'c.commentid');
    $_GET['order_by'] = $order_by_tmp;

    // Add new comments
    if ($cfg['mc_only_logged_in'] and !$auth['login']) $func->information(t('Bitte loggen dich ein, bevor du einen Kommentar verfasst'), NO_LINK);
    else {
      if ($_GET['commentid']) $row = $db->qry_first("SELECT creatorid FROM %prefix%comments WHERE commentid = %int%", $_GET['commentid']);
      if (!$_GET['commentid'] or ($row['creatorid'] and $row['creatorid'] == $auth['userid']) or $auth['type'] >= 2) {
        include_once('inc/classes/class_masterform.php');
        $mf = new masterform();
        $mf->LogID = $id;

        $mf->AddField(t('Kommentar'), 'text', '', LSCODE_BIG);
        if (!$auth['login']) $mf->AddField('', 'captcha', IS_CAPTCHA);
        $mf->AddFix('relatedto_item', $mod);
        $mf->AddFix('relatedto_id', $id);
        if(!$_GET['commentid']){$mf->AddFix('date', 'NOW()');}
        if(!$_GET['commentid']){$mf->AddFix('creatorid', $auth['userid']);}
        if ($mf->SendForm('', 'comments', 'commentid', $_GET['commentid'])) {

        	// Send email-notifications to thread-subscribers
        	$path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

          include_once("modules/mail/class_mail.php");
          $mail = new mail();

          if (!$_GET['fid']) $_GET['fid'] = $thread['fid'];
        	// Internet-Mail
        	$subscribers = $db->qry('SELECT b.userid, u.firstname, u.name, u.email FROM %prefix%comments_bookmark AS b
        		LEFT JOIN %prefix%user AS u ON b.userid = u.userid
        		WHERE b.email = 1 AND b.relatedto_item = %string% AND b.relatedto_id = %int%', $mod, $id);
        	while ($subscriber = $db->fetch_array($subscribers)) if ($subscriber['userid'] != $auth['userid'])
        		$mail->create_inet_mail($subscriber["firstname"]." ".$subscriber["name"], $subscriber["email"], t('Es gibt einen neuen Kommentar'), str_replace('%URL%', $_SERVER['HTTP_REFERER'], t('Es wurde ein neuer Kommentar in einem Lansuite-Modul geschrieben: %URL%')), $cfg["sys_party_mail"]);
        	$db->free_result($subscribers);
        
        	// Sys-Mail
        	$subscribers = $db->qry('SELECT userid FROM %prefix%comments_bookmark AS b
            WHERE b.sysemail = 1 AND b.relatedto_item = %string% AND b.relatedto_id = %int%', $mod, $id);
        	while ($subscriber = $db->fetch_array($subscribers)) if ($subscriber['userid'] != $auth['userid'])
        		$mail->create_sys_mail($subscriber["userid"], t('Es gibt einen neuen Kommentar'), str_replace('%URL%', $_SERVER['HTTP_REFERER'], t('Es wurde ein neuer Kommentar in einem Lansuite-Modul geschrieben: %URL%')));
        	$db->free_result($subscribers);
        	
        	// Update LastChange in $update_table, if $update_table is set
        	if ($update_table) {
        	  list($key, $val) = each($update_table);
            $db->qry('UPDATE %prefix%'. $key .' SET changedate=NOW() WHERE '. $val .' = %int%', $id);
          }
        }

      } else $func->error(t('Du bist nicht berechtigt, diesen Kommentar zu editieren'));
    }

    $dsp->AddFieldsetEnd();
    #echo '</ul>';

    // Bookmarks and Auto-Mail
    if ($auth['login'] and $auth['type'] > 1) {
    	if ($_GET['set_bm']) {
    		$db->qry_first('DELETE FROM %prefix%comments_bookmark WHERE relatedto_id = %int% AND relatedto_item = %string%', $id, $mod);
    		if ($_POST["check_bookmark"]) $db->qry('INSERT INTO %prefix%comments_bookmark
          SET relatedto_id = %int%, relatedto_item = %string%, userid = %int%, email = %int%, sysemail = %int%',
          $id, $mod, $auth['userid'], $_POST['check_email'], $_POST['check_sysemail']);
    	}
    
    	$bookmark = $db->qry_first('SELECT 1 AS found, email, sysemail FROM %prefix%comments_bookmark WHERE relatedto_id = %int% AND relatedto_item = %string% AND userid = %int%', $id, $mod, $auth['userid']);
    	if ($bookmark['found']) $_POST['check_bookmark'] = 1;
    	if ($bookmark['email']) $_POST['check_email'] = 1;
    	if ($bookmark['sysemail']) $_POST['check_sysemail'] = 1;

    	$dsp->SetForm($_SERVER['REQUEST_URI'] . '&set_bm=1');
    	$dsp->AddFieldsetStart(t('Monitoring'));
      $additionalHTML = "onclick=\"CheckBoxBoxActivate('email', this.checked)\"";
    	$dsp->AddCheckBoxRow("check_bookmark", t('Lesezeichen'), t('Diesen Beitrag in meine Lesezeichen aufnehmen<br><i>(Lesezeichen ist Vorraussetzung, um Benachrichtigung per Mail zu abonnieren)</i>'), "", 1, $_POST["check_bookmark"], '', '', $additionalHTML);
    	$dsp->StartHiddenBox('email', $_POST["check_bookmark"]);
    	$dsp->AddCheckBoxRow("check_email", t('E-Mail Benachrichtigung'), t('Bei Antworten auf diesen Beitrag eine Internet-Mail an mich senden'), "", 1, $_POST["check_email"]);
    	$dsp->AddCheckBoxRow("check_sysemail", t('System-E-Mail'), t('Bei Antworten auf diesen Beitrag eine System-Mail an mich senden'), "", 1, $_POST["check_sysemail"]);
      if ($bookmark["found"]) $dsp->StopHiddenBox();
    	$dsp->AddFormSubmitRow("edit");
    	if (!$bookmark["found"]) $dsp->StopHiddenBox();
    	$dsp->AddFieldsetEnd();
    }
	}
}
?>
