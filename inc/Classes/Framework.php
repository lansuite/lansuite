<?php

namespace LanSuite;

class Framework
{

    /**
     * @var int|string
     */
    private $timer = '';

    /**
     * @var array|string
     */
    private $timer2 = '';

    /**
     * Checksum of Content
     *
     * @var string
     */
    private $content_crc = '';

    /**
     * Size of Content
     *
     * @var string
     */
    private $content_size = '';

    /**
     * Clean URL-Query (keys : path, query, base)
     *
     * @var array
     */
    public $internal_url_query = [];

    /**
     * Design
     *
     * @var string
     */
    private $design = "simple";

    /**
     * Displaymodus (popup)
     *
     * @var string
     */
    public $modus = '';

    /**
     * All framework messages
     *
     * @var string
     */
    private $framework_messages = '';

    /**
     * Content
     *
     * @var string
     */
    private $main_content = '';

    /**
     * Headercode for Meta Tags
     *
     * @var string
     */
    private $main_header_metatags = '';

    /**
     * Headercode for JS-Files
     *
     * @var string
     */
    private $main_header_jsfiles = '';

    /**
     * Headercode for JS-Code
     *
     * @var string
     */
    private $main_header_jscode = '';

    /**
     * Headercode for CSS-Files
     *
     * @var string
     */
    private $main_header_cssfiles = '';

    /**
     * Headercode for CSS-Code
     *
     * @var string
     */
    private $main_header_csscode = '';

    /**
     * @var bool
     */
    public $IsMobileBrowser = false;

    /**
     * @var string
     */
    private $pageTitle = '';

    public function __construct()
    {
        // Set Script-Start-Time, to calculate the scripts runtime
        $this->design = '';
        $this->timer = time();
        $this->timer2 = explode(' ', microtime());

        if (isset($_SERVER['REQUEST_URI'])) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
                $url = 'https://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            } else {
                $url = 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            }

