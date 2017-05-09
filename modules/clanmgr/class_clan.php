<?php

class Clan
{

  // Create new clan
    public function Add($name, $userid, $url = '', $password = '')
    {
        global $db, $func, $lang;

        if ($name == '') {
            return false;
        }
    
        if (substr($url, 0, 7) != 'http://') {
            $url = 'http://'. $url;
        }
        
        $db->qry("INSERT INTO %prefix%clan SET
      name = %string%,
      url = %string%,
      password = %string%
      ", $name, $url, $password);

        $this->AddMember($db->insert_id(), $userid, 1);

        return $db->insert_id();
    }
  
  // Join clan
    public function AddMember($clanid, $userid, $isAdmin = 0)
    {
        global $db;
    
        $db->qry("UPDATE %prefix%user SET
      clanid = %int%,
      clanadmin = %int%
      WHERE userid = %int%
      ", $clanid, $isAdmin, $userid);
      
        return true;
    }
  
  //Leave clan
    public function RemoveMember($userid)
    {
        global $db;

        $db->qry("UPDATE %prefix%user SET
      clanid = '0'
      WHERE userid = %int%
      ", $userid);

        return true;
    }

  //Check Clan Passwort
    public function CheckClanPW($clanid, $clanpw)
    {
        global $db, $auth;

        $clan = $db->qry_first("SELECT password FROM %prefix%clan WHERE clanid = %int%", $clanid);
        if ($clan['password'] and $clan['password'] == md5($clanpw)) {
            return true;
        }
        return false;
    }
}
