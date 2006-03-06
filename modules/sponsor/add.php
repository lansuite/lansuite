<?php

switch($_GET['step']) {
	default:
		if ($_GET['action'] == 'change') {
			if ($_GET['sponsorid'] == '') {
				$mastersearch = new MasterSearch($vars, 'index.php?mod=sponsor&action=change', 'index.php?mod=sponsor&action=change&sponsorid=', '');
				$mastersearch->LoadConfig('sponsor', '', $lang['sponsor']['add_ms']);
				$mastersearch->PrintForm();
				$mastersearch->Search();
				$mastersearch->PrintResult();
				$templ['index']['info']['content'] .= $mastersearch->GetReturn();
			} else {
				$sponsor = $db->query_first("SELECT * FROM {$config['tables']['sponsor']} WHERE sponsorid = {$_GET['sponsorid']}");
				$_POST['name'] = $sponsor['name'];
				$_POST['url'] = $sponsor['url'];
				$pic_url = $sponsor['pic_path'];
				$_POST['text'] = $sponsor['text'];
				$_POST['pos'] = $sponsor['pos'];
				$_POST['active'] = $sponsor['active'];
				$_POST['rotation'] = $sponsor['rotation'];
				$_POST['sponsor'] = $sponsor['sponsor'];
			}
		}
	break;

	case 2:
		// Check for errors
		if ($_POST['name'] == '') {
			$name_error = $lang['sponsor']['err_name'];
			$_GET['step'] = 1;
		}
		if (strlen($_POST['text']) > 5000) {
			$text_error = $lang['sponsor']['err_text'];
			$_GET['step'] = 1;
		}

    // --- Sponsor Page Banner ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload']['name']) {    
			$pic_url = $func->FileUpload('pic_upload', 'ext_inc/banner/');

		// 2) Was an external URL given? 
		} elseif ($_POST['pic_url'] != 'http://' and $_POST['pic_url'] != '') $pic_url = $_POST['pic_url'];

		// 3) Was a code submitted?
    elseif ($_POST['pic_code'] != '') {
      $pic_url = $_POST['pic_code'];
      if (substr($pic_url, 0, 12) != 'html-code://') $pic_url = 'html-code://'. $pic_url;
    }
      		

		// --- Rotation Banner ---
		// 1) Was a special banner uploaded?
    if ($_FILES['pic_upload_banner']['name']) {
      if ($_FILES['pic_upload']['name']) $_FILES['pic_upload_banner']['name'] = $_FILES['pic_upload']['name'];
      $func->FileUpload('pic_upload_banner', 'ext_inc/banner/', 'banner_'. $_FILES['pic_upload_banner']['name']);
  		if (!$pic_url) $pic_url = 'ext_inc/banner/'. $_FILES['pic_upload_banner']['name'];

    // 2) Otherwise use an automatic resized image of the first banner, if available
		} elseif ($_FILES['pic_upload']['name']) $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'], 468, 60);


		// --- Box Button ---
		// 1) Was a special banner uploaded?
    if ($_FILES['pic_upload_button']['name']) {
      if ($_FILES['pic_upload']['name']) $_FILES['pic_upload_button']['name'] = $_FILES['pic_upload']['name'];
      $func->FileUpload('pic_upload_button', 'ext_inc/banner/', 'button_'. $_FILES['pic_upload_button']['name']);
  		if (!$pic_url) $pic_url = 'ext_inc/banner/'. $_FILES['pic_upload_button']['name'];

    // 2) Otherwise use an automatic resized image of the first banner, if available
		} elseif ($_FILES['pic_upload']['name']) $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'], 468, 60);
	break;
}

