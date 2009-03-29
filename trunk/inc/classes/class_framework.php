<?php

/**
 * Manage the Framework/HTML output for Lansuite
 *
 * @package lansuite_core
 * @author bytekilla,knox,...
 * @version $Id$
 * @access public
 */
class framework {

  /**#@+
   * Intern Variables
   * @access private
   * @var mixed
   */
    var $timer = "";
    var $timer2 = "";
    var $send_size = "0";
    var $content_crc = "";                  // Checksum of Content
    var $content_size = "";                 // Size of Content
    
    var $internal_url_query = array();      // Clean URL-Query (keys : path, query, base)
    var $design = "simple";                 // Design
    var $modus = "";                        // Displaymodus (popup)
    var $framework_messages = "";           // All Frameworkmessages
    var $main_content = "";                 // Content
    var $main_header_metatags = "";         // Headercode for Meta Tags
    var $main_header_jsfiles = "";          // Headercode for JS-Files
    var $main_header_jscode = "";           // Headercode for JS-Code
    var $main_header_cssfiles = "";         // Headercode for CSS-Files
    var $main_header_csscode = "";          // Headercode for CSS-Code
  /**#@-*/
  
  /**
   * CONSTRUCTOR : Initialize basic Variables
   */
    function framework() {
        // Set Script-Start-Time, to calculate the scripts runtime
        $this->design = $design;
        $this->timer = time();
        $this->timer2 = explode(' ', microtime());
    }

  /**
   * Set Design
   *
   * @param string Chosen Design
   */
    function set_design($design) {
        $this->design = $design;    
    }

  /**
   * Set Displaymodus
   *
   * @param string Displaymodus (popup,base,print)
   */
    function set_modus($modus) {
        $this->modus = $modus;      
    }
    
  /**
   * Add String/Html to MainContent
   *
   * @param string Contentstring
   */
    function add_content($content) {
        $this->main_content .= $content;    
    }

  /**
   * Add JS-Code for implementing in Header
   *
   * @param string JS-Codestring
   */
    function add_js_code($jscode) {
        // Wrapt jeden code neu. Evtl. zusammenfassen
        $this->main_header_jscode .= "<script type=\"text/javascript\">\n".$jscode."\n</script>\n";
    }

  /**
   * Add JS-File for implementing in Header (as Sourcefile)
   *
   * @param string Path to JS-File
   */
    function add_js_path($jspath) {
        $this->main_header_jsfiles .= "<script src=\"".$jspath."\" type=\"text/javascript\"></script>\n";
    }

  /**
   * Add CSS-Code for implementing in Header
   *
   * @param string CSS-Codestring
   */
    function add_css_code($csscode) {
        // Wrapt jeden code neu. Evtl. zusammenfassen
        $this->main_header_csscode .= "<style type=\"text/css\">\n<!--\n".$csscode."\n-->\n</style>\n";
    }

  /**
   * Add Path vor CSS-File for implementing in Header
   *
   * @param string JS-Codestring
   */
    function add_css_path($csspath) {
        $this->main_header_cssfiles .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$csspath."\" />\n";
    }

  /**
   * Calculate the scripts runtime
   *
   * @return mixed Scriptruntime
   */
    function out_work() {
        /* Aus der Klasse Sitetool. Feine Sache, evtl. aussagekräfigeren Namen
        vergeben*/
        $timer = explode(' ', microtime());
        $worktime = $timer[1] - $this->timer2[1];
        $worktime += $timer[0] - $this->timer2[0];
        return sprintf("%.5f", $worktime);
    }

