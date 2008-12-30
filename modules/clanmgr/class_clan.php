<?php 

class Clan {

  // Create new clan
  function Add($name, $userid, $url = '', $password = '') {
    global $db, $config, $func, $lang;

    if ($name == '') return false;
    
    if (substr($url, 0, 7) != 'http://') $url = 'http://'. $url;
        
    $db->qry("INSERT INTO %prefix%clan SET
      name = %string%,
      url = %string%,
      password = %string%
      ", $name, $url, $password);

    $this->AddMember($db->insert_id(), $userid, 1);

    return $db->insert_id();
  }
  
  // Join clan
  function AddMember($clanid, $userid, $isAdmin = 0) {
    global $db, $config;
    
    $db->qry("UPDATE %prefix%user SET
      clanid = %int%,
      clanadmin = %int%
      WHERE userid = %int%
      ", $clanid, $isAdmin, $userid);
      
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
}
?>