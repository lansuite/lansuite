<?php
	/*
	#	HINWEISE BITTE LESEN !!!
	#	PLEASE READ THIS INSTRUCTIONS
  #
	#	IMPORTANT !!!
	#	
	#	We request you retain the full copyright notice below including the link to www.one-network.org.
	#	This not only gives respect to the large amount of time given freely by the developers
	#	but also helps build interest, traffic and use of Lansuite.
	#	Consequently many bugs can be reported and get fixed quickly.	
	#
	#	WICHTIG !!!
	#
	#	Wir bitten Sie die gesamten Copyrightvermerke einschlie�lich des Links 
	#	zu www.one-network.org nicht zu entfernen.
	#	Dies zeigt nicht nur den Entwicklern, die eine Menge unbezahlte Zeit in dieses Projekt 
	#	gesteckt haben, Respekt, sondern tr�gt auch der Beteiligung am Support, 
	#	der Verbreitung und der Anzahl der Nutzer von Lansuite bei.
	#	Somit k�nnen viele Fehler schnell gemeldet und behoben werden.
	#		
	#		
	*/

class sitetool {
	var $timer = "";
	var $timer2 = "";
	var $send_size = "0";
	var $content = "";			// Content
	var $content_crc = "";		// Checksum of Content
	var $content_size = "";		// Size of Content

################# Script-Start (Output-Init)

	// Constructor
	function sitetool() {
		// Set Script-Start-Time, to calculate the scripts runtime
		$this->timer = time();
		$this->timer2 = explode(' ', microtime());
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
			or (ereg("(error</b>:)(.+) in <b>(.+)</b> on line <b>(.+)</b>", $index))
			or (ereg("SQL-Failure. Database respondet:", $index))
			) return 0;
		elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false) return "x-gzip";
		elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false) return "gzip"; 
		else return 0; 
	}

	// F�r Statistik
	function get_send_size(){
		return $this->send_size;	
	}

	// Finalize Output and return Outputbuffer
	function out_optimizer() {
		global $templ, $cfg, $db, $lang, $index, $design;
    
		$compression_mode = $this->check_optimizer();

		// Check for {footer}-String in Design
		if (!$_GET['contentonly'] and strpos($index, "{footer}") === false) echo "<font face=\"Verdana\" color=\"#ff0000\" site=\"6\">".t('Der Eintrag {footer} wurde unerlaubt aus der index.htm entfernt!')."</font>";
		else {

			$ru_suffix = "";
			// if (strpos($_SERVER['REQUEST_URI'], ".php") === false) $ru_suffix .= "index.php";
			// Alte fullscreen Variablen l�schen
			$_SERVER['REQUEST_URI'] = str_replace("&amp;fullscreen=yes", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("&amp;fullscreen=no", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("?fullscreen=yes", "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("?fullscreen=no", "", $_SERVER['REQUEST_URI']);

			// Vorbreiten f�r neue fullscreen Variable
			if (strpos($_SERVER['REQUEST_URI'], "?") === false) $ru_suffix .= "?";
			else $ru_suffix .= "&";

			// Erweiterung f�r Statisktik
			if ($compression_mode and $cfg['sys_compress_level']){
				$this->send_size = sprintf("%01.2f",((strlen(gzcompress($index, $cfg['sys_compress_level'])))/1024));
	   		$site_size = ' | Size: '. $this->send_size .' KB | Uncompressed: '. sprintf("%01.2f", ((strlen($index))/1024))." KB";
			} else {
        $this->send_size = sprintf("%01.2f", (strlen($index)) / 1024);
  			$site_size = " | Size: ". $this->send_size ." KB";
      }

$footer = '
<a  href="ext_inc/newsfeed/news.xml" title="Latest news feed"><img src="ext_inc/footer_buttons/button-rss.png" width="80" height="15" alt="Latest news feed" border="0" /></a>
<a  href="index.php?mod=about&action=license" rel="license" title="GNU General Public License"><img src="ext_inc/footer_buttons/button_gpl.png" width="80" height="15" alt="GNU General Public License" border="0" /></a>
<a  href="https://www.paypal.com/xclick/business=jochen.jung%40gmx.de&amp;item_name=Lansuite&amp;no_shipping=2&amp;no_note=1&amp;tax=0&amp;currency_code=EUR&amp;lc=DE" title="Donate"><img src="ext_inc/footer_buttons/button-donate.gif" alt="Donate" width="80" height="15" border="0" /></a>
<a  href="http://www.php.net" title="Powered by PHP"><img src="ext_inc/footer_buttons/button-php.gif" width="80" height="15" alt="Powered by PHP" border="0" /></a>
<a  href="http://www.mysql.com" title="MySQL Database"><img src="ext_inc/footer_buttons/mysql.gif" width="80" height="15" alt="MySQL Database" border="0" /></a>
<!--
<a  href="http://validator.w3.org/check/referer" title="Valid XHTML 1.0"><img src="ext_inc/footer_buttons/button-xhtml.png" width="80" height="15" alt="Valid XHTML 1.0" border="0" /></a>
<a  href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid CSS"><img src="ext_inc/footer_buttons/button-css.png" width="80" height="15" alt="Valid CSS" border="0" /></a>
-->
<a  href="http://www.lansuite.de" title="Lansuite"><img src="ext_inc/footer_buttons/button_lansuite.png" width="80" height="15" alt="Lansuite" border="0" /></a>
';

			// Define Footer-Message
			$footer .= HTML_NEWLINE .'<a href="index.php?mod=about" class="menu">'. $templ['index']['info']['version'].' &copy;2001-'.date('y').'</a>'
      .' | DB-Querys: '. $db->count_query
      .' | Processed in: '. round($this->out_work(), 2) .' Sec'. $site_size
			.' | <a href="'. $_SERVER['REQUEST_URI'].$ru_suffix .'fullscreen=yes" class="menu">Fullscreen</a>';

			if ($cfg["sys_optional_footer"]) $footer .= HTML_NEWLINE.$cfg["sys_optional_footer"];
      if ($_GET['contentonly']) $index .= '<div id="NewLSfooter">'. $footer .'</div>';

			$index = str_replace("{footer}", $footer, $index);

			// change & to &amp;
			$index = preg_replace("~&(?=(\w+|[a-f0-9]+)=)~i", "&amp;", $index);
			#$index = preg_replace("~&(?!(\w+|#\d+|#x[a-f0-9]+);)~i", "&amp;", $index);
      // Delete empty images
      #$index = preg_replace("~<img src=\"/\"((\w|\s|\"|\=)+)>~i", "", $index);

			if ($compression_mode and $cfg['sys_compress_level']) {
				Header("Content-Encoding: $compression_mode");
				echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
				$index = "<!-- SiteTool - Compressed by $compression_mode -->\n". $index;
				$this->content_size = strlen($index);
				$this->content_crc = crc32($index);
				$index = gzcompress($index, $cfg['sys_compress_level']);
				$index = substr($index, 0, strlen($index) - 4); // Letzte 4 Zeichen werden abgeschnitten. Aber Warum?
				echo $smarty->display('design/$design/index.tpl');
				echo pack('V', $this->content_crc) . pack('V', $this->content_size); 
			} else echo $smarty->display('design/$design/index.tpl');
		}
	}
}
?>
