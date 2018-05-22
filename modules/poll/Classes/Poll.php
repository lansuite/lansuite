<?php

namespace LanSuite\Module\Poll;

class Poll
{
    /**
     * @param int $pollid
     * @param boolean $anonym
     * @param int $boxmode
     * @param int $width
     * @return void
     */
    public function ShowResult($pollid, $anonym, $boxmode = 0, $width = 400)
    {
        global $db, $auth, $dsp, $box;
  
        $total = $db->qry_first('
          SELECT
            COUNT(v.polloptionid) AS votes
          FROM %prefix%polloptions AS o
          LEFT JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
          WHERE
            o.pollid = %int%
          GROUP BY o.pollid', $pollid);

        $res = $db->qry('
          SELECT
            COUNT(v.polloptionid) AS votes,
            o.caption,
            o.polloptionid
          FROM %prefix%polloptions AS o
          LEFT JOIN %prefix%pollvotes AS v ON o.polloptionid = v.polloptionid
          WHERE
            o.pollid = %int%
          GROUP BY o.polloptionid
          ORDER BY o.polloptionid', $pollid);

        while ($row = $db->fetch_array($res)) {
            ($total['votes'])? $score = ceil(($width / $total['votes']) * $row['votes']) : $score = 0;
            $score_rest = $width - $score;
            $votes_text = $row['votes'];
      
            if ($score && !$anonym) {
                $votes_text .= '<br />Gevoted haben:';
                $users = $db->qry('
                  SELECT
                    u.username
                  FROM %prefix%pollvotes AS v
                  LEFT JOIN %prefix%user AS u ON v.userid = u.userid
                  WHERE
                    v.polloptionid = %int%', $row['polloptionid']);
                while ($user = $db->fetch_array($users)) {
                    $votes_text .= '<br />'. $user['username'];
                }
                $db->free_result($users);
            }
            $votebar = '<ul class="BarOccupied infolink" style="width:'. (int)$score .'px;">&nbsp;<span class="infobox">Votes: '. $votes_text .'</span></ul><ul id="infobox" class="BarFree" style="width:'. $score_rest .'px;"></ul><ul class="BarClear">&nbsp;</ul>';
            if ($boxmode) {
                $box->Row($votebar .' '. $row['caption']);
            } else {
                $dsp->AddDoubleRow($row['caption'], $votebar);
            }
        }
        $db->free_result($res);

        if (!$boxmode and $auth["login"] == 1) {
            new \LanSuite\MasterComment('Poll', $pollid, array('polls' => 'pollid'));
        }
    }
}
