<?php

define('FIELD_OPTIONAL', 1);
define('HTML_ALLOWED', 1);
define('LSCODE_ALLOWED', 1);
define('HTML_WYSIWYG', 2);
define('LSCODE_BIG', 3);
define('IS_PASSWORD', 1);
define('IS_NEW_PASSWORD', 2);
define('IS_SELECTION', 3);
define('IS_MULTI_SELECTION', 4);
define('IS_FILE_UPLOAD', 5);
define('IS_PICTURE_SELECT', 6);
define('IS_TEXT_MESSAGE', 7);
define('IS_CAPTCHA', 8);

define('READ_DB_PROC', 0);
define('CHECK_ERROR_PROC', 1);

class masterform {

	var $FormFields = array();
	var $Groups = array();
	var $SQLFields = array();
	var $SQLFieldTypes = array();
	var $SQLFieldUnique = array();
	var $DependOn = array();
	var $error = array();
	var $AdditionalDBAfterSelectFunction = '';
	var $AdditionalDBPreUpdateFunction = '';
	var $AdditionalDBUpdateFunction = '';
	var $CheckBeforeInserFunction = '';
	var $DependOnStarted = 0;
	var $isChange = false;
	var $FormEncType = '';
	var $PWSecID = 0;
	var $AdditionalKey = '';
  var $AddInsertControllField = '';
  var $AddChangeCondition = '';
  var $NumFields = 0;
  var $insert_id = -1;
  var $LogID = 0;
  
  function masterform($MFID = 0) {
    $this->MFID = $MFID;
  }
	
  function AddFix($name, $value){
    $this->SQLFields[] = $name;
    $_POST[$name] = $value;
  }

  function AddField($caption, $name, $type = '', $selections = '', $optional = 0, $callback = '', $DependOnThis = 0, $DependOnCriteria = '') {
    if ($type == IS_TEXT_MESSAGE) $optional = 1;
    $arr = array();
    $arr['caption'] = $caption;
    $arr['name'] = $name;
    $arr['type'] = $type;
    if ($type == IS_FILE_UPLOAD) $this->FormEncType = 'multipart/form-data';
    $arr['optional'] = 0;
    $arr['optional'] = $optional;
    if ($DependOnThis) $this->DependOn[$name] = $DependOnThis;
    $arr['callback'] = $callback;
    $arr['selections'] = $selections;
    $arr['DependOnCriteria'] = $DependOnCriteria;
    $this->FormFields[] = $arr;
    $this->SQLFields[] = $name;
    $this->NumFields++;
  }
  
  function AddGroup($caption = '') {
    if (count($this->FormFields) > 0) {
      $arr = array();
      $arr['caption'] = $caption;
      $arr['fields'] = $this->FormFields;
      $this->Groups[] = $arr;
      $this->FormFields = array();
    }
  }