            $CurentURL = parse_url($url);
            $this->internal_url_query['base'] = $CurentURL['path'];
            $this->internal_url_query['query'] = '';
            if (isset($CurentURL['query']) && $CurentURL['query']) {
                $this->internal_url_query['base'] .= '?' . $CurentURL['query'];
                $this->internal_url_query['query'] = preg_replace('/[&]?fullscreen=(no|yes)/sUi', '', $CurentURL['query']);
            }
            $this->internal_url_query['host'] = $CurentURL['host'];
        }
        if (!$this->internal_url_query['host']) {
            $this->internal_url_query['host'] = $_SERVER['SERVER_NAME'];
        }

        $this->add_js_path('ext_scripts/jquery-min.js');
        $this->add_js_path('ext_scripts/jquery-ui/jquery-ui.custom.min.js');
        $this->add_js_path('scripts.js');

        $this->add_css_path('ext_scripts/jquery-ui/smoothness/jquery-ui.custom.css');
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
     * Set the design
     *
     * @param string $design Chosen Design
     * @return void
     */
    public function set_design($design)
    {
        $this->design = $design;
    }

    /**
     * Set the display modus
     *
     * @param string $modus Displaymodus (popup,base,print)
     * @return void
     */
    public function set_modus($modus)
    {
        $this->modus = $modus;
    }

    /**
     * Add String/Html to MainContent
     *
     * @param string $content
     * @return void
     */
    public function add_content($content)
    {
        $this->main_content .= $content;
    }

    /**
     * Add JS-Code for implementation in header
     *
     * @param $jscode
     * @return void
     */
    public function add_js_code($jscode)
    {
        $this->main_header_jscode .= "<script type=\"text/javascript\">\n".$jscode."\n</script>\n";
    }

    /**
     * Add JS-File for implementation in header (as sourcefile)
     *
     * @param string $jspath Path to JS-File
     * @return void
     */
    public function add_js_path($jspath)
    {
        $this->main_header_jsfiles .= "<script src=\"".$jspath."\" type=\"text/javascript\"></script>\n";
    }

    /**
     * Add CSS-Code for implementation in header
     *
     * @param string $csscode
     * @return void
     */
    public function add_css_code($csscode)
    {
        $this->main_header_csscode .= "<style type=\"text/css\">\n<!--\n".$csscode."\n-->\n</style>\n";
    }

    /**
     * Add Path for a CSS-File to be included in the header output
     *
     * @param string $csspath
     * @return void
     */
    public function add_css_path($csspath)
    {
        $this->main_header_cssfiles .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$csspath."\" />\n";
    }

    /**
     * Calculate the scripts runtime
     *
     * @return string
     */
    private function out_work()
    {
        $timer = explode(' ', microtime());
        $worktime = $timer[1] - $this->timer2[1];
        $worktime += $timer[0] - $this->timer2[0];

        return sprintf("%.5f", $worktime);
    }

    /**
     * Check for errors in content and returns Zip-Mode
     *
     * @return int|string
     */
    private function check_optimizer()
    {
        global $PHPErrors, $db;

        if (headers_sent() || connection_aborted() || $PHPErrors || (isset($db) && $db->errorsFound)) {
            return 0;
        } elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false) {
            return "x-gzip";
        } elseif (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false) {
            return "gzip";
        }

        return 0;
    }

    /**
     * Switch Fullscreen-Setting
     *
     * @param string $fullscreen
     * @return void
     */
    public function fullscreen($fullscreen)
    {
        if (isset($fullscreen)) {
            if ($fullscreen == 'yes') {
                $_SESSION['lansuite']['fullscreen'] = true;
            } elseif ($fullscreen == 'no') {
                $_SESSION['lansuite']['fullscreen'] = false;
            }
        }
    }

    /**
     * Show clean URL-Query for build internal Links
     *
     * @param string $mode Needed part of URL (keys : query, base)
     * @return mixed
     */
    public function get_clean_url_query($mode)
    {
        return $this->internal_url_query[$mode];
    }

    /**
     * @param string $add
     * @return void
     */
    public function AddToPageTitle($add)
    {
        if ($add) {
            if ($this->pageTitle == '') {
                $this->pageTitle = $add;
            } else {
                $this->pageTitle .= ' - '. $add;
            }
        }
    }

    /**
     * Display/output all HTML new version
     *
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function html_out()
    {
        global $templ, $cfg, $db, $auth, $smarty, $func, $debug;
        $compression_mode = $this->check_optimizer();

        // Prepare Header
        if ($_GET['sitereload']) {
            $smarty->assign('main_header_sitereload', '<meta http-equiv="refresh" content="'.$_GET['sitereload'].'; URL='.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'">');
        }

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
        if ($cfg['google_analytics_id']) {
            $EndJS = "<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', " . json_encode($cfg['google_analytics_id']) . ", 'auto');
ga('set', 'anonymizeIp', true);
ga('send', 'pageview');
</script>";
        }
        $smarty->assign('EndJS', $EndJS);

        // Switch Displaymodus (popup, base, print, normal, beamer)
        switch ($this->modus) {
            case 'print':
                // Make a Printpopup (without Boxes and Special CSS for printing)
                $smarty->assign('MainContentStyleID', 'ContentFullscreen');
                $smarty->display("design/simple/templates/main.htm");
                break;

            case 'popup':
                // Make HTML for Popup
                $smarty->assign('MainContentStyleID', 'ContentFullscreen');

                if ($compression_mode && $cfg['sys_compress_level']) {
                    header("Content-Encoding: $compression_mode");
                    echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
                    $index = $smarty->fetch("design/{$this->design}/templates/main.htm"). "\n<!-- Compressed by $compression_mode -->";
                    $this->content_size = strlen($index);
                    $this->content_crc = crc32($index);
                    $index = gzcompress($index, $cfg['sys_compress_level']);
                    $index = substr($index, 0, strlen($index) - 4); // Letzte 4 Zeichen werden abgeschnitten. Aber Warum?
                    echo $index;
                    echo pack('V', $this->content_crc) . pack('V', $this->content_size);
                } else {
                    $smarty->display("design/{$this->design}/templates/main.htm");
                }
                break;

            case 'base':
                // Make HTML for Sites Without HTML (e.g. for generation Pictures etc)
                echo $this->main_content;
                break;

            case 'ajax':
                // Make HTML for Sites Without HTML (e.g. for generation Pictures etc)
                echo $this->main_content;
                break;

            default:
                // Footer
                $smarty->assign('main_footer_version', $templ['index']['info']['version']);
                $smarty->assign('main_footer_date', date('y'));
                $smarty->assign('main_footer_countquery', $db->count_query);
                $smarty->assign('main_footer_timer', round($this->out_work(), 2));
                $smarty->assign('main_footer_cleanquery', $this->get_clean_url_query('query'));

                if ($cfg["sys_footer_impressum"]) {
                    $smarty->assign('main_footer_impressum', $cfg["sys_footer_impressum"]);
                }

                $main_footer_mem_usage = '';
                if (function_exists('memory_get_peak_usage')) {
                    $main_footer_mem_usage = 'Memory-Usage: '. $func->FormatFileSize(memory_get_peak_usage()) .' |';
                }
                $smarty->assign('main_footer_mem_usage', $main_footer_mem_usage);

                $footer = $smarty->fetch('design/templates/footer.htm');

                if ($cfg["sys_optional_footer"]) {
                    $footer .= HTML_NEWLINE.$cfg["sys_optional_footer"];
                }
                $smarty->assign('Footer', $footer);

                // Normal HTML-Output with Boxes
                $smarty->assign('Design', $this->design);

                // Unterscheidung fullscreen / Normal
                if ($_SESSION['lansuite']['fullscreen'] or $this->modus == 'beamer') {
                    $smarty->assign('MainContentStyleID', 'ContentFullscreen');
                } else {
                    $smarty->assign('MainContentStyleID', 'Content');
                }

                if ($auth['login']) {
                    $smarty->assign('MainLogout', '<a href="index.php?mod=auth&action=logout" class="menu">Logout</a>');
                }

                // Ausgabe Hauptseite
                if (!$_SESSION['lansuite']['fullscreen'] and !$this->modus == 'beamer') {
                    $smarty->assign('MainFrameworkmessages', $this->framework_messages);
                    $smarty->assign('MainLeftBox', $templ['index']['control']['boxes_letfside']);
                    $smarty->assign('MainRightBox', $templ['index']['control']['boxes_rightside']);
                    $smarty->assign('MainLogo', '<img src="design/'.$this->design.'/images/lansuite-logo.gif" alt="Lansuite Logo" title="Lansuite Logo" border="0" />');
                    if ($auth['type'] >= 2 and isset($debug)) { // and $cfg['sys_showdebug'] (no more, for option now in inc/base/config)
                        $smarty->assign('MainDebug', $debug->show());
                    }
                } elseif ($_SESSION['lansuite']['fullscreen']) {
                    // Ausgabe Vollbildmodus
                    $smarty->assign('CloseFullscreen', '<a href="index.php?'. $this->get_clean_url_query('query') .'&amp;fullscreen=no" class="menu"><img src="design/'. $this->design .'/images/arrows_delete.gif" border="0" alt="" /><span class="infobox">'. t('Vollbildmodus schließen') .'</span> Lansuite - Vollbildmodus</a>');
                }

                // Ausgabe des Hautteils mit oder ohne Kompression
                if ($compression_mode and $cfg['sys_compress_level']) {
                    header("Content-Encoding: $compression_mode");
                    echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
                    echo gzcompress($smarty->fetch("design/{$this->design}/templates/main.htm") ."\n<!-- Compressed by $compression_mode -->", $cfg['sys_compress_level']);
                } else {
                    $smarty->display("design/{$this->design}/templates/main.htm");
                }
                break;
        }
    }
}
