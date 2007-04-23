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
  global $db, $config, $language, $lang, $TranslationFirstRun, $func;

  if ($input == '') return '';
  if (!$db->success) return ReplaceParameters($input, $parameters);

  // Get CallingFile
  $bt = debug_backtrace();
  if ($bt[1]['function'] == 'translate') $CallingFile = 'DB';
  else {
    $CallingFile = $bt[0]['file'];
    $BasePath = substr(__FILE__, 0, strpos(__FILE__, 'inc\classes\class_translation.php'));
    $CallingFile = str_replace($BasePath, '', $CallingFile);
  }

  // Generate Mod-Name from FILE
  if ($CallingFile == 'DB') $mod = 'DB';
  else {
    $CallingFile = str_replace('\\','/', $CallingFile);
    if (strpos($CallingFile, 'modules/') !== false) {
      $start = strpos($CallingFile, 'modules/') + 8;
      $mod = substr($CallingFile, $start, strrpos($CallingFile, '/') - $start);
    } else $mod = 'System';
  }

  (strlen($input) > 255)? $long = '_long' : $long = '';

  // Load System, DB and current Mod on first call
  if ($TranslationFirstRun) {
    $res = $db->qry('SELECT file, id, org, '. $language .' FROM %prefix%translation WHERE file = \'System\' OR file = \'DB\' OR file = %string%', $mod);
    while ($row = $db->fetch_array($res)) {
      if ($row['file'] == 'System' or $row['file'] == 'DB') $db_mod = 'initial';
      ($row[$language])? $lang[$db_mod][$row['id']] = $row[$language] : $lang[$db_mod][$row['id']] = $row['org'];
    }
    $TranslationFirstRun = 0;
  }

  // Already in $lang?
  $key = md5($input);
  if ($lang['initial'][$key] != '') return ReplaceParameters($lang['initial'][$key], $parameters);
  elseif ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);

  // Read from DB
  else {
    // DB-Query
    $res = $db->qry('SELECT id, org, '. $language .' FROM %prefix%translation'. $long .' WHERE file = %string%', $mod);
    while ($row = $db->fetch_array($res)) {
      ($row[$language])? $lang[$mod][$row['id']] = $row[$language] : $lang[$mod][$row['id']] = $row['org'];
    }

    if ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);
    else {
      // Insert into DB
      if ($key != '' and $mod != '' and $input != '') $db->qry('REPLACE INTO %prefix%translation'. $long .' SET id = %string%, file = %string%, org = %string%', $key, $mod, $input);
      return ReplaceParameters($input, $parameters);
    }
  }
}
?>