<?php

$TranslationFirstRun = 1;

function ReplaceParameters($input, $parameters = NULL) {
  $z = 1;
  if ($parameters) foreach ($parameters as $parameter) {
    $input = str_replace('%'.$z, $parameter, $input);
    $z++;
  }
  return $input;
}

function t($input, $parameters = NULL) {
  global $db, $config, $language, $LSCurFile, $lang, $TranslationFirstRun, $func;

  if ($input == '') return '';
  else {

    if (!$db->success) return ReplaceParameters($input, $parameters);
    else {

      // Define current languages SQL-Field
      if ($language == 'de') $sql_lang_field = 'org';
      else $sql_lang_field = $language;

      // Load System, DB and current Mod on first call
      if ($TranslationFirstRun) {
        $res = $db->query("SELECT id, org, $sql_lang_field FROM {$config['tables']['translation']} WHERE file = 'System' OR file = 'DB' or file = '$mod'");
        while ($row = $db->fetch_array($res)) {
          if ($row[$sql_lang_field]) $lang['initial'][$row['id']] = $row[$sql_lang_field];
          else $lang['initial'][$row['id']] = $row['org'];
        }

        $TranslationFirstRun = 0;
      }

      // Already in $lang?
      $key = md5($input);
      if ($lang['initial'][$key] != '') return ReplaceParameters($lang['initial'][$key], $parameters);
      elseif ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);

      // Read from DB
      else {

        // Generate Mod-Name from FILE
        if ($LSCurFile == 'DB') $mod = 'DB';
        else {
          $LSCurFile = str_replace('\\','/', $LSCurFile);
          if (strpos($LSCurFile, 'modules/') !== false) {
            $start = strpos($LSCurFile, 'modules/') + 8;
            $mod = substr($LSCurFile, $start, strrpos($LSCurFile, '/') - $start);
          } else $mod = 'System';
        }

        // DB-Query
        $res = $db->query("SELECT id, org, $sql_lang_field FROM {$config['tables']['translation']} WHERE file = '$mod'");
        while ($row = $db->fetch_array($res)) {
          if ($row[$sql_lang_field]) $lang[$mod][$row['id']] = $row[$sql_lang_field];
          else $lang[$mod][$row['id']] = $row['org'];
        }
        if ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);

        // Insert into DB
        else {
          $db->query("REPLACE INTO {$config['tables']['translation']} SET id = '$key', file = '$mod', org = '". $func->escape_sql($input) ."'");
          return ReplaceParameters($input, $parameters);
        }
      }
    }
  }
}

#class translation {
#
#}
?>
