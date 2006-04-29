<?php

define('FIELD_OPTIONAL', 1);
define('HTML_ALLOWED', 1);

class masterform {

	var $FormFields = array();
	var $Groups = array();
	var $SQLFields = array();
	var $SQLFieldTypes = array();
	var $error = array();
	var $CurrentDBFields = array();

  function AddFix($name, $value){
    $this->SQLFields[] = $name;
    $_POST[$name] = $value;
  }

  function AddField($caption, $name, $optional = 0, $callback = '', $selections = '') {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['name'] = $name;
    $arr['optional'] = $optional;
    $arr['callback'] = $callback;
    $arr['selections'] = $selections;
    $this->FormFields[] = $arr;
    $this->SQLFields[] = $name;
  }
  
  function AddGroup($caption = '') {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['fields'] = $this->FormFields;
    $this->Groups[] = $arr;
    $this->FormFields = array();
  }


  // Print form
	function SendForm($BaseURL, $table, $idname = '', $id = 0) {
    global $dsp, $db, $config, $func, $sec, $lang;

    $StartURL = $BaseURL .'&'. $idname .'='. $id;

    // Get SQL-Field Types
    $res = $db->query("DESCRIBE {$config['tables'][$table]}");
    while ($row = $db->fetch_array($res)) $SQLFieldTypes[$row['Field']] = $row['Type'];
    $db->free_result($res);

    // Read current values, if change
    if ($id) {
      $db_query = '';
      foreach ($this->SQLFields as $key => $val) {
        if ($SQLFieldTypes[$val] == 'datetime') $db_query .= "UNIX_TIMESTAMP($val) AS $val, ";
        else $db_query .= "$val, ";
      }
      $db_query = substr($db_query, 0, strlen($db_query) - 2);

      $row = $db->query_first("SELECT 1 AS found, $db_query FROM {$config['tables'][$table]} WHERE $idname = ". (int)$id);
      if ($row['found']) foreach ($this->SQLFields as $key => $val) {
        $this->CurrentDBFields[$val] = $row[$val];
        if ($_POST[$val] == '') $_POST[$val] = $row[$val];
      } else {
        $func->error($lang['mf']['err_invalid_id']);
        return false;
      }
    }

    // Error-Switch
    switch ($_GET['mf_step']) {
      default:
        $sec->unlock($table);
      break;

      // Check for errors
      case 2:
        if ($this->Groups) foreach ($this->Groups as $GroupKey => $group) {
          if ($group['fields']) foreach ($group['fields'] as $FieldKey => $field) {

            // Convert Post-date to unix-timestap
            if ($SQLFieldTypes[$field['name']] == 'datetime')
              $_POST[$field['name']] = $func->date2unixstamp($_POST[$field['name'].'_value_year'], $_POST[$field['name'].'_value_month'],
              $_POST[$field['name'].'_value_day'], $_POST[$field['name'].'_value_hours'], $_POST[$field['name'].'_value_minutes'], 0);

            // Check for value
            if (!$field['optional'] and !$_POST[$field['name']]) $this->error[$field['name']] = $lang['mf']['err_no_value'];

            // Check Int
            elseif (strpos($SQLFieldTypes[$field['name']], 'int') !== false and $SQLFieldTypes[$field['name']] != 'tinyint(1)'
              and $SQLFieldTypes[$field['name']] != "enum('0','1')"
              and $_POST[$field['name']] and (int)$_POST[$field['name']] == 0) $this->error[$field['name']] = $lang['mf']['err_no_integer'];

            // Check date
            elseif ($SQLFieldTypes[$field['name']] == 'datetime'
              and !checkdate($_POST[$field['name'].'_value_month'], $_POST[$field['name'].'_value_day'], $_POST[$field['name'].'_value_year']))
              $this->error[$field['name']] = $lang['mf']['err_invalid_date'];

            // Callbacks
            elseif ($field['callback']) {
              $err = call_user_func($field['callback'], $_POST[$field['name']]);
              if ($err) $this->error[$field['name']] = $err;
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
    		$this->AddGroup(); // Adds non-group-fields to fake group
    		$dsp->SetForm($StartURL .'&mf_step=2');

        // Output fields
        if ($this->Groups) foreach ($this->Groups as $GroupKey => $group) {
          if ($group['caption']) $dsp->AddFieldsetStart($group['caption']);
          if ($group['fields']) foreach ($group['fields'] as $FieldKey => $field) switch($SQLFieldTypes[$field['name']]) {
            default:
              // Dropdown
              if ($field['selections']) {
                // Pre-Defined
                if (is_array($field['selections'])) {
              		$selections = array();
              		foreach($field['selections'] as $key => $val) {
              			($_POST[$field['name']] == $key) ? $selected = " selected" : $selected = "";
              			$selections[] = "<option$selected value=\"$key\">$val</option>";
              		}
              		$dsp->AddDropDownFieldRow($field['name'], $field['caption'], $selections, $this->error[$field['name']], $field['optional']);
                // Picture Dropdown from path
                } elseif (is_dir($field['selections'])) {
               		$dsp->AddPictureDropDownRow($field['name'], $field['caption'], $field['selections'], $this->error[$field['name']], $field['optional'], $_POST[$field['name']]);
                }
              // Textfield
              } else $dsp->AddTextFieldRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', $field['optional']);
            break;

            // Textarea
            case 'text':
              $maxchar = 65535;
            case 'mediumtext':
              if (!$maxchar) $maxchar = 16777215;
            case 'longtext':
              if (!$maxchar) $maxchar = 4294967295;
              if ($field['selections']) $dsp->AddTextAreaPlusRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', '', $field['optional'], $maxchar);
              else $dsp->AddTextAreaRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', '', $field['optional']);
            break;

            // Checkbox
            case "enum('0','1')":
            case 'tinyint(1)':
              list($field['caption1'], $field['caption2']) = split('\|', $field['caption']);
              $dsp->AddCheckBoxRow($field['name'], $field['caption1'], $field['caption2'], $this->error[$field['name']], $field['optional'], $_POST[$field['name']]);
            break;

            // Date-Select
            case 'datetime':
              $dsp->AddDateTimeRow($field['name'], $field['caption'], $_POST[$field['name']], $this->error[$field['name']], '', '', '', '', '', $field['optional']);
            break;
          }
          if ($group['caption']) $dsp->AddFieldsetEnd();
        }

    		if ($id) $dsp->AddFormSubmitRow('change');
    		else $dsp->AddFormSubmitRow('add');
        $dsp->AddContent();
      break;

      // Update DB
      case 2:
        if (!$this->SQLFields) $func->error('No Fields!');
        elseif (!$sec->locked($table, $StartURL)) {
          $db_query = '';
          foreach ($this->SQLFields as $key => $val) {
            if ($SQLFieldTypes[$val] == 'datetime') $db_query .= "$val = FROM_UNIXTIME(". $_POST[$val]. "), ";
            else $db_query .= "$val = '$_POST[$val]', ";
          }

          $db_query = substr($db_query, 0, strlen($db_query) - 2);

          if ($id) {
            $db->query("UPDATE {$config['tables'][$table]} SET $db_query WHERE $idname = ". (int)$id);
            $func->confirmation($lang['mf']['change_success'], $StartURL);
          } else {
            $db->query("INSERT INTO {$config['tables'][$table]} SET $db_query");
            $func->confirmation($lang['mf']['add_success'], $StartURL);
          }

          $sec->lock($table);
          return true;
        }
      break;
    }

    return false;
  }
}
?>