<?php
	/*
	#
	#
	#	HINWEISE BITTE LESEN !!!
	#	PLEASE READ THIS INSTRUCTIONS
	#
	#
	#
	#
	#	IMPORTANT !!!
	#	
	#	We request you retain the full copyright notice below including the link to www.one-network.org.
	#	This not only gives respect to the large amount of time given freely by the developers
	#	but also helps build interest, traffic and use of Lansuite 2.0.
	#	Consequently many bugs can be reported and get fixed quickly.	
	#
	#	WICHTIG !!!
	#
	#	Wir bitten Sie die gesamten Copyrightvermerke einschließlich des Links 
	#	zu www.one-network.org nicht zu entfernen.
	#	Dies zeigt nicht nur den Entwicklern, die eine Menge unbezahlte Zeit in dieses Projekt 
	#	gesteckt haben, Respekt, sondern trägt auch der Beteiligung am Support, 
	#	der Verbreitung und der Anzahl der Nutzer von Lansuite 2.0 bei.
	#	Somit können viele Fehler schnell gemeldet und behoben werden.
	#		
	#		
	*/

class sitetool {

	var $dir = "";
	var $timer = "";
	var $timer2 = "";
	var $send_size = "0";
	
	var $content = "";			// Content
	var $content_crc = "";		// Checksum of Content
	var $content_size = "";		// Size of Content


################# Script-Start (Output-Init)

	// Constructor
	function sitetool($dir) {	// dir = "" (called in index.php)
		// Still used??? Dont think so... [By KnoX]
		$this->dir = $dir;

		// Set Script-Start-Time, to calculate the scripts runtime
		// Should be called earlyer...
		$this->timer = time();
		$this->timer2 = explode(' ', microtime());

		// Activate Output-buffer
		ob_implicit_flush(0);
		ob_start();
	}


################# Script-End (Output-Compress & Send)

	// Calculate the scripts runtime
	function out_work() {
		$timer = explode(' ', microtime());
		$worktime = $timer[1] - $this->timer2[1];
		$worktime += $timer[0] - $this->timer2[0];
		return sprintf("%.5f", $worktime);
	}

	// Check for errors in content
	function check_optimizer() {
		if (headers_sent()
			or connection_aborted()
			or (ereg("(error</b>:)(.+) in <b>(.+)</b> on line <b>(.+)</b>", $this->content))
			or (ereg("SQL-Failure. Database respondet:", $this->content))
			) return 0;
		elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false) return "x-gzip";
		elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"],'gzip') !== false) return "gzip"; 
		else return 0; 
	}

	// Für Statistik
	function get_send_size(){
		return $this->send_size;	
	}

	// Finalize Output and return Outputbuffer
	function out_optimizer() {
		global $templ, $cfg, $db, $lang;

		// Get Content
		$this->content = ob_get_contents();
		ob_end_clean();

		$check = $this->check_optimizer();

		// Check for {footer}-String in Design
		if (strpos($this->content, "{footer}") === false) echo "<font face=\"Verdana\" color=\"#ff0000\" site=\"6\">{$lang['class_sitetool']['footer_violation']}</font>";
		else {

			$ru_suffix = "";
			// if (strpos($_SERVER['REQUEST_URI'], ".php") === false) $ru_suffix .= "index.php";
			// Alte fullscreen Variablen löschen
			$_SERVER['REQUEST_URI'] = str_replace("&amp;fullscreen=yes", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("&amp;fullscreen=no", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("?fullscreen=yes", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("?fullscreen=no", "", $_SERVER['REQUEST_URI']);

			// Vorbreiten für neue fullscreen Variable
			if (strpos($_SERVER['REQUEST_URI'], "?") === false) $ru_suffix .= "?";
			else $ru_suffix .= "&";

			// Erweiterung für Statisktik
			if ($check and $cfg['sys_compress_level']){
				$this->send_size = sprintf("%01.2f",((strlen(gzcompress($this->content, $cfg['sys_compress_level'])))/1024));
				$compressed = " | Compressed: ". $this->send_size ." kBytes";		
			} else $this->send_size = sprintf("%01.2f", (strlen($this->content)) / 1024);

			$uncompressed = " | Uncompressed: ".sprintf ("%01.2f", ((strlen($this->content))/1024))." KBytes";
			$processed = " | Processed in: ". $this->out_work() ." Sec";
			$dbquery = "Total DB-Querys: ". $db->count_query;

			// Define Footer-Message
			$footer = $templ['index']['info']['version']." &copy; 2001-".date("Y")." <a href=\"http://www.One-Network.org\" target=\"_blank\" class=\"menu\">One-Network.org</a>
			| All rights reserved
			| <a href=\"index.php?mod=about\" class=\"menu\">about Lansuite</a>
			| <a href=\"".$_SERVER['REQUEST_URI'].$ru_suffix."fullscreen=yes\" class=\"menu\">Vollbild</a>
			<br/>$dbquery $processed $compressed $uncompressed";

			if ($cfg["sys_optional_footer"]) $footer .= $cfg["sys_optional_footer"];
			$this->content = str_replace("{footer}", $footer, $this->content);

			// change & to &amp;
			$this->content = preg_replace("~&(?=(\w+|[a-f0-9]+)=)~i", "&amp;", $this->content);
			// $this->content = preg_replace("~&(?!(\w+|#\d+|#x[a-f0-9]+);)~i", "&amp;", $this->content);
			$this->content = preg_replace("~<img src=\"/\"((\w|\s|\"|\=)+)>~i", "", $this->content);

			if ($check and $cfg['sys_compress_level']) {
				Header("Content-Encoding: $check");
				echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				$this->content = "<!-- SiteTool - Compressed by $check -->\n".$this->content;
				$this->content_size = strlen($this->content);
				$this->content_crc = crc32($this->content);
				$this->content = gzcompress($this->content, $cfg['sys_compress_level']);
				$this->content = substr($this->content, 0, strlen($this->content) - 4); // Letzte 4 Zeichen werden abgeschnitten. Aber Warum?
				echo $this->content;
				echo pack('V', $this->content_crc) . pack('V', $this->content_size); 
			} else echo $this->content;
		}
	}
}
?>
