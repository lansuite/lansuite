<?php
include_once("modules/install/class_import.php");
$import = New Import();

class Install {

  function IsWriteableRec($dir) {
    $ret = '';
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false) if ($file != '.' and $file != '..' and $file != '.svn') {
        if (!is_writable($dir .'/'. $file)) $ret .= $dir .'/'. $file .'<br>';
        if (is_dir($dir .'/'. $file)) $ret .= $this->IsWriteableRec($dir .'/'. $file);
      }
      closedir($dh);
    }
    return $ret;
  }

  // Get Config (/inc/base/config.php) an change it
  function WriteConfig($values = NULL) {
      global $config;

      $conf = @file("inc/base/config.php");

      $i = 1;
      while ($row = $conf[$i]) {
          // Get Next Element if this is a Comment or a "Header"
          if (stristr($row, ";")) {
              $i++;
              continue;
          }
          if (stristr($row, "[")) {
              $setting['category'] = substr(trim($row), 1, -1);
              $i++;
              continue;
          }

          $setting['name'] = trim(strtok($row, "="));

          $tabs = "";
          for ($z = 0; $z < (4 - (strlen($setting['name']) / 8)); $z++) $tabs .= "\t";
          if ($setting['name']) $conf[$i] = $setting['name'] . $tabs ."= \"". $config[$setting['category']][$setting['name']] ."\"\r\n";


          $i++;
      } // END while( $row = $file[$i] )


      // Write new settings to the config.php file
      // Have we opened the file??? If not, tell the user ...
      if($fh = @fopen("inc/base/config.php", "w")) {
          foreach($conf AS $row) {
              @fwrite($fh, $row, strlen($row));
          }
          @fclose( $fh );
          return 1;
      } else return 0;
  }


  // Connect to DB and create Database, if not exist
  function TryCreateDB($createnew = NULL){
    global $config;
    
    $link_id = mysql_connect($config['database']['server'], $config['database']['user'], $config['database']['passwd']);
    if (!$link_id) return 0;
    else {

      // Try to select DB
      if (@mysql_select_db($config['database']['database'], $link_id)) {
        // If User wants to rewrite all tables, drop databse. It will be created anew in the next step
        if (!$_GET["quest"] and $createnew and $_GET["step"] == 3) $this->DeleteAllTables();
        $ret_val = 1;

      } else {   
        // Try to create DB
        @mysql_query("/*!40101 SET NAMES utf8_general_ci */;", $link_id);
        $query_id = @mysql_query('CREATE DATABASE '. $config['database']['database'] .' CHARACTER SET utf8', $link_id);
        if ($query_id) $ret_val = 3; else $ret_val = 2;
      }
    }
    mysql_close($link_id);
    
    // Return-Values:
    // 0 = Server not available
    // 1 = DB already exists
    // 2 = Create failed (i.e. insufficient rights)
    // 3 = Create successe
    return $ret_val;
  }

  // Creates a DB-table using the file $table, located in the mod_settings-directory of the module $mod
  function WriteTableFromXMLFile($mod, $rewrite = NULL){
    global $import;

    $import->GetImportHeader("modules/$mod/mod_settings/db.xml");
    $import->ImportXML($rewrite);
    if ($rewrite AND $mod == 'install') {
      $this->InsertModules($rewrite);
      $this->InsertMenus($rewrite);
    }
  }


  // Scans 'install/db_skeleton/' for non-existand tables and creates them
  // Puts the results to the screen, by using $dsp->AddSingleRow for each table, if $display_to_screen = 1
  function CreateNewTables($display_to_screen) {
      global $dsp, $config, $db, $import;

      $tablecreate = Array("anz" => 0, "created" => 0, "exist" => 0, "failed" => "");
      $dsp->AddSingleRow("<b>". t('Tabellen erstellen') ."</b>");

      #$db->query("CREATE TABLE IF NOT EXISTS {$config["database"]["prefix"]}table_names (name varchar(80) NOT NULL default '', PRIMARY KEY(name)) TYPE = MyISAM CHARACTER SET utf8");
      #$db->query("REPLACE INTO {$config["database"]["prefix"]}table_names SET name = 'table_names'");

      // Delete references, if table exists, for they will be recreated in WriteTableFromXMLFile
      if (in_array($config['database']['prefix'] .'references', $import->installed_tables)) $db->qry('TRUNCATE TABLE %prefix%references');

      if (is_dir("modules")) {
      // Do install-mod first! (for translations-table must exist)
        if (is_dir("modules/install/mod_settings")) {
          // Try to find DB-XML-File
          if (file_exists("modules/install/mod_settings/db.xml")){
            $this->WriteTableFromXMLFile('install');
            if ($display_to_screen) $dsp->AddDoubleRow("Modul 'install'", "[<a href=\"index.php?mod=install&action=db&step=7&module=install&quest=1\">".t('zurücksetzen')."</a>]");
          }
        }

        $modules_dir = opendir("modules/");
        while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND $module != "install" AND is_dir("modules/$module")) {
        
          if (is_dir("modules/$module/mod_settings")) {
            // Try to find DB-XML-File
            if (file_exists("modules/$module/mod_settings/db.xml")){
              $this->WriteTableFromXMLFile($module);
              if ($display_to_screen) $dsp->AddDoubleRow("Modul '$module'", "[<a href=\"index.php?mod=install&action=db&step=7&module=$module&quest=1\">".t('zurücksetzen')."</a>]");
            }
          }
        }
        closedir($modules_dir);
      }

      if ($display_to_screen) $dsp->AddDoubleRow("<b>". t('Alle Tabellen') ."</b>", "[<a href=\"index.php?mod=install&action=db&step=3&quest=1\">".t('zurücksetzen')."</a>]");

      return $tablecreate;
  }


  // Insert Setting-Entrys in DB, if not exist
  function InsertSettings($module) {
    global $db, $config, $xml, $func;
    
    $ConfigFileName = "modules/$module/mod_settings/config.xml";
    if (file_exists($ConfigFileName)) {
      $handle = fopen ($ConfigFileName, "r");
      $xml_file = fread ($handle, filesize ($ConfigFileName));
      fclose ($handle);
      
      $SettingList = array();
      
      $xml_config = $xml->get_tag_content('config', $xml_file);
      // Read types
      $xml_types = $xml->get_tag_content_array('typedefinition', $xml_config);
      if ($xml_types) while ($xml_type = array_shift($xml_types)) {
        $xml_head = $xml->get_tag_content('head', $xml_type);
        $name = $xml->get_tag_content('name', $xml_head);
        $db->query("DELETE FROM {$config["database"]["prefix"]}config_selections WHERE cfg_key = '". $func->escape_sql($name) ."'");
        
        $xml_entries = $xml->get_tag_content_array('entry', $xml_type);
        if ($xml_entries) while ($xml_entry = array_shift($xml_entries)) {
        $value = $xml->get_tag_content('value', $xml_entry);
        $description = $xml->get_tag_content('description', $xml_entry);              
        
        if ($name != '' and $description != '') $db->query("INSERT INTO {$config["database"]["prefix"]}config_selections SET
          cfg_key = '". $func->escape_sql($name) ."',
          cfg_value = '". $func->escape_sql($value) ."',
          cfg_display = '". $func->escape_sql($description) ."'
          ");
        }
      }

      // Read settings
      $xml_groups = $xml->get_tag_content_array('group', $xml_config);
      if ($xml_groups) while ($xml_group = array_shift($xml_groups)) {
        $xml_head = $xml->get_tag_content('head', $xml_group);
        $group = $xml->get_tag_content('name', $xml_head);
        
        $xml_items = $xml->get_tag_content_array('item', $xml_group);
        if ($xml_items) while ($xml_item = array_shift($xml_items)) {
          $name = $xml->get_tag_content('name', $xml_item);
          $type = $xml->get_tag_content('type', $xml_item);
          $default = $xml->get_tag_content('default', $xml_item);
          $description = $xml->get_tag_content('description', $xml_item);
          array_push($SettingList, $name);
          
          // Insert into DB, if not exists
          $found = $db->query_first("SELECT cfg_key FROM {$config["database"]["prefix"]}config WHERE cfg_key = '$name'");
          if (!$found['cfg_key']) $db->query("INSERT INTO {$config["database"]["prefix"]}config SET
            cfg_key = '". $func->escape_sql($name) ."',
            cfg_value = '". $func->escape_sql($default) ."',
            cfg_type = '". $func->escape_sql($type) ."',
            cfg_group = '". $func->escape_sql($group) ."',
            cfg_desc = '". $func->escape_sql($description) ."',
            cfg_module = '$module'
            ");
        }
      }
      
      // Delete Settings from DB, which are no longer in the modules config.sql
      $settings_db = $db->query("SELECT cfg_key FROM {$config["database"]["prefix"]}config WHERE (cfg_module = '$module')");
      while ($setting_db = $db->fetch_array($settings_db)) {
        if (!in_array($setting_db["cfg_key"], $SettingList)) $db->query("DELETE FROM {$config["database"]["prefix"]}config WHERE cfg_key = '{$setting_db["cfg_key"]}'");
      }
    }
  }


  // Insert PLZ-Entrys in DB, if not exist
  function InsertPLZs() {
      global $db, $config;

      $return_val = 1;
      $find = $db->query("SELECT * FROM {$config["tables"]["locations"]}");
      if ($db->num_rows($find) == 0) {
          $return_val = 2;
          $file = "modules/install/db_insert_locations.php";
//          $file = "modules/install/db_insert_locations.sql";
          if (file_exists($file)) {
/*
              $fp = fopen($file, "r");
              $contents = fread($fp, filesize($file));
              fclose($fp);
              $querys = explode(";", trim($contents));
*/
              include_once($file);
              foreach ($querys as $val) if ($val) { 
                  if (!$db->query("REPLACE INTO {$config["database"]["prefix"]}locations (plz, breite, laenge) VALUES ". $val)) $return_val = 0;
              }
          }
      }
      $db->free_result($find);

      return $return_val;
      // 0 = At least one create failed
      // 1 = Alreday existing
      // 2 = Create success
  }


  // Auto-Load Modules from XML-Files
  function InsertModules($rewrite = false) {
      global $db, $config, $xml, $func;

      // Tabelle Modules leeren um Module zu deinstallieren
      if($_GET["action"] == "wizard"){
          $db->query("TRUNCATE TABLE {$config["tables"]["modules"]}");
      }
      
      $mod_list = array();
      $modules_dir = opendir("modules/");
      while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {

          $file = "modules/$module/mod_settings/module.xml";
          if (file_exists($file)) {
              $handle = fopen ($file, "r");
              $xml_file = fread ($handle, filesize ($file));
              fclose ($handle);

              array_push($mod_list, $module);

              $name = $xml->get_tag_content("name", $xml_file);
              $caption = $xml->get_tag_content("caption", $xml_file);
              $description = $xml->get_tag_content("description", $xml_file);
              $author = $xml->get_tag_content("author", $xml_file);
              $email = $xml->get_tag_content("email", $xml_file);
              $active = $xml->get_tag_content("active", $xml_file);
              $changeable = $xml->get_tag_content("changeable", $xml_file);
              $version = $xml->get_tag_content("version", $xml_file);
              $state = $xml->get_tag_content("state", $xml_file);

              $mod_found = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["modules"]} WHERE name = '$module'");

              $this->InsertSettings($module);

              if ($name) {
                  if (!$mod_found["found"]) $db->query_first("REPLACE INTO {$config["tables"]["modules"]} SET
                      name='". $func->escape_sql($name) ."',
                      caption='". $func->escape_sql($caption) ."',
                      description='". $func->escape_sql($description) ."',
                      author='". $func->escape_sql($author) ."',
                      email='". $func->escape_sql($email) ."',
                      active='". $func->escape_sql($active) ."',
                      changeable='". $func->escape_sql($changeable) ."',
                      version='". $func->escape_sql($version) ."',
                      state='". $func->escape_sql($state) ."'
                      ");
                  elseif ($rewrite) $db->query_first("REPLACE INTO {$config["tables"]["modules"]} SET
                      name='". $func->escape_sql($name) ."',
                      caption='". $func->escape_sql($caption) ."',
                      description='". $func->escape_sql($description) ."',
                      author='". $func->escape_sql($author) ."',
                      email='". $func->escape_sql($email) ."',
                      changeable='". $func->escape_sql($changeable) ."',
                      version='". $func->escape_sql($version) ."',
                      state='". $func->escape_sql($state) ."'
                      ");
              }
          }
      }

      // Delete non-existend Modules from DB
      $mods = $db->query("SELECT name FROM {$config["tables"]["modules"]}");
      while($row = $db->fetch_array($mods)) {
          if (!in_array($row["name"], $mod_list)) $db->query("DELETE FROM {$config["tables"]["modules"]} WHERE name = '{$row["name"]}'");
      }
      $db->free_result($mods);
  }


  // Auto-Load Menuentries from XML-Files
  function InsertMenus($rewrite = false) {
    global $db, $config, $xml;

    $menubox = $db->qry_first('SELECT boxid FROM %prefix%boxes WHERE source = \'menu\' AND active = 1');

    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {
      $file = "modules/$module/mod_settings/menu.xml";
      if (file_exists($file)) {
        $menu_found = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["menu"]} WHERE module = '$module'");

        $i = 0;
        if ($rewrite or (!$menu_found["found"])) {
          $db->query_first("DELETE FROM {$config["tables"]["menu"]} WHERE module = '$module'");

          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          $menu = $xml->get_tag_content("menu", $xml_file);

          $main_pos = $xml->get_tag_content("pos", $menu);
          $entrys = $xml->get_tag_content_array("entry", $menu);

          foreach ($entrys as $entry) {
            $action = $xml->get_tag_content("action", $entry);
            $file = $xml->get_tag_content("file", $entry);
            $caption = $xml->get_tag_content("caption", $entry);
            $hint = $xml->get_tag_content("hint", $entry);
            $link = $xml->get_tag_content("link", $entry);
            $requirement = $xml->get_tag_content("requirement", $entry);
            $level = $xml->get_tag_content("level", $entry);
            $needed_config = $xml->get_tag_content("needed_config", $entry);

            if ($file == "") $file = $action;
            if (!$level) $level = 0;
            if (!$requirement) $requirement = 0;

            ($level == 0)? $pos = $main_pos : $pos = $i;
            $i++;

            $db->query_first("INSERT INTO {$config["tables"]["menu"]} SET
              module='$module',
              action='$action',
              file='$file',
              caption='$caption',
              hint='$hint',
              link='$link',
              requirement=$requirement,
              level=$level,
              pos=$pos,
              needed_config='$needed_config',
              boxid='{$menubox['boxid']}'
              ");
          }
        }
      }
    }
  }


  function InsertTranslations() {
    global $translation;

    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {
      $translation->xml_write_file_to_db($module);
    }
    $translation->xml_write_file_to_db('DB');
    $translation->xml_write_file_to_db('System');
  }


  // System prüfen
  function envcheck() {
    global $db, $dsp, $config, $func;

    $continue = 1;

    // Environment Check
    $ok = "<span class=\"okay\">".t('erfolgreich')."</span>" . HTML_NEWLINE;
    $failed = "<span class=\"error\">".t('fehlgeschlagen')."</span>" . HTML_NEWLINE;
    $warning = "<span class=\"warning\">".t('bedenkliche Einstellung')."</span>" . HTML_NEWLINE;
    $not_possible = "<span class=\"warning\">".t('Leider nicht möglich')."</span>" . HTML_NEWLINE;

    // Display System-Variables
    $mysql_version = @mysql_get_server_info();
    if (!$mysql_version) $mysql_version = t('Unbekannt');
    $SysInfo = "<table width=\"99%\">"
        ."<tr><td class=\"row_value\">PHP-Version:</td><td class=\"row_value\">". phpversion() ."</td></tr>"
        ."<tr><td class=\"row_value\">MySQL-Version:</td><td class=\"row_value\">$mysql_version</td></tr>"
        ."<tr><td class=\"row_value\">Max. Script-Execution-Time:</td><td class=\"row_value\">". ini_get('max_execution_time') ." Sec.</td></tr>"
        ."<tr><td class=\"row_value\">Max. Data-Input-Zeit:</td><td class=\"row_value\">". ini_get('max_input_time') ." Sec.</td></tr>"
        ."<tr><td class=\"row_value\">Memory Limit:</td><td class=\"row_value\">". ini_get('memory_limit') ." MB</td></tr>"
        ."<tr><td class=\"row_value\">Max. Post-Form Size:</td><td class=\"row_value\">". (float)ini_get('post_max_size') ." MB</td></tr>";
    if (function_exists('disk_total_space') and function_exists('disk_free_space')) $SysInfo .= "<tr><td class=\"row_value\">Free space:</td><td class=\"row_value\">". $func->FormatFileSize(disk_free_space('.')) .' / '. $func->FormatFileSize(disk_total_space('.')) .'</td></tr>';
    $SysInfo .= "</table>";
    $dsp->AddDoubleRow("System-Info", $SysInfo);

    // PHP-Version
    if (version_compare(phpversion(), "4.1.2") >= 0) $phpv_check = $ok;
    else $phpv_check = $failed . t('Auf Ihrem System wurde die PHP-Version %1 gefunden.  Lansuite benötigt mindestens PHP Version 4.3.0. Sie können zwar die Installation fortsetzen, allerdings kann keinerlei Garantie auf die ordnungsgemäße Funktionsweise gegeben werden. Laden und installieren Sie sich eine aktuellere Version von <a href=\'http://www.php.net\' target=\'_blank\'>www.php.net</a>.', phpversion());
    $dsp->AddDoubleRow("PHP Version", $phpv_check);

    // MySQL installed?
    if (extension_loaded("mysql")) $mysql_check = $ok;
    else {
        $mysql_check = $failed . t('Die MySQL-Erweiterung ist in PHP nicht geladen. Diese wird benötigt um auf die Datenbank zuzugreifen. Bevor keine Datenbank verfügbar ist, kann Lansuite nicht installiert werden. Den MySQL-Server gibt es unter <a href=\'http://www.mysql.com\' target=\'_blank\'>www.mysql.com</a> zum Download.');
        $continue = 0;
    }
    $dsp->AddDoubleRow("MySQL", $mysql_check);

    // Register Globals
    if (ini_get('register_globals') == FALSE) $rg_check = $ok;
    else $rg_check = $warning . t('Auf Ihrem System ist die PHP-Einstellung <b>register_globals</b> auf <b>On</b> gesetzt. Dies kann unter Umständen ein Sicherheitsrisiko darstellen, wenn auch kein großes (siehe dazu: <a href=\'http://www.php.net/manual/de/security.globals.php\' target=\'_blank\'>www.php.net</a>). Sie sollten in Ihrer <b>PHP.ini</b> die Option <b>register_globals</b> auf <b>Off</b> setzen! Bitte vergessen Sie nicht, Ihren Webserver nach dieser Änderung neu zu starten.');
    $dsp->AddDoubleRow("Register Globals", $rg_check);

    // Test Safe-Mode
    if (!ini_get("safe_mode")) $safe_mode = $ok;
    else $safe_mode = $not_possible . t('Auf Ihrem System ist die PHP-Einstellung <b>safe_mode</b> auf <b>On</b> gesetzt. safe_mode ist dazu gedacht, einige Systemfunktionen auf dem Server zu sperren um Angriffe zu verhindern (siehe dazu: <a href=\'http://de2.php.net/features.safe-mode\' target=\'_blank\'>www.php.net</a>). Doch leider benötigen einige Lansuite-Module (speziell: LansinTV, Serverstatistiken oder das Server-Modul) Zugriff auf genau diese Funktionen. Sie sollten daher in Ihrer <b>PHP.ini</b> die Option <b>safe_mode</b> auf <b>Off</b> setzen! <br /> Außer bei oben genannten Modulen, kann es bei aktiviertem safe_mode auch zu Problemen bei dem Generieren von Buttons, wie dem am Ende dieser Seite kommen.');
    $dsp->AddDoubleRow("Safe Mode", $safe_mode);

    // Magic Quotes
    if (get_magic_quotes_gpc()){
        $mq_check = $ok;
        $config["environment"]["mq"] = 1;
    } else {
        $mq_check = $not_possible . t('Auf Ihrem System ist die PHP-Einstellung <b>magic_quotes_gpc</b> auf <b>Off</b> gesetzt. Um mit Lansuite arbeiten zu können muss diese Option aktiviert sein. Ändern Sie bitte in Ihrer <b>PHP.ini</b> die Option <b>magic_quotes_gpc </b> auf <b> On </b>! Bitte vergessen Sie nicht, Ihren Webserver nach dieser Änderung neu zu starten.');
        $config["environment"]["mq"] = 0;
    }
    $dsp->AddDoubleRow("Magic Quotes", $mq_check);

    // GD-Lib
    if (extension_loaded ('gd')){
        if (function_exists("gd_info")) {
            $GD = gd_info();
            if (!strstr($GD["GD Version"], "2.0")) $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> nur in der Version GD1  gefunden. Damit ist die Qualität der erzeugten Bilder wesentlich schlechter. Es wird deshalb empfohlen GD2 zu benutzen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
            elseif (!$GD["FreeType Support"]) $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> ohne Free-Type Support gefunden. Dadurch werden die Schriftarten in Grafiken (z.b. im Turnierbaum) nicht sehr schön dargestellt. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
            else $gd_check = $ok;
            $gd_check .= '<table>';
            foreach ($GD as $key => $val) $gd_check .= '<tr><td class="content">'. $key .'</td><td class="content">'. $val .'</td></tr>';
            $gd_check .= '</table>';
            $config["environment"]["gd"] = 1;
        } else $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> nur in der Version GD1  gefunden. Damit ist die Qualität der erzeugten Bilder wesentlich schlechter. Es wird deshalb empfohlen GD2 zu benutzen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
    } else {
        $gd_check = $failed . t('Auf Ihrem System konnte das PHP-Modul <b>GD-Library</b> nicht gefunden werden. Durch diese Programmierbibliothek werden in Lansuite Grafiken, wie z.B. Turnierbäume generiert. Ab PHP Version 4.3.0 ist die GD bereits in PHP enthalten. Sollten Sie PHP 4.3.0 installiert haben und diese Meldung dennoch erhalten, überprüfen Sie, ob das GD-Modul evtl. deaktiviert ist. In PHP Version 4.2.3 ist die GD nicht enthalten. Wenn Sie diese Version benutzen muss GD 2.0 separat heruntergeladen, installiert und in PHP einkompiliert werden. Sollten Sie Windows und PHP 4.2.3 benutzen, empfehlen wir auf PHP 4.3.0 umzusteigen, da Sie sich auf diese Weise viel Arbeit sparen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie erhebliche Einschränkungen im Gebrauch machen müssen.');
        $config["environment"]["gd"] = 0;
    }
    $dsp->AddDoubleRow("GD Library", $gd_check);

    // SNMP-Lib
    if (extension_loaded('snmp')){
        $snmp_check = $ok;
        $config["environment"]["snmp"] = 1;
    } else {
        $snmp_check = $not_possible . t('Auf Ihrem System konnte das PHP-Modul <b>SNMP-Library</b> nicht gefunden werden. SNMP ermöglicht es, auf Netzwerkdevices zuzugreifen, um detaillierte Informatioen über diese zu liefern. Ohne diese Bibliothek kann das Lansuite-Modul <b> NOC </b> (Netzwerküberwachung) nicht arbeiten. Das Modul NOC wird <b>automatisch deaktiviert</b>.');
        $config["environment"]["snmp"] = 0;
    }
    $dsp->AddDoubleRow("SNMP Library", $snmp_check);

    // FTP-Lib
    if (extension_loaded('ftp')){
        $ftp_check = $ok;
        $config["environment"]["ftp"] = 1;
    } else {
        $ftp_check = $not_possible . t('Auf Ihrem System konnte das PHP-Modul <b>FTP-Library</b> nicht gefunden werden. Dies kann zur Folge haben, dass Module, die auf FTP-Server zugreifen (z.B. Downloadmodul, Servermodul), nicht korrekt funktionieren.');
        $config["environment"]["ftp"] = 0;
    }
    $dsp->AddDoubleRow("FTP Library", $ftp_check);

    // config.php Rights
    $lansuite_conf = "inc/base/config.php";
    if (!file_exists($lansuite_conf)) $cfgfile_check = $failed . t('Die Datei <b>config.php</b> befindet sich <b>nicht</b> im Lansuite-Verzeichnis <b> inc/base/ </b>. Bitte überprüfen Sie die Datei auf korrekte Groß- und Kleinschreibung.');
    elseif (!is_writable($lansuite_conf)) $cfgfile_check = $failed . t('Die Datei <b>config.php</b> im Lansuite-Verzeichnis <b> inc/base/ </b> muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen.');
    else $cfgfile_check = $ok;
    $dsp->AddDoubleRow(t('Schreibrechte auf die Konfigurationsdatei'), $cfgfile_check);
    if ($cfgfile_check != $ok) $continue = 0;

    // Server statistic
    $config["server_stats"]["status"] = 0;
    $config["server_stats"]["uptime"] = 0;
    $config["server_stats"]["cpuinfo"] = 0;
    $config["server_stats"]["meminfo"] = 0;
    $config["server_stats"]["loadavg"] = 0;
    $config["server_stats"]["ifconfig"] = 0;
    $config["server_stats"]["ls_getinfo"] = 0;

    // Testing Safe-Mode and execution of system-programs
    if (!ini_get("safe_mode")) {
      if (stristr(strtolower($_SERVER['SERVER_SOFTWARE']) , "win") == ""){
        if (@shell_exec("cat /proc/uptime") == ""){
          $env_stats .= "<strong>/proc/uptime</strong>" . HTML_NEWLINE;
          $config["server_stats"]["uptime"] = 0;
        } else $config["server_stats"]["uptime"] = 1;

        if (@shell_exec("/sbin/ifconfig") == "" and @shell_exec("/usr/sbin/ifconfig") == ""){
          $env_stats .= "<strong>ifconfig</strong>" . HTML_NEWLINE;
          $config["server_stats"]["ifconfig"] = 0;
        } else $config["server_stats"]["ifconfig"] = 1;

        if (@shell_exec("cat /proc/loadavg") == ""){
          $env_stats .= "<strong>/proc/loadavg</strong>" . HTML_NEWLINE;
          $config["server_stats"]["loadavg"] = 0;
        } else $config["server_stats"]["loadavg"] = 1;

        if (@shell_exec("cat /proc/cpuinfo") == ""){
          $env_stats .= "<strong>/proc/cpuinfo</strong>" . HTML_NEWLINE;
          $config["server_stats"]["cpuinfo"] = 0;
        } else $config["server_stats"]["cpuinfo"] = 1;  

        if (@shell_exec("cat /proc/meminfo") == ""){
          $env_stats .= "<strong>/proc/meminfo</strong>" . HTML_NEWLINE;
          $config["server_stats"]["meminfo"] = 0;
        } else $config["server_stats"]["meminfo"] = 0;

        if ($env_stats == "") $server_stats = $ok;
        else $server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, t('Auf ihrem System leider nicht möglich. Der Befehl oder die Datei HTML_NEWLINE{FEHLER} wurde nicht gefunden. Evtl. sind nur die Berechtigungen der Datei nicht ausreichend gesetzt.'));

        $config["server_stats"]["status"] = 1;
      } else {
        system("modules\stats\ls_getinfo.exe", $status);
        if ($status == 0){
          $config["server_stats"]["status"] = 1;
          $config["server_stats"]["ls_getinfo"] = 1;
          $server_stats = $ok;
        } else {
          $env_stats = "<strong>modules/stats/ls_getinfo.exe</strong>" . HTML_NEWLINE;
          $server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, t('Auf ihrem System leider nicht möglich. Der Befehl oder die Datei HTML_NEWLINE{FEHLER} wurde nicht gefunden. Evtl. sind nur die Berechtigungen der Datei nicht ausreichend gesetzt.'));
          $config["server_stats"]["status"] = 0;
        }
      }
      $dsp->AddDoubleRow("Server Stats", $server_stats);
    }

    // Ext_inc Rights
    $ext_inc = "ext_inc";
    if (!file_exists($ext_inc)) $ext_inc_check = $failed . t('Der Ordner <b>ext_inc</b> existiert <b>nicht</b> im Lansuite-Verzeichnis. Bitte überprüfen Sie den Pfad auf korrekte Groß- und Kleinschreibung. Legen Sie den Ordner gegebenfalls bitte selbst an.');
    else {
      $ret = $this->IsWriteableRec($ext_inc);
      if ($ret != '') $ext_inc_check = $failed . t('In den Ordner <b>ext_inc</b> und alle seine Unterordner muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen. Die folgenden Dateien besitzten noch keinen Schreibzugriff:'). '<br><b>'. $ret .'<b>';
      else $ext_inc_check = $ok;
    }
    $dsp->AddDoubleRow(t('Schreibrechte im Ordner \'ext_inc\''), $ext_inc_check);

    // Debug Backtrace
    if (function_exists('debug_backtrace')) $debug_bt_check = $ok;
    else $debug_bt_check = $warning . t('Die Funktion "Debug Backtrace" ist auf deinem System nicht vorhanden. Diese wird jedoch benötigt, um Übersetzungs-Texte einem bestimmten Modul zuzuordnen. Solange du lansuite nur in Deutsch verwenden willst, sollte dies keine Auswirkung haben');
    $dsp->AddDoubleRow('Debug Backtrace', $debug_bt_check);

    // Error Reporting
    if (error_reporting() <= (E_ALL ^ E_NOTICE)) $errreport_check = $ok;
    else $errreport_check = $warning . t('In Ihrer php.ini ist \'error_reporting\' so konfiguriert, dass auch unwichtige Fehlermeldungen angezeigt werden. Dies kann dazu führen, dass störende Fehlermeldungen in Lansuite auftauchen. Wir empfehlen diese Einstellung auf \'E_ALL ^ E_NOTICE\' zu ändern. In dieser Einstellung werden dann nur noch Fehler angezeigt, welche die Lauffähigkeit des Skriptes beeinträchtigen.');
    $dsp->AddDoubleRow("Error Reporting", $errreport_check);

            // Session Use Only Cookies
            if (ini_get('session.use_only_cookies')) $only_cookies_check = $ok;
            else $only_cookies_check = $warning . t('Es wird empfohlen session.use_only_cookies in der php.ini auf 1 zu setzen! Dies verhindert, dass Session-IDs in der URL angezeigt werden. Wenn dies nicht verhindert wird, können unvorsichtige Benutzer, deren Browser keine Cookies zulassen, durch weiterleiten der URL an Dritte ihre Session preisgeben, was einer weitergabe des Passwortes gleichkommt.');
            $dsp->AddDoubleRow("Sesssion.use_only_cookies", $only_cookies_check);

    // Get Operating System
    $software_arr =  preg_split('/\s/', $_SERVER['SERVER_SOFTWARE'], 0);
    $environment_os =  preg_replace('/\(|\)/', "", $software_arr[1]);
    $config["environment"]["os"] = $environment_os;

    // Get Directory
    $config["environment"]["dir"] = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'] - 1, "index.php"));

    // Set Configs
    $this->WriteConfig();

    // Check MySQL-Config
    if ($db->success) {
      $mysql_check = '';
      $res = $db->qry('SHOW variables WHERE Variable_name LIKE "key_buffer_size"');
      while ($row = $db->fetch_array($res)) {
        $mysql_check .= $row[0] .' = '. $row[1] .'<br />';
      }
      $db->free_result($res);
      $res = $db->qry('SHOW status WHERE Variable_name LIKE "Key_blocks%"');
      while ($row = $db->fetch_array($res)) {
        $mysql_check .= $row[0] .' = '. $row[1] .'<br />';
      }
      $db->free_result($res);
      $dsp->AddDoubleRow("MySQL-Config", $mysql_check);
    }

    return $continue;
  }

  // Scans all db.xml-files and deletes all tables listed in them
  // This meens lansuite is not able to clean up tables, which changed their name during versions
  // But this is much safer than DROP DATABASE, for this clean methode would drop other web-systems using the same DB table, too
  function DeleteAllTables () {
    global $import, $xml, $db;
  
    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {
      $file = "modules/$module/mod_settings/db.xml";

      if (file_exists($file)) {
        $import->GetImportHeader($file);
        $tables = $xml->get_tag_content_array("table", $import->xml_content_lansuite);

        foreach ($tables as $table) {        
          $table_head = $xml->get_tag_content("table_head", $table, 0);
          $table_name = $xml->get_tag_content("name", $table_head);
          $db->qry_first("DROP TABLE IF EXISTS %prefix%%plain%", $table_name);
        }
      }
    }
  }
  
  function check_updates(){
    global $db, $config;
/*
    include("modules/install/class_update.php");
    
    $update = new update();
    // Check update for Version 2.0.2
    $file = "modules/install/update/update202.php";
    if(file_exists($file)) include($file);
*/
  }
}
?>