  // Print form
	function SendForm($BaseURL, $table, $idname = '', $id = 0) {     // $BaseURL is no longer in use!
    global $dsp, $db, $config, $func, $sec, $lang, $templ, $CurentURLBase;

		$this->AddGroup(); // Adds non-group-fields to fake group

    if ($BaseURL) $StartURL = $BaseURL .'&'. $idname .'='. $id;
    else {
      $StartURL = $CurentURLBase;
      $StartURL = str_replace('&mf_step=2', '', $StartURL);
      if (strpos($StartURL, '&'. $idname .'='. $id) == 0) $StartURL .= '&'. $idname .'='. $id;
    }
    if ($id) $this->isChange = true;

    $AddKey = '';
    if ($this->AdditionalKey != '') $AddKey = $this->AdditionalKey .' AND ';
    $InsContName = 'InsertControll'. $this->MFID;

    // If the table entry should be created, or deleted wheter the control field is checked
    if ($this->AddInsertControllField != '') {
      $find_entry = $db->query("SELECT * FROM {$config['tables'][$table]} WHERE $AddKey $idname = ". (int)$id);
      ($db->num_rows($find_entry))? $this->isChange = 1 : $this->isChange = 0;
      $db->free_result($find_entry);
    }

    // Get SQL-Field Types
    $res = $db->query("DESCRIBE {$config['tables'][$table]}");
    while ($row = $db->fetch_array($res)) {
      $SQLFieldTypes[$row['Field']] = $row['Type'];
      if ($row['Key'] == 'PRI' or $row['Key'] == 'UNI') $SQLFieldUnique[$row['Field']] = true;
      else $SQLFieldUnique[$row['Field']] = false;
    }
    $db->free_result($res);

    // Split fields, which consist of more than one
    if ($this->SQLFields) foreach ($this->SQLFields as $key => $val) if (strpos($this->SQLFields[$key], '|') > 0) {
      $subfields = split('\|', $this->SQLFields[$key]);
      if ($subfields) foreach ($subfields as $subfield) $this->SQLFields[] = $subfield;
    }

    // Delete non existing DB fields, from array
    if ($this->SQLFields) foreach ($this->SQLFields as $key => $val) if (!$SQLFieldTypes[$val]) unset($this->SQLFields[$key]);

    // Error-Switch
    switch ($_GET['mf_step']) {
      default:

        $_SESSION['mf_referrer'] = $func->internal_referer;

        // Read current values, if change
        if ($this->isChange) {
          $db_query = '';
          if ($this->SQLFields) foreach ($this->SQLFields as $val) {
#            if ($SQLFieldTypes[$val] == 'datetime' or $SQLFieldTypes[$val] == 'date') $db_query .= ", UNIX_TIMESTAMP($val) AS $val";
#            else $db_query .= ", $val";
            $db_query .= ", $val";
          }

          $row = $db->query_first("SELECT 1 AS found $db_query FROM {$config['tables'][$table]} WHERE $AddKey $idname = ". (int)$id);
          if ($row['found']) {
            foreach ($this->SQLFields as $key => $val) $_POST[$val] = str_replace('"', '&quot;', $row[$val]); #$func->db2edit($row[$val])
          } else {
            $func->error($lang['mf']['err_invalid_id']);
            return false;
          }
        }
        if ($this->AdditionalDBAfterSelectFunction) $addUpdSuccess = call_user_func($this->AdditionalDBAfterSelectFunction, '');
      break;

      // Check for errors and convert data, if necessary (dates, passwords, ...)
      case 2:
        if ($this->Groups) foreach ($this->Groups as $GroupKey => $group) {
          if ($group['fields']) foreach ($group['fields'] as $FieldKey => $field) if($field['name']) {
            $err = false;

            // Copy WYSIWYG editor variable
            if (($SQLFieldTypes[$field['name']] == 'text' or $SQLFieldTypes[$field['name']] == 'mediumtext' or $SQLFieldTypes[$field['name']] == 'longtext')
              and $field['selections'] == HTML_WYSIWYG) $_POST[$field['name']] = $_POST['FCKeditor1'];

            // If not in DependOn-Group, or DependOn-Group is active
            if (!$this->DependOnStarted or $_POST[$this->DependOnField]) {

              // -- Convertions --
              // Convert Post-date to unix-timestap
              if ($SQLFieldTypes[$field['name']] == 'datetime')
#                $_POST[$field['name']] = $func->date2unixstamp($_POST[$field['name'].'_value_year'], $_POST[$field['name'].'_value_month'],
#                $_POST[$field['name'].'_value_day'], $_POST[$field['name'].'_value_hours'], $_POST[$field['name'].'_value_minutes'], 0);

                //1997-12-31 23:59:59
                $_POST[$field['name']] = $_POST[$field['name'].'_value_year'] .'-'. $_POST[$field['name'].'_value_month'] .'-'.
                $_POST[$field['name'].'_value_day'] .' '. $_POST[$field['name'].'_value_hours'] .':'. $_POST[$field['name'].'_value_minutes'] .':00';

              if ($SQLFieldTypes[$field['name']] == 'date')
#                $_POST[$field['name']] = $func->date2unixstamp($_POST[$field['name'].'_value_year'], $_POST[$field['name'].'_value_month'],
#                $_POST[$field['name'].'_value_day'], 0, 0, 0);

                $_POST[$field['name']] = $_POST[$field['name'].'_value_year'] .'-'. $_POST[$field['name'].'_value_month'] .'-'. $_POST[$field['name'].'_value_day'];

              // Upload submitted file
              if ($_POST[$field['name'].'_keep']) {
                foreach ($this->SQLFields as $key => $val) if ($val == $field['name']) unset($this->SQLFields[$key]);
              } elseif ($field['type'] == IS_FILE_UPLOAD) $_POST[$field['name']] = $func->FileUpload($field['name'], $field['selections']);


              // -- Checks --
              // Exec callback
              if ($field['type'] == IS_CALLBACK) $err = call_user_func($field['selections'], $field['name'], CHECK_ERROR_PROC);
              if ($err) $this->error[$field['name']] = $err;

              // Check for value
              if (!$field['optional'] and $_POST[$field['name']] == '') $this->error[$field['name']] = $lang['mf']['err_no_value'];

              // Check Int
              elseif (strpos($SQLFieldTypes[$field['name']], 'int') !== false and $SQLFieldTypes[$field['name']] != 'tinyint(1)'
                and $SQLFieldTypes[$field['name']] != "enum('0','1')"
                and $_POST[$field['name']] and (int)$_POST[$field['name']] == 0) $this->error[$field['name']] = $lang['mf']['err_no_integer'];

              // Check date
              elseif (($SQLFieldTypes[$field['name']] == 'datetime' or $SQLFieldTypes[$field['name']] == 'date')
                and !checkdate($_POST[$field['name'].'_value_month'], $_POST[$field['name'].'_value_day'], $_POST[$field['name'].'_value_year']))
                $this->error[$field['name']] = $lang['mf']['err_invalid_date'];
                
              // Check new passwords
              elseif ($field['type'] == IS_NEW_PASSWORD and $_POST[$field['name']] != $_POST[$field['name'].'2'])
                $this->error[$field['name'].'2'] = $lang['mf']['err_pw2'];

              // Check captcha
              elseif ($field['type'] == IS_CAPTCHA and ($_POST['captcha'] == '' or $_COOKIE['image_auth_code'] != md5(strtoupper($_POST['captcha']))))
                $this->error['captcha'] = $lang['mf']['err_captcha'];

              // Callbacks
              elseif ($field['callback']) {
                $err = call_user_func($field['callback'], $_POST[$field['name']]);
                if ($err) $this->error[$field['name']] = $err;
              }

              // Check double uniques
              if (!$this->isChange and $SQLFieldUnique[$field['name']]) {
                $row = $db->query_first("SELECT 1 AS found FROM {$config['tables'][$table]} WHERE {$field['name']} = '{$_POST[$field['name']]}'");
                if ($row['found']) $this->error[$field['name']] = $lang['mf']['err_double_on_unique'];
              }
            }

            // Manage Depend-On-Groups
            if ($this->DependOnStarted >= 1) $this->DependOnStarted--;
            if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) {
              $this->DependOnStarted = $this->DependOn[$field['name']];
              $this->DependOnField = $field['name'];
            }

          }
        }

        if (count($this->error) > 0) $_GET['mf_step']--;
      break;
    }


