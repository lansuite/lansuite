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
    global $config, $db;
    
    if(!$db->connect(1))
    {
    	//No success connection
    	if($db->connectfailure == 1) $ret_val = 0;
    	elseif($db->connectfailure == 2 and $config['database']['database'] == '') $ret_val = 4;
    	elseif($db->connectfailure == 2 and $config['database']['database'] != '')
    	{
    		//Try to create DB
	        $db->set_charset();
	        $query_id = $db->qry('CREATE DATABASE '. $config['database']['database'] .' CHARACTER SET utf8');
	        if ($query_id) $ret_val = 3; else $ret_val = 2;
	    }
    }
    else 
    {
      	// If User wants to rewrite all tables, drop databse. It will be created anew in the next step
        if (!$_GET["quest"] and $createnew and $_GET["step"] == 3) $this->DeleteAllTables();
        if($createnew) $ret_val = 5; else $ret_val = 1;
    } 
    
    // Return-Values:
    // 0 = Server not available
    // 1 = DB already exists
    // 2 = Create failed (i.e. insufficient rights)
    // 3 = Create successe
    // 4 = no Database
    // 5 = DB overwrite
    return $ret_val;
  }

  // Creates a DB-table using the file $table, located in the mod_settings-directory of the module $mod
  function WriteTableFromXMLFile($mod, $rewrite = NULL){
    global $db, $config, $import;
    
    // Delete references, if table exists, for they will be recreated in ImportXML()
    if (in_array($config['database']['prefix'] .'ref', $import->installed_tables)) $db->qry('TRUNCATE TABLE %prefix%ref');

    $import->GetImportHeader("modules/$mod/mod_settings/db.xml");
    $import->ImportXML($rewrite);
    if ($mod == 'install') {
      $this->InsertModules($rewrite);
      $this->InsertMenus($rewrite);
    }
  }


  // Scans 'install/db_skeleton/' for non-existand tables and creates them
  // Puts the results to the screen, by using $dsp->AddSingleRow for each table, if $display_to_screen = 1
  function CreateNewTables($display_to_screen = 1) {
    global $dsp, $config, $db, $func;

    $tablecreate = Array("anz" => 0, "created" => 0, "exist" => 0, "failed" => "");
    if ($display_to_screen) $dsp->AddSingleRow("<b>". t('Tabellen erstellen') ."</b>");

    if (is_dir("modules")) {
      // Do install-mod first! (for translations-table must exist)
      if (is_dir("modules/install/mod_settings")) {
        // Try to find DB-XML-File
        if (file_exists("modules/install/mod_settings/db.xml")){
          $this->WriteTableFromXMLFile('install'); // Calls InsertModules and InsertMenus as well
          if ($display_to_screen) $dsp->AddDoubleRow("Modul 'install'", "[<a href=\"index.php?mod=install&action=db&step=7&module=install&quest=1\">".t('zurücksetzen')."</a>]");
        }
      }

      // Try to find and import db.xml-Files
      foreach($func->ActiveModules as $module => $caption) {
        if ($module != 'install' and file_exists("modules/$module/mod_settings/db.xml")) {
          $this->WriteTableFromXMLFile($module);
          if ($display_to_screen) $dsp->AddDoubleRow("Modul '$module'", "[<a href=\"index.php?mod=install&action=db&step=7&module=$module&quest=1\">".t('zurücksetzen')."</a>]");
        }
      }
    }

    if ($display_to_screen) $dsp->AddDoubleRow("<b>". t('Alle Tabellen') ."</b>", "[<a href=\"index.php?mod=install&action=db&step=3&quest=1\">".t('zurücksetzen')."</a>]");

    return $tablecreate;
  }

  // Insert PLZ-Entrys in DB, if not exist
  function InsertPLZs() {
      global $db, $cfg;

      if ($cfg['guestlist_guestmap'] == 1) {
        $return_val = 1;
        $find = $db->qry("SELECT * FROM %prefix%locations");
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
                    if (!$db->qry("REPLACE INTO %prefix%locations (plz, breite, laenge) VALUES %plain%", $val)) $return_val = 0;
                }
            }
        }
        $db->free_result($find);
      } else $return_val = 1;
      
      return $return_val;
      // 0 = At least one create failed
      // 1 = Alreday existing
      // 2 = Create success
  }


  // Auto-Load Modules from XML-Files
  // And boxes
  function InsertModules($rewrite = false) {
    global $db, $xml, $func;

    // Tabelle Modules leeren um Module zu deinstallieren
    if($_GET["action"] == "wizard") {
      $db->qry("TRUNCATE TABLE %prefix%modules");
    }
    $db->qry("TRUNCATE %prefix%plugin");

    $mod_list = array();
    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {

      // module.xml
      $file = "modules/$module/mod_settings/module.xml";
      $ModActive = 0;
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
        $requires = $xml->get_tag_content("requires", $xml_file);
        $reqPhp = $xml->get_tag_content("php", $requires);
        $reqMysql = $xml->get_tag_content("mysql", $requires);

        $ModActive = $active;
        $mod_found = $db->qry_first("SELECT 1 AS found FROM %prefix%modules WHERE name = %string%", $module);

        if ($name) {
          if (!$mod_found["found"]) $db->qry_first("REPLACE INTO %prefix%modules
            SET name=%string%, caption=%string%, description=%string%, author=%string%, email=%string%, active=%string%, changeable=%string%, version=%string%, state=%string%, reqphp=%string%, reqmysql=%string%",
            $name, $caption, $description, $author, $email, $active, $changeable, $version, $state, $reqPhp, $reqMysql);
          elseif ($rewrite) $db->qry_first("REPLACE INTO %prefix%modules
            SET name=%string%, caption=%string%, description=%string%, author=%string%, email=%string%, changeable=%string%, version=%string%, state=%string%, reqphp=%string%, reqmysql=%string%",
            $name, $caption, $description, $author, $email, $changeable, $version, $state, $reqPhp, $reqMysql);
        }
      }

      if ($ModActive or $func->isModActive($module)) {

        // config.xml
        $file = "modules/$module/mod_settings/config.xml";
        if (file_exists($file)) {
          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          $SettingList = array();

          $xml_config = $xml->get_tag_content('config', $xml_file);
          // Read types
          $xml_types = $xml->get_tag_content_array('typedefinition', $xml_config);
          if ($xml_types) while ($xml_type = array_shift($xml_types)) {
            $xml_head = $xml->get_tag_content('head', $xml_type);
            $name = $xml->get_tag_content('name', $xml_head);
            $db->qry("DELETE FROM %prefix%config_selections WHERE cfg_key = %string%", $name);

            $xml_entries = $xml->get_tag_content_array('entry', $xml_type);
            if ($xml_entries) while ($xml_entry = array_shift($xml_entries)) {
            $value = $xml->get_tag_content('value', $xml_entry);
            $description = $xml->get_tag_content('description', $xml_entry);

            if ($name != '' and $description != '') $db->qry("INSERT INTO %prefix%config_selections SET cfg_key = %string%, cfg_value = %string%, cfg_display = %string%",
      $name, $value, $description);
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
              $pos = $xml->get_tag_content('pos', $xml_item);
              array_push($SettingList, $name);

              // Insert into DB, if not exists
              $found = $db->qry_first("SELECT cfg_key FROM %prefix%config WHERE cfg_key = %string%", $name);
              if (!$found['cfg_key']) $db->qry("INSERT INTO %prefix%config SET cfg_key = %string%, cfg_value = %string%, cfg_type = %string%, cfg_group = %string%, cfg_desc = %string%, cfg_module = %string%, cfg_pos = %int%",
      $name, $default, $type, $group, $description, $module, $pos);
            }
          }

          // Delete Settings from DB, which are no longer in the modules config.sql
          $settings_db = $db->qry("SELECT cfg_key FROM %prefix%config WHERE (cfg_module = %string%)", $module);
          while ($setting_db = $db->fetch_array($settings_db)) {
            if (!in_array($setting_db["cfg_key"], $SettingList)) $db->qry("DELETE FROM %prefix%config WHERE cfg_key = %string%", $setting_db["cfg_key"]);
          }
        }

        // boxes.xml
        $file = "modules/$module/boxes/boxes.xml";
        if (file_exists($file)) {
          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          ($module == 'install')? $modTmp = '' : $modTmp = $module;

          $boxes = $xml->get_tag_content_array("box", $xml_file);
          foreach ($boxes as $box) {
            $name = $xml->get_tag_content("name", $box);
            $place = $xml->get_tag_content("place", $box);
            $pos = $xml->get_tag_content("pos", $box);
            $active = $xml->get_tag_content("active", $box);
            $internet = $xml->get_tag_content("internet", $box);
            $login = $xml->get_tag_content("login", $box);
            $source = $xml->get_tag_content("source", $box);
            $callback = $xml->get_tag_content("callback", $box);

            $mod_found = $db->qry_first("SELECT 1 AS found FROM %prefix%boxes WHERE source = %string% AND module = %string%", $source, $modTmp);
            if ($rewrite or !$mod_found['found']) {
              $db->qry_first("DELETE FROM %prefix%boxes WHERE source = %string% AND module = %string%", $source, $modTmp);
              $db->qry_first("INSERT INTO %prefix%boxes
                SET name=%string%, place=%string%, pos=%string%, active=%string%, internet=%string%, login=%string%, source=%string%, callback=%string%, module=%string%",
                $name, $place, $pos, $active, $internet, $login, $source, $callback, $modTmp);
            }
          }
        }

        // plugins.xml
        $file = "modules/$module/plugins/plugins.xml";
        if (file_exists($file)) {
          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          $plugins = $xml->get_tag_content_array("plugin", $xml_file);
          foreach ($plugins as $plugin) {
            $name = $xml->get_tag_content("name", $plugin);
            $caption = $xml->get_tag_content("caption", $plugin);
            $icon = $xml->get_tag_content("icon", $plugin);
            $pos = $xml->get_tag_content("pos", $plugin);
            $db->qry("INSERT INTO %prefix%plugin SET module=%string%, pluginType=%string%, caption=%string%, pos=%int%, icon=%string%", $module, $name, $caption, $pos, $icon);
          }
        }
        
        // translation.xml
        $file = "modules/$module/mod_settings/translation.xml";
        if (file_exists($file)) {

          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          $entries = $xml->getTagContentArray("entry", $xml_file);
          foreach ($entries as $entry) {
            $id = $xml->getFirstTagContent("id", $entry);
            $org = $xml->getFirstTagContent("org", $entry, 1);
            $de = $xml->getFirstTagContent("de", $entry, 1);
            $en = $xml->getFirstTagContent("en", $entry, 1);
            $es = $xml->getFirstTagContent("es", $entry, 1);
            $fr = $xml->getFirstTagContent("fr", $entry, 1);
            $nl = $xml->getFirstTagContent("nl", $entry, 1);
            $it = $xml->getFirstTagContent("it", $entry, 1);
            $file = $xml->getFirstTagContent("file", $entry);

            if (strlen($org) > 255) $long = '_long'; else $long = '';

            // Insert only, if id-file combination does not exist
            $db->qry('INSERT INTO %prefix%translation%plain% SET
              id=%string%, file=%string%, org=%string%, de=%string%, en=%string%, es=%string%, fr=%string%, nl=%string%, it=%string%
              ON DUPLICATE KEY UPDATE id = id',
              $long, $id, $file, $org, $de, $en, $es, $fr, $nl, $it);
          }
        }
      }
    }

    // Delete non-existend Modules from DB
    $mods = $db->qry("SELECT name FROM %prefix%modules");
    while($row = $db->fetch_array($mods)) if (!in_array($row["name"], $mod_list)) $db->qry("DELETE FROM %prefix%modules WHERE name = %string%", $row["name"]);
    $db->free_result($mods);

    // Generate module table, so Lansuite knows, which modules need to be written
    $func->getActiveModules();
  }


  // Auto-Load Menuentries from XML-Files
  function InsertMenus($rewrite = false) {
    global $db, $xml, $func;

    if ($rewrite) $db->qry("TRUNCATE TABLE %prefix%menu");
    $menubox = $db->qry_first('SELECT boxid FROM %prefix%boxes WHERE source = \'menu\' AND active = 1');

    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($func->isModActive($module)) {
      $menu_found = $db->qry_first("SELECT 1 AS found FROM %prefix%menu WHERE module = %string%", $module);
      if (!$menu_found["found"]) {

        $file = "modules/$module/mod_settings/menu.xml";
        if (file_exists($file)) {

          $handle = fopen ($file, "r");
          $xml_file = fread ($handle, filesize ($file));
          fclose ($handle);

          $menu = $xml->get_tag_content("menu", $xml_file);
          $main_pos = $xml->get_tag_content("pos", $menu);
          $entrys = $xml->get_tag_content_array("entry", $menu);

          $i = 0;
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

            $db->qry_first("INSERT INTO %prefix%menu SET module=%string%, action=%string%, file=%string%, caption=%string%, hint=%string%, link=%string%, requirement=%string%, level=%string%, pos=%string%, needed_config=%string%, boxid=%int%",
  $module, $action, $file, $caption, $hint, $link, $requirement, $level, $pos, $needed_config, $menubox['boxid']);
          }
        }
      }
    }
  }


  // System prüfen
  function envcheck() {
    global $db, $dsp, $func;

    $continue = 1;

    // Environment Check
    $ok = "<span class=\"okay\">".t('Erfolgreich')."</span>" . HTML_NEWLINE;
    $failed = "<span class=\"error\">".t('Fehlgeschlagen')."</span>" . HTML_NEWLINE;
    $warning = "<span class=\"warning\">".t('Bedenkliche Einstellung')."</span>" . HTML_NEWLINE;
    $optimize = "<span class=\"warning\">".t('Optimierungsbedarf')."</span>" . HTML_NEWLINE;
    $not_possible = "<span class=\"warning\">".t('Leider nicht möglich')."</span>" . HTML_NEWLINE;


    #### Critical ####
    $dsp->AddFieldSetStart("Kritisch - Diese Test müssen alle erfolgreich sein, damit Lansuite funktioniert");

    // PHP-Version
    if (version_compare(phpversion(), "4.1.2") >= 0) $phpv_check = $ok . phpversion();
    else $phpv_check = $failed . t('Auf Ihrem System wurde die PHP-Version %1 gefunden. Lansuite benötigt mindestens PHP Version 4.3.0. Sie können zwar die Installation fortsetzen, allerdings kann keinerlei Garantie auf die ordnungsgemäße Funktionsweise gegeben werden. Laden und installieren Sie sich eine aktuellere Version von <a href=\'http://www.php.net\' target=\'_blank\'>www.php.net</a>.', phpversion());
    $dsp->AddDoubleRow("PHP Version", $phpv_check);

    // MySQL installed?
    if (extension_loaded("mysql") or extension_loaded("mysqli")) {
      $mysql_version = sprintf("%s\n", $db->client_info());
      if (!$mysql_version) $mysql_version = t('Unbekannt');
      $mysql_check = $ok . $mysql_version;
    } else {
        $mysql_check = $failed . t('Die MySQL-Erweiterung ist in PHP nicht geladen. Diese wird benötigt um auf die Datenbank zuzugreifen. Bevor keine Datenbank verfügbar ist, kann Lansuite nicht installiert werden. Den MySQL-Server gibt es unter <a href=\'http://www.mysql.com\' target=\'_blank\'>www.mysql.com</a> zum Download.');
        $continue = 0;
    }
    $dsp->AddDoubleRow("MySQL-Extention", $mysql_check);

    // config.php Rights
    $lansuite_conf = "inc/base/config.php";
    if (!file_exists($lansuite_conf)) $cfgfile_check = $failed . t('Die Datei <b>config.php</b> befindet sich <b>nicht</b> im Lansuite-Verzeichnis <b> inc/base/ </b>. Bitte überprüfen Sie die Datei auf korrekte Groß- und Kleinschreibung.');
    elseif (!is_writable($lansuite_conf)) $cfgfile_check = $failed . t('Die Datei <b>config.php</b> im Lansuite-Verzeichnis <b> inc/base/ </b> muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen.');
    else $cfgfile_check = $ok;
    $dsp->AddDoubleRow(t('Schreibrechte auf die Konfigurationsdatei'), $cfgfile_check);
    if ($cfgfile_check != $ok) $continue = 0;

    // Ext_inc Rights
    $ext_inc = "ext_inc";
    if (!file_exists($ext_inc)) $ext_inc_check = $failed . t('Der Ordner <b>ext_inc</b> existiert <b>nicht</b> im Lansuite-Verzeichnis. Bitte überprüfen Sie den Pfad auf korrekte Groß- und Kleinschreibung. Legen Sie den Ordner gegebenfalls bitte selbst an.');
    else {
      $ret = $this->IsWriteableRec($ext_inc);
      if ($ret != '') $ext_inc_check = $failed . t('In den Ordner <b>ext_inc</b> und alle seine Unterordner muss geschrieben werden können. Ändern Sie bitte die Zugriffsrechte entsprechend. Dies können Sie mit den meisten guten FTP-Clients erledigen. Die Datei muss mindestens die Schreibrechte (chmod) 666 besitzen. Die folgenden Dateien besitzten noch keinen Schreibzugriff:'). '<br><b>'. $ret .'<b>';
      else $ext_inc_check = $ok;
    }
    $dsp->AddDoubleRow(t('Schreibrechte im Ordner \'ext_inc\''), $ext_inc_check);

    // Error Reporting
    if (error_reporting() <= (E_ALL ^ E_NOTICE)) $errreport_check = $ok;
    else $errreport_check = $warning . t('In Ihrer php.ini ist \'error_reporting\' so konfiguriert, dass auch unwichtige Fehlermeldungen angezeigt werden. Dies kann dazu führen, dass störende Fehlermeldungen in Lansuite auftauchen. Wir empfehlen diese Einstellung auf \'E_ALL ^ E_NOTICE\' zu ändern. In dieser Einstellung werden dann nur noch Fehler angezeigt, welche die Lauffähigkeit des Skriptes beeinträchtigen.');
    $dsp->AddDoubleRow("Error Reporting", $errreport_check);

    $dsp->AddFieldSetEnd();


    #### Warning ####
    $dsp->AddFieldSetStart("Warnungen - Lansuite kann trotz evtl. Fehler verwendet werden");

    // GD-Lib
    if (extension_loaded ('gd')){
        if (function_exists("gd_info")) {
            $GD = gd_info();
            if (!strstr($GD["GD Version"], "2.0")) $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> nur in der Version GD1 gefunden. Damit ist die Qualität der erzeugten Bilder wesentlich schlechter. Es wird deshalb empfohlen GD2 zu benutzen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
            elseif (!$GD["FreeType Support"]) $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> ohne Free-Type Support gefunden. Dadurch werden die Schriftarten in Grafiken (z.b. in den User-Avataren) nicht sehr schön dargestellt. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
            else $gd_check = $ok;
            $gd_check .= '<table>';
            foreach ($GD as $key => $val) $gd_check .= '<tr><td class="content">'. $key .'</td><td class="content">'. $val .'</td></tr>';
            $gd_check .= '</table>';
        } else $gd_check = $warning . t('Auf Ihrem System wurde das PHP-Modul <b>GD-Library</b> nur in der Version GD1  gefunden. Damit ist die Qualität der erzeugten Bilder wesentlich schlechter. Es wird deshalb empfohlen GD2 zu benutzen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie entsprechende Einschränkungen im Gebrauch machen müssen.');
    } else {
        $gd_check = $failed . t('Auf Ihrem System konnte das PHP-Modul <b>GD-Library</b> nicht gefunden werden. Durch diese Programmierbibliothek werden in Lansuite Grafiken, wie z.B. User-Avatare generiert. Ab PHP Version 4.3.0 ist die GD bereits in PHP enthalten. Sollten Sie PHP 4.3.0 installiert haben und diese Meldung dennoch erhalten, überprüfen Sie, ob das GD-Modul evtl. deaktiviert ist. In PHP Version 4.2.3 ist die GD nicht enthalten. Wenn Sie diese Version benutzen muss GD 2.0 separat heruntergeladen, installiert und in PHP einkompiliert werden. Sollten Sie Windows und PHP 4.2.3 benutzen, empfehlen wir auf PHP 4.3.0 umzusteigen, da Sie sich auf diese Weise viel Arbeit sparen. Sollten Sie die Auswahl zwischen GD und GD2 haben, wählen Sie immer das neuere GD2. Sie können die Installation jetzt fortführen, allerdings werden Sie erhebliche Einschränkungen im Gebrauch machen müssen.');
    }
    $dsp->AddDoubleRow("GD Library", $gd_check);

    // Test Safe-Mode
    if (!ini_get("safe_mode")) $safe_mode = $ok;
    else $safe_mode = $not_possible . t('Auf Ihrem System ist die PHP-Einstellung <b>safe_mode</b> auf <b>On</b> gesetzt. safe_mode ist dazu gedacht, einige Systemfunktionen auf dem Server zu sperren um Angriffe zu verhindern (siehe dazu: <a href=\'http://de2.php.net/features.safe-mode\' target=\'_blank\'>www.php.net</a>). Doch leider benötigen einige Lansuite-Module (speziell: LansinTV, Serverstatistiken oder das Server-Modul) Zugriff auf genau diese Funktionen. Sie sollten daher, wenn Sie Probleme in diesen Modulen haben, in Ihrer <b>PHP.ini</b> die Option <b>safe_mode</b> auf <b>Off</b> setzen! <br />Außer bei oben genannten Modulen, kann es bei aktiviertem safe_mode außerdem auch zu Problemen bei dem Generieren von Buttons, wie dem am Ende dieser Seite kommen.');
    $dsp->AddDoubleRow("Safe Mode", $safe_mode);

    // Testing Safe-Mode and execution of system-programs
    if (!ini_get("safe_mode")) {
      if (stristr(strtolower($_SERVER['SERVER_SOFTWARE']) , "win") == ""){
        if (@shell_exec("cat /proc/uptime") == ""){
          $env_stats .= "<strong>/proc/uptime</strong>" . HTML_NEWLINE;
        }

        if (@shell_exec("/sbin/ifconfig") == "" and @shell_exec("/usr/sbin/ifconfig") == ""){
          $env_stats .= "<strong>ifconfig</strong>" . HTML_NEWLINE;
        }

        if (@shell_exec("cat /proc/loadavg") == ""){
          $env_stats .= "<strong>/proc/loadavg</strong>" . HTML_NEWLINE;
        }

        if (@shell_exec("cat /proc/cpuinfo") == ""){
          $env_stats .= "<strong>/proc/cpuinfo</strong>" . HTML_NEWLINE;
        }  

        if (@shell_exec("cat /proc/meminfo") == ""){
          $env_stats .= "<strong>/proc/meminfo</strong>" . HTML_NEWLINE;
        }

        if ($env_stats == "") $server_stats = $ok;
        else $server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, t('Auf ihrem System leider nicht möglich. Der Befehl oder die Datei HTML_NEWLINE{FEHLER} wurde nicht gefunden. Evtl. sind nur die Berechtigungen der Datei nicht ausreichend gesetzt.'));

        $config["server_stats"]["status"] = 1;
      } else {
        system("modules\stats\ls_getinfo.exe", $status);
        if ($status == 0){
          $server_stats = $ok;
        } else {
          $env_stats = "<strong>modules/stats/ls_getinfo.exe</strong>" . HTML_NEWLINE;
          $server_stats = $not_possible . ereg_replace("{FEHLER}", $env_stats, t('Auf ihrem System leider nicht möglich. Der Befehl oder die Datei HTML_NEWLINE{FEHLER} wurde nicht gefunden. Evtl. sind nur die Berechtigungen der Datei nicht ausreichend gesetzt.'));
        }
      }
      $dsp->AddDoubleRow("Server Stats", $server_stats);
    }

