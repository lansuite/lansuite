<?php 

class Clan {

  function Add($name, $url = '', $password = ''){
    global $db, $config, $lang;

    if ($name == '') return false;
    
    // Invalid chars in clan name
		if (preg_match("/([.^\"\'`´]+)/", $name)) return $lang['usrmgr']['add_err_user_chars'];
		
    $db->query("INSERT INTO {$config['tables']['clan']} SET
      name = '$name',
      url = '$url',
      password = '". md5($password) ."'
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