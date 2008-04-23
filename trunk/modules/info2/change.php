<?php

function ShowActiveState($val){
  global $dsp, $templ, $lang, $line;

  if ($val) {
    $templ['ms2']['icon_name'] = 'yes';
    $templ['ms2']['icon_title'] = t('Ja');
  } else {
    $templ['ms2']['icon_name'] = 'no';
    $templ['ms2']['icon_title'] = t('Nein');
  }
  return '<a href="index.php?mod=info2&action=change&step=20&id='. $line['infoID'] .'">'. $dsp->FetchModTpl('mastersearch2', 'result_icon') .'</a>';
}

if ($auth['type'] <= 1) {
  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2();

  $ms2->query['from'] = "{$config['tables']['info']} AS i";
  $ms2->query['where'] = "i.active";

  $ms2->config['EntriesPerPage'] = 50;

  $ms2->AddResultField(t('Seitentitel'), 'i.caption');
  $ms2->AddResultField(t('Untertitel'), 'i.shorttext', '', 140);

  $ms2->AddIconField('details', 'index.php?mod=info2&action=show_info2&id=', t('Details'));
  $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');

} else {
  if ($_POST['content'] == '') $_POST['content'] = $_POST['FCKeditor1'];

  switch($_GET["step"]){
  	default:
  		$dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier können Sie den Inhalt der Info-Seiten editieren.'));
  		$dsp->SetForm("index.php?mod=info2&action=change&step=2");
  		$dsp->AddFormSubmitRow("add");
  		$dsp->AddContent();

      include_once('modules/mastersearch2/class_mastersearch2.php');
      $ms2 = new mastersearch2();

      $ms2->query['from'] = "{$config['tables']['info']} AS i";

      $ms2->config['EntriesPerPage'] = 50;

      $ms2->AddResultField(t('Seitentitel'), 'i.caption');
      $ms2->AddResultField(t('Untertitel'), 'i.shorttext', '', 140);
      $ms2->AddResultField(t('Aktiv'), 'i.active', 'ShowActiveState');

	$ms2->AddIconField('details', 'index.php?mod=info2&action=show_info2&id=', t('Details'));
      if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=info2&action=change&step=2&id=', t('Editieren'));
      if ($auth['type'] >= 2) $ms2->AddMultiSelectAction('Aktiv-Status ändern', 'index.php?mod=info2&action=change&step=20', 1);
      if ($auth['type'] >= 3) $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=info2&action=change&step=10', 1);

      $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');
  	break;
	
	// Generate Editform
  	case 2:
  		if ($_POST["content"] == "" and $_POST["title"] == "" and $_GET["id"] != ""){
  			$module = $db->query_first("SELECT info.text, info.caption, info.shorttext, menu.id FROM {$config['tables']['info']} AS info
  						LEFT JOIN {$config['tables']['menu']} AS menu ON info.caption = menu.caption AND action = 'show_info2'
  						WHERE info.infoID = '{$_GET["id"]}'");
  			$_POST["content"] = $module["text"];
  			$_POST["title"] = $module["caption"];
  			$_POST["subtitle"] = $module["shorttext"];
  		}

  		$dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier können Sie den Inhalt der Seite editieren.'));
  		$dsp->SetForm("index.php?mod=info2&action=change&step=3&id={$_GET["id"]}&menuid={$module["id"]}");

  		$dsp->AddTextFieldRow("title", t('Seitentitel'), $_POST["title"], $title_error);
  		$dsp->AddTextFieldRow("subtitle", t('Untertitel'), $_POST["subtitle"], $title_error);

  		if ($cfg["info2_use_fckedit"]) {

	        ob_start();
	        include_once("ext_scripts/FCKeditor/fckeditor.php");
	        $oFCKeditor = new FCKeditor('FCKeditor1') ;
	        $oFCKeditor->BasePath	= 'ext_scripts/FCKeditor/';
	        $oFCKeditor->Value = $_POST['content'];
	        $oFCKeditor->Height = 380;
	        $oFCKeditor->Create();
	        $fcke_content = ob_get_contents();
	        ob_end_clean();
	        $dsp->AddSingleRow($fcke_content);

  		} else $dsp->AddTextAreaRow("content", "", $_POST["content"], "", 80, 25, 0);

  		$dsp->AddFormSubmitRow("add");
  		$dsp->AddBackButton("index.php?mod=info2&action=change", "info2/form");
  		$dsp->AddContent();
  	break;
	
	// Write Content to DB
  	case 3:
  		if ($_POST["title"] == "" or $_POST["content"] == "") $func->information(t('Bitte füllen Sie alle Felder aus!'), "index.php?mod=info2&action=change&step=2&id={$_GET["id"]}");
  		else {
  			$info_menu = $db->query_first("SELECT pos FROM {$config['tables']['menu']} WHERE module='info2'");

  			if ($_GET["id"] == "") {
  				$db->query("INSERT INTO {$config['tables']['info']}
  					SET caption = '{$_POST["title"]}',
  					shorttext = '{$_POST["subtitle"]}',
  					text = '{$_POST["content"]}'");

  				$func->confirmation(t('Der Eintrag wurde erfolgreich erstellt.'), "index.php?mod=info2&action=change");

  			} else {
  				$menu_intem = $db->query_first("SELECT active, caption, shorttext FROM {$config['tables']['info']} WHERE infoID = {$_GET["id"]}");

  				if ($menu_intem['active'] == 1){
            ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

  					$db->query("UPDATE {$config['tables']['menu']}
  						SET module = 'info2',
  						caption = '{$_POST["title"]}',
  						hint = '{$_POST["subtitle"]}',
  						level = $level,
  						link = '?mod=info2&action=show_info2&id=". $_GET["id"] ."'
  						WHERE id = '{$_GET["menuid"]}'");
  				}

  				$db->query("UPDATE {$config['tables']['info']}
  					SET caption = '{$_POST["title"]}',
  					shorttext = '{$_POST["subtitle"]}',
  					text = '{$_POST["content"]}'
  					WHERE infoID = '{$_GET["id"]}'");

  				$func->confirmation(t('Der Eintrag wurde erfolgreich geändert.'), "index.php?mod=info2&action=change");
  			}
  		}
  	break;

  	// Delete entry
  	case 10:
  		foreach($_POST["action"] AS $item => $val) {
  			$menu_intem = $db->query_first("SELECT caption FROM {$config['tables']['info']} WHERE infoID = $item");
  			$db->query("DELETE FROM {$config['tables']['menu']} WHERE action = 'show_info2' AND caption = '{$menu_intem["caption"]}'");
  			$db->query("DELETE FROM {$config['tables']['info']} WHERE infoID = $item");
  		}

  		$func->confirmation(t('Der Eintrag wurde gelöscht.'), "index.php?mod=info2&action=change");
  	break;

  	// Change active state
  	case 20:
		if ($_GET['id']) $_POST["action"][$_GET['id']] = '1';
		foreach($_POST["action"] AS $item => $val) {
  			$menu_intem = $db->query_first("SELECT active, caption, shorttext FROM {$config['tables']['info']} WHERE infoID = $item");
  			$info_menu = $db->query_first("SELECT pos FROM {$config['tables']['menu']} WHERE module='info2'");
  			if ($menu_intem["active"]) {
  				// Set not active and delete menuitem
  				$db->query("UPDATE {$config['tables']['info']} SET active = 0 WHERE infoID = $item");
  				$db->query("DELETE FROM {$config['tables']['menu']} WHERE action = 'show_info2' AND caption = '{$menu_intem["caption"]}'");
  			} else {
  				// Set active and write menuitem
          ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

          $link = str_replace('<', '&lt;', $menu_intem["caption"]);
          $link = str_replace('>', '&gt;', $link);
  				$db->query("UPDATE {$config['tables']['info']} SET active = 1 WHERE infoID = $item");
  				$db->query("INSERT INTO {$config['tables']['menu']}
  					SET module = 'info2',
  					caption = '{$menu_intem["caption"]}',
  					hint = '{$menu_intem["shorttext"]}',
  					link = '?mod=info2&action=show_info2&id=". $item ."',
  					requirement = 0,
  					level = $level,
  					pos = {$info_menu["pos"]},
  					action = 'show_info2',
  					file = 'show'
  					");
  			}
  		}

  		$func->confirmation(t('Der Aktiv-Status wurde erfolgreich geändert.'), "index.php?mod=info2&action=change");
  	break;

  }
}
?>
