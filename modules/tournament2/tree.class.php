<?php
/*
 * class: TourneyTree
 * source: http://www.spYders.de
 * File: tree.class.php
 * Author: sparkY <sparky@splatterworld.de>
 * License: GPL (general public license)
 * Description: basic class for tree generation of double/single elimination tournaments.
 * 				This is more like a proof-of-concept implementation, since it will get slow if you
 *				want to generate a grid of size 512+ on-the-fly - recommend to use kinda caching for bigger brackets!
 *
 *				Feel free to contact me or contribute.
*/
class TourneyTree
{
    public $lb_teams = array();
    public $wb_teams = array();
    public $size;
    public $wb_rounds;
    public $wb_num_rows ;
    public $wb_num_cols;
    public $wb_tbl = array();
    public $wb_indexes = array();
    public $lb_rounds;
    public $lb_num_rows;
    public $lb_num_cols;
    public $lb_tbl = array();
    public $lb_indexes = array();

    public function TourneyTree($size, $wb_teams, $lb_teams = false)
    {
        // init vars !
        $this->size = $size;
        $this->wb_rounds = TourneyTree::log2($size);
        $this->wb_num_rows = $this->size * 2;
        $this->wb_num_cols = $this->wb_rounds * 2;
        $this->wb_tbl = array();
        $this->wb_indexes = array();
        $this->wb_teams = $wb_teams;

        if ($lb_teams !== false || $wb_teams === false) {
            // should be -1 including final!
            $this->lb_rounds = (2*TourneyTree::log2($size))-1;
            $this->lb_num_rows = $size * 2;
            $this->lb_num_cols = $this->lb_rounds * 2;
            $this->lb_tbl = array();
            $this->lb_indexes = array();
            $this->lb_teams = $lb_teams;
        }

        if ($wb_teams === false) {
            $this->generateFakeTeams($this->wb_teams, $this->lb_teams);
        }
    }

    public function numWBRounds($size)
    {
        return TourneyTree::log2($size);
    }

    public function numLBRounds($size)
    {
        return (2*TourneyTree::log2($size))-1;
    }

    public function calcMW($x, $y)
    {
        return (($x+$y)/2);
    }

    public function calcGauss($x)
    {
        return ((int)$x);
    }

    public function log2($x)
    {
        return (log($x)/log(2));
    }

    public function getPrevCoords($round, $indexes, $offset = 2)
    {
        $tmp = $indexes;
        $tmp2 = $round-$offset;
        $r1 = array_shift($tmp[$tmp2]);
        $r2 = array_shift($tmp[$tmp2]);
        return array($r1, $r2);
    }

    public function generateBar($x, $y, &$tbl)
    {
        $tbl[$x][$y] = true;
    }

    public function generateArrows(&$tbl)
    {
        for ($i=1; $i < count($tbl);) {
            $t = $s = 0;
            for ($x=0; $x < count($tbl[$i]); $x++) {
                if ($tbl[$i][$x] === true && $t == 0) {
                    $s=$x;
                }
                if ($tbl[$i][$x] === true) {
                    $t++;
                }
                if ($tbl[$i][$x] === false && $t > 0) {
                    $idx = (int)(($t / 2));
                    $tbl[$i][$s+$idx] = 'ARROW';
                    $t = $s = 0;
                }
            }
            $i += 2;
        }
    }

    public function LBTeamsInRound($round, $size)
    {
        $i = (2*TourneyTree::log2($size))-1;
        if ($round < 1 || $round >= $i) {
            return false;
        }
        //if ($round == $i) return 1; // use this if final should be included
        $ret = TourneyTree::log2(pow(2, ($i - $round)));
        if (($ret % 2) == 1) {
            $ret++;
        }
        return $ret;
    }

    public function WBTeamsInRound($round, $size)
    {
        $max_rounds = TourneyTree::log2($size);
        if ($round < 1 || $round > $max_rounds) {
            return false;
        }
        return pow(2, ($max_rounds - ($round - 1)));
    }

    public function generateFakeTeams(&$wb, &$lb)
    {
        $wb = $lb = array();
        $sz = TourneyTree::log2($this->size);

        for ($i=0; $i < $sz; $i++) {
            $wb[$i] = array();
            $x = TourneyTree::WBTeamsInRound($i+1, $this->size);
            for ($j=1; $j <= $x; $j++) {
                array_push($wb[$i], $j);
            }
        }
        array_push($wb, array('1'));

        for ($i=0; $i < ((2*$sz)-2); $i++) {
            $lb[$i] = array();
            $x = TourneyTree::LBTeamsInRound($i+1, $this->size);
            for ($j=1; $j <= $x; $j++) {
                array_push($lb[$i], $j);
            }
        }
        array_push($lb, array('1'));
        return true;
    }

    public function makeWBTable()
    {
        for ($x=0; $x <= $this->wb_num_cols; $x++) {
            if ((($x+1) % 2) == 1) {
                $this->wb_indexes[$x+1] = array();
            }

            for ($y=0; $y < $this->wb_num_rows; $y++) {
                $this->wb_tbl[$x][$y] = false;
            }
        }
    }

