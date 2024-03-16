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
     * Metatags for the header
     */
    private string $mainHeaderMetatags = '';

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
     * TODO deprecate IsMobileBrowser
     *
     * @var bool
     */
    public $IsMobileBrowser = false;

    /**
     * Page title
     */
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
     * Request object
     */
    private Request $request;

    /**
     * Display modus constants
     */
    public const DISPLAY_MODUS_PRINT = 'print';
    public const DISPLAY_MODUS_POPUP = 'popup';
    public const DISPLAY_MODUS_AJAX = 'ajax';
    public const DISPLAY_MODUS_BASE = 'base';
    public const DISPLAY_MODUS_BEAMER = 'beamer';

    /**
     * Smarty templating engine
     */
    private \Smarty $templateEngine;

    /**
     * Debugging object
     */
    private ?Debug $debug = null;

    public function __construct(Request $request)
    {
        $this->request = $request;

        // Set Script-Start-Time, to calculate the scripts runtime
        $this->timer = time();
        $this->timer2 = explode(' ', microtime());

        $queryString = $this->request->getQueryString() ?? '';
        $this->internal_url_query = [
            self::URL_QUERY_PART_BASE => $this->request->getPathInfo(),
            self::URL_QUERY_PART_QUERY => $queryString,
            self::URL_QUERY_PART_HOST => $this->request->getHttpHost(),
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
        $this->mainHeaderMetatags = '<link rel="canonical" href="index.php?'. $query .'" />';
    }

    /**
     * Set the template engine
     */
    public function setTemplateEngine(\Smarty $engine): void
    {
        $this->templateEngine = $engine;
    }

    /**
     * Set the debug mode
     */
    public function setDebugMode(Debug $debug): void
    {
        $this->debug = $debug;
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
     * Check for the accepted encoding
     */
    private function getCompressionMode(): string
    {
        $encodings = $this->request->getEncodings();
        if (in_array('x-gzip', $encodings)) {
            return "x-gzip";
        }

        if (in_array('gzip', $encodings)) {
            return "gzip";
        }

        return '';
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
     * Adds a message into the page title
     *
     * @param string $pageTitleMessage
     * @return void
     */
    public function addToPageTitle(string $pageTitleMessage): void
    {
        $message = trim($pageTitleMessage);
        if (!$message) {
            return;
        }

        if ($this->pageTitle == '') {
            $this->pageTitle = $message;
        } else {
            $this->pageTitle .= ' - ' . $message;
        }
    }

    /**
     * Generates the correct Google Analytics JavaScript code
     */
    private function getGoogleAnalyticsJavaScript(string $googleAnalyticsID): string
    {
        $javaScriptCode = "<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', " . json_encode($googleAnalyticsID, JSON_THROW_ON_ERROR) . ", 'auto');
ga('set', 'anonymizeIp', true);
ga('send', 'pageview');
</script>";
        return $javaScriptCode;
    }

    /**
     * Sets the default values for the used templates
     */
    private function setTemplateDefaultValues(string $mainContentStyleID): void
    {
        $this->templateEngine->assign('MainLeftBox', '');
        $this->templateEngine->assign('MainRightBox', '');
        $this->templateEngine->assign('Footer', '');
        $this->templateEngine->assign('CloseFullscreen', '');
        $this->templateEngine->assign('MainFrameworkmessages', $this->framework_messages);
        $this->templateEngine->assign('Design', $this->getDesign());
        $this->templateEngine->assign('MainDebug', '');
        $this->templateEngine->assign('MainContentStyleID', $mainContentStyleID);
    }

    /**
     * Sends the HTML output
     *
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function sendHTMLOutput(): void
    {
        global $templ, $cfg, $db, $auth, $func;

        $compressionMode = $this->getCompressionMode();
        $designPath = 'design/' . $this->getDesign();

        // Site Reload header
        $this->templateEngine->assign('main_header_sitereload', '');
        $siteReloadParameter = intval($this->request->query->get('sitereload'));
        if ($siteReloadParameter) {
            $reloadURL = $this->request->getRequestUri();
            $this->templateEngine->assign('main_header_sitereload', '<meta http-equiv="refresh" content="' . $siteReloadParameter . '; URL=' . $reloadURL . '">');
        }

        // Assign Metatags, CSS and JS
        $this->templateEngine->assign('main_header_metatags', $this->mainHeaderMetatags);
        $this->templateEngine->assign('main_header_jsfiles', $this->mainHeaderJavaScriptfiles);
        $this->templateEngine->assign('main_header_jscode', $this->mainHeaderJavaScriptCode);
        $this->templateEngine->assign('main_header_cssfiles', $this->mainHeaderCSSFiles);
        $this->templateEngine->assign('main_header_csscode', $this->mainHeaderCSSCode);

        // TODO deprecate "IsMobileBrowser"
        $this->templateEngine->assign('IsMobileBrowser', $this->IsMobileBrowser);
        $this->templateEngine->assign('DisplayMode', $this->getDisplayModus());

        $this->templateEngine->assign('MainTitle', $this->pageTitle);
        $this->templateEngine->assign('MainLogout', '');
        $this->templateEngine->assign('MainLogo', '');

        $this->templateEngine->assign('MainBodyJS', $templ['index']['body']['js'] ?? '');
        $this->templateEngine->assign('MainJS', $templ['index']['control']['js'] ?? '');

        $this->templateEngine->assign('MainContent', $this->mainContent);

        $pageBottomJavaScript = '';
        if ($cfg['google_analytics_id']) {
            $pageBottomJavaScript = $this->getGoogleAnalyticsJavaScript($cfg['google_analytics_id']);
        }
        $this->templateEngine->assign('EndJS', $pageBottomJavaScript);

        // Switch Displaymodus (print, popup, ajax, base, normal)
        switch ($this->getDisplayModus()) {
            // Make a Printpopup (without Boxes and Special CSS for printing)
            case self::DISPLAY_MODUS_PRINT:
                $this->setTemplateDefaultValues('ContentFullscreen');
                $this->templateEngine->display("design/simple/templates/main.htm");
                break;

            // Make HTML for Popup
            case self::DISPLAY_MODUS_POPUP:
                $this->setTemplateDefaultValues('ContentFullscreen');
                if ($compressionMode && $cfg['sys_compress_level']) {
                    header("Content-Encoding: $compressionMode");
                    $index = $this->templateEngine->fetch($designPath . '/templates/main.htm') . PHP_EOL . '<!-- Compressed by $compressionMode -->';
                    $contentSize = strlen($index);
                    $contentCRC = crc32($index);
                    $index = gzcompress($index, $cfg['sys_compress_level']);
                    echo $index;
                    echo pack('V', $contentCRC) . pack('V', $contentSize);
                } else {
                    $this->templateEngine->display($designPath . '/templates/main.htm');
                }
                break;

            // Make HTML for sites without HTML (e.g. for generation pictures etc)
            case self::DISPLAY_MODUS_AJAX:
            case self::DISPLAY_MODUS_BASE:
                echo $this->mainContent;
                break;

            // Footer
            default:
                $this->setTemplateDefaultValues('Content');

                $this->templateEngine->assign('main_footer_version', $templ['index']['info']['version'] ?? '');
                $this->templateEngine->assign('main_footer_date', date('y'));
                $this->templateEngine->assign('main_footer_countquery', $db->count_query);
                $this->templateEngine->assign('main_footer_timer', round($this->out_work(), 2));
                $this->templateEngine->assign('main_footer_cleanquery', $this->getURLQueryPart(self::URL_QUERY_PART_QUERY));

                $this->templateEngine->assign('main_footer_impressum', '');
                if ($cfg["sys_footer_impressum"]) {
                    $this->templateEngine->assign('main_footer_impressum', $cfg["sys_footer_impressum"]);
                }

                $main_footer_mem_usage = 'Memory-Usage: '. $func->FormatFileSize(memory_get_peak_usage()) .' |';
                $this->templateEngine->assign('main_footer_mem_usage', $main_footer_mem_usage);

                $footer = $this->templateEngine->fetch('design/templates/footer.htm');
                if ($cfg["sys_optional_footer"]) {
                    $footer .= HTML_NEWLINE . $cfg["sys_optional_footer"];
                }
                $this->templateEngine->assign('Footer', $footer);

                // Fullscreen or normal view?
                $sessionFullScreenSet = false;
                if (array_key_exists('lansuite', $_SESSION) && array_key_exists('fullscreen', $_SESSION['lansuite'])) {
                    $sessionFullScreenSet = $_SESSION['lansuite']['fullscreen'];
                }
                if ($sessionFullScreenSet || $this->getDisplayModus() == self::DISPLAY_MODUS_BEAMER) {
                    $this->templateEngine->assign('MainContentStyleID', 'ContentFullscreen');
                }

                if ($auth['login']) {
                    $this->templateEngine->assign('MainLogout', '<a href="index.php?mod=auth&action=logout" class="menu">Logout</a>');
                }

                // Output Main page
                if (!$sessionFullScreenSet && $this->getDisplayModus() != self::DISPLAY_MODUS_BEAMER) {
                    if (isset($templ)) {
                        $this->templateEngine->assign('MainLeftBox', $templ['index']['control']['boxes_letfside']);
                        $this->templateEngine->assign('MainRightBox', $templ['index']['control']['boxes_rightside']);
                    }

                    $this->templateEngine->assign('MainLogo', '<img src="' . $designPath . '/images/lansuite-logo.gif" alt="Lansuite Logo" title="Lansuite Logo" border="0" />');
                    if ($auth['type'] >= \LS_AUTH_TYPE_ADMIN && $this->debug) {
                        $this->templateEngine->assign('MainDebug', $this->debug->show());
                    }

                // Output fullscreen
                } elseif ($_SESSION['lansuite']['fullscreen']) {
                    $this->templateEngine->assign('CloseFullscreen', '<a href="index.php?'. $this->getURLQueryPart(self::URL_QUERY_PART_QUERY) .'&amp;fullscreen=no" class="menu"><img src="' . $designPath . '/images/arrows_delete.gif" border="0" alt="" /><span class="infobox">'. t('Vollbildmodus schließen') .'</span> Lansuite - Vollbildmodus</a>');
                }

                // Output of the main content (with or without compression)
                if ($compressionMode && $cfg['sys_compress_level']) {
                    header("Content-Encoding: $compressionMode");
                    echo gzcompress($this->templateEngine->fetch($designPath . '/templates/main.htm') . PHP_EOL . "<!-- Compressed by $compressionMode -->", $cfg['sys_compress_level']);
                } else {
                    $this->templateEngine->display($designPath . '/templates/main.htm');
                }
                break;
        }
    }
}
