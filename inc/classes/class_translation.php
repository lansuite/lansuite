<?php
function ReplaceParameters($input, $parameters = NULL) {
  $z = 1;
  if ($parameters) foreach ($parameters as $parameter) {
    $input = str_replace('%'.$z, $parameter, $input);
    $z++;
  }
  return $input;
}


function t($input, $parameters = NULL) {
  global $db, $config, $language, $LSCurFile, $lang;

  if (!$db->success) return ReplaceParameters($input, $parameters);
  else {
    // Already in $lang?
    $key = md5($input);
    if ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);
    
    // Read from DB
    else { 
      if ($language == 'de') $sql_lang_field = 'org';
      else $sql_lang_field = $language;

      // Generate Mod-Name from FILE
      $LSCurFile = str_replace('\\','/', $LSCurFile);
      if (strpos($LSCurFile, 'modules/') !== false) {
        $start = strpos($LSCurFile, 'modules/') + 8;
        $mod = substr($LSCurFile, $start, strrpos($LSCurFile, '/') - $start);
      } else $mod = substr($LSCurFile, strrpos($LSCurFile, '/') + 1, strlen($LSCurFile));

      // DB-Query
      $res = $db->query("SELECT id, org, $sql_lang_field FROM {$config['tables']['translation']} WHERE file = '$mod'");
      while ($row = $db->fetch_array($res)) {
        if ($row[$sql_lang_field]) $lang[$mod][$row['id']] = $row[$sql_lang_field];
        else $lang[$mod][$row['id']] = $row['org'];
      }
      if ($lang[$mod][$key] != '') return ReplaceParameters($lang[$mod][$key], $parameters);

      // Insert into DB
      else {
#        $db->query("REPLACE INTO {$config['tables']['translation']} SET id = '$key', file = '$mod', org = '$input'");
        return ReplaceParameters($input, $parameters);      
      }

    }
  }
}

#class translation {
#
#}
?>
