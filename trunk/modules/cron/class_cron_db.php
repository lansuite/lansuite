<?php

/**
  *	Klasse um DB Funktionen zu verwalten
  *	Abgeleitet von cron_paren
  * 
  */
class cron_db extends cron_parent {
	
	
	/**
	 * Job ausführen 
	 *
	 * @param int $jobid
	 */
	function start_job($jobid){
		global $db, $config;
		
		$time = time();
		
		$job = $db->query_first("SELECT * FROM {$config["tables"]["cron_config"]} WHERE config_id = $jobid");
		
		$data = unserialize($job['cron_key']);
		
		foreach ($config['tables'] as $table){
			$all_tables .= " $table,";
		}
		
		$db_string = str_replace("%all_tables%",$all_tables,stripslashes($data['sqlexec']));
		eval("\$db_string = \"$db_string\";");

		if($db_string != ""){
			$db->query($db_string);
		}
	}
	
	
	/**
	 * Name der Funktion
	 *
	 * @return string
	 */
	function name(){
		return "DB Funktionen";
	}
	
	
	/**
	 * Konfigmenu aufrufen und den entscheidenden schritt ausführen.
	 *
	 */
	function confmenu(){
		global $func;
		
		if($_GET['step'] == 2){
			$this->config_change();
		}elseif (isset($_GET['del'])){
			$this->del_conf((int) $_GET['confid']);
			$this->config_display();
		}elseif(isset($_GET['confid'])){
			$this->config_edit();
		}else{
			$this->config_display();
		}
		
		if($_POST['rot'] != 1) $_POST['rot'] = 0;
		
		$starttime = $func->date2unixstamp($_POST["starttime_value_year"], $_POST["starttime_value_month"], $_POST["starttime_value_day"], $_POST["starttime_value_hours"],$_POST["starttime_value_mins"], 0);
		
		if($_GET['cronjob'] == "add"){
			$this->add_job("cron_db",(int) $_GET['confid'],$_POST['rot'],(int) $_POST['rottime'],$starttime);
		}
		

	}
	
	/**
	 * Alle Konfigeinträge anzeigen. 
	 *
	 */
	function config_display(){
		global $db, $dsp, $lang, $config, $func;
				
		$cron = $db->query("SELECT * FROM {$config["tables"]["cron_config"]} WHERE cron_class='cron_db'");
		
		$dsp->NewContent($lang['cron']['db_conf_menu']);
		
		if($db->num_rows($cron) == 0){
			$dsp->AddSingleRow($lang['cron']['db_conf_noentries']);
		}else{
			while ($row = $db->fetch_array($cron)){
				
				$data = unserialize($row['cron_key']);
				
				$dsp->AddDoubleRow("<a href=\"index.php?mod=cron&action=config&job=cron_db&confid={$row['config_id']}\">{$row['cron_var']}</a>",
					$lang['cron']['db_conf_rotation'] 		. " : " . 	$data['rotation'] .  "<br/>".
					$lang['cron']['db_conf_rotationtime']	. " : " . 	$data['rottime'] .  "<br/>".
					$lang['cron']['db_conf_sql_exec']		. " : " . 	$data['sqlexec'] .  "<br/>"
				);
				
			}
		}
		$dsp->AddDoubleRow("",$dsp->FetchButton('index.php?mod=cron&action=config&job=cron_db&confid=0',"add"));
		$dsp->AddBackButton('index.php?mod=cron&action=config');
		$dsp->AddContent();
		
	}

	/**
	 * Konfigurationseintrag editieren, löschen oder als neuen Job hinzufügen.
	 *
	 */
	function config_edit(){
		global $db, $dsp, $lang, $config, $func;
		
		if($_GET['confid'] != 0){
			$row = $db->query_first("SELECT * FROM {$config["tables"]["cron_config"]} WHERE config_id = '". (int) $_GET['confid'] ."'");
			$data = unserialize($row['cron_key']);
			$_POST['key'] = $row['cron_var'];
			$_POST['rot'] = $data['rotation'];
			$_POST['rottime'] = $data['rottime'];
			$_POST['sql'] = $data['sqlexec'];
			
			$dsp->NewContent($lang['cron']['db_conf_change']);
		}else{
			$dsp->NewContent($lang['cron']['db_conf_add']);
		}
		$dsp->SetForm("index.php?mod=cron&action=config&job=cron_db&confid=" . $_GET['confid'] . "&step=2");
		$dsp->AddTextFieldRow("key",$lang['cron']['db_conf_name'],$_POST['key'],"");		
		$dsp->AddCheckBoxRow("rot",$lang['cron']['db_conf_rotation'],"","",NULL,$_POST['rot']);
		$dsp->AddTextFieldRow("rottime",$lang['cron']['db_conf_rotationtime'],$_POST['rottime'],"");
		$dsp->AddTextAreaRow("sql",$lang['cron']['db_conf_sql_exec'],$_POST['sql'],"");
		
		if($_GET['confid'] != 0){
			$dsp->AddFormSubmitRow("change");	
			$dsp->AddDoubleRow("",$dsp->FetchButton("index.php?mod=cron&action=config&job=cron_db&confid=" . $_GET['confid'] . "&del=1","delete"));			
			$dsp->SetForm("index.php?mod=cron&action=config&job=cron_db&confid=" . $_GET['confid'] . "&cronjob=add");	
			$dsp->AddDateTimeRow("starttime",$lang['cron']['db_conf_start_time'],"","");
			$dsp->AddFormSubmitRow("add");
		}else{
			$dsp->AddFormSubmitRow("add");		
		}
		$dsp->AddContent();
		
	}
	
	/**
	 * Konfiguration ändern und neue einträge hinzufügen.
	 *
	 */
	function config_change(){
		global $db, $dsp, $lang, $config, $func;
		
		$data['rotation'] 	= $_POST['rot'];
		$data['rottime'] 	= $_POST['rottime'];
		$data['sqlexec'] 	= $_POST['sql'];
		
		if($_GET['confid'] == 0){
			if($db->query("INSERT INTO {$config["tables"]["cron_config"]} SET 
							cron_class 	= 'cron_db',
							cron_var	= '". $_POST['key'] ."',
							cron_key	= '". addslashes(serialize($data)) . "'")
			)$func->confirmation($lang['cron']['db_conf_add_change'],'index.php?mod=cron&action=config');
			else $func->error($lang['cron']['db_conf_err'],'index.php?mod=cron&action=config');
			
		}else{
			if($db->query("UPDATE {$config["tables"]["cron_config"]} SET 
					cron_class 	= 'cron_db',
					cron_var	= '". $_POST['key'] ."',
					cron_key	= '". addslashes(serialize($data)) . "' WHERE config_id = " . (int) $_GET['confid'])
			)$func->confirmation($lang['cron']['db_conf_add_change'],'index.php?mod=cron&action=config');
			else $func->error($lang['cron']['db_conf_err'],'index.php?mod=cron&action=config');
		}
	}
	
	
}


?>