    public function makeLBTable()
    {
        for ($x=0; $x < $this->lb_num_cols; $x++) {
            if ((($x+1) % 2) == 1) {
                $this->lb_indexes[$x+1] = array();
            }

            for ($y=0; $y < $this->lb_num_rows; $y++) {
                $this->lb_tbl[$x][$y] = false;
            }
        }
    }

    public function fillWBTable()
    {
        for ($x=0; $x <= $this->wb_num_cols; $x++) {
            $round = $x+1;

            for ($y=0; $y < $this->wb_num_rows; $y++) {
                $row = $y+1;

                if ($x == 0) {
                    if ((count($this->wb_teams[$x]) % 2) == 1) {
                        $this->generateBar($x+1, $y, $this->wb_tbl);
                        $this->generateBar($x+1, $y-1, $this->wb_tbl);
                    }

                    if (($row % 2) == 1) {
                        $this->wb_tbl[$x][$y] = array_shift($this->wb_teams[$x]);        // insert team
                            array_push($this->wb_indexes[$round], array($x, $y));
                    }
                }

                    // rounds > 1 < max
                    // $x / 2 is the actual round!
                    // $round-2 is the actual previous round
                if ($x > 0 && $x < $this->wb_num_cols && ($x % 2) == 0) {
                    if ((count($this->wb_teams[$x/2]) % 2) == 1) {
                        $this->generateBar($x+1, $y, $this->wb_tbl);
                        $this->generateBar($x+1, $y-1, $this->wb_tbl);
                    }

                    $tmp = $this->getPrevCoords($round, $this->wb_indexes);
                    if ($this->calcMW($tmp[0][1], $tmp[1][1]) == $y) {
                        $this->wb_tbl[$x][$y] = array_shift($this->wb_teams[($x/2)]);        // insert team
                        array_shift($this->wb_indexes[$round-2]);
                        array_shift($this->wb_indexes[$round-2]);
                        array_push($this->wb_indexes[$round], array($x, $y));
                    }
                }

                    // final round
                if ($x == $this->wb_num_cols) {
                    if (($y*2)+2 == $this->wb_num_rows) {
                        $tmp = array_shift($this->wb_teams[($x/2)]);
                        $this->wb_tbl[$x][$y] = $tmp;        // insert team
                        // we need this team again later for the final in DE!!!
                        array_push($this->wb_teams[($x/2)], $tmp);
                    }
                }
            }
        }
    }

    public function fillLBTable()
    {
        for ($x=0; $x < $this->lb_num_cols; $x++) {
            $round = $x+1;
            for ($y=0; $y < $this->lb_num_rows; $y++) {
                $row = $y+1;

                    // first round
                if ($x == 0) {
                    if ((count($this->lb_teams[$x]) % 2) == 1) {
                        $this->generateBar($x+1, $y, $this->lb_tbl);
                        $this->generateBar($x+1, $y-1, $this->lb_tbl);
                    }
                    if (($row % 2) == 1 && count($this->lb_teams[$x]) > 0) {
                        $this->lb_tbl[$x][$y] = array_shift($this->lb_teams[$x]);        // insert team
                        array_push($this->lb_indexes[$round], array($x, $y));
                        // we need them for the positions of the following round
                        if (count($this->lb_teams[$x]) <= 0) {
                            for ($t=2; $t < $this->lb_num_rows/2; $t++) {
                                array_push($this->lb_indexes[$round], array($x, $y+$t));
                                $t += 2;
                            }
                        }
                    }
                }

                    // rounds > 1
                if ($x > 0 && $x < ($this->lb_num_cols-2) && ($x % 2) == 0) {
                    if ((count($this->lb_teams[$x/2]) % 2) == 1) {
                        $this->generateBar($x+1, $y, $this->lb_tbl);
                        $this->generateBar($x+1, $y-1, $this->lb_tbl);
                    }

                    $tmp = $this->getPrevCoords($round, $this->lb_indexes);
                    if ($this->calcMW($tmp[0][1], $tmp[1][1]) == $y) {
                        if (count($this->lb_teams[($x/2)]) > 0) {
                            $this->lb_tbl[$x][$y] = array_shift($this->lb_teams[($x/2)]);
                        }        // insert team
                        array_shift($this->lb_indexes[$round-2]);

                        if ((($x/2) % 2) == 0) {
                            array_shift($this->lb_indexes[$round-2]);
                        }
                        array_push($this->lb_indexes[$round], array($x, $y));

                        if (count($this->lb_teams[($x/2)]) <= 0) {
                            $tmp = $this->getPrevCoords($round, $this->lb_indexes, 0);
                            $delta = $tmp[1][1] - $tmp[0][1] ;
                            if ($delta > 0) {
                                for ($t=$delta; $t < ($this->lb_num_rows/2);) {
                                    array_push($this->lb_indexes[$round], array($x, $y+$t));
                                    $t += $delta;
                                }
                            }
                        }
                    }
                }

                    // this is the overall-final
                if ($x > 1 && $x == $this->lb_num_cols-2) {
                    $tmp = $this->getPrevCoords($round, $this->lb_indexes);
                    if ($this->calcMW($tmp[0][1], $tmp[1][1]) == $y) {
                        $max_lb = count($this->lb_teams)-1;
                        $max_lb = count($this->wb_teams)-1;
                        $final = ($this->lb_teams[$this->lb_rounds-1]);
                        $this->lb_tbl[$x][$y] = array_shift($final);
                        $this->lb_tbl[$x][$y+2] = array_shift($final);
                    }
                }
            }
        }
    }

