<?php

	switch($_GET['step']){
	
		case 1:
			$f_uploaded = $func->FileUpload('file', 'ext_inc/mmc/uploads/');
			if (!$_POST['name']) $err[0] = 'Bitte geben Sie einen Namen an';
			if (!$f_uploaded AND !$_POST['location']) $err[1] = 'Bitte geben Sie einen Datei oder eine URL an';
			if (count($err)>0) {
				$_GET['step'] = 0;
			} else {
				if ($f_uploaded){
					$f_url = $cfg['mmc_intranet'].'/'.$f_uploaded;
				} else {
					$f_url = $_POST['location'];
				}
				$db->query_first("INSERT INTO {$config['tables']['mmc_media']} (name, file, ownerid) VALUES ('".$_POST['name']."', '".$f_url."', '".$auth['userid']."')");
				$func->confirmation($f_uploaded?'Die Datei wurde erfolgreich hochgeladen':'Die Datei-URL wurde erfolgreich in die Datenbank geschrieben.' , '');
				break;
			}
		
		default:
		case 0:
		
			$dsp->NewContent($lang['mmc']['headline'], 'Datei zur Mediandatenbank hinzufügen');
			$dsp->AddSingleRow('Hier können Sie eine Datei hochladen <b>ODER</b> bei großen Dateien eine URL angeben.<br /><b>Hinweis:</b> Die Datei muss wärend des Abspielens über die URL erreichbar sein!');
			$dsp->SetForm("index.php?mod=mmc&action=addmedia&step=1", '', '','multipart/form-data');
			$dsp->AddTextFieldRow('name', 'Name', '', $err[0]);
			$dsp->AddFileSelectRow('file', 'Datei hochladen', $err[1]);
			$dsp->AddTextFieldRow('location', 'URL zur Datei (Http oder Ftp)', '', '');
			$dsp->AddFormSubmitRow('save');	
			$dsp->AddContent();
	}
	
?>