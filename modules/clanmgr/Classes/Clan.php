<?php

namespace LanSuite\Module\ClanMgr;

class Clan
{

    /**
     * Add a new clan
     *
     * @param string $name
     * @param int $userid
     * @param string $url
     * @param string $password
     * @return bool|int|string
     */
    public function Add($name, $userid, $url = '', $password = '')
    {
        global $db;

        if ($name == '') {
            return false;
        }
    
        if ($url!='' && substr($url, 0, 7) != 'http://') {
            $url = 'http://'. $url;
        }
        
        $db->qry("
          INSERT INTO %prefix%clan
          SET
            name = %string%,
            url = %string%,
            password = %string%", $name, $url, $password);

        $this->AddMember($db->insert_id(), $userid, 1);

        return $db->insert_id();
    }

    /**
     * Add a member to a clan
     *
     * @param int $clanid
     * @param int $userid
     * @param int $isAdmin
     * @return bool
     */
    public function AddMember($clanid, $userid, $isAdmin = 0)
    {
        global $db;
    
        $db->qry("
          UPDATE %prefix%user
          SET
            clanid = %int%,
            clanadmin = %int%
          WHERE userid = %int%", $clanid, $isAdmin, $userid);
      
        return true;
    }

    /**
     * Remove a member from a clan
     *
     * @param int $userid
     * @return bool
     */
    public function RemoveMember($userid)
    {
        global $db;

        $db->qry("
          UPDATE %prefix%user 
          SET
            clanid = '0'
          WHERE userid = %int%", $userid);

        return true;
    }
}
