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
     * @param int $bcid
     * @return void
     */
    public function set2first($bcid)
    {
        global $db;

        $db->qry("UPDATE %prefix%beamer_content SET lastView = '0' WHERE bcid = %int% LIMIT 1", $bcid);
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
        global $db;

        if ($beamerid == '') {
            return;
        }

        $row = $db->qry_first('SELECT b%plain% As active FROM %prefix%beamer_content WHERE bcID = %int%', $beamerid, $bcid);
        $active = $row['active'];
        if ($active == "1") {
            $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '0' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        } else {
            $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '1' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
        }
    }

    /**
     * @param int $bcid
     * @return void
     */
    public function deleteContent($bcid)
    {
        global $db;
  
        $db->qry("DELETE FROM %prefix%beamer_content WHERE bcID = %int% LIMIT 1", $bcid);
    }

    /**
     * @param array $c
     * @return void
     */
    public function saveContent($c)
    {
        global $db;
  
        $lastview = time();
        if (!$c['bcid']) {
            $db->qry(
                "INSERT INTO %prefix%beamer_content SET caption = %string%, maxRepeats = %string%, contentType = %string%, lastView = %string%, contentData = %string%",
                $c['caption'],
                $c['maxrepeats'],
                $c['type'],
                $lastview,
                $c['text']
            );
        } else {
            $caption_sql = '';
            if ($c['caption'] != "") {
                $caption_sql = " , caption = '{$c['caption']}' ";
            }
            $db->qry("UPDATE %prefix%beamer_content SET contentData = %string% %plain% WHERE bcid = %int%", $c['text'], $caption_sql, $c['bcid']);
        }
    }

    /**
     * @param int $bcid
     * @return array|bool|null
     */
    public function getContent($bcid)
    {
        global $db;

        $row = $db->qry_first("SELECT * FROM %prefix%beamer_content WHERE bcid = %int% LIMIT 1", $bcid);

        return $row;
    }

    /**
     * @param string $beamerid
     * @return string
     */
    public function getCurrentContent($beamerid)
    {
        global $db;
  
        $row = $db->qry_first('SELECT * FROM %prefix%beamer_content WHERE active = 1 AND b%plain% = 1 ORDER BY lastView ASC', $beamerid);
        $db->qry('UPDATE %prefix%beamer_content SET lastView = %int% WHERE bcID = %int% LIMIT 1', time(), $row['bcID']);

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
     * @return array
     */
    public function getAllTournamentsAsOptionList()
    {
        global $db;

        $tournaments = [];
        $result = $db->qry('SELECT tournamentid, name FROM %prefix%tournament_tournaments');
        while ($row = $db->fetch_array($result)) {
            $tournaments[] = "<option value=\"{$row['tournamentid']}\">{$row['name']}</option>";
        }

        return $tournaments;
    }

    /**
     * @param int $ctid
     * @return string
     */
    public function getTournamentNamebyID($ctid)
    {
        global $db;

        $result = $db->qry_first('SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $ctid);

        return $result['name'];
    }
}