    // Form-Switch
    switch ($_GET['mf_step']) {

      // Output form
      default:
        $sec->unlock($table);
    		$dsp->SetForm($StartURL .'&mf_step=2', '', '', $this->FormEncType);

        // InsertControll check box - the table entry will only be created, if this check box is checked, otherwise the existing entry will be deleted
        if ($this->AddInsertControllField != '') {
          $find_entry = $db->query("SELECT * FROM {$config['tables'][$table]} WHERE $AddKey $idname = ". (int)$id);
          if ($db->num_rows($find_entry)) $_POST[$InsContName] = 1;

          $this->DependOnStarted = $this->NumFields;
          $additionalHTML = "onclick=\"CheckBoxBoxActivate('box_$InsContName', this.checked)\"";
          $dsp->AddCheckBoxRow($InsContName, $this->AddInsertControllField, '', '', $field['optional'], $_POST[$InsContName], '', '', $additionalHTML);
          $dsp->StartHiddenBox('box_'.$InsContName, $_POST[$InsContName]);
        }

        // Output fields
        if ($this->Groups) foreach ($this->Groups as $GroupKey => $group) {
          if ($group['caption']) $dsp->AddFieldsetStart($group['caption']);
          if ($group['fields']) foreach ($group['fields'] as $FieldKey => $field) {

            $additionalHTML = '';
            if (!$field['type']) $field['type'] = $SQLFieldTypes[$field['name']];
            switch ($field['type']) {

              case 'text': // Textarea
                $maxchar = 65535;
              case 'mediumtext':
                if (!$maxchar) $maxchar = 16777215;
              case 'longtext':
                if (!$maxchar) $maxchar = 4294967295;
                if ($field['selections'] == HTML_ALLOWED or $field['selections'] == LSCODE_ALLOWED) $dsp->AddTextAreaPlusRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', '', $field['optional'], $maxchar);
                elseif ($field['selections'] == LSCODE_BIG) $dsp->AddTextAreaPlusRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], 70, 20, $field['optional'], $maxchar);
                elseif ($field['selections'] == HTML_WYSIWYG) {
                  ob_start();
                  include_once("ext_scripts/FCKeditor/fckeditor.php");
                  $oFCKeditor = new FCKeditor('FCKeditor1') ;
                  $oFCKeditor->BasePath	= 'ext_scripts/FCKeditor/';
                  $oFCKeditor->Value = $_POST[$field['name']];
                  $oFCKeditor->Height = 460;
                  $oFCKeditor->Create();
                  $fcke_content = ob_get_contents();
                  ob_end_clean();
                  $dsp->AddSingleRow($fcke_content);
                  if ($this->error[$field['name']]) $dsp->AddDoubleRow($field['caption'], $dsp->errortext_prefix . $this->error[$field['name']] . $dsp->errortext_suffix);
                }
                else $dsp->AddTextAreaRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', '', $field['optional']);
              break;

              case "enum('0','1')": // Checkbox
              case 'tinyint(1)':
                if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) $additionalHTML = "onclick=\"CheckBoxBoxActivate('box_{$field['name']}', this.checked)\"";
                list($field['caption1'], $field['caption2']) = split('\|', $field['caption']);
                if (!$_POST[$field['name']]) unset($_POST[$field['name']]);
                $dsp->AddCheckBoxRow($field['name'], $field['caption1'], $field['caption2'], $this->error[$field['name']], $field['optional'], $_POST[$field['name']], '', '', $additionalHTML);
              break;

              case 'datetime': // Date-Select
                $values = array();
                list($date, $time) = split(' ', $_POST[$field['name']]);
                list($values['year'], $values['month'], $values['day']) = split('-', $date);
                list($values['hour'], $values['min'], $values['sec']) = split(':', $time);

                $dsp->AddDateTimeRow($field['name'], $field['caption'], 0, $this->error[$field['name']], $values, '', '', '', '', $field['optional']);
              break;

              case 'date': // Date-Select
                $values = array();
                list($date, $time) = split(' ', $_POST[$field['name']]);
                list($values['year'], $values['month'], $values['day']) = split('-', $date);
                list($values['hour'], $values['min'], $values['sec']) = split(':', $time);

                if ($field['selections']) $area = split('/', $field['selections']);
                $start = $area[0];
                $end = $area[1];
                $dsp->AddDateTimeRow($field['name'], $field['caption'], 0, $this->error[$field['name']], $values, '', $start, $end, 1, $field['optional']);
              break;

              case IS_PASSWORD: // Password-Row
                if (strlen($_POST[$field['name']]) == 32) $_POST[$field['name']] = ''; // Dont show MD5-sum, read from DB on change
                $dsp->AddPasswordRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', $field['optional']);
              break;

              case IS_NEW_PASSWORD: // New-Password-Row
                if (strlen($_POST[$field['name']]) == 32) $_POST[$field['name']] = ''; // Dont show MD5-sum, read from DB on change
                $PWSecID++;
                $dsp->AddPasswordRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', $field['optional'], "onkeyup=\"CheckPasswordSecurity(this.value, document.images.seclevel{$PWSecID})\"");
                $dsp->AddPasswordRow($field['name'].'2', $field['caption'].' '.$lang['mf']['pw2_caption'], $_POST[$field['name'].'2'], $this->error[$field['name'].'2'], '', $field['optional'], 0);
                $templ['pw_security']['id'] = $PWSecID;
                $dsp->AddDoubleRow('', $dsp->FetchTpl('design/templates/ls_row_pw_security.htm'));
              break;

              case IS_CAPTCHA: // Captcha-Row
                $dsp->AddTextFieldRow('captcha', 'Captcha <img src="ext_scripts/captcha.php">', $_POST['captcha'], $this->error['captcha']);
              break;

              case IS_SELECTION: // Pre-Defined Dropdown
                if ($field['DependOnCriteria']) $addCriteria = ", Array('". implode("', '", $field['DependOnCriteria']) ."')";
                else $addCriteria = '';
                if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) $additionalHTML = "onchange=\"DropDownBoxActivate('box_{$field['name']}', this.options[this.options.selectedIndex].value{$addCriteria})\"";
                if (is_array($field['selections'])) {
              		$selections = array();
              		foreach($field['selections'] as $key => $val) {
              			($_POST[$field['name']] == $key) ? $selected = " selected" : $selected = "";
              			$selections[] = "<option$selected value=\"$key\">$val</option>";
              		}
                  $dsp->AddDropDownFieldRow($field['name'], $field['caption'], $selections, $this->error[$field['name']], $field['optional'], $additionalHTML);
                }
              break;

