<?php 

class Clan {

  function Add($name, $url = '', $password = '') {
    global $db, $config, $func, $lang;

    if ($name == '') return false;
    
    if (substr($url, 0, 7) != 'http://') $url = 'http://'. $url;
		
    $db->qry("INSERT INTO %prefix%clan SET
      name = %string%,
      url = %string%,
      password = %string%
      ", $name, $url, $password);

    return $db->insert_id();
  }
  
  // Join Clan
  function AddMember($clanid, $userid){
    global $db, $config;
    
    $db->qry("UPDATE %prefix%user SET
      clanid = %int%
      WHERE userid = %int%
      ", $clanid, $userid);
      
    return true;
  }
  
  //Leave clan
  function RemoveMember($userid){
    global $db, $config;

    $db->qry("UPDATE %prefix%user SET
      clanid = '0'
      WHERE userid = %int%
      ", $userid);

    return true;
  }