switch($_GET['step']) {
	default:
		if ($_GET['action'] == 'add' or $_GET['sponsorid'] != '') {
      $sec->unlock('sponsor');

			if ($_POST['url'] == '') $_POST['url'] = 'http://';
			if ($pic_url == '') $pic_url = 'http://';
			if ($_POST['pos'] == '') $_POST['pos'] = '0';
			if ($_POST['active'] == '') $_POST['active'] = SELECTED;
			if ($_POST['rotation'] == '') $_POST['rotation'] = SELECTED;
			if ($_POST['sponsor'] == '') $_POST['sponsor'] = SELECTED;
			if (substr($pic_code, 0, 12) == 'html-code://') $pic_code = substr($sponsor['pic_path'], 12, strlen($sponsor['pic_path']) - 12);

			$dsp->NewContent($lang['sponsor']['add_caption'], $lang['sponsor']['add_sub_caption']);
			$dsp->SetForm("index.php?mod=sponsor&action={$_GET['action']}&step=2&sponsorid={$_GET['sponsorid']}", '', '', 'multipart/form-data');

			$dsp->AddTextFieldRow('name', $lang['sponsor']['add_name'], $_POST['name'], $name_error);
			$dsp->AddTextFieldRow('url', $lang['sponsor']['add_url'], $_POST['url'], '', '', OPTIONAL);
			$dsp->AddHRuleRow();
			$dsp->AddFileSelectRow('pic_upload', $lang['sponsor']['add_pic_upload'], $pic_error, '', '', OPTIONAL);
			$dsp->AddTextFieldRow('pic_url', $lang['sponsor']['add_pic'], $pic_url, '', '', OPTIONAL);
			$dsp->AddTextAreaRow('pic_code', $lang['sponsor']['add_pic_code'], $pic_code, '', '', 4, OPTIONAL);
			$dsp->AddSingleRow($lang['sponsor']['add_other_sizes']);
			$dsp->AddFileSelectRow('pic_upload_banner', $lang['sponsor']['add_pic_upload'] .' (120 x 60)', $pic_error, '', '', OPTIONAL);
			$dsp->AddFileSelectRow('pic_upload_button', $lang['sponsor']['add_pic_upload'] .' (468 x 60)', $pic_error, '', '', OPTIONAL);
			$dsp->AddHRuleRow();
			$dsp->AddTextFieldRow('pos', $lang['sponsor']['add_pos'], $_POST['pos'], '', '', OPTIONAL);
			$dsp->AddCheckBoxRow('sponsor', $lang['sponsor']['add_sponsor'], $lang['sponsor']['add_sponsor2'], '', OPTIONAL, $_POST['sponsor']);
			$dsp->AddCheckBoxRow('rotation', $lang['sponsor']['add_banner'], $lang['sponsor']['add_banner2'], '', OPTIONAL, $_POST['rotation']);
			$dsp->AddCheckBoxRow('active', $lang['sponsor']['add_active'], $lang['sponsor']['add_active2'], '', OPTIONAL, $_POST['active']);
			$dsp->AddTextAreaPlusRow('text', $lang['sponsor']['add_text'], $_POST['text'], $text_error);

			$dsp->AddFormSubmitRow('add');
			$dsp->AddBackButton('index.php?mod=sponsor', 'sponsor/add');
			$dsp->AddContent();
		}
	break;

	case 2:
    if (!$sec->locked('sponsor')) {
      $sec->lock('sponsor');
	
  		if ($_GET['action'] == 'change') {
  			$db->query("UPDATE {$config['tables']['sponsor']} SET
  								name = '{$_POST['name']}',
  								url = '{$_POST['url']}',
  								pic_path = '{$pic_url}',
  								text = '{$_POST['text']}',
  								pos = ". (int)$_POST['pos'] .",
  								rotation = '{$_POST['rotation']}',
  								sponsor = '{$_POST['sponsor']}',
  								active = ". (int)$_POST['active'] ."
  								WHERE sponsorid = {$_GET['sponsorid']}");
  			$func->confirmation($lang['sponsor']['change_success'], "index.php?mod=sponsor&action={$_GET['action']}");
  		}
  		if ($_GET['action'] == 'add') {
  			$db->query("INSERT INTO {$config['tables']['sponsor']} SET
  								name = '{$_POST['name']}',
  								url = '{$_POST['url']}',
  								pic_path = '{$pic_url}',
  								text = '{$_POST['text']}',
  								pos = ". (int)$_POST['pos'] .",
  								rotation = '{$_POST['rotation']}',
  								sponsor = '{$_POST['sponsor']}',
  								active = ". (int)$_POST['active'] ."
  								");
  			$func->confirmation($lang['sponsor']['add_success'], "index.php?mod=sponsor&action={$_GET['action']}");
  		}
  	}
	break;
}
?>