              case IS_MULTI_SELECTION: // Pre-Defined Multiselection
                if (is_array($field['selections'])) {
              		$selections = array();
              		foreach($field['selections'] as $key => $val) {
              		  $selected = '';
                    if ($_POST[$field['name']]) foreach($_POST[$field['name']] as $PostedField) {
                      if ($PostedField == $key) {
                        $selected = ' selected';
                        break;
                      }
                    }
              			$selections[] = "<option value=\"$key\"$selected>$val</option>";
              		}
                  $dsp->AddSelectFieldRow($field['name'], $field['caption'], $selections, $this->error[$field['name']], $field['optional'], 7);
                }
              break;

              case IS_FILE_UPLOAD: // File Upload to path
                if (is_dir($field['selections'])) {
                  $dsp->AddFileSelectRow($field['name'], $field['caption'], $this->error[$field['name']], '', '', $field['optional']);
                  if ($_POST[$field['name']]) {
                    $FileEnding = strtolower(substr($_POST[$field['name']], strrpos($_POST[$field['name']], '.'), 5));
                    if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') $img = HTML_NEWLINE.'<img src="'. $_POST[$field['name']] .'" />';
                    else $img = '';
                    $dsp->AddCheckBoxRow($field['name'].'_keep', t('Aktuelle Datei beibehalten'), $_POST[$field['name']] . $img, '', $field['optional'], 1);
                  }
                }
              break;

