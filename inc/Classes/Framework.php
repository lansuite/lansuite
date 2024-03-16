<?php

namespace LanSuite;

use Symfony\Component\HttpFoundation\Request;

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
     * Design
     */
    private string $design = '';

    /**
     * All framework messages
     */
    private string $framework_messages = '';

    /**
     * Main Content
     */
    private string $mainContent = '';

    /**
     * Headercode for Meta Tags
     */
    private string $main_header_metatags = '';

    /**
     * JavaScript files for the header
     */
    private string $mainHeaderJavaScriptfiles = '';

    /**
     * JavaScript-Code for the header
     */
    private string $mainHeaderJavaScriptCode = '';

    /**
     * CSS files for the header
     */
    private string $mainHeaderCSSFiles = '';

    /**
     * Headercode for CSS-Code
     */
    private string $mainHeaderCSSCode = '';

    /**
     * @var bool
     */
    public $IsMobileBrowser = false;

    private string $pageTitle = '';

    /**
     * URL-Query parts.
     *
     * Possible keys: base, query, host
     *
     * @var array
     */
    private $internal_url_query = [];

    /**
     * Possible keys for URL query parts.
     */
    public const URL_QUERY_PART_BASE = 'base';
    public const URL_QUERY_PART_QUERY = 'query';
    public const URL_QUERY_PART_HOST = 'host';

    /**
     * Display modus
     *
     * @var string
     */
    private $modus = '';

    /**
     * Display modus constants
     */
    public const DISPLAY_MODUS_PRINT = 'print';
    public const DISPLAY_MODUS_POPUP = 'popup';
    public const DISPLAY_MODUS_AJAX = 'ajax';
    public const DISPLAY_MODUS_BASE = 'base';
    public const DISPLAY_MODUS_BEAMER = 'beamer';

    public function __construct(Request $request)
    {
        // Set Script-Start-Time, to calculate the scripts runtime
        $this->timer = time();
        $this->timer2 = explode(' ', microtime());

        $queryString = $request->getQueryString() ?? '';
        $this->internal_url_query = [
            self::URL_QUERY_PART_BASE => $request->getPathInfo(),
            self::URL_QUERY_PART_QUERY => $queryString,
            self::URL_QUERY_PART_HOST => $request->getHttpHost(),
        ];
        if ($queryString) {
            $this->internal_url_query[self::URL_QUERY_PART_BASE] .= '?' . $queryString;
        }

        $this->addJavaScriptFile('ext_scripts/jquery-min.js');
        $this->addJavaScriptFile('ext_scripts/jquery-ui/jquery-ui.min.js');
        $this->addJavaScriptFile('scripts.js');

        $this->addCSSFile('ext_scripts/jquery-ui/jquery-ui.min.css');
        $this->addCSSFile('design/style.css');

        $this->generateCanonicalMetatagHeader();
    }

    /**
     * Generates the metatag header rel="canonical"
     *
     * @return void
     */
    private function generateCanonicalMetatagHeader(): void
    {
        $queryPart = $this->getURLQueryPart(self::URL_QUERY_PART_QUERY);
        if (!$queryPart) {
            return;
        }

        $query = preg_replace('/&language=(de|en|it|fr|es|nl)/sUi', '', $queryPart);
        $query = preg_replace('/&order_by=(.)*&/sUi', '&', $query);
        $query = preg_replace('/&order_dir=(asc|desc)/sUi', '', $query);
        $query = preg_replace('/&EntsPerPage=[0..9]*/sUi', '', $query);
        $this->main_header_metatags = '<link rel="canonical" href="index.php?'. $query .'" />';
    }

    /**
     * Set the design
     *
     * @param string $design Chosen Design
     * @return void
     */
    public function setDesign(string $design): void
    {
        $this->design = $design;
    }

    /**
     * Returns the website design.
     *
     * @return string
     */
    public function getDesign(): string
    {
        return $this->design;
    }

    /**
     * Set the display modus
     *
     * @param string $modus Display modus (popup,base,print, etc.)
     * @return void
     */
    public function setDisplayModus(string $modus): void
    {
        $this->modus = $modus;
    }

    /**
     * Returns the display modus
     *
     * @return string
     */
    public function getDisplayModus(): string
    {
        return $this->modus;
    }

    /**
     * Add String/Html to MainContent
     *
     * @param string $content
     * @return void
     */
    public function addContent(string $content): void
    {
        $this->mainContent .= $content;
    }

    /**
     * Add JavaScript-Code for the header
     *
     * @param string $javaScriptCode
     * @return void
     */
    public function addJavaScriptCode(string $javaScriptCode): void
    {
        $this->mainHeaderJavaScriptCode .= '<script type="text/javascript">' . PHP_EOL . $javaScriptCode . PHP_EOL . '</script>' . PHP_EOL;
    }

    /**
     * Add a JavaScript file into the header
     *
     * @param string $javaScriptFilePath Path to JavaScript file
     * @return void
     */
    public function addJavaScriptFile(string $javaScriptFilePath): void
    {
        $this->mainHeaderJavaScriptfiles .= '<script src="' . $javaScriptFilePath . '" type="text/javascript"></script>' . PHP_EOL;
    }

    /**
     * Add CSS-Code into the header
     *
     * @param string $cssCode
     * @return void
     */
    public function addCSSCode(string $cssCode): void
    {
        $this->mainHeaderCSSCode .= '<style>' . PHP_EOL . $cssCode . PHP_EOL . '</style>' . PHP_EOL;
    }

    /**
     * Add a CSS file into the header
     *
     * @param string $cssFilePath
     * @return void
     */
    public function addCSSFile(string $cssFilePath): void
    {
        $this->mainHeaderCSSFiles .= '<link rel="stylesheet" type="text/css" href="' . $cssFilePath . '" />' . PHP_EOL;
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
     */
    private function check_optimizer(): int|string
    {
        global $PHPErrors, $db;

        if (headers_sent() || connection_aborted() || $PHPErrors || (isset($db) && $db->errorsFound)) {
            return 0;
        } elseif (str_contains($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip')) {
            return "x-gzip";
        } elseif (str_contains($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip')) {
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
     * Get an URL query part to build internal links
     *
     * @param string $mode Needed part of URL (keys: base, query, host)
     * @return string
     */
    public function getURLQueryPart($part): string
    {
        if(!array_key_exists($part, $this->internal_url_query)) {
            return '';
        }
        return $this->internal_url_query[$part];
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
        global $templ, $cfg, $db, $auth, $smarty, $func, $debug, $request;
        $compression_mode = $this->check_optimizer();

        // Prepare Header
        if ($request->query->get('sitereload')) {
            $smarty->assign('main_header_sitereload', '<meta http-equiv="refresh" content="'.$_GET['sitereload'].'; URL='.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'">');
        } else {
            $smarty->assign('main_header_sitereload', '');
        }

        // Add special CSS and JS
        $smarty->assign('main_header_metatags', $this->main_header_metatags);
        $smarty->assign('main_header_jsfiles', $this->mainHeaderJavaScriptfiles);
        $smarty->assign('main_header_jscode', $this->mainHeaderJavaScriptCode);
        $smarty->assign('main_header_cssfiles', $this->mainHeaderCSSFiles);
        $smarty->assign('main_header_csscode', $this->mainHeaderCSSCode);

        $smarty->assign('IsMobileBrowser', $this->IsMobileBrowser);
        $smarty->assign('DisplayMode', $this->getDisplayModus());

        $smarty->assign('MainTitle', $this->pageTitle);
        $smarty->assign('MainLogout', '');
        $smarty->assign('MainLogo', '');

        $smarty->assign('MainBodyJS', $templ['index']['body']['js'] ?? '');
        $smarty->assign('MainJS', $templ['index']['control']['js'] ?? '');

        $smarty->assign('MainContent', $this->mainContent);

        $EndJS = '';
        if ($cfg['google_analytics_id']) {
            $EndJS = "<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', " . json_encode($cfg['google_analytics_id'], JSON_THROW_ON_ERROR) . ", 'auto');
ga('set', 'anonymizeIp', true);
ga('send', 'pageview');
</script>";
        }
        $smarty->assign('EndJS', $EndJS);

        // Switch Displaymodus (popup, base, print, normal, beamer)
        switch ($this->getDisplayModus()) {
            case self::DISPLAY_MODUS_PRINT:
                // Make a Printpopup (without Boxes and Special CSS for printing)
                $smarty->assign('MainContentStyleID', 'ContentFullscreen');
                $smarty->display("design/simple/templates/main.htm");
                break;

            case self::DISPLAY_MODUS_POPUP:
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

            case self::DISPLAY_MODUS_AJAX:
            case self::DISPLAY_MODUS_BASE:
                // Make HTML for Sites Without HTML (e.g. for generation Pictures etc)
                echo $this->mainContent;
                break;

            default:
                // Footer
                $smarty->assign('main_footer_version', $templ['index']['info']['version'] ?? '');
                $smarty->assign('main_footer_date', date('y'));
                $smarty->assign('main_footer_countquery', $db->count_query);
                $smarty->assign('main_footer_timer', round($this->out_work(), 2));
                $smarty->assign('main_footer_cleanquery', $this->getURLQueryPart(self::URL_QUERY_PART_QUERY));

                if ($cfg["sys_footer_impressum"]) {
                    $smarty->assign('main_footer_impressum', $cfg["sys_footer_impressum"]);
                } else {
                    $smarty->assign('main_footer_impressum', '');
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
                $sessionFullScreenSet = false;
                if (array_key_exists('lansuite', $_SESSION) && array_key_exists('fullscreen', $_SESSION['lansuite'])) {
                    $sessionFullScreenSet = $_SESSION['lansuite']['fullscreen'];
                }
                if ($sessionFullScreenSet || $this->getDisplayModus() == self::DISPLAY_MODUS_BEAMER) {
                    $smarty->assign('MainContentStyleID', 'ContentFullscreen');
                } else {
                    $smarty->assign('MainContentStyleID', 'Content');
                }

                if ($auth['login']) {
                    $smarty->assign('MainLogout', '<a href="index.php?mod=auth&action=logout" class="menu">Logout</a>');
                }

                // Ausgabe Hauptseite
                $smarty->assign('CloseFullscreen', '');
                if (!$sessionFullScreenSet && $this->getDisplayModus() != self::DISPLAY_MODUS_BEAMER) {
                    $smarty->assign('MainFrameworkmessages', $this->framework_messages);
                    if (isset($templ)) {
                        $smarty->assign('MainLeftBox', $templ['index']['control']['boxes_letfside']);
                        $smarty->assign('MainRightBox', $templ['index']['control']['boxes_rightside']);
                    } else {
                        $smarty->assign('MainLeftBox', '');
                        $smarty->assign('MainRightBox', '');
                    }
                    $smarty->assign('MainLogo', '<img src="design/'.$this->design.'/images/lansuite-logo.gif" alt="Lansuite Logo" title="Lansuite Logo" border="0" />');
                    $smarty->assign('MainDebug', '');
                    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN and isset($debug)) { // and $cfg['sys_showdebug'] (no more, for option now in inc/base/config)
                        $smarty->assign('MainDebug', $debug->show());
                    }
                } elseif ($_SESSION['lansuite']['fullscreen']) {
                    // Ausgabe Vollbildmodus
                    $smarty->assign('CloseFullscreen', '<a href="index.php?'. $this->getURLQueryPart(self::URL_QUERY_PART_QUERY) .'&amp;fullscreen=no" class="menu"><img src="design/'. $this->design .'/images/arrows_delete.gif" border="0" alt="" /><span class="infobox">'. t('Vollbildmodus schließen') .'</span> Lansuite - Vollbildmodus</a>');
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
