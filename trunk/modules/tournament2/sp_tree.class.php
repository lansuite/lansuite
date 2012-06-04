<?php
/**
 * lansuite tourney tree class
 *
 * author: sparkY <sparky@splatterworld.de>
 * description: special wrapper class fo lansuite tourney module. Actually uses TourneyTree class
 *
 */

class lansuiteTree extends TourneyTree {

	var $wb_teams = array();
	var $lb_teams = array();
	var $size = NULL;
	var $st = NULL;
	var $tree = NULL;
	var $db = NULL;

	function lansuiteTree($id, $size, &$db) {
		$this->size = $size;
		$this->st = "SELECT games.round, teams.name, teams.teamid, games.leaderid, games.gameid, games.score, games.position
				FROM %prefix%t2_games AS games
				LEFT JOIN %prefix%t2_teams AS teams ON ( games.tournamentid = teams.tournamentid )
					AND ( games.leaderid = teams.leaderid)
				WHERE (games.tournamentid = '".(int)$id."')
				AND (games.group_nr = 0) AND (games.round = %s) GROUP BY games.gameid ORDER BY games.position DESC";
		$this->db = $db;
	}

	function prepareWB() {
		for ($i=0; $i <= TourneyTree::numWBRounds($this->size); $i++) {
			$res = $this->db->qry(sprintf($this->st, $i));
			while($row = $this->db->fetch_array($res)) {
				$this->wb_teams[$i][] = (!$row['name'] ? array('name' => NULL, 'score' => 0): $row);
			}
		}

		// determine winner of each match
		foreach ($this->wb_teams as $round => $teams) {
			for($i=0; $i<count($teams); $i++) {
				$t1 = $this->wb_teams[$round][$i];
				$i++;
				$t2 = $this->wb_teams[$round][$i];

				if (!$t1['name']) {
					$this->wb_teams[$round][$i-1]['iswinner'] = 2;
					$this->wb_teams[$round][$i]['iswinner'] = 1;
				} elseif (!$t2['name']) {
					$this->wb_teams[$round][$i]['iswinner'] = 1;
					$this->wb_teams[$round][$i-1]['iswinner'] = 2;
				} elseif ($t1['score'] > $t2['score']) {
					$this->wb_teams[$round][$i-1]['iswinner'] = 1;
					$this->wb_teams[$round][$i]['iswinner'] = 0;
				} elseif ($t1['score'] < $t2['score']) {
					$this->wb_teams[$round][$i-1]['iswinner'] = 0;
					$this->wb_teams[$round][$i]['iswinner'] = 1;
				}
	
				if(!$t1['name'] && !$t2['name']) {
					$this->wb_teams[$round][$i-1]['iswinner'] = 2;
					$this->wb_teams[$round][$i]['iswinner'] = 2;
				}
			}
		}
	}

	function prepareLB() {
		$x=0;
		for ($i=0.5; $i <= (TourneyTree::numLBRounds($this->size)/2); ) {
			$res = $this->db->qry(sprintf($this->st, ($i*(-1))));
			while($row = $this->db->fetch_array($res)) {
				$this->lb_teams[$x][] = (!$row['name'] ? array('name' => NULL, 'score' => 0): $row);
			}
			$x++;
			$i = $i + 0.5;
		}

		// little fix coz lansuite saves overall-final to WB but we exspect it to be in LB
		$fix = array_pop($this->wb_teams);
		array_push($this->lb_teams, $fix);
		$fix = array_pop($this->wb_teams);
		array_push($this->wb_teams, $fix, $fix);

		foreach ($this->lb_teams as $round => $teams) {
			for($i=0; $i<count($teams); $i++) {
				$t1 = $this->lb_teams[$round][$i];
				$i++;
				$t2 = $this->lb_teams[$round][$i];

				if (!$t1['name']) {
					$this->lb_teams[$round][$i-1]['iswinner'] = 2;
					$this->lb_teams[$round][$i]['iswinner'] = 1;
				} elseif (!$t2['name']) {
					$this->lb_teams[$round][$i]['iswinner'] = 1;
					$this->lb_teams[$round][$i-1]['iswinner'] = 2;
				} elseif ($t1['score'] > $t2['score']) {
					$this->lb_teams[$round][$i-1]['iswinner'] = 1;
					$this->lb_teams[$round][$i]['iswinner'] = 0;
				} elseif ($t1['score'] < $t2['score']) {
					$this->lb_teams[$round][$i-1]['iswinner'] = 0;
					$this->lb_teams[$round][$i]['iswinner'] = 1;
				}

				if(!$t1['name'] && !$t2['name']) {
					$this->lb_teams[$round][$i-1]['iswinner'] = 2;
					$this->lb_teams[$round][$i]['iswinner'] = 2;
				}
			}
		}
	}

	function mkTree() {
		if ($this->tree) return;
		$this->tree = new TourneyTree($this->size, $this->wb_teams, $this->lb_teams);
	}

	function getWBString() {
		if (!$this->tree) $this->mkTree();
		return $this->tree->printWB();
	}

	function getLBString() {
		if (!$this->tree) $this->mkTree();
		return $this->tree->printLB();
	}

}
?>