              case IS_PICTURE_SELECT: // Picture Dropdown from path
                if (is_dir($field['selections']))
                  $dsp->AddPictureDropDownRow($field['name'], $field['caption'], $field['selections'], $this->error[$field['name']], $field['optional'], $_POST[$field['name']]);
              break;
              
              case IS_TEXT_MESSAGE:
                $dsp->AddDoubleRow($field['caption'], $field['selections']);
              break;

              case IS_CALLBACK:
                $ret = call_user_func($field['selections'], $field['name'], OUTPUT_PROC, $this->error[$field['name']]);
                if ($ret) $dsp->AddDoubleRow($field['caption'], $ret);
              break;

              default: // Normal Textfield
                $dsp->AddTextFieldRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', $field['optional']);
              break;
            }

            // Start HiddenBox
            if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) {
              $dsp->StartHiddenBox('box_'.$field['name'], $_POST[$field['name']]);
              $this->DependOnStarted = $this->DependOn[$field['name']] + 1;
              unset($this->DependOn[$field['name']]);
            }
            // Stop HiddenBox, when counter has reached the last box-field
            if ($this->DependOnStarted == 1) $dsp->StopHiddenBox();
            // Decrease counter
            if ($this->DependOnStarted > 0) $this->DependOnStarted--;
          }
          if ($group['caption']) $dsp->AddFieldsetEnd();
        }

    		if ($id) $dsp->AddFormSubmitRow('edit');
    		else $dsp->AddFormSubmitRow('add');
        $dsp->AddContent();
      break;

      // Update DB
      case 2:
