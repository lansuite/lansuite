<?php

function Update($id) {
  global $db, $cfg, $row;

	if ($id != '') {
		$menu_intem = $db->qry_first('SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %int%', $id);

		if ($menu_intem['active']) {
      ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

			$db->qry("UPDATE %prefix%menu
        SET module = 'info2',
				caption = %string%,
				hint = %string%,
				level = %int%,
				link = %string%
				WHERE id = %int%",
        $_POST["caption"], $_POST["shorttext"], $level, '?mod=info2&action=show_info2&id='. $id, $row["id"]);
		}
	}
	
	return true;
}

function ShowActiveState($val){
  global $dsp, $templ, $lang, $line;

  if ($val) return $dsp->FetchIcon('', 'yes', t('Ja'));
  else return $dsp->FetchIcon('', 'no', t('Nein'));
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
      if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=info2&action=change&step=2&infoID=', t('Editieren'));
      if ($auth['type'] >= 2) $ms2->AddMultiSelectAction('Deaktivieren', 'index.php?mod=info2&action=change&step=20', 1);
      if ($auth['type'] >= 2) $ms2->AddMultiSelectAction('Aktivieren (jedoch nicht verlinken)', 'index.php?mod=info2&action=change&step=21', 1);
      if ($auth['type'] >= 2) $ms2->AddMultiSelectAction('Aktivieren und verlinken', 'index.php?mod=info2&action=change&step=22', 1);
      if ($auth['type'] >= 2) $ms2->AddMultiSelectAction('Aktivieren und verlinken nur für Admins', 'index.php?mod=info2&action=change&step=23', 1);
      if ($auth['type'] >= 3) $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=info2&action=change&step=10', 1);

      $ms2->PrintSearch('index.php?mod=info2', 'i.infoID');
  	break;
	
	// Generate Editform
  	case 2:
			if ($_GET['infoID'] != '') $row = $db->qry_first("SELECT m.id FROM %prefix%info AS i
        LEFT JOIN %prefix%menu AS m ON i.caption = m.caption AND m.action = 'show_info2'
        WHERE i.infoID = %int%", $_GET["infoID"]);

  		$dsp->NewContent(t('Informationsseite - Bearbeiten'), t('Hier können Sie den Inhalt der Seite editieren.'));

      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();

      foreach ($translation->valid_lang as $val) {
        $_POST[$language] = 1;
        $mf->AddField(t($translation->lang_names[$val]).'|'.t('Einen Text für die Sprache "%1" definieren', t($translation->lang_names[$val])), $val, 'tinyint(1)', '', FIELD_OPTIONAL, '', 3);
        if ($val == 'de') $val = '';
        else $val = '_'. $val;
        $mf->AddField(t('Seitentitel'), 'caption'. $val);
        $mf->AddField(t('Untertitel'), 'shorttext'. $val);
        if ($cfg['info2_use_fckedit']) $mf->AddField(t('Text'), 'text'. $val, '', HTML_WYSIWYG);
        else $mf->AddField(t('Text'), 'text'. $val);
      }
      $mf->AdditionalDBUpdateFunction = 'Update';
      $mf->SendForm('index.php?mod=info2&action=change&step=2', 'info', 'infoID', $_GET['infoID']);
      
  		$dsp->AddBackButton("index.php?mod=info2&action=change", "info2/form");
  	break;

  	// Delete entry
  	case 10:
  		foreach($_POST["action"] AS $item => $val) {
  			$menu_intem = $db->qry_first("SELECT caption FROM %prefix%info WHERE infoID = %string%", $item);
  			$db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);
  			$db->qry("DELETE FROM %prefix%info WHERE infoID = %string%", $item);
  		}

  		$func->confirmation(t('Der Eintrag wurde gelöscht.'), "index.php?mod=info2&action=change");
  	break;

  	// Deactivate
  	case 20:
  		if ($_GET['id']) $_POST["action"][$_GET['id']] = '1';
  		foreach($_POST["action"] AS $item => $val) {
				$db->qry("UPDATE %prefix%info SET active = 0 WHERE infoID = %string%", $item);
  			$menu_intem = $db->qry_first("SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %string%", $item);
				$db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);
      }
      $func->confirmation(t('Eintrag deaktiviert'), "index.php?mod=info2&action=change");
  	break;
    
    // Activate
    case 21:
  		if ($_GET['id']) $_POST["action"][$_GET['id']] = '1';
  		foreach($_POST["action"] AS $item => $val) {
				$db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
      }
      $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
  	break;
    
    // Activate and link
    case 22:
  		if ($_GET['id']) $_POST["action"][$_GET['id']] = '1';
  		foreach($_POST["action"] AS $item => $val) {
  			$menu_intem = $db->qry_first("SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %string%", $item);
  			$info_menu = $db->qry_first("SELECT pos FROM %prefix%menu WHERE module='info2'");

				$db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);

        ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

        $link = str_replace('<', '&lt;', $menu_intem["caption"]);
        $link = str_replace('>', '&gt;', $link);
				$db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
				$db->qry("INSERT INTO %prefix%menu
					SET module = 'info2',
					caption = %string%,
					hint = %string%,
					link = %string%,
					requirement = 0,
					level = %string%,
					pos = %string%,
					action = 'show_info2',
					file = 'show'
					", $menu_intem["caption"], $menu_intem["shorttext"], "?mod=info2&action=show_info2&id=$item", $level, $info_menu["pos"]);
      }
      $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
  	break;
    
    // Activate and link (admin only)
    case 23:
  		if ($_GET['id']) $_POST["action"][$_GET['id']] = '1';
  		foreach($_POST["action"] AS $item => $val) {
  			$menu_intem = $db->qry_first("SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %string%", $item);
  			$info_menu = $db->qry_first("SELECT pos FROM %prefix%menu WHERE module='info2'");

				$db->qry("DELETE FROM %prefix%menu WHERE action = 'show_info2' AND caption = %string%", $menu_intem["caption"]);

        ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

        $link = str_replace('<', '&lt;', $menu_intem["caption"]);
        $link = str_replace('>', '&gt;', $link);
				$db->qry("UPDATE %prefix%info SET active = 1 WHERE infoID = %string%", $item);
				$db->qry("INSERT INTO %prefix%menu
					SET module = 'info2',
					caption = %string%,
					hint = %string%,
					link = %string%,
					requirement = 2,
					level = %string%,
					pos = %string%,
					action = 'show_info2',
					file = 'show'
					", $menu_intem["caption"], $menu_intem["shorttext"], "?mod=info2&action=show_info2&id=$item", $level, $info_menu["pos"]);
      }
      $func->confirmation(t('Eintrag aktiviert'), "index.php?mod=info2&action=change");
  	break;

  }
}
?>
