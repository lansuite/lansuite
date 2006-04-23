<?php

switch($_GET['step']) {
	default:
		if ($_GET['action'] == 'change') {
			if ($_GET['sponsorid'] == '') {
        include_once('modules/sponsor/search.inc.php');
			} else {
				$sponsor = $db->query_first("SELECT * FROM {$config['tables']['sponsor']} WHERE sponsorid = {$_GET['sponsorid']}");
				$_POST['name'] = $sponsor['name'];
				$_POST['url'] = $sponsor['url'];
				$_POST['pic_path'] = $sponsor['pic_path'];
				$_POST['pic_path_banner'] = $sponsor['pic_path_banner'];
				$_POST['pic_path_button'] = $sponsor['pic_path_button'];
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
			$pic_path = $func->FileUpload('pic_upload', 'ext_inc/banner/');

		// 2) Was an external URL given? 
		} elseif ($_POST['pic_path'] != 'http://' and $_POST['pic_path'] != '') $pic_path = $_POST['pic_path'];

		// 3) Was a code submitted?
    elseif ($_POST['pic_code'] != '') {
      $pic_path = $_POST['pic_code'];
      if (substr($pic_path, 0, 12) != 'html-code://') $pic_path = 'html-code://'. $pic_path;
    }
      		

		// --- Rotation Banner ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_banner']['name']) {    
			$pic_path_banner = $func->FileUpload('pic_upload_banner', 'ext_inc/banner/');

		// 2) Was an external URL given? 
		} elseif ($_POST['pic_path_banner'] != 'http://' and $_POST['pic_path_banner'] != '') $pic_path_banner = $_POST['pic_path_banner'];

		// 3) Was a code submitted?
    elseif ($_POST['pic_code_banner'] != '') {
      $pic_path_banner = $_POST['pic_code_banner'];
      if (substr($pic_path_banner, 0, 12) != 'html-code://') $pic_path_banner = 'html-code://'. $pic_path_banner;
    
    // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
      $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'], 468, 60);
      $pic_path_banner = 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'];
    }
      		
/*
		// 1) Was a special banner uploaded?
    if ($_FILES['pic_upload_banner']['name']) {
      if ($_FILES['pic_upload']['name']) $_FILES['pic_upload_banner']['name'] = $_FILES['pic_upload']['name'];
      $func->FileUpload('pic_upload_banner', 'ext_inc/banner/', 'banner_'. $_FILES['pic_upload_banner']['name']);
  		if (!$pic_url) $pic_url = 'ext_inc/banner/'. $_FILES['pic_upload_banner']['name'];

    // 2) Otherwise use an automatic resized image of the first banner, if available
		} elseif ($_FILES['pic_upload']['name']) $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/banner_'. $_FILES['pic_upload']['name'], 468, 60);
*/

		// --- Box Button ---
    // 1) Was a picture uploaded?
    if ($_FILES['pic_upload_button']['name']) {    
			$pic_path_button = $func->FileUpload('pic_upload_button', 'ext_inc/banner/');

		// 2) Was an external URL given? 
		} elseif ($_POST['pic_path_button'] != 'http://' and $_POST['pic_path_button'] != '') $pic_path_button = $_POST['pic_path_button'];

		// 3) Was a code submitted?
    elseif ($_POST['pic_code_button'] != '') {
      $pic_path_button = $_POST['pic_code_button'];
      if (substr($pic_path_button, 0, 12) != 'html-code://') $pic_path_button = 'html-code://'. $pic_path_button;
    
    // 4) Was a normal banner uploaded, that could be resized?
    } elseif ($_FILES['pic_upload']['name']) {
      $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'], 468, 60);
      $pic_path_button = 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'];
    }

/*
		// 1) Was a special box button uploaded?
    if ($_FILES['pic_upload_button']['name']) {
      if ($_FILES['pic_upload']['name']) $_FILES['pic_upload_button']['name'] = $_FILES['pic_upload']['name'];
      $func->FileUpload('pic_upload_button', 'ext_inc/banner/', 'button_'. $_FILES['pic_upload_button']['name']);
  		if (!$pic_url) $pic_url = 'ext_inc/banner/'. $_FILES['pic_upload_button']['name'];

    // 2) Otherwise use an automatic resized image of the first banner, if available
		} elseif ($_FILES['pic_upload']['name']) $gd->CreateThumb('ext_inc/banner/'. $_FILES['pic_upload']['name'], 'ext_inc/banner/button_'. $_FILES['pic_upload']['name'], 468, 60);
*/		
	break;
}