#        if (!$this->SQLFields) $func->error('No Fields!');
        if (!$sec->locked($table, $StartURL)) {

          if ($this->Groups) foreach ($this->Groups as $group) if ($group['fields']) foreach ($group['fields'] as $field) {
            // Convert Passwords
            if ($field['type'] == IS_NEW_PASSWORD and $_POST[$field['name']] != '') {
              $_POST[$field['name'] .'_original'] = $_POST[$field['name']];
              $_POST[$field['name']] = md5($_POST[$field['name']]);
            }
          }

          if ($this->CheckBeforeInserFunction) {
            if (!call_user_func($this->CheckBeforeInserFunction, $id)) return false;
          }

          if ($this->AdditionalDBPreUpdateFunction) $addUpdSuccess = call_user_func($this->AdditionalDBPreUpdateFunction, $id);
          $ChangeError = false;
          if ($this->AddChangeCondition) $ChangeError = call_user_func($this->AddChangeCondition, $id);

          if ($ChangeError) $func->information($ChangeError);
          else {
            $addUpdSuccess = true;

            // Generate INSERT/UPDATE query
            $db_query = '';
            if ($this->SQLFields) {
              foreach ($this->SQLFields as $key => $val) {
#                if ($SQLFieldTypes[$val] == 'datetime' or $SQLFieldTypes[$val] == 'date') $db_query .= "$val = FROM_UNIXTIME(". $_POST[$val]. "), ";
#                else $db_query .= "$val = '{$_POST[$val]}', ";
                if (($SQLFieldTypes[$val] == 'datetime' or $SQLFieldTypes[$val] == 'date') and $_POST[$val] == 'NOW()') $db_query .= "$val = NOW(), ";
                else $db_query .= "$val = '{$_POST[$val]}', ";
              }
              $db_query = substr($db_query, 0, strlen($db_query) - 2);
  
              // If the table entry should be created, or deleted wheter the control field is checked
              if ($this->AddInsertControllField != '' and !$_POST[$InsContName])
                $db->query("DELETE FROM {$config['tables'][$table]} WHERE $AddKey $idname = ". (int)$id);
  
              // Send query
              else {
                if ($this->isChange) {
                  $db->query("UPDATE {$config['tables'][$table]} SET $db_query WHERE $AddKey $idname = ". (int)$id);
                  $func->log_event(t('Eintrag #%1 in Tabelle "%2" geändert', array($id, $config['tables'][$table])), 1, '', $this->LogID);
                } else {
                  $DBInsertQuery = $db_query;
                  if ($this->AdditionalKey != '') $DBInsertQuery .= ', '. $this->AdditionalKey;
                  if ($this->AddInsertControllField) $DBInsertQuery .= ', '. $idname .' = '. (int)$id;
                  $db->query("INSERT INTO {$config['tables'][$table]} SET $DBInsertQuery");
                  $id = $db->insert_id();
                  $this->insert_id = $id;
                  $func->log_event(t('Eintrag #%1 in Tabelle "%2" eingefügt', array($id, $config['tables'][$table])), 1, '', $this->LogID);
                  $addUpdSuccess = $id;
                }
              }
            }

            if ($this->AdditionalDBUpdateFunction) $addUpdSuccess = call_user_func($this->AdditionalDBUpdateFunction, $id);
            if ($addUpdSuccess) {
              if ($this->isChange) $func->confirmation($lang['mf']['change_success'], $_SESSION['mf_referrer']);
              else $func->confirmation($lang['mf']['add_success'], $StartURL);
            }
          }
          
          unset($_SESSION['mf_referrer']);
          $sec->lock($table);
          return $addUpdSuccess;
          /* Will be
           1) return of AdditionalDBPreUpdateFunction if AddChangeCondition returns true
           2) return of AdditionalDBUpdateFunction if set
           3) Insert_id
          */  
        }
      break;
    }

    return false;
  }
}


// Error-Callback-Functions
function CheckValidEmail($email){
  if ($email == '') return t('Bitte geben Sie eine Email ein');
  elseif (substr_count($email, '@') != 1) return t('Die Adresse muss genau ein @-Zeichen enthalten');
  else {
    $ccTLD = array('ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'eh', 'er', 'es', 'et', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tm', 'tn', 'to', 'tp', 'tr', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw');
    $gTLD = array('arpa', 'com', 'edu' , 'gov', 'int', 'mil', 'net', 'org');
    $newTLD = array('aero', 'biz', 'coop', 'info', 'museum', 'name', 'pro');
    $TLD = array_merge($ccTLD, $gTLD);
    $allTLD = array_merge($TLD, $newTLD);

    list($userName, $hostName) = explode('@', $email);
    if (!preg_match("/^[a-z0-9\_\-\.\%]+$/i", $userName)) return t('Diese Email ist ungültig (Falscher Benutzer-Teil)');
    if (!preg_match("/^([a-z0-9]+[\-\.]{0,1})+\.[a-z]+$/i", $hostName)) return t('Diese Email ist ungültig (Falscher Host-Teil)');

    $subdomains = explode('.', $hostName);
    $tld = $subdomains[count($subdomains) - 1];
    if (!in_array($tld, $allTLD)) return t('Diese Email ist ungültig (Nicht exitsierende Domain)');
  }
  return false;
}
?>