    public function RenderBar()
    {
        $ret = sprintf('<table height="100%%" border="0" cellspacing="0" cellpadding="2">');
        $ret .= sprintf('<tr>');
        $ret .= sprintf('    <td bgcolor="#CCCCCC" width="5">&nbsp;</td>');
        $ret .= sprintf('    <td width="15">&nbsp;</td>');
        $ret .= sprintf('    <td width="5">&nbsp;</td>');
        $ret .= sprintf('</tr>');
        $ret .= sprintf('</table>');
        return $ret;
    }

    public function RenderArrow()
    {
        $ret = sprintf('<table height="100%%" border="0" cellspacing="0" cellpadding="2">');
        $ret .= sprintf('<tr>');
        $ret .= sprintf('    <td bgcolor="#CCCCCC" width="5">&nbsp;</td>');
        $ret .= sprintf('    <td width="15" align="right">-&gt;</td>');
        $ret .= sprintf('    <td width="5">&nbsp;</td>');
        $ret .= sprintf('</tr>');
        $ret .= sprintf('</table>');
        return $ret;
    }

    public function RenderTeam($team)
    {
        if ($team['iswinner'] == 1) {
            $col = '00FF00';
        } else {
            $col = 'FF0000;';
        }

        if ($team['iswinner'] == 2) {
            $team['score']    = 0;
            $col = '808080';
        }
    //echo '<pre>';
    //print_r($this);
    //echo '</pre>';
        $ret = sprintf('<table border="0" cellspacing="0" cellpadding="2">');
        $ret .= sprintf('<tr>');
        $ret .= sprintf('    <td width="100%%"><b>%s</b></td>', (count($team) > 3 ? $team['name'] : 'Joker'));
        $ret .= sprintf('    <td bgcolor="#DDDDDD" width="15" align="center" style="color: #%s"><small><font color="#%s">%d</font></small></td>', $col, $col, $team['score']);
        $ret .= sprintf('</tr>');
        $ret .= sprintf('</table>');
        return $ret;
    }


    public function printWB()
    {
        $this->makeWBTable();
        $this->fillWBTable();
        $this->generateArrows($this->wb_tbl);
        $ret = '<table border="0" cellspacing="0" cellpadding="0">';
        for ($y=0; $y < $this->wb_num_rows; $y++) {
            $ret .= "<tr>";
            for ($x=0; $x <= $this->wb_num_cols; $x++) {
                if ($this->wb_tbl[$x][$y] != 'ARROW' && $this->wb_tbl[$x][$y] !== false) {
                    $ret .= '<td align="left" bgcolor="#CCCCCC">';
                } else {
                    $ret .= "<td>";
                }

                if (!is_bool($this->wb_tbl[$x][$y]) && $this->wb_tbl[$x][$y] == 'ARROW') {
                    $ret .= $this->RenderArrow();    // cell is an arrow
                } elseif ($this->wb_tbl[$x][$y] === true) {
                    $ret .= $this->RenderBar();    // cell is a bar
                } elseif ($this->wb_tbl[$x][$y] && $this->wb_tbl[$x][$y] != 'ARROW') {
                    $ret .= $this->RenderTeam($this->wb_tbl[$x][$y]);    // cell is a team
                } else {
                    $ret .= "&nbsp;";
                }
                $ret .= "</td>";
            }
            $ret .= "</tr>";
        }
        $ret .='</table>';
        return $ret;
    }

    public function printLB()
    {
        $this->makeLBTable();
        $this->fillLBTable();
        $this->generateArrows($this->lb_tbl);

        $ret = '<table border="0" cellspacing="0" cellpadding="0">';
        for ($y=0; $y < $this->lb_num_rows; $y++) {
            $ret .= "<tr>";
            for ($x=0; $x < $this->lb_num_cols; $x++) {
                if ($this->lb_tbl[$x][$y] != 'ARROW' && $this->lb_tbl[$x][$y] !== false) {
                    $ret .= '<td align="left" bgcolor="#CCCCCC">';
                } else {
                    $ret .= "<td>";
                }

                if (!is_bool($this->lb_tbl[$x][$y]) && $this->lb_tbl[$x][$y] == 'ARROW') {
                    $ret .= $this->RenderArrow();
                } elseif ($this->lb_tbl[$x][$y] === true) {
                    $ret .= $this->RenderBar();
                } elseif ($this->lb_tbl[$x][$y] && $this->lb_tbl[$x][$y] != 'ARROW') {
                    $ret .= $this->RenderTeam($this->lb_tbl[$x][$y]);
                } else {
                    $ret .= '&nbsp;';
                }
                $ret .= "</td>";
            }
            $ret .= "</tr>";
        }
        $ret .= '</table>';
        return $ret;
    }
}
