<?php 

class Clan {

  // Create new clan
  function Add($name, $userid, $url = '', $password = '') {
    global $db, $func, $lang;

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
    global $db;
    
    $db->qry("UPDATE %prefix%user SET
      clanid = %int%,
      clanadmin = %int%
      WHERE userid = %int%
      ", $clanid, $isAdmin, $userid);
      
    return true;
  }
  
  //Leave clan
  function RemoveMember($userid){
    global $db;

    $db->qry("UPDATE %prefix%user SET
      clanid = '0'
      WHERE userid = %int%
      ", $userid);

    return true;
  }

  //Check Clan Passwort
  function CheckClanPW($clanid, $clanpw) {
  global $db, $auth;

  $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $clanid);
  if ($clan['password'] and $clan['password'] == md5($clanpw)) return true;
  return false;
  }
  
    function GetClanMembers($clanid=NULL){
        global $db, $auth;  
      //What clanID do we have?
      //0 = not in a clan
      //else = ClanID  
      // NULL = use the clan of the logged in user
      if ($clanid == NULL) $clanid = $auth['clanid'];
      $clan_members = $db->qry('SELECT nickname, userid from %prefix%user WHERE clanid=%int%', $clanid);
      return $clan_members;
      //TODO: Get User for the clan ID
    }
  
  
  
  
  }
?>