  /**
   * Check for errors in content and returns Zip-Mode
   *
   * @return string Returns the possible zip-mode
   */
    function check_optimizer() {
        if (headers_sent()
            or connection_aborted()
            or (ereg("(error</b>:)(.+) in <b>(.+)</b> on line <b>(.+)</b>", $this->main_content))
            or (ereg("SQL-Failure. Database respondet:", $this->main_content))
            ) return 0;
        elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false) return "x-gzip";
        elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false) return "gzip"; 
        else return 0; 
    }

  /**
   * Für Statistik
   *
   * @return int Returns the size
   */
    function get_send_size(){
        return $this->send_size;    
    }

  /**
   * Switch Fullscreen-Setting  
   *
   * @param string Fullscreenparameter (yes, no)
   */
    function fullscreen($fullscreen){
        if (isset($fullscreen)) {
            if ($fullscreen == 'yes')   $_SESSION['lansuite']['fullscreen'] = true;
            elseif ($fullscreen == 'no')    $_SESSION['lansuite']['fullscreen'] = false;
        }
    }


  /**
   * Make clean URL-Query for internal links  
   *
   * @param string Request-URI
   */
    function make_clean_url_query($request_uri){
        if (isset($request_uri)) {
            $CurentURL = @parse_url($request_uri);
            // Filter for Query
            $URLQuery = preg_replace('/[&]?fullscreen=(no|yes)/sUi', '', $CurentURL['query']); // Remove Fullscreenvar
            $this->internal_url_query['base'] = $CurentURL['path'].'?'.$CurentURL['query']; // Enspricht alter $CurentURLBase;
            $this->internal_url_query['query'] = $URLQuery; // Enspricht alter $URLQuery;
            $this->internal_url_query['host'] = $CurentURL['host'];
        }
    }

  /**
   * Show clean URL-Query for build internal Links    
   *
   * @param string Needed part of URL (keys : query, base)
   * @return string Returns the clean URL-Part
   */
    function get_clean_url_query($mode){
        return $this->internal_url_query[$mode];
    }

  /**
   * Display/output all HTML new Version
   *
   * @return string Returns the Complete HTML
   */
  function html_out() {
    global $dsp, $templ, $cfg, $db, $lang, $auth, $smarty, $func;
    $compression_mode = $this->check_optimizer();

    ### Prepare Header  
    if ($_GET['sitereload']) 
    $smarty->assign('main_header_sitereload', '<meta http-equiv="refresh" content="'.$_GET['sitereload'].'; URL='.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'">');
    // Add special CSS and JS
    $smarty->assign('main_header_metatags', $this->main_header_metatags);
    $smarty->assign('main_header_jsfiles', $this->main_header_jsfiles);
    $smarty->assign('main_header_jscode', $this->main_header_jscode);
    $smarty->assign('main_header_cssfiles', $this->main_header_cssfiles);
    $smarty->assign('main_header_csscode', $this->main_header_csscode);
    
    ### Switch Displaymodus (popup, base, print, normal)                    
    switch ($this->modus) {
      case 'popup': 
        // Make HTML for Popup
        $smarty->assign('MainTitle', $cfg['sys_page_title']);
        $smarty->assign('MainContentStyleID', 'ContentFullscreen');
        $smarty->assign('MainBodyJS', $templ['index']['body']['js']);
        $smarty->assign('MainJS', $templ['index']['control']['js']);
        $smarty->assign('MainLogout', '');
        $smarty->assign('MainLogo', '');
        $smarty->assign('MainContent', $this->main_content);                            
        // TODO : Rendundant... zusammenfassen
        if ($compression_mode and $cfg['sys_compress_level']) {
            Header("Content-Encoding: $compression_mode");
            echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $index = "<!-- SiteTool - Compressed by $compression_mode -->\n". $smarty->fetch("design/{$this->design}/templates/main.htm");
            $this->content_size = strlen($index);
            $this->content_crc = crc32($index);
            $index = gzcompress($index, $cfg['sys_compress_level']);
            $index = substr($index, 0, strlen($index) - 4); // Letzte 4 Zeichen werden abgeschnitten. Aber Warum?
            echo $index;
            echo pack('V', $this->content_crc) . pack('V', $this->content_size); 
        } else $smarty->display("design/{$this->design}/templates/main.htm");
      break;
  
      case 'base':
        // Make HTML for Sites Without HTML (e.g. for generation Pictures etc)
        echo $this->main_content;
      break;
	  
	  case 'ajax':
        // Make HTML for Sites Without HTML (e.g. for generation Pictures etc)
        echo $this->main_content;
      break;
  
      case 'print':
        // Make a Printpopup (without Boxes and Special CSS for printing)
        $smarty->assign('MainTitle', $cfg['sys_page_title']);
        $smarty->assign('MainContentStyleID', 'ContentFullscreen');
        $smarty->assign('MainBodyJS', $templ['index']['body']['js']);
        $smarty->assign('MainJS', $templ['index']['control']['js']);
        $smarty->assign('MainLogout', '');
        $smarty->assign('MainLogo', '');
        $smarty->assign('MainContent', $this->main_content);                            
        $smarty->display("design/simple/templates/main.htm");
      break;
  
      default :
        // Footer    
        $smarty->assign('main_footer_version', $templ['index']['info']['version']);
        $smarty->assign('main_footer_date', date('y'));
        $smarty->assign('main_footer_countquery',$db->count_query);
        $smarty->assign('main_footer_timer', round($this->out_work(), 2));
        $smarty->assign('main_footer_cleanquery', $this->get_clean_url_query('query'));
        $footer = $smarty->fetch('design/templates/footer.htm');
        if ($cfg["sys_optional_footer"]) $footer .= HTML_NEWLINE.$cfg["sys_optional_footer"];
        $smarty->assign('Footer', $footer);

        // Title
        $smarty->assign('MainTitle', $cfg['sys_page_title']);

        // Normal HTML-Output with Boxes 
        $smarty->assign('Design', $this->design);
  
        // Unterscheidung fullscreen / Normal
        if ($_SESSION['lansuite']['fullscreen']) $smarty->assign('MainContentStyleID', 'ContentFullscreen');
        else $smarty->assign('MainContentStyleID', 'Content');
        
        $smarty->assign('MainBodyJS', $templ['index']['body']['js']);
        $smarty->assign('MainJS', $templ['index']['control']['js']);

        if ($auth['login']) $smarty->assign('MainLogout', '<a href="index.php?mod=auth&action=logout" class="menu">Logout</a>');     
    
        // Ausgabe Hauptseite
        if (!$_SESSION['lansuite']['fullscreen']) {
          $smarty->assign('MainFrameworkmessages', $this->framework_messages); 
          $smarty->assign('MainLeftBox', $templ['index']['control']['boxes_letfside']);
          $smarty->assign('MainRightBox', $templ['index']['control']['boxes_rightside']);
          $smarty->assign('MainLogo', '<img src="design/'.$this->design.'/images/index_top_lansuite.gif" alt="Logo" title="Lansuite" border="0" />');
          $smarty->assign('MainDebug', $func->ShowDebug());
        } else {
          // Ausgabe Vollbildmodus    
          $smarty->assign('MainLogo', '<a href="index.php?'. $this->get_clean_url_query('query') .'&amp;fullscreen=no" class="menu"><img src="design/'. $this->design .'/images/arrows_delete.gif" border="0" alt="" /><span class="infobox">'. t('Vollbildmodus schließen') .'</span></a> Lansuite - Vollbildmodus');
        }
		
		/*$this->main_header_jscode .= "
				$(document).ready(function(){
					$('#MainContentTabs').tabs({
    					click: function(tab) {
        					location.href = $.data(tab, 'href');
        					return false;
    					}
					});
				});
		";*/
		
		// MainContent with JQuery-Tabs for LS-Messenger
		#$main_content_with_tabs .= "<div class='ui-tabs ui-widget ui-widget-content ui-corner-all' id='MainContentTabs'>\n";
		#$main_content_with_tabs .= "  <ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'>\n";
		#$main_content_with_tabs .= "    <li class='ui-state-default ui-corner-top ui-tabs-selected ui-state-active'><a href='#main_content' title='Lansuite'><em>Lansuite</em></a></li>\n";
		#$main_content_with_tabs .= "  </ul>\n";
		#$main_content_with_tabs .= "  <div class='ui-content'>\n";
		#$main_content_with_tabs .= "    <div id='main_content'>\n";
		#$main_content_with_tabs .= "    <br />\n";
		$main_content_with_tabs .= $this->main_content;
		#$main_content_with_tabs .= "    </div>\n";
		#$main_content_with_tabs .= "  </div>\n";
		#$main_content_with_tabs .= "</div>\n";
		
		$smarty->assign("MainContent", $main_content_with_tabs);
		
        // Ausgabe des Hautteils mit oder ohne Kompression
        if ($compression_mode and $cfg['sys_compress_level']) {
            Header("Content-Encoding: $compression_mode");
            echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $index = "<!-- SiteTool - Compressed by $compression_mode -->\n". $smarty->fetch("design/{$this->design}/templates/main.htm");
            $this->content_size = strlen($index);
            $this->content_crc = crc32($index);
            $index = gzcompress($index, $cfg['sys_compress_level']);
            $index = substr($index, 0, strlen($index) - 4); // Letzte 4 Zeichen werden abgeschnitten. Aber Warum?
            echo $index;
            echo pack('V', $this->content_crc) . pack('V', $this->content_size); 
        } else $smarty->display("design/{$this->design}/templates/main.htm");
      break;
    }
  }
}
?>