switch($_GET['step']) {
	default:
		if ($_GET['action'] == 'add' or $_GET['sponsorid'] != '') {
      $sec->unlock('sponsor');

			if ($_POST['url'] == '') $_POST['url'] = 'http://';
			if ($_POST['pos'] == '') $_POST['pos'] = '0';
#			if ($_POST['active'] == '') $_POST['active'] = SELECTED;
#			if ($_POST['rotation'] == '') $_POST['rotation'] = SELECTED;
			if ($_POST['sponsor'] == '') $_POST['sponsor'] = SELECTED;
			if (substr($_POST['pic_path'], 0, 12) == 'html-code://') $pic_code = substr($sponsor['pic_path'], 12, strlen($sponsor['pic_path']) - 12);
      else $pic_path = $_POST['pic_path'];
			if (substr($_POST['pic_path_banner'], 0, 12) == 'html-code://') $pic_code_banner = substr($sponsor['pic_path_banner'], 12, strlen($sponsor['pic_path_banner']) - 12);
      else $pic_path_banner = $_POST['pic_path_banner'];
			if (substr($_POST['pic_path_button'], 0, 12) == 'html-code://') $pic_code_button = substr($sponsor['pic_path_button'], 12, strlen($sponsor['pic_path_button']) - 12);
      else $pic_path_button = $_POST['pic_path_button'];

			$dsp->NewContent($lang['sponsor']['add_caption'], $lang['sponsor']['add_sub_caption']);
			$dsp->SetForm("index.php?mod=sponsor&action={$_GET['action']}&step=2&sponsorid={$_GET['sponsorid']}", '', '', 'multipart/form-data');

			$dsp->AddTextFieldRow('name', $lang['sponsor']['add_name'], $_POST['name'], $name_error);
			$dsp->AddTextFieldRow('url', $lang['sponsor']['add_url'], $_POST['url'], '', '', OPTIONAL);
			$dsp->AddHRuleRow();

			$dsp->AddCheckBoxRow('sponsor" onChange="change_check_box_state(\'banner_page\', this.checked)', $lang['sponsor']['add_sponsor'], $lang['sponsor']['add_sponsor2'], '', OPTIONAL, $_POST['sponsor']);
			$dsp->StartHiddenBox('banner_page', $_POST['sponsor']);
			$dsp->AddFileSelectRow('pic_upload', $lang['sponsor']['add_pic_upload'], $pic_error, '', '', OPTIONAL);
			$dsp->AddTextFieldRow('pic_path', $lang['sponsor']['add_pic'], $pic_path, '', '', OPTIONAL);
			$dsp->AddTextAreaRow('pic_code', $lang['sponsor']['add_pic_code'], $pic_code, '', '', 4, OPTIONAL);
			$dsp->StopHiddenBox();
			$dsp->AddHRuleRow();

			$dsp->AddSingleRow($lang['sponsor']['add_other_sizes']);
			$dsp->AddCheckBoxRow('rotation" onChange="change_check_box_state(\'banner_rotation\', this.checked)', $lang['sponsor']['add_banner'], $lang['sponsor']['add_banner2'], '', OPTIONAL, $_POST['rotation']);
			$dsp->StartHiddenBox('banner_rotation', $_POST['rotation']);
			$dsp->AddFileSelectRow('pic_upload_banner', $lang['sponsor']['add_pic_upload'] .' (468 x 60)', $pic_error, '', '', OPTIONAL);
			$dsp->AddTextFieldRow('pic_path_banner', $lang['sponsor']['add_pic'], $pic_path_banner, '', '', OPTIONAL);
			$dsp->AddTextAreaRow('pic_code_banner', $lang['sponsor']['add_pic_code'], $pic_code_banner, '', '', 4, OPTIONAL);
			$dsp->StopHiddenBox();
			$dsp->AddHRuleRow();

			$dsp->AddCheckBoxRow('active" onChange="change_check_box_state(\'button\', this.checked)', $lang['sponsor']['add_active'], $lang['sponsor']['add_active2'], '', OPTIONAL, $_POST['active']);
			$dsp->StartHiddenBox('button', $_POST['active']);
			$dsp->AddFileSelectRow('pic_upload_button', $lang['sponsor']['add_pic_upload'] .' (120 x 60)', $pic_error, '', '', OPTIONAL);
			$dsp->AddTextFieldRow('pic_path_button', $lang['sponsor']['add_pic'], $pic_path_button, '', '', OPTIONAL);
			$dsp->AddTextAreaRow('pic_code_button', $lang['sponsor']['add_pic_code'], $pic_code_button, '', '', 4, OPTIONAL);
			$dsp->StopHiddenBox();
			$dsp->AddHRuleRow();

			$dsp->AddTextFieldRow('pos', $lang['sponsor']['add_pos'], $_POST['pos'], '', '', OPTIONAL);
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
  								pic_path = '{$pic_path}',
  								pic_path_banner = '{$pic_path_banner}',
  								pic_path_button = '{$pic_path_button}',
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
  								pic_path = '{$pic_path}',
  								pic_path_banner = '{$pic_path_banner}',
  								pic_path_button = '{$pic_path_button}',
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