#    // Debug Backtrace
#    if (function_exists('debug_backtrace')) $debug_bt_check = $ok;
#    else $debug_bt_check = $warning . t('Die Funktion "Debug Backtrace" ist auf deinem System nicht vorhanden. Diese wird jedoch benötigt, um Übersetzungs-Texte einem bestimmten Modul zuzuordnen. Solange du lansuite nur in Deutsch verwenden willst, sollte dies keine Auswirkung haben');
#    $dsp->AddDoubleRow('Debug Backtrace', $debug_bt_check);
    
    // SNMP-Lib
    if (extension_loaded('snmp')){
        $snmp_check = $ok;
    } else {
        $snmp_check = $not_possible . t('Auf Ihrem System konnte das PHP-Modul <b>SNMP-Library</b> nicht gefunden werden. SNMP ermöglicht es, auf Netzwerkdevices zuzugreifen, um detaillierte Informatioen über diese zu liefern. Ohne diese Bibliothek kann das Lansuite-Modul <b> NOC </b> (Netzwerküberwachung) nicht arbeiten. Das Modul NOC wird <b>automatisch deaktiviert</b>.');
    }
    $dsp->AddDoubleRow("SNMP Library", $snmp_check);

    // FTP-Lib
    if (extension_loaded('ftp')){
        $ftp_check = $ok;
    } else {
        $ftp_check = $not_possible . t('Auf Ihrem System konnte das PHP-Modul <b>FTP-Library</b> nicht gefunden werden. Dies hat zur Folge haben, dass das Download-Modul nur im Standard-Modus, jedoch nicht im FTP-Modus, verwendet werden kann');
    }
    $dsp->AddDoubleRow("FTP Library", $ftp_check);

    $dsp->AddFieldSetEnd();


    #### Information ####
    $dsp->AddFieldSetStart(t('Informationen - Interesante Server-Einstellungen im Überblick'));

    // Display System-Variables
    $dsp->AddFieldSetStart(t('Webserver'));
    if (function_exists('disk_total_space') and function_exists('disk_free_space')) $dsp->AddDoubleRow(t('Freier Speicherplatz'), $func->FormatFileSize(disk_free_space('.')) .' / '. $func->FormatFileSize(disk_total_space('.')));
    $dsp->AddFieldSetEnd();

    $dsp->AddFieldSetStart(t('PHP'));
    $dsp->AddDoubleRow('Max. Script-Execution-Time', (float)ini_get('max_execution_time') .' Sec');
    $dsp->AddDoubleRow('Max. Data-Input-Zeit', (float)ini_get('max_input_time') .' Sec');
    $dsp->AddDoubleRow('Memory Limit', (float)ini_get('memory_limit') .' MB');
    $post_max_size = (float)ini_get('post_max_size');
    if ($post_max_size > 1000) $post_max_size = $post_max_size / 1024; // For some PHP-Versions use KB, instead of MB
    $dsp->AddDoubleRow('Max. Post-Form Size', (float)ini_get('post_max_size') .' MB');
    $dsp->AddDoubleRow('Magic Quotes', get_magic_quotes_gpc());
    $dsp->AddFieldSetEnd();

    if ($db->success) {
      $dsp->AddFieldSetStart(t('MySQL'));

      // key_buffer_size
      $dsp->AddFieldSetStart(t('Key buffer size -  MySQL empfiehlt für optimale Performance: 25% des Arbeitsspeichers. Oft reicht weniger.'));
      $res = $db->qry('SHOW variables LIKE "key_buffer_size"');
      while ($row = $db->fetch_array($res)) $dsp->AddDoubleRow($row[0], $row[1]);
      $db->free_result($res);
      $dsp->AddFieldSetEnd();

      // Key_blocks
      $dsp->AddFieldSetStart(t('Key blocks - Performance: Key_blocks_unused sollte niemals 0 erreichen! Wenn der Wert nahe 0 ist: key_buffer_size erhöhen'));
      $res = $db->qry('SHOW status LIKE "Key_blocks%"');
      while ($row = $db->fetch_array($res)) $dsp->AddDoubleRow($row[0], $row[1]);
      $db->free_result($res);
      $dsp->AddFieldSetEnd();

      // Query cache
      $dsp->AddFieldSetStart(t('Query cache - Beschleunigt MySQL-Abfragen. Sollte aktiv sein'));
      $res = $db->qry('SHOW VARIABLES LIKE \'have_query_cache\'');
      while ($row = $db->fetch_array($res)) $dsp->AddDoubleRow($row[0], $row[1]);
      $db->free_result($res);
      $res = $db->qry('SHOW STATUS LIKE \'Qcache%\'');
      while ($row = $db->fetch_array($res)) $dsp->AddDoubleRow($row[0], $row[1]);
      $db->free_result($res);
      $dsp->AddFieldSetEnd();

      $dsp->AddFieldSetEnd();
    }

    $dsp->AddFieldSetEnd();


    #### Information ####
    $dsp->AddFieldSetStart(t('Sicherheit - Diese Einstellungen sollten aus Security-Gründen vorgenommen werden'));
    
    // Session Use Only Cookies
    if (ini_get('session.use_only_cookies')) $only_cookies_check = $ok;
    else $only_cookies_check = $warning . t('Es wird empfohlen session.use_only_cookies in der php.ini auf 1 zu setzen! Dies verhindert, dass Session-IDs in der URL angezeigt werden. Wenn dies nicht verhindert wird, können unvorsichtige Benutzer, deren Browser keine Cookies zulassen, durch weiterleiten der URL an Dritte ihre Session preisgeben, was einer Weitergabe des Passwortes gleichkommt.');
    $dsp->AddDoubleRow("Session.use_only_cookies", $only_cookies_check);

    $dsp->AddFieldSetEnd();


    #### Performace ####
    $dsp->AddFieldSetStart("Performace - Hier können Sie ansetzen um die Performace Ihres Servers zu optimieren");

    // Register Globals
    if (ini_get('register_globals') == FALSE) $check = $ok;
    else $check = $optimize . t('Auf Ihrem System ist die PHP-Einstellung register_globals auf On gesetzt. Dabei muss PHP mehr Speicher für Globale Variablen belegen, als eigentlich für Lansuite notwendig. Sie können diese Einstellung in der php.ini ändern. Vergessen Sie nicht Ihren Webserver nach dieser Änderung neu zu starten.');
    $dsp->AddDoubleRow("Register Globals", $check);

    if (ini_get('expose_php') == FALSE) $check = $ok;
    else $check = $optimize . t('Auf Ihrem System ist die PHP-Einstellung expose_php auf On gesetzt. Diese Einstellung fügt - wenn sie auf On steht - jedem HTTP-Response einen Headereintrag hinzu, dass die Seite mit PHP erstellt wurde. Das ist unnötig, denn Ihre Besucher interessiert das sowieso nicht und aus Performancesicht muss der hinzugefügte Headereintrag natürlich mit zum Client übertragen werden. Also besser auf Off stellen.');
    $dsp->AddDoubleRow("Expose PHP", $check);

    if (ini_get('register_argc_argv') == FALSE) $check = $ok;
    else $check = $optimize . t('Auf Ihrem System ist die PHP-Einstellung register_argc_argv auf On gesetzt. Diese Einstellung ist nur nützlich, wenn man PHP über die Kommandozeile aufruft und dort Parameter mitgeben möchte. Das ist für Lansuite nicht notwendig und belegt unnötig Speicher');
    $dsp->AddDoubleRow("Register_argc_argv", $check);

    $dsp->AddFieldSetEnd();
    $dsp->AddFieldSetEnd();

    return $continue;
  }

  // Scans all db.xml-files and deletes all tables listed in them
  // This meens lansuite is not able to clean up tables, which changed their name during versions
  // But this is much safer than DROP DATABASE, for this clean methode would drop other web-systems using the same DB table, too
  function DeleteAllTables () {
    global $xml, $db;
  
    $modules_dir = opendir("modules/");
    while ($module = readdir($modules_dir)) if ($module != "." AND $module != ".." AND $module != ".svn" AND is_dir("modules/$module")) {
      $file = "modules/$module/mod_settings/db.xml";

      if (file_exists($file)) {
        $handle = fopen ($file, "r");
        $xml_file = fread ($handle, filesize ($file));
        fclose ($handle);

        $tables = $xml->get_tag_content_array("table", $xml_file);
        foreach ($tables as $table) {        
          $table_head = $xml->get_tag_content("table_head", $table, 0);
          $table_name = $xml->get_tag_content("name", $table_head);
          $db->qry_first("DROP TABLE IF EXISTS %prefix%%plain%", $table_name);
        }
      }
    }
  }
  
  function check_updates(){
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
