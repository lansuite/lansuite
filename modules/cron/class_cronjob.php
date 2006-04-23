<?php

include_once("modules/cron/class_parent.php");

class cronjob{

	var $class_id;
	var $job_class;
	var $rottime;
	var $rot;
	var $function;
	var $loaded_class;

	/**
	 * Auf anstehende Aufgaben Prüfen
	 * Falls eine Aufgabe ansteht wird sie ausgeführt und entweder gelöscht oder die Ausführzeit neu gesetzt
	 * Es wird pro Seitenaufruf immer nur eine Aufgabe ausgeführt um Systemlast zu minimieren.
	 * 
	 */
	function check_jobs(){
		global $db, $config;
		$time = time();
		
		$job = $db->query("SELECT * FROM {$config['tables']['cron_job']} WHERE starttime < {$time} ORDER BY starttime ASC LIMIT 0,1");
		
		if($db->num_rows($job) != 0){
			$job_data = $db->fetch_array($job);
			
			$this->class_id = $job_data['class_id'];
			$this->rot = $job_data['rot'];
			$this->rottime = $job_data['rottime'];
			$this->job_class = $job_data['job_class'];
			$this->function = $job_data['function'];
			
			$this->start_job($job_data['job_class'],$job_data['class_id']);

			if($job_data['rotation'] == 1){
				$db->query("UPDATE {$config['tables']['cron_job']} SET starttime = ($time + {$job_data['rottime']}) WHERE jobid = ". $job_data['jobid']);
			}else {
				$db->query("DELETE FROM {$config['tables']['cron_job']} WHERE jobid = ". $job_data['jobid']);				
			}
		}
	}
	
	
	/**
	 * Ausführen einer Aufgabe.
	 * Mit dieser Funktion wird die entspechende Funktion zum ausführen des Jobs aufgerufen.
	 *
	 * @param unknown_type $job_class
	 * @param unknown_type $job_id
	 */
	function start_job($job_class,$job_id){
		
		if(file_exists("modules/cron/class_{$job_class}.php")){
			include_once("modules/cron/class_{$job_class}.php");
			$class = new $job_class($this);
			
			$class->start_job($job_id);
		}
		
	}
	
	/**
	 * Cronmodul laden und zugreifbar machen.
	 *
	 */
	function load_job($job_class){
		if(file_exists("modules/cron/class_{$job_class}.php")){
			include_once("modules/cron/class_{$job_class}.php");
			$this->loaded_class = new $job_class($this);
		}
	}
		
	/**
	 * Anzeigen aller anstehenden Aufgaben. 
	 * Hier können auch Aufgaben gelöscht werden.
	 *
	 */
	function menu_joblist(){
		global $dsp, $lang, $db, $config, $vars, $templ;
		
		$cron = new cron_parent($this);
		
		if($_GET['del_job']){
			foreach ($_POST['action'] as $key => $val) $cron->del_job($key);
		}else{
      include_once('modules/mastersearch2/class_mastersearch2.php');
      $ms2 = new mastersearch2('news');

      $ms2->query['from'] = "{$config['tables']['cron_job']} AS j
        LEFT JOIN {$config['tables']['cron_config']} AS c ON j.class_id = c.config_id";

      $ms2->AddTextSearchField('Job', array('j.job_class' => 'like', 'c.cron_var' => 'like'));

      $ms2->AddResultField('Cron-Klasse', 'j.job_class');
      $ms2->AddResultField('Name', 'c.cron_var');
      $ms2->AddResultField('Nächste Ausführung', 'j.starttime', 'MS2GetDate');

      #$ms2->AddIconField('details', 'index.php?mod=cron&action=joblist', $lang['ms2']['details']);
      $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=cron&action=joblist&del_job=1', 1);

      $ms2->PrintSearch('index.php?mod=cron&action=joblist', 'j.jobid');
		}
	}
	
	/**
	 * Allgemeines oder spezifisches Konfigurationsmenu aufrufen.
	 *
	 * @param int $job
	 */
	function menu_config($job = ""){
		global $dsp;
		
		// Sucht alle class_cron.. Klassen und gibt den Link zu spezifischen Konfig aus
		if($job == ""){
			$handle = opendir("modules/cron/");
			$dsp->NewContent($lang['cron']['config_cap']);
			while ($file = readdir($handle)){

				if(stristr($file,"class_cron_")){
					$class_name = substr($file,6,-4);
					if(file_exists("modules/cron/$file")){
						include_once("modules/cron/$file");
						if(class_exists($class_name)){
							$new_class = new $class_name($this);
							$dsp->AddDoubleRow("","<a href=\"index.php?mod=cron&action=config&job=" . urlencode($class_name) . "\">" . $new_class->name() . "</a>");
						}
					}
				}
			}
			$dsp->AddContent();
		}else{
			// Aufruf der spezifischen Konfig des Moduls.
			$job = urldecode($job);
			include_once("modules/cron/class_{$job}.php");
			$class = new $job($this);
			
			$class->confmenu();
		}		
	}
	
}


?>