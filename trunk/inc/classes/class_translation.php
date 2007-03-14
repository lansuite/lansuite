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
  if (!$db->success) return ReplaceParameters($input, $parameters);

  // Load System, DB and current Mod on first call
  if ($TranslationFirstRun) {
    $res = $db->query("SELECT id, org, $language FROM {$config['tables']['translation']} WHERE file = 'System' OR file = 'DB' or file = '$mod'");
    while ($row = $db->fetch_array($res)) {
      if ($row[$language]) $lang['initial'][$row['id']] = $row[$language];
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

    (strlen($input) > 255)? $long = '_long' : $long = '';

    // DB-Query
    $res = $db->qry('SELECT id, org, '. $language .' FROM %prefix%translation'. $long .' WHERE file = %string%', $mod);
    while ($row = $db->fetch_array($res)) {
      if ($row[$language]) $lang[$mod][$row['id']] = $row[$language];
      else $lang[$mod][$row['id']] = $row['org'];
    }
    if ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);

    // Insert into DB
    else {
      $db->qry('REPLACE INTO %prefix%translation'. $long .' SET id = %string%, file = %string%, org = %string%', $key, $mod, $input);
      return ReplaceParameters($input, $parameters);
    }
  }
}
?>