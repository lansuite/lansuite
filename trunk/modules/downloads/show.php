<?php

// Use /ext_inc/downloads
if (!$cfg['download_use_ftp']) {
  $BaseDir = 'ext_inc/downloads/';

  // Don't allow directories above base!
  $_GET['dir'] = str_replace('..', '', $_GET['dir']);

  // Generate up-links
  $Dirs = split('/',$_GET['dir']);
  $LinkUp = '<a href="index.php?mod=downloads" class="menu">Downloads</a>';
  $LinkUpDir = '';
  $FileName = '';
  foreach ($Dirs as $val) if ($val != '') {
    $LinkUpDir .= $val;
    $LinkUp .= ' - <a href="index.php?mod=downloads&dir='. $LinkUpDir .'" class="menu">'. $val .'</a>';
    $LinkUpDir .= '/';
    $FileName = $val;
  }
  $dsp->NewContent(t('Downloads'), $LinkUp);

  // Download dialoge, if file is selected
  if (is_file($BaseDir.$_GET['dir'])  ) {
  	$row = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["download_stats"]} WHERE file = '{$_GET['dir']}' AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
  	if ($row['found']) $db->query("UPDATE {$config["tables"]["download_stats"]} SET hits = hits + 1 WHERE file = '{$_GET['dir']}' AND DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
  	else $db->query("INSERT INTO {$config["tables"]["download_stats"]} SET file = '{$_GET['dir']}', hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");

    header('Content-type: application/octetstream'); # Others: application/octet-stream # application/force-download
    header('Content-Disposition: attachment; filename="'. $FileName .'"');
    header("Content-Length: " .(string)(filesize($BaseDir.$_GET['dir'])));
    readfile($BaseDir.$_GET['dir']);
#    header('Location: http://'. $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']) . $BaseDir . $_GET['dir']);
    exit;


  // Display directory
  } else {

    // Display Dir-Info-Text from DB
    $row = $db->query_first("SELECT dirid, text, allow_upload FROM {$config['tables']['download_dirs']} WHERE name = '{$_GET['dir']}'");
    if (!$row['dirid']) {
      $db->query("INSERT INTO {$config['tables']['download_dirs']} SET name = '{$_GET['dir']}'");
      $row['dirid'] = $db->insert_id();
    }
    if ($row['text']) {
      $dsp->AddFieldSetStart(t('Ordner-Information'));
      $dsp->AddSingleRow($func->text2html($row['text']));
      $dsp->AddFieldSetEnd();
    }

    // Upload submittet file
    if ($_GET['step'] == 20 and $auth['type'] >= 2 or ($auth['login'] and $row['allow_upload'])) {
      $func->FileUpload('upload', $BaseDir.$_GET['dir']);
    }

    $dsp->AddFieldSetStart(t('Navigation: ') . $LinkUp);
    $FileList = array();
    $DLDesign = opendir($BaseDir.$_GET['dir']);
    while ($CurFile = readdir($DLDesign)) if ($CurFile != '.' and $CurFile != '..') $FileList[] = $CurFile;
    closedir($DLDesign);
    sort($FileList);

    foreach ($FileList as $CurFile) {
      $CreateTime = filectime($BaseDir.'/'.$CurFilePath);

      if ($_GET['dir']) $CurFilePath = $_GET['dir'] .'/'. $CurFile;
      else $CurFilePath = $CurFile;

      if ($CurFilePath != 'info.txt' and $CurFilePath != 'CVS') {

        // Dir
        if (is_dir($BaseDir.'/'.$CurFilePath)) {
          $dsp->AddSingleRow('<a href="index.php?mod=downloads&dir='. $CurFilePath .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_folder.gif" border="0" /> '. $CurFile .'</a>');

        // File
        } else {
          $Size = filesize($BaseDir.'/'.$CurFilePath);
          $dsp->AddSingleRow('<a href="index.php?mod=downloads&dir='. $CurFilePath .'" class="menu"><img src="design/'. $auth['design'] .'/images/downloads_file.gif" border="0" /> '. $CurFile .' ['. $func->FormatFileSize($Size) .']'.'</a>');
        }
      }
    }
    $dsp->AddFieldSetEnd();

    // File Upload Box
    if ($auth['type'] >= 2 or ($auth['login'] and $row['allow_upload'])) {
      $dsp->AddFieldSetStart(t('Datei hochladen'));
      $dsp->SetForm('index.php?mod=downloads&step=20', '', '', 'multipart/form-data');
      $dsp->AddFileSelectRow('upload', t('Datei'), '', '', '', 1);
      $dsp->AddFormSubmitRow('add');
      $dsp->AddFieldSetEnd();
    }

    // Comments
    if ($_GET['mf_step'] != 2 or $_GET['step'] != 10){
      include('inc/classes/class_mastercomment.php');
      new Mastercomment('downloads', $row['dirid']);
    }

    // Admin functions for dir
    if ($auth['type'] >= 2 and ($_GET['mf_step'] != 2 or $_GET['step'] == 10)) {
      $dsp->AddFieldSetStart(t('Ordner Text und Einstellungen editieren'));
      include_once('inc/classes/class_masterform.php');
      $mf = new masterform();

      $mf->AddField(t('Text'), 'text', '', LSCODE_BIG, FIELD_OPTIONAL);
      $mf->AddField(t('Benutzer-Upload erlauben?'), 'allow_upload', '', '', FIELD_OPTIONAL);
      if (!$_GET['dirid']) {
        $mf->AddFix('name', $_GET['dir']);
        $mf->AddFix('userid', $auth['userid']);
      }

      $mf->SendForm('index.php?mod=downloads&step=10&dir='. $_GET['dir'], 'download_dirs', 'dirid', $row['dirid']);
      $dsp->AddFieldSetEnd();
    }
  }
  $dsp->AddContent();


// Try to connect to FTP-Server
} elseif (!extension_loaded(ftp)) $func->error($lang["downloads"]["no_ftp_extension"], "");
else {

  session_register("downloads_dir");

  $server 		= $cfg['download_server'];
  $port 			= $cfg['download_port'];
  $loginuser 		= $cfg['download_username'];
  $loginpassword 	= $cfg['download_password'];
  $subdir			= $cfg['download_subdir'];

	//
	// Connect to FTP-Server and log in with data from config file
	//
	$connect = @ftp_connect($server, $port, "2");
	$login	 = @ftp_login($connect, $loginuser, $loginpassword);

	//
	// Debug Output
	//
	if($cfg['sys_showdebug_ftp'] == "1")
	{
		$debug[] = "FTP> Connect to " . $server . " on Port:" . $port;
		$debug[] = "FTP> " . $connect;
		$debug[] = "FTP> Login with " . $loginuser . ", " . $loginpassword;
		$debug[] = "FTP> Login (true=1): " . $login;
	}

	//
	// Check connection
	//
	if($connect != FALSE AND $login != FALSE)
	{
		//
		// Go up
		//
		if($_GET[go_dir] == "up")
		{
			//
			// Remove last element from array
			//
			array_pop($_SESSION['downloads_dir']);  
		}
		//
		// Go to a subdirectory
		//
		elseif($_GET[go_dir])
		{	
				
			//
			// Set $set_dir ( User defined subdirectorys )
			//
			if(count($_SESSION['downloads_dir']) > "0")
			{
				foreach($_SESSION['downloads_dir'] AS $dir_entry)
				{
					$set_dir .= "/" . $dir_entry;	
				}
			}
			
			//
			// Join dir
			//
			$join_dir = @ftp_chdir($connect, $subdir . $set_dir . "/" . $_GET[go_dir]);
			
			//
			// Check for success
			//
			if($join_dir == TRUE AND $_GET[go_dir] != "." AND $_GET[go_dir] != "..") 
			{
				//
				// Add folder to subdirectory array
				//
				$_SESSION['downloads_dir'][] = $_GET[go_dir]; 
			}
			
			//
			// Unset vars
			//
			unset($set_dir);
			unset($join_dir);
			
		} // Go to a subdirectory
	
		//
		// Set $set_dir ( User defined subdirectorys )
		//
		if(count($_SESSION['downloads_dir']) > "0")
		{
			foreach($_SESSION['downloads_dir'] AS $dir_entry)
			{
				$set_dir .= "/" . $dir_entry;	
			}
		}
		
		//
		// Join FTP-directory
		//
		if($subdir != "" OR $set_dir != "")
		{
			$join_dir = @ftp_chdir($connect, $subdir . $set_dir);
		}
	
		//
		// Read joined dir
		//
		$dir	 = ftp_pwd($connect); 
		
		//
		// Read content of joined dir
		//
		$content = ftp_rawlist($connect, $dir);	
		
			//
			// Debug Output 
			//
			if($cfg['sys_showdebug_ftp'] == "1")
			{
				$debug[] = "FTP> Set timeout ". $set_timeout;;
				$debug[] = "FTP> Join dir";
				$debug[] = "FTP> " . $join_dir;
				$debug[] = "FTP> Read dir";
				$debug[] = "FTP> " . $dir;
				$debug[] = "FTP> Read content";
			}
	
		//
		// Content set
		//
		if($content)
		{
			//
			// Foreach content
			//
			foreach($content AS $cur_line)
			{	
			
					//
					// Debug Output 
					//
					if($cfg['sys_showdebug_ftp'] == "1")
					{
						$debug[] = "FTP> " . $cur_line . HTML_NEWLINE;
					}
				
				//
				// Explode data 
				//	
				if(ereg("([-d])([rwxst-]{9}).* ([0-9]*) ([a-zA-Z]+[0-9: ]* [0-9]{2}:?[0-9]{2}) (.+)", $cur_line, $regs))
				{      
						
					//
					// Define type
					//
					if($regs[1] == "d")
					{
						$lineinfo['folder']	= TRUE;
					}
					else
					{
						$lineinfo['folder']	= FALSE;
					}
					
					//
					// Define size
					//
			        	$lineinfo['size']		= $regs[3];
			        	$lineinfo['name']		= $regs[5];
			        	
			        	//
					// < 1 Kb
					//
			        	if($lineinfo['size'] < "1024")
			        	{
			        		$lineinfo['rounded_size']	= "< 1 KB";
			        	}
			        	
			        	//
					// > 1 MB
					//
			        	elseif($lineinfo['size'] >= "1024000")
			        	{
			        		$lineinfo['rounded_size']	= round($lineinfo['size'] / "1024" / "1000","2") . " MB";
			        	}
			        	
			        	//
					// 1 KB < AND < 1 Mb
					//
			        	else
			        	{
			        		$lineinfo['rounded_size']	= round($lineinfo['size'] / "1024" ,"2") . " KB";
			        	}
			        	
			   		//
					// Dateityp
					//
					$lineinfo['type']		= explode(".",$lineinfo['name']);
					if(count($lineinfo['type']) > "1")
					{
						$lineinfo['datatype']	= $lineinfo['type'][count($lineinfo['type'])-1] . "-Datei";
					}
					
				} // Explode data
		
				//
				// Add file to template
				//
				if($lineinfo['size'] > "0" AND $lineinfo['folder'] != TRUE)
				{
					$loginpassword = str_replace("@", "(at)", $loginpassword);

					if($loginuser == "" AND $loginpassword == "")
					{
						$account = "";
					}
					else
					{
						$account = $loginuser . ":" . $loginpassword;
					}

					$templ['downloads']['show']['case']['control']['rows'] .= "<tr><td width=\"5%\"><a href=\"ftp://" . $account . "@" . $server . ":" . $port . $subdir . $set_dir . "/" . $lineinfo['name'] . "\" class=\"menu\" target=\"_blank\"><img src=\"design/" . $config['lansuite']['default_design'] . "/images/downloads_file.gif\" border=\"0\"></td><td width=\"45%\"><a href=\"ftp://" . $account . "@" . $server . ":" . $port . "/" .  $subdir . $set_dir . "/" . $lineinfo['name'] . "\" class=\"menu\" target=\"_blank\">" . $lineinfo['name'] . "</a></td><td width=\"35%\">" . $lineinfo['datatype'] ."</td><td width=\"15%\">" . $lineinfo['rounded_size'] . " </td></tr>";
				
				} // Add file to template
				
				//
				// Add folder to template
				//
				elseif($lineinfo['folder'] == TRUE AND $lineinfo['name'] != "." AND $lineinfo['name'] != "..")
				{
		
					$linkurl = rawurlencode($lineinfo['name']);
					$templ['downloads']['show']['case']['control']['rows'] .= "<tr><td width=\"5%\"><a href=\"index.php?mod=downloads&action=show&go_dir=" . $linkurl . "\" class=\"menu\" ><img src=\"design/" . $config['lansuite']['default_design'] . "/images/downloads_folder.gif\" border=\"0\"></a></td><td width=\"48%\"><a href=\"index.php?mod=downloads&action=show&go_dir=" . $linkurl . "\" class=\"menu\" >" . $lineinfo['name'] . "</a></td><td width=\"35%\"> Ordner </td><td width=\"12%\"></td></tr>";
//					$templ['downloads']['show']['case']['control']['rows'] .= "<tr><td width=\"5%\"><a href=\"index.php?mod=downloads&action=show&go_dir=" . $lineinfo['name'] . "\" class=\"menu\" ><img src=\"design/" . $config['lansuite']['default_design'] . "/images/downloads_folder.gif\" border=\"0\"></a></td><td width=\"48%\"><a href=\"index.php?mod=downloads&action=show&go_dir=" . $lineinfo['name'] . "\" class=\"menu\" >" . $lineinfo['name'] . "</a></td><td width=\"35%\"> Ordner </td><td width=\"12%\"></td></tr>";
				
				} // Add folder to template
				
				//
				// Unset lineinfo
				//
				unset($lineinfo);
				
			} // Foreach content
			
		} // Content set
		
		//
		// content not set
		//
		else
		{
			$templ['downloads']['show']['case']['control']['rows']			= "<tr><td>Keine Inhalte vorhanden</td></tr>";
		}
		
		//
		// Is a subdirectory set?
		//
		if(count($_SESSION['downloads_dir']) > "0")
		{
			//
			// Define path template vars
			//
			$templ['downloads']['show']['case']['info']['path']	= $dir . "/";
			$templ['downloads']['show']['case']['control']['goup']	= "index.php?mod=downloads&action=show&go_dir=up";
			
			//
			// Output path template 
			//
			$templ['downloads']['show']['case']['control']['path'] = $dsp->FetchModTpl("downloads", "show_path");
			
		} // Is a subdirectory set?
		
		//
		// Output template
		//
		$dsp->NewContent($lang["downloads"]["show_caption"], $lang["downloads"]["show_subcaption"]);
		$dsp->AddSingleRow($dsp->FetchModTpl("downloads", "show_case"));
		$dsp->AddContent();
		
		//
		// Close connection
		//
		$quit = ftp_quit($connect);
		
			//
			// Debug Output 
			//
			if($cfg['sys_showdebug_ftp'] == "1")
			{
				$debug[] = "FTP> Quit connection " . $connect. HTML_NEWLINE;
			}
		
	} // Check connection
	
	//
	// No connection
	//
	else
	{
		
		//
		// Error
		//
		$func->error($lang["downloads"]["show_err_noconnection"], "");
		
		//
		// Log event
		//
		$func->log_event(str_replace("%SERVER%", $server, str_replace("%PORT%", $port, $lang["downloads"]["show_noconnection_log"])), "2");
			
	} // No connection
	
} // FTP-Support is avaiable and FTP-Server IP = Webserver IP
?>
