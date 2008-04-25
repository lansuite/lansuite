<?php 

class Clan {

  function Add($name, $url = '', $password = '') {
    global $db, $config, $func, $lang;

    if ($name == '') return false;
    
    if (substr($url, 0, 7) != 'http://') $url = 'http://'. $url;
		
    $db->query("INSERT INTO {$config['tables']['clan']} SET
      name = '$name',
      url = '$url',
      password = '$password'
      ");

    return $db->insert_id();
  }
  
  // Join Clan
  function AddMember($clanid, $userid){
    global $db, $config;
    
    $db->query("UPDATE {$config['tables']['user']} SET
      clanid = '$clanid'
      WHERE userid = '$userid'
      ");
      
    return true;
  }
  
  //Leave clan
  function RemoveMember($userid){
    global $db, $config;

    $db->query("UPDATE {$config['tables']['user']} SET
      clanid = '0'
      WHERE userid = '$userid'
      ");

    return true;
  }
}
?>
