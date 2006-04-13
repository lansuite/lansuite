<?php



/**
 * Cronjob Modul f�r Turniermodul
 *
 */
class cron_tmod extends cron_parent {
	
	var $cronjob;
	
	function cron_tmod($cronjob){
		$this->cronjob = $cronjob;	
	}
	
	/**
	 * Name des Moduls ausgeben
	 *
	 * @return string
	 */
	function name(){
		return "Torunement Function";
	}
	

	/**
	 * Turnierauftrag ausf�hren
	 *
	 */
	function start_job(){
		global $gd, $func, $tournament, $width, $x_start, $height, $height_menu, $box_height, $box_width, $config, $dsp, $db, $tournamentid, $akt_round, $max_round, $color, $team_anz, $dg, $img_height, $lang, $map, $tfunc, $language;
		#$_GET["tournamentid"] =	$this->cronjob->class_id;
		#$_GET["group"]	=		$this->cronjob->function;
		#include_once("modules/tournament2/tree_img.php");
	}
	
	/**
	 * Neuen Turnierbaum erzeugauftrag hinzuf�gen
	 *
	 * @param int $tournamentid
	 * @param int $group
	 */
	function add_job($tournamentid,$group = ""){
		parent::add_job("cron_tmod",$tournamentid,0,0,0,$group);
	}
	
}

?>