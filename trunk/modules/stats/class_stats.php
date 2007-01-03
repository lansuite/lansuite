<?php

class stats {

	var $stat_data = array();
	
	// Constructor
	function stats() {
		global $db, $config, $auth;

		// Update usage stats
		// Is the user known, or is it a new visit? - After 30min idle this counts as a new visit

    // Existing session -> Only hit
		if ($_COOKIE['last_hit'] > (time() - 60 * 30)) {
			$find = $db->query_first_rows("SELECT hits FROM {$config["tables"]["stats_usage"]} WHERE DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
			if ($find["number"] > 0) $db->query("UPDATE {$config["tables"]["stats_usage"]} SET hits = hits + 1 WHERE DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
			else $db->query("INSERT INTO {$config["tables"]["stats_usage"]} SET visits = 0, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");

    // New session -> Hit and visit
    } else {
			$find = $db->query_first_rows("SELECT hits FROM {$config["tables"]["stats_usage"]} WHERE DATE_FORMAT(time, '%Y%m%d%H') = DATE_FORMAT(NOW(), '%Y%m%d%H')");
			if ($find["number"] > 0) $db->query("UPDATE {$config["tables"]["stats_usage"]} SET visits = visits + 1, hits = hits + 1 WHERE DATE_FORMAT(time, '%Y-%m-%d %H:00:00') = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
			else $db->query("INSERT INTO {$config["tables"]["stats_usage"]} SET visits = 1, hits = 1, time = DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')");
		}
    setcookie('last_hit', time(), time() + (30 * 60));


		// Update search engine data
		$search_engine = '';
		if (strpos($_SERVER['http_referer'], 'ttp://www.google.') > 0) $search_engine = 'google';
		elseif (strpos($_SERVER['http_referer'], '.yahoo.com/search') > 0) $search_engine = 'yahoo';
		elseif (strpos($_SERVER['http_referer'], '.altavista.com') > 0) $search_engine = 'altavista';
		elseif (strpos($_SERVER['http_referer'], 'ttp://search.msn.') > 0) $search_engine = 'msn';
		elseif (strpos($_SERVER['http_referer'], '.aol.de/suche') > 0) $search_engine = 'aol_de';
		elseif (strpos($_SERVER['http_referer'], 'search.aol.com/') > 0) $search_engine = 'aol_com';
		elseif (strpos($_SERVER['http_referer'], '.web.de/') > 0) $search_engine = 'web_de';

		if ($search_engine != '') {
  		$query_var = array(
  			"google" => 'q',
  			"yahoo" => "p",
  			"altavista" => "q",
  			"msn" => "q",
  			"aol_de" => "q",
  			"aol_com" => "query",
  			"web_de" => "su"
  			);

			// Read URL parameters into an array
			$url_paras = explode ("?", $_SERVER["http_referer"]); // URL part behind ? -> $url_paras[1]
			$url_paras = explode ("&", $url_paras[1]);

			foreach($url_paras as $akt_para){
				list($para_var, $para_val) = split ("=", $akt_para);

				// Search for parameter containing the search term
				if ($para_var == $query_var[$search_engine]){
					$row = $db->query_first_rows("SELECT term FROM {$config["tables"]["stats_se"]} WHERE term = '$para_val' AND se = '$search_engine'");
					if ($row["number"] > 0) $db->query("UPDATE {$config["tables"]["stats_se"]} SET hits = hits + 1, last = '". time() ."' WHERE term = '$para_val' AND se = '$search_engine'");
					else $db->query("INSERT INTO {$config["tables"]["stats_se"]} SET hits = 1, term = '$para_val', se = '$search_engine', first = '". time() ."', last = '". time() ."'");
				}
			}
		}
	}


	// this function is called on each page
	function update($time, $size) {
		global $db, $config, $auth;

		// Update duration and traffic
    $time = round($time, 0);
    $size = round($size, 0);
		$db->query("UPDATE {$config["tables"]["stats"]} SET hits = hits + 1, time = time + '$time', size = size + '$size'");
	}
	
	// Auslesen der CPU Informationen
	function cpu_info() {
		$data['cpu_info'] = eregi_replace("model name	:","",nl2br(shell_exec("cat /proc/cpuinfo | grep 'model name'")));
		return $data;
	}
	
	// Auslesen des Speichers
	function mem_info() {

		$version = file("/proc/version");
		$version = $version[0];
		
		$memory = file("/proc/meminfo");

		//speicher auslesen aus Kernel 2.6 und 2.5
		if (preg_match ("/\bLinux version 2\.5\b/i", $version) || preg_match ("/\bLinux version 2\.6\b/i", $version))  {
			$data['mem_total'] = explode(":",$memory[0]);    
			$data['mem_total'] = explode("kB",$data['mem_total'][1]);  
			$data['mem_total'] = round($data['mem_total'][0] / 1024,"2");
			$data['mem_free'] = explode(":",$memory[1]);     
			$data['mem_free'] = explode("kB",$data['mem_free'][1]);   
			$data['mem_free'] = round($data['mem_free'][0] / 1024,"2");
			$data['swap_total'] = explode(":",$memory[11]); 
			$data['swap_total'] = explode("kB",$data['swap_total'][1]); 
			$data['swap_total'] = round($data['swap_total'][0] / 1024,"2");
			$data['swap_free'] = explode(":",$memory[12]);  
			$data['swap_free'] = explode("kB",$data['swap_free'][1]);   
			$data['swap_free'] = round($data['swap_free'][0] / 1024,"2");
		}

		// Speicher auslesen f�r Kernel 2.4
		elseif (preg_match ("/\bLinux version 2\.4\b/i", $version)) {
			$data['mem_total'] = explode(":",$memory[3]);    
			$data['mem_total'] = explode("kB",$data['mem_total'][1]);  
			$data['mem_total'] = round($data['mem_total'][0] / 1024,"2");
			$data['mem_free'] = explode(":",$memory[4]);     
			$data['mem_free'] = explode("kB",$data['mem_free'][1]);    
			$data['mem_free'] = round($data['mem_free'][0] / 1024,"2");
			$data['swap_total'] = explode(":",$memory[15]); 
			$data['swap_total'] = explode("kB",$data['swap_total'][1]); 
			$data['swap_total'] = round($data['swap_total'][0] / 1024,"2");
			$data['swap_free'] = explode(":",$memory[16]);  
			$data['swap_free'] = explode("kB",$data['swap_free'] [1]);   
			$data['swap_free'] = round($data['swap_free'] [0] / 1024,"2");
		}

		return $data;
	}
	
	
	function load_avg(){
		$loadavg = file ("/proc/loadavg");
		$loadavg = split(" ",$loadavg[0]);
		
		return $loadavg;
	}
	
	// Auslesen der Uptime
	function uptime() {
		//uptime 
		$uptime = file("/proc/uptime");
		$uptime = explode(" ",$uptime[0]);
		$uptime = round($uptime[0],"0");

		return $uptime;
	}
	
	// Netzwerkdaten 
	function ifconfig(){
		exec("/sbin/ifconfig", $ifconfig_output);
		foreach ($ifconfig_output AS $line) { $network_info .= $line; }

		$RX_bytes = explode("RX bytes:",$network_info);
		$RX_bytes = explode(" ",$RX_bytes[1]); $RX_bytes = $RX_bytes[0];
		
		$data['rxmbytes'] = round(($RX_bytes / 1024 / 1024),"1");
		
		if ($RX_bytes > (1024*1024)) $data['RX'] = round(($RX_bytes / 1024 / 1024),"1") . " Mbyte";
		elseif ($RX_bytes > 1024) $data['RX'] = round(($RX_bytes / 1024),"1") . " kbyte";
		else $data['RX'] = $RX_bytes . " Byte";

		$TX_bytes = explode("TX bytes:",$network_info);
		$TX_bytes = explode(" ",$TX_bytes[1]); $TX_bytes = $TX_bytes[0];

		$data['txmbytes'] = round(($TX_bytes / 1024 / 1024),"1");
		
		if ($TX_bytes > (1024*1024)) $data['TX'] = round(($TX_bytes / 1024 / 1024),"1") . " Mbyte";
		elseif ($TX_bytes > 1024) $data['TX'] = round(($TX_bytes / 1024),"1") . " kbyte";			
		else $data['TX'] = $TX_bytes . " Byte";

		return $data;
	}

	function getExportData() {
		global $cfg, $config;
		
		$stats['name']  	= $_SESSION['party_info']['name'];
		$stats['url'] 		= $cfg["sys_partyurl"];
		$stats['plz'] 		= $_SESSION['party_info']['partyplz'];
		$stats['surl'] 		= $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];		
		$stats['guests']	= $_SESSION['party_info']['max_guest'];
		$stats['start'] 	= $_SESSION['party_info']['partybegin'];
		$stats['end'] 		= $_SESSION['party_info']['partyend'];
		$stats['mail'] 		= $cfg["sys_party_mail"];
		$stats['version'] 	= $config['lansuite']['version'];

		return $stats;		
	}
	
	function export() {
		$stats = $this->getExportData();

		$stats_data  = 'name='.urlencode($stats["name"]);
		$stats_data .= '&url='.urlencode($stats["url"]);
		$stats_data .= '&plz='.urlencode($stats["plz"]);
		$stats_data .= '&surl='.urlencode($stats['surl']);		
		$stats_data .= '&guests='.$stats["guests"];
		$stats_data .= '&start='.$stats["start"];
		$stats_data .= '&end='.$stats["end"];
		$stats_data .= '&mail='.$stats["mail"];
		$stats_data .= '&version='.urlencode($stats['version']);

		include("http://www.lansuite.de/report.php?".$stats_data);		
	}	
}
?>
