<?php

if (!VALID_LS) {
    die("Direct access not allowed!");
} // Direct-Call-Check

class beamer
{


  // Constructor
    public function __construct()
    {
        /*
         echo " ACTION: ".$_GET['action'];
         echo " BeamerID: ".$_GET['beamerid'];
         *
         *
    */
    }
  
    public function __destruct()
    {
    }
  
  
    public function countSQL($where)
    {
        global $db;
        $row = $db->qry_first("SELECT count(bcID) as n FROM %prefix%beamer_content %plain%", $where);
        return $row['n'];
    }
  
    public function countContent($status = null, $beamerid = null)
    {
            // Liefert die Anzahl der Inhalte mit Wahlmöglichkeit vom Aktiv-Status
        if ($beamerid) {
            $beamerid_sql = " b$beamerid = '1' ";
        } else {
            $beamerid_sql = "";
        }
        switch ($status) {
            case "":
                $status_sql = "";
                break;
            case "1":
                $status_sql = " active = '1' ";
                break;
            case "0":
                $status_sql = " active = '0' ";
                break;
        }
    
        if (($status_sql) and ($beamerid_sql)) {
            $and_sql = " AND ";
        }
        if (($status_sql) or ($beamerid_sql)) {
            $add_sql = " WHERE " . $status_sql . $and_sql . $beamerid_sql;
        } else {
            $add_sql = "";
        }
        return $this->countSQL($add_sql);
    }

 
    public function set2first($bcid)
    {
        global $db;
        $update = $db->qry("UPDATE %prefix%beamer_content SET lastView = '0' WHERE bcid = %int% LIMIT 1", $bcid);
    }
 
 
    public function toggleActive($bcid)
    {
        global $db;
        $row = $db->qry_first("SELECT active FROM %prefix%beamer_content WHERE bcID = %int%", $bcid);
        $active = $row['active'];
    
        if ($active == "1") {
            $insert_sql = $db->qry("UPDATE %prefix%beamer_content SET active = '0' WHERE bcID = %int% LIMIT 1", $bcid);
        } else {
            $insert_sql = $db->qry("UPDATE %prefix%beamer_content SET active = '1' WHERE bcID = %int% LIMIT 1", $bcid);
        }
    }


    public function toggleBeamerActive($bcid, $beamerid)
    {
        global $db;
        if ($beamerid == "") {
            return;
        }
        $row = $db->qry_first('SELECT b%plain% As active FROM %prefix%beamer_content WHERE bcID = %int%', $beamerid, $bcid);
        $active = $row['active'];
        if ($active == "1") {
            $insert_sql = $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '0' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        } else {
            $insert_sql = $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '1' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        }
    }

    public function deleteContent($bcid)
    {
        global $db;
  
        $delete =  $db->qry("DELETE FROM %prefix%beamer_content WHERE bcID = %int% LIMIT 1", $bcid);
    }
  

    public function saveContent($c)
    {
        global $db;
  
        $lastview = time();
        if (!$c['bcid']) {
            $insert = $db->qry(
                "INSERT INTO %prefix%beamer_content SET caption = %string%, maxRepeats = %string%, contentType = %string%, lastView = %string%, contentData = %string%",
                $c['caption'],
                $c['maxrepeats'],
                $c['type'],
                $lastview,
                $c['text']
            );
        } else {
            if ($c['caption'] != "") {
                $caption_sql = " , caption = '{$c['caption']}' ";
            }
            $update = $db->qry("UPDATE %prefix%beamer_content SET contentData = %string% %plain% WHERE bcid = %int%", $c['text'], $caption_sql, $c['bcid']);
        }
    }

  
    public function getContent($bcid)
    {
        global $db, $func;
        $row = $db->qry_first("SELECT * FROM %prefix%beamer_content WHERE bcid = %int% LIMIT 1", $bcid);
        return $row;
    }
  
  
  /*	System: Es wird immer der älteste Eintrag angezeigt und bei jedem Anzeigen der Counter gezählt. Ist der Counter auf Null bekommt der
  * 	Eintrag den aktuellen Zeitstempler und der Counter geht wieder auf sein max-wert.
  *     Die Übergabe des Content muss hinsichtlich anderen Medien noch angepasst werden. Mit TEXT passt das erstmal so.
  */
  
    public function getCurrentContent($beamerid)
    {
        global $db, $func;
  
        $row = $db->qry_first('SELECT * FROM %prefix%beamer_content WHERE active = 1 AND b%plain% = 1 ORDER BY lastView ASC', $beamerid);
        $update = $db->qry('UPDATE %prefix%beamer_content SET lastView = %int% WHERE bcID = %int% LIMIT 1', time(), $row['bcID']);
    
    
        switch ($row['contentType']) {
            case 'text':
                return $row['contentData'];
            break;
            case 'wrapper':
                            $arr = explode("*", $row['contentData']);
                            // $oframe = "<center><object data=\"{$arr[0]}\" type=\"application/x-shockwave-flash\"></center>"; // type="image/svg+xml" width="200" height="200"
                            $iframe = "<center><iframe src=\"{$arr[0]}\" frameborder=\"0\" width=\"{$arr[2]}\" height=\"{$arr[1]}\"></iframe></center>";
                            // $eframe = "<embed src=\"{$arr[0]}\" swLiveConnect=\"false\" type=\"application/x-shockwave-flash\" ".
                            //		  "pluginspage=\"http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"></embed>";
                return $iframe;
        
                        break;

            case 'turnier':
                $t = "<center><h2>" . $row['caption'] . "</h2><iframe src=\"index.php?mod=tournament2&amp;action=tree_frame&amp;design=base&amp;tournamentid={$row['contentData']}&amp;group=0\" style=\"width: 100%; min-width: 600px;\" width=\"100%\" height=\"500\" frameborder=\"0\"></iframe><p />";
              #case 'turnier':		$t = "<center><h2>" . $row['caption'] . "</h2><img src=\"ext_inc/tournament_trees/tournament_{$row['contentData']}.png\" border=\"0\"><p />";
                return $t;
                        break;
        }
    }
  
  
    public function getAllTournamentsAsOptionList()
    {
        global $db, $func;
    
        $result = $db->qry('SELECT tournamentid, name FROM %prefix%tournament_tournaments');
        while ($row = $db->fetch_array($result)) {
            $tournaments[] = "<option value=\"{$row['tournamentid']}\">{$row['name']}</option>";
        }
        return $tournaments;
    }
  
    public function getTournamentNamebyID($ctid)
    {
        global $db, $func;
        $result = $db->qry_first('SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $ctid);
        return $result['name'];
    }
}
