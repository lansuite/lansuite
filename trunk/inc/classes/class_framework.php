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
    var $IsMobileBrowser = false;
    var $pageTitle = '';
  /**#@-*/
  
  /**
   * CONSTRUCTOR : Initialize basic Variables
   */
    function framework() {
        // Set Script-Start-Time, to calculate the scripts runtime
        $this->design = $design;
        $this->timer = time();
        $this->timer2 = explode(' ', microtime());

        if (isset($_SERVER['REQUEST_URI'])) {
          if ($CurentURL = parse_url($_SERVER['REQUEST_URI'])) {
            $this->internal_url_query['base'] = $CurentURL['path'].'?'.$CurentURL['query']; // Enspricht alter $CurentURLBase;
            $this->internal_url_query['query'] = preg_replace('/[&]?fullscreen=(no|yes)/sUi', '', $CurentURL['query']); // Enspricht alter $URLQuery;
            $this->internal_url_query['host'] = $CurentURL['host'];
          }
        }
        if (!$this->internal_url_query['host']) $this->internal_url_query['host'] = $_SERVER['SERVER_NAME'];
        
        $this->add_js_path('ext_scripts/jquery-1.3.2.min.js');
        $this->add_js_path('ext_scripts/jquery-ui/jquery-ui-1.7.2.custom.min.js');
        $this->add_js_path('scripts.js');

        $this->add_css_path('ext_scripts/jquery-ui/smoothness/jquery-ui-1.7.2.custom.css');
        $this->add_css_path('design/style.css');
        
        if ($this->internal_url_query['query']) {
          $query = preg_replace('/&language=(de|en|it|fr|es|nl)/sUi', '', $this->internal_url_query['query']);
          $query = preg_replace('/&order_by=(.)*&/sUi', '&', $query);
          $query = preg_replace('/&order_dir=(asc|desc)/sUi', '', $query);
          $query = preg_replace('/&EntsPerPage=[0..9]*/sUi', '', $query);
          $this->main_header_metatags = '<link rel="canonical" href="index.php?'. $query .'" />';
        }
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
      global $PHPErrorsFound, $db;

      if (headers_sent() or connection_aborted() or $PHPErrors or (isset($db) and $db->errorsFound)) return 0;
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
   * Show clean URL-Query for build internal Links    
   *
   * @param string Needed part of URL (keys : query, base)
   * @return string Returns the clean URL-Part
   */
    function get_clean_url_query($mode){
        return $this->internal_url_query[$mode];
    }

  function AddToPageTitle($add) {
    global $cfg;

    if ($add) {
      if ($this->pageTitle == '') $this->pageTitle = $add;
      else $this->pageTitle .= ' - '. $add;
    }
  } 

  /**
   * Display/output all HTML new Version
   *
   * @return string Returns the Complete HTML
   */
  function html_out() {
    global $dsp, $templ, $cfg, $db, $lang, $auth, $smarty, $func, $debug;
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

    $smarty->assign('IsMobileBrowser', $this->IsMobileBrowser);
    $smarty->assign('DisplayMode', $this->modus);

    $smarty->assign('MainTitle', $this->pageTitle);
    $smarty->assign('MainLogout', '');
    $smarty->assign('MainLogo', '');
    $smarty->assign('MainBodyJS', $templ['index']['body']['js']);
    $smarty->assign('MainJS', $templ['index']['control']['js']);
    $smarty->assign('MainContent', $this->main_content);

    $EndJS = '';
    if ($cfg['google_analytics_id']) $EndJS = '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("'. $cfg['google_analytics_id'] .'");
pageTracker._trackPageview();
</script>';
    $smarty->assign('EndJS', $EndJS);

    ### Switch Displaymodus (popup, base, print, normal)                    
    switch ($this->modus) {
      case 'print':
        // Make a Printpopup (without Boxes and Special CSS for printing)
        $smarty->assign('MainContentStyleID', 'ContentFullscreen');
        $smarty->display("design/simple/templates/main.htm");
      break;
  
      case 'popup': 
        // Make HTML for Popup
        $smarty->assign('MainContentStyleID', 'ContentFullscreen');

        // TODO : Rendundant... zusammenfassen
        if ($compression_mode and $cfg['sys_compress_level']) {
          header("Content-Encoding: $compression_mode");
          echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
          $index = $smarty->fetch("design/{$this->design}/templates/main.htm"). "\n<!-- Compressed by $compression_mode -->";
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
  
      default :
        // Footer    
        $smarty->assign('main_footer_version', $templ['index']['info']['version']);
        $smarty->assign('main_footer_date', date('y'));
        $smarty->assign('main_footer_countquery',$db->count_query);
        $smarty->assign('main_footer_timer', round($this->out_work(), 2));
        $smarty->assign('main_footer_cleanquery', $this->get_clean_url_query('query'));
        
        $main_footer_mem_usage = '';
        if (function_exists('memory_get_peak_usage')) $main_footer_mem_usage = 'Memory-Usage: '. $func->FormatFileSize(memory_get_peak_usage()) .' |';
        $smarty->assign('main_footer_mem_usage', $main_footer_mem_usage);

        $footer = $smarty->fetch('design/templates/footer.htm');

        if ($cfg["sys_optional_footer"]) $footer .= HTML_NEWLINE.$cfg["sys_optional_footer"];
        $smarty->assign('Footer', $footer);

        // Normal HTML-Output with Boxes 
        $smarty->assign('Design', $this->design);
  
        // Unterscheidung fullscreen / Normal
        if ($_SESSION['lansuite']['fullscreen']) $smarty->assign('MainContentStyleID', 'ContentFullscreen');
        else $smarty->assign('MainContentStyleID', 'Content');
        
        if ($auth['login']) $smarty->assign('MainLogout', '<a href="index.php?mod=auth&action=logout" class="menu">Logout</a>');     
    
        // Ausgabe Hauptseite
        if (!$_SESSION['lansuite']['fullscreen']) {
          $smarty->assign('MainFrameworkmessages', $this->framework_messages); 
          $smarty->assign('MainLeftBox', $templ['index']['control']['boxes_letfside']);
          $smarty->assign('MainRightBox', $templ['index']['control']['boxes_rightside']);
          $smarty->assign('MainLogo', '<img src="design/'.$this->design.'/images/lansuite-logo.gif" alt="Lansuite Logo" title="Lansuite Logo" border="0" />');
          if ($auth['type'] >= 2 and isset($debug)) { // and $cfg['sys_showdebug'] (no more, for option now in inc/base/config)
            $smarty->assign('MainDebug', $debug->show());
          }
        } else {
          // Ausgabe Vollbildmodus    
          $smarty->assign('CloseFullscreen', '<a href="index.php?'. $this->get_clean_url_query('query') .'&amp;fullscreen=no" class="menu"><img src="design/'. $this->design .'/images/arrows_delete.gif" border="0" alt="" /><span class="infobox">'. t('Vollbildmodus schließen') .'</span> Lansuite - Vollbildmodus</a>');
        }
		
    		// Start Javascript-Code for MainContent with JQuery-Tabs
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
    		#$main_content_with_tabs .= $this->main_content;
    		#$main_content_with_tabs .= "    </div>\n";
    		#$main_content_with_tabs .= "  </div>\n";
    		#$main_content_with_tabs .= "</div>\n";
    		
    		#$smarty->assign("MainContent", $main_content_with_tabs);
		
        // Ausgabe des Hautteils mit oder ohne Kompression
        if ($compression_mode and $cfg['sys_compress_level']) {
          header("Content-Encoding: $compression_mode");
          echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
          $index = $smarty->fetch("design/{$this->design}/templates/main.htm") ."\n<!-- Compressed by $compression_mode -->";
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
