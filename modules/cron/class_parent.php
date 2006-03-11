<?php


/**
 * Parent Klasse für alle Cronfunktionen
 *
 */
class cron_parent {
	
	/**
	 * Variable für die Aufrufende Cronjob Klasse 
	 * Damit kann auf die existierenden Cronjobvaribalen zugegriffen werden.
	 *
	 * @var Cronjobclass
	 */
	var $cronjob;
	
	/**
	 * Konstuktor für Cronjob_Arbeits_Klassen
	 *
	 * @param cronjobclass $cronjob
	 */
	function class_cron_parent($cronjob){
		$this->cronjob = $cronjob;
	}
	
	
	/**
	 * Name der Klasse
	 * Wird diese Funktion nicht überschrieben hat die Klasse keinen Namen.
	 *
	 * @return string
	 */
	function name(){
		return "Parent Classname, Name for Job not defined";
	}
	/**
	 * Konfigurationsmenu für den entsprechenden Cronjob
	 * Falls diese Funktion nicht überschrieben wird existiert keine Konfiguration
	 * 
	 */
	function confmenu(){
		global $func, $lang;
		
		$func->error($lang['cron']['menu_nomenu'],"index.php?mod=cronjob");
		
	}
	
	
	
	/**
	 * Funktion um den Cronjob auszuführen.
	 * Diese Funktion muss überschrieben werden sonnst wird ein Fehler erzeugt.
	 *
	 */
	function start_job(){
		global $func;
		
		$func->error($lang['cron']['error_modul'],"index.php?mod=cronjob");
		
	}
	
	/**
	 * Neuen Job hinzufügen
	 * Mit dieser Funktion können abgeleitete Klassen direkt einen Job einfügen
	 *
	 * @param string $class
	 * @param int $class_id
	 * @param int $rot
	 * @param int $rottime
	 * @param int $starttime
	 * @return boolean
	 */
	function add_job($class, $class_id, $rot, $rottime, $starttime, $function = ""){
		global $db, $config;
		
		if($db->query("INSERT INTO {$config['tables']['cron_job']} 
				SET job_class='{$class}',
					class_id = '{$class_id}',
					rotation = '{$rot}',
					rottime  = '{$rottime}',
					starttime= '{$starttime}',
					function=  '{$function}'")) return true;
		else return false;
		
	}
	
	/**
	 * Löschen eines CronJobs aus der Aufgabentabelle
	 *
	 * @param int $jobid
	 * @return boolean
	 */
	function del_job($jobid){
		global $db, $config;
		
		if($db->query("DELETE FROM {$config['tables']['cron_job']} WHERE jobid = $jobid")) return true;
		else return false;
		
	}
	
	/**
	 * Löschen eines Konfigeintrages.
	 * Mit dieser Funktion können Abgeleitete Klassen einen Konfigeintrag direkt löschen
	 *
	 * @param int $confid
	 * @return boolean
	 */
	function del_conf($confid){
		global $db, $config;
		
		if($db->query("DELETE FROM {$config['tables']['cron_config']} WHERE config_id = $confid")) 
		{	
			if($db->query("DELETE FROM {$config['tables']['cron_job']} WHERE class_id = $confid")) return true;
			else return false;
		}else return false;
		
		
	}
	
}



?>