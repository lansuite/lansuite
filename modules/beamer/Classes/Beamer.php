<?php

namespace LanSuite\Module\Beamer;

class Beamer
{
    /**
     * @param string $where
     * @return int
     */
    private function countSQL($where)
    {
        global $db;

        $row = $db->qry_first("SELECT COUNT(bcID) AS n FROM %prefix%beamer_content %plain%", $where);

        return $row['n'];
    }

    /**
     * @param string $status
     * @param string $beamerid
     * @return int
     */
    public function countContent($status = null, $beamerid = null)
    {
        $beamerid_sql = '';
        if ($beamerid) {
            $beamerid_sql = " b" . intval($beamerid) . " = '1' ";
        }

        $status_sql = '';
        switch ($status) {
            case '':
                $status_sql = '';
                break;
            case '1':
                $status_sql = " active = '1' ";
                break;
            case '0':
                $status_sql = " active = '0' ";
                break;
        }

        $and_sql = '';
        if ($status_sql && $beamerid_sql) {
            $and_sql = ' AND ';
        }

        $add_sql = '';
        if ($status_sql || $beamerid_sql) {
            $add_sql = " WHERE " . $status_sql . $and_sql . $beamerid_sql;
        }

        return $this->countSQL($add_sql);
    }

    /**
     * Enforces a given $bcid to be displayed next by resetting the last view date
     * @param int $bcid
     * @return void
     */
    public function set2first($bcid)
    {
        global $Database;

        $Database->query("UPDATE %prefix%beamer_content SET lastView = '0' WHERE bcid = ? LIMIT 1", [$bcid]);
    }

    /**
     * @param int $bcid
     * @return void
     */
    public function toggleActive($bcid)
    {
        global $db;
        $row = $db->qry_first("SELECT active FROM %prefix%beamer_content WHERE bcID = %int%", $bcid);
        $active = $row['active'];
    
        if ($active == "1") {
            $db->qry("UPDATE %prefix%beamer_content SET active = '0' WHERE bcID = %int% LIMIT 1", $bcid);
        } else {
            $db->qry("UPDATE %prefix%beamer_content SET active = '1' WHERE bcID = %int% LIMIT 1", $bcid);
        }
    }

    /**
     * @param int $bcid
     * @param $beamerid
     * @return void
     */
    public function toggleBeamerActive($bcid, $beamerid)
    {
        global $Database;

        if ($beamerid == '') {
            return;
        }

        $row = $Database->queryWithOnlyFirstRow('SELECT b%plain% As active FROM %prefix%beamer_content WHERE bcID = %int%', $beamerid, $bcid);
        $active = $row['active'];
        if ($active == "1") {
            $Database->query("UPDATE %prefix%beamer_content SET b%plain% = '0' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        } else {
            $Database->query("UPDATE %prefix%beamer_content SET b%plain% = '1' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        }
    }

    /**
     * @param int $bcid
     * @return void
     */
    public function deleteContent($bcid)
    {
        global $Database;
  
        $Database->query("DELETE FROM %prefix%beamer_content WHERE bcID = ? LIMIT 1", [$bcid]);
    }

    /**
     * @param array $c
     * @return void
     */
    public function saveContent($c)
    {
        global $Database;
  
        $lastview = time();
        $bcId = $c['bcid'] ?? 0;
        if (!$bcId) {
            $db->query(
                "INSERT INTO %prefix%beamer_content SET caption = ?, maxRepeats = ?, contentType = ?, lastView = ?, contentData = ?",
                [$c['caption'],
                $c['maxrepeats'],
                $c['type'],
                $lastview,
                $c['text']]
            );
        } else {
            $caption_sql = '';
            if ($c['caption'] != "") {
                $caption_sql = " , caption = '{$c['caption']}' ";
            }
            $Database->query("UPDATE %prefix%beamer_content SET contentData = ? $caption_sql WHERE bcid = ?", [$c['text'], $c['bcid']]);
        }
    }

    /**
     * @param int $bcid
     */
    public function getContent($bcid): array|bool|null
    {
        global $Database;

        $row = $Database->queryWithOnlyFirstRow("SELECT * FROM %prefix%beamer_content WHERE bcid = ? LIMIT 1", [$bcid]);

        return $row;
    }

    /**
     * Gets the beamer page not seen for the longest time for a given beamer
     * @todo Check what this really does
     * @param string $beamerid
     * @return string Either the content to be shown or empty string
     */
    public function getCurrentContent(string $beamerid = '')
    {
        global $Database;
  
        $row = $Database->queryWithOnlyFirstRow('SELECT * FROM %prefix%beamer_content WHERE active = 1 AND b%plain% = 1 ORDER BY lastView ASC', $beamerid);
        $Database->query('UPDATE %prefix%beamer_content SET lastView = ? WHERE bcID = ? LIMIT 1', [time(), $row['bcID']]);
        //@todo check context of execution. If this is not executed only in context of display, then the last view should not be updated here but in the visualisation
        switch ($row['contentType']) {
            case 'text':
                return $row['contentData'];
            break;

            case 'wrapper':
                $arr = explode("*", $row['contentData']);
                $iframe = "<center><iframe src=\"{$arr[0]}\" frameborder=\"0\" width=\"{$arr[2]}\" height=\"{$arr[1]}\"></iframe></center>";
                return $iframe;
            break;

            case 'turnier':
                $t = "<center><h2>" . $row['caption'] . "</h2><iframe src=\"index.php?mod=tournament2&amp;action=tree_frame&amp;design=base&amp;tournamentid={$row['contentData']}&amp;group=0\" style=\"width: 100%; min-width: 600px;\" width=\"100%\" height=\"500\" frameborder=\"0\"></iframe></center>>";
                return $t;
            break;
        }

        return '';
    }

    /**
     * Returns List options for all found tournaments
     * @param int $partyID Number of Party to select tournaments for. All tournaments will be returned when not provided
     * @return array
     */
    public function getAllTournamentsAsOptionList(int $partyID)
    {
        global $Database;

        $tournaments = [];
        $tQuery = 'SELECT tournamentid, name FROM %prefix%tournament_tournaments';
        if (isset($partyID) && $partyID >=0) {
            $tQuery .= ' where PartyID = ?';
            $result = $Database->query($tQuery,$partyID);
        } else {
            $result = $Database->query($tQuery);
        }
        
        while ($row = $db->fetch_array($result)) {
            $tournaments[] = "<option value=\"{$row['tournamentid']}\">{$row['name']}</option>";
        }

        return $tournaments;
    }

    /**
     * Function to obtain the Name of a Tournament by its ID
     * 
     * @param int $ctid ID of the tournament
     * @return string Name of the Tournament or empty if not found
     * @todo move Tournament function to Tournament Moduke
     */

    public function getTournamentNamebyID($ctid)
    {
        global $Database;

        $result = $Database->queryWithOnlyFirstRow('SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = ?', [$ctid]);

        return $result['name'];
    }
}
