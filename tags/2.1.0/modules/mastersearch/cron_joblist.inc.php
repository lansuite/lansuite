<?php

	$this->config['search_fields'][]  = "j.job_class";
	$this->config['search_fields'][]  = "c.cron_var";
	$this->config['sql_statment']     = "SELECT * FROM {$config['tables']['cron_job']} AS j JOIN {$config['tables']['cron_config']} AS c ON j.class_id=c.config_id ";
					
	$this->config['orderby']          = "starttime, ASC";
	$this->config['linkcol']       	  = "jobid";
	$this->config['entrys_page']      = $config["size"]["table_rows"];
	$this->config['list_only']		  = true;

	$this->config['hidden_searchform'] = true;	

	$this->config['no_items_caption'] = $lang['ms']['cron']['no_items_caption'];
	$this->config['no_items_link']	  = "";

	$z = 0;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['cron']['class'];
	$this->config['result_fields'][$z]['sqlrow']   = "j.job_class";
	$this->config['result_fields'][$z]['row']      = "job_class";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "20";
	$this->config['result_fields'][$z]['checkbox']   = "checkbox";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['cron']['caption'];
	$this->config['result_fields'][$z]['sqlrow']   = "c.cron_var";
	$this->config['result_fields'][$z]['row']      = "cron_var";
	$this->config['result_fields'][$z]['width']    = "40%";
	$this->config['result_fields'][$z]['maxchar']  = "50";
	$z++;
	$this->config['result_fields'][$z]['name']     = $lang['ms']['cron']['starttime'];
	$this->config['result_fields'][$z]['sqlrow']   = "j.starttime";
	$this->config['result_fields'][$z]['row']      = "starttime";
	$this->config['result_fields'][$z]['width']    = "20%";
	$this->config['result_fields'][$z]['maxchar']  = "10";
	$this->config['result_fields'][$z]['callback'] = "GetDate";
	$z++;

	$this->config['action_select']['select_all']	= $lang['ms']['select_all'];
	$this->config['action_select']['select_none']	= $lang['ms']['select_none'];
	$this->config['action_select']['hr']	= "------------------------";
	$this->config['action_select']['del']	= $lang['ms']['cron']['delete'];
	$this->config['action_secure']['del']	= 1;
?>