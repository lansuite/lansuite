<?php

/**
 * Global Functions
 *
 * @package lansuite_core
 * @author ls_admin
 * @version $Id$
 * @access public
 * @todo Remove Dialogfunctions and create own class
*/

// Rewrite PHP's htmlspecialchars for ' is replaced by &#039;, instead of &#39;
function htmlspecchars($string, $quote_style = ENT_COMPAT, $format = null)
{
    $aTransSpecchar = array('&' => '&amp;', '"' => '&quot;', '<' => '&lt;', '>' => '&gt;');
    if (ENT_NOQUOTES == $quote_style) {
        unset($aTransSpecchar['"']);
    } elseif (ENT_QUOTES == $quote_style) {
        $aTransSpecchar["'"] = '&#39;';
    } // (apos) htmlspecialchars() uses '&#039;'
    return strtr($string, $aTransSpecchar);
}


class func
{
    public $ActiveModules = array();
  /**
   * CONSTRUCTOR : Get referer and transform in a internal Link
   *
   */
    public function __construct()
    {
        define('NO_LINK', -1);
        $url_array = parse_url($_SERVER['HTTP_REFERER']);
        $this->internal_referer = "index.php?".$url_array['query'].$url_array['fragment'];
    }


  /**
   * Read the Config-settings from DB
   * @global mixed Databaseobject
   * @global array Baseconfig from File
   * @return $cfg
   */
    public function read_db_config()
    {
        global $db;

        // Idea: Select current mod only, does not work with plugin.
        // $res = $db->qry('SELECT cfg_value, cfg_key, cfg_type FROM %prefix%config WHERE cfg_module = "install" OR cfg_module = %string%', $mod);
        $res = $db->qry('SELECT cfg_value, cfg_key, cfg_type FROM %prefix%config');
        while ($row = $db->fetch_array($res, 0)) {
            switch ($row['cfg_type']) {
                case 'integer':
                case 'int':
                      $cfg["{$row['cfg_key']}"] = (int)$row['cfg_value'];
                    break;
                case 'boolean':
                case 'bool':
                      $cfg["{$row['cfg_key']}"] = (bool)$row['cfg_value'];
                    break;
                case 'float':
                      $cfg["{$row['cfg_key']}"] = (float)$row['cfg_value'];
                    break;
                default:
                      $cfg["{$row['cfg_key']}"] = $row['cfg_value'];
                    break;
            }
        }
        $db->free_result($res);

        return $cfg;
    }

  #### Date Conversions ####
  /**
   * Convert a date string to a timestamp
   * @string strStr Date to convert
   * @optional string Format Format, the date is in
   * @return timestamp
   */
    public function str2time($strStr, $strPattern = 'Y-m-d H:i:s')
    {
        // an array of the valide date characters, see: http://php.net/date#AEN21898
        $arrCharacters = array(
         'd', // day
         'm', // month
         'y', // year, 2 digits
         'Y', // year, 4 digits
         'H', // hours
         'i', // minutes
         's'  // seconds
        );
        // transform the characters array to a string
        $strCharacters = implode('', $arrCharacters);

        // splits up the pattern by the date characters to get an array of the delimiters between the date characters
        $arrDelimiters = preg_split('~['.$strCharacters.']~', $strPattern);
        // transform the delimiters array to a string
        $strDelimiters = quotemeta(implode('', array_unique($arrDelimiters)));

        // splits up the date by the delimiters to get an array of the declaration
        $arrStr    = preg_split('~['.$strDelimiters.']~', $strStr);
        // splits up the pattern by the delimiters to get an array of the used characters
        $arrPattern = preg_split('~['.$strDelimiters.']~', $strPattern);

        // if the numbers of the two array are not the same, return false, because the cannot belong together
        if (count($arrStr) !== count($arrPattern)) {
            return false;
        }

        // creates a new array which has the keys from the $arrPattern array and the values from the $arrStr array
        $arrTime = array();
        for ($i = 0; $i < count($arrStr); $i++) {
            $arrTime[$arrPattern[$i]] = $arrStr[$i];
        }

        // gernerates a 4 digit year declaration of a 2 digit one by using the current year
        if (isset($arrTime['y']) && !isset($arrTime['Y'])) {
            $arrTime['Y'] = substr(date('Y'), 0, 2) . $arrTime['y'];
        }

        // if a declaration is empty, it will be filled with the current date declaration
        foreach ($arrCharacters as $strCharacter) {
            if (empty($arrTime[$strCharacter])) {
                $arrTime[$strCharacter] = date($strCharacter);
            }
        }

        // checks if the date is a valide date
        if (!checkdate($arrTime['m'], $arrTime['d'], $arrTime['Y'])) {
            return false;
        }

        $intTime = mktime($arrTime['H'], $arrTime['i'], $arrTime['s'], $arrTime['m'], $arrTime['d'], $arrTime['Y']);
        return $intTime;
    }

  /**
   * Convert a timestamp, to a nice, readable string
   * @string timestamp
   * @string type One of year, month, date, time, shorttime, datetime, daydatetime, daydate, or shortdaytime
   * @return string
   */
    public function unixstamp2date($func_timestamp, $func_art)
    {
        if ((int)$func_timestamp == 0) {
            return '---';
        } else {
            switch ($func_art) {
                case "year":
                    $func_date  = date("Y", $func_timestamp);
                    break;
                case "month":
                    $func_date  = date("Y", $func_timestamp) ." - ". t(date("F", $func_timestamp));
                    break;
                case "date":
                    $func_date  = date("d.m.Y", $func_timestamp);
                    break;
                case "time":
                    $func_date  = date("H:i", $func_timestamp);
                    break;
                case "shorttime":
                    $func_date  = date("H:i", $func_timestamp);
                    break;
                case "datetime":
                    $func_date  = date("d.m.Y H:i", $func_timestamp);
                    break;
                case "daydatetime":
                        $day[0] = t('Sonntag');
                        $day[1] = t('Montag');
                        $day[2] = t('Dienstag');
                        $day[3] = t('Mittwoch');
                        $day[4] = t('Donnerstag');
                        $day[5] = t('Freitag');
                        $day[6] = t('Samstag');

                        $func_date .= $day[date("w", $func_timestamp)];
                        $func_date .= ", ";
                        $func_date .= date("d.m.Y H:i", $func_timestamp);
                    break;

                case "daydate":
                        $day[0] = t('Sonntag');
                        $day[1] = t('Montag');
                        $day[2] = t('Dienstag');
                        $day[3] = t('Mittwoch');
                        $day[4] = t('Donnerstag');
                        $day[5] = t('Freitag');
                        $day[6] = t('Samstag');
                        $func_date .= date("d.m.Y", $func_timestamp) . " (". $day[date("w", $func_timestamp)] .")";
                    break;

                case "shortdaytime":
                        $day[0] = t('So');
                        $day[1] = t('Mo');
                        $day[2] = t('Di');
                        $day[3] = t('Mi');
                        $day[4] = t('Do');
                        $day[5] = t('Fr');
                        $day[6] = t('Sa');

                        $func_date .= $day[date("w", $func_timestamp)];
                        $func_date .= ", ";
                        $func_date .= date("H:i", $func_timestamp);
                    break;
            }
        }
          return $func_date;
    }

  /**
   * Calculates the age to a given birthday timestamp
   * @timestamp birthhday
   * @optional timestamp attime Calculates the age the person will have at this time
   * @return int age
   */
    public function age($birthday, $attime = 0)
    {
        if ($attime) {
            $yeardiff = date("Y", $attime) - date("Y", $birthday);
            $monthdiff = date("m", $attime) - date("m", $birthday);
            $daydiff = date("j", $attime) - date("j", $birthday);
        } else {
            $yeardiff = date("Y") - date("Y", $birthday);
            $monthdiff = date("m") - date("m", $birthday);
            $daydiff = date("j") - date("j", $birthday);
        }

        if (($monthdiff < 0) || ($monthdiff == 0 and $daydiff < 0)) {
            $age = $yeardiff - 1;
        } else {
            $age = $yeardiff;
        }

          return (int)$age;
    }


  #### Infobox ####
    public function setainfo($text, $userid, $priority, $item, $itemid)
    {
        global $db;

        if ($priority != "0" and $priority != "1" and $priority != "2") {
            echo(t('Function setainfo needs Priority defined as Integer: 0 low (grey), 1 middle (green), 2 high (orange)'));
        } else {
            $date = date("U");
            $db->qry("INSERT INTO %prefix%infobox SET userid=%int%, class=%string%, id_in_class = %int%, text=%string%, date=%string%, priority=%string%", $userid, $item, $itemid, $text, $date, $priority);
        }
    }

  #### Dialog functions ####
    public function GeneralDialog($type, $text, $link_target = '', $JustReturn = 0, $link_type = '')
    {
        global $smarty, $dsp, $FrameworkMessages;

        // Link
        if ($link_target == NO_LINK) {
            $smarty->assign('link', '');
        } else {
            switch ($link_type) {
                case "FORWARD":
                    $link_text = t('Weiter');
                    $link_description = t('Weiter zur naechsten Seite');
                    break;
                default: // i.e. BACK
                    $link_text = t('Zurück');
                    $link_description = t('Zurück zur vorherigen Seite');
                    break;
            }

            if (!$link_target) {
                $link_target = $this->internal_referer;
            }
            $smarty->assign('link', $dsp->FetchCssButton($link_text, $link_target, $link_description));
        }

        // Text
        switch ($text) {
            case "ACCESS_DENIED":
                $text = t('Du hast keine Zugriffsrechte für diesen Bereich.');
                break;
            case "NO_LOGIN":
                $text = t('Du bist nicht eingeloggt. Bitte logge dich erst ein, bevor du diesen Bereich betritst.');
                break;
            case "NO_REFRESH":
                $text = t('Du hast diese Anfrage wiederholt ausgeführt.');
                break;
        }
        $smarty->assign('msg', $text);

        if ($JustReturn) {
            $FrameworkMessages .= $smarty->fetch('design/templates/'. $type .'.htm');
        } else {
            $dsp->AddContentLine($smarty->fetch('design/templates/'. $type .'.htm'));
        }
    }

    public function confirmation($text, $link_target = '', $JustReturn = 0, $link_type = '')
    {
        return $this->GeneralDialog('confirmation', $text, $link_target, $JustReturn, $link_type);
    }

    public function information($text, $link_target = '', $JustReturn = 0, $link_type = '')
    {
        return $this->GeneralDialog('information', $text, $link_target, $JustReturn, $link_type);
    }

    public function error($text, $link_target = '', $JustReturn = 0, $link_type = '')
    {
        $this->log_event('LS Error: '. $text, 3, 'LS-Fehler');
        return $this->GeneralDialog('error', $text, $link_target, $JustReturn, $link_type);
    }

    public function question($text, $link_target_yes, $link_target_no = '')
    {
        global $smarty, $dsp;

        if ($link_target_no == '') {
            $link_target_no = $this->internal_referer;
        }
        $smarty->assign('question', $text);
        $smarty->assign('action', $link_target_yes);
        $smarty->assign('yes', $dsp->FetchIcon($link_target_yes, 'yes'));
        $smarty->assign('no', $dsp->FetchIcon($link_target_no, 'no'));

        $dsp->AddContentLine($smarty->fetch('design/templates/question.htm'));
    }

    public function multiquestion($questionarray, $linkarray, $text = '')
    {
        global $smarty, $dsp;

        if ($text == '') {
            $text = t('Bitte wähle eine Möglichkeit aus:');
        }
        $smarty->assign('msg', $text);

        if (is_array($questionarray)) {
            foreach ($questionarray as $ind => $question) {
                $row .= '<br /><br /><a href="'. $linkarray[$ind] .'">'. $question .'</a>';
            }
        }
        $smarty->assign('row', $row);

        $dsp->AddContentLine($smarty->fetch("design/templates/multiquestion.htm"));
    }


  #### String conversion (Anti-Hacking + Nicer look) ####
  // NoHTML is applied to every field retrieved from an SQL-Query, as well as GET, Request_Uri, Http_Referrer and Query_String
  // If you would like to use HTML code in one of these, you have to transform the code again, using AllowHTML
    public function NoHTML($string, $soft = 0)
    {
        if ($soft) {
            $aTransSpecchar = array('"' => '&quot;', '<' => '&lt;', '>' => '&gt;');
        } else {
            $aTransSpecchar = array('&' => '&amp;', '"' => '&quot;', '<' => '&lt;', '>' => '&gt;');
        }
          return strtr($string, $aTransSpecchar);
    }

  // See above
    public function AllowHTML($string)
    {
        $aTransSpecchar = array('&amp;' => '&', '&quot;' => '"', '&lt;' => '<', '&gt;' => '>');
        return strtr($string, $aTransSpecchar);
    }

  // Add slashes at any non GPC-variable
  // This function musst be used, if ' come from other sources, than $_GET, or $_POST
  // for example language-files
    public function escape_sql($text)
    {
        $text = addslashes(stripslashes($text));
        return $text;
    }

  // If ls-code should be displayed
    public function text2html($string, $mode = 0)
    {
 // mode 0: default; 1: wiki before; 2: wiki after; 4: basic
        global $db, $auth;

        if ($mode != 4) {
            if ($mode != 1) {
                preg_replace_callback(
                    '#\[c\]((.)*)\[\/c\]#sUi',
                    create_function(
                        '$treffer',
                        'global $HighlightCode, $HighlightCount; $HighlightCount++; $HighlightCode[$HighlightCount] = $treffer[1];'
                    ),
                    $string
                );
            }

            if ($mode != 2) {
                $img_start = "<img src=\"design/".$auth["design"]."/images/";
                $img_start2 = '<img src="ext_inc/smilies/';
                $img_end   = '" border="0" alt="" />';

                $string = preg_replace('#\\[img\\]([^[]*)\\[/img\\]#sUi', '<img src="\1" border="0" class="img" alt="" style="max-width:468px; max-height:450px; overflow:hidden;" />', $string);
                $string = preg_replace('#\\[url=(https?://[^\\]]*)\\]([^[]*)\\[/url\\]#sUi', '<a target="_blank" href="\\1" rel="nofollow">\\2</a>', $string);

                if ($mode != 1) {
                    $string = preg_replace('#(\\s|^)(https?://(.)*)(\\s|$)#sUi', '\\1<a target="_blank" href="\\2" rel="nofollow">\\2</a>\\4', $string);
                }
            }
        }

        if ($mode != 2) {
            $string = str_replace("\r", '', $string);
            $string = str_replace("\n", "<br />\n", $string);
            $string = str_replace("[br]", "<br />\n", $string);
            $string = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $string);

            $string = preg_replace('#\[b\](.*)\[/b\]#sUi', '<b>\\1</b>', $string);
            $string = preg_replace('#\[i\](.*)\[/i\]#sUi', '<i>\\1</i>', $string);
            $string = preg_replace('#\[u\](.*)\[/u\]#sUi', '<u>\\1</u>', $string);
            $string = preg_replace('#\[s\](.*)\[/s\]#sUi', '<s>\\1</s>', $string);
            $string = preg_replace('#\[sub\](.*)\[/sub\]#sUi', '<sub>\\1</sub>', $string);
            $string = preg_replace('#\[sup\](.*)\[/sup\]#sUi', '<sup>\\1</sup>', $string);
        }

        if ($mode != 4) {
            if ($mode != 2) {
                $string = preg_replace('#\[quote\](.*)\[/quote\]#sUi', '<blockquote><div class="tbl_small">Zitat:</div><div class="tbl_7">\\1</div></blockquote>', $string);

                $string = preg_replace('#\[size=([0-9]+)\]#sUi', '<font style="font-size:\1px">', $string);
                $string = str_replace('[/size]', '</font>', $string);
                $string = preg_replace('#\[color=([a-z]+)\]#sUi', '<font color="\1">', $string);
                $string = str_replace('[/color]', '</font>', $string);
            }

            if ($mode != 1) {
                $string = preg_replace_callback(
                    '#\[c\](.)*\[\/c\]#sUi',
                    create_function(
                        '$treffer',
                        'global $func, $HighlightCode, $HighlightCount2;
                        $HighlightCount2++;
                        $geshi = new GeSHi($HighlightCode[$HighlightCount2], \'php\');
                        $geshi->set_header_type(GESHI_HEADER_NONE);
                        return \'
                            <blockquote>
                                <div class="tbl_small">Code:</div>
                                <div class="tbl_7">
                                    \'. $func->AllowHTML(\'<code>\' . $geshi->parse_code() . \'</code>\') .\'
                                </div>
                            </blockquote>\';'
                    ),
                    $string
                );
            }

            if ($mode != 1) {
                $res = $db->qry("SELECT shortcut, image FROM %prefix%smilies");
                while ($row = $db->fetch_array($res)) {
                    $string = str_replace($row['shortcut'], $img_start2 . $row['image'] . $img_end, $string);
                }
                $db->free_result($res);
            }
        }


        return $string;
    }

  // Wiki Syntax
    public function Text2Wiki($string)
    {
        $arr = explode("\n", $this->Text2HTML($string, 1));

        $COpen = 0;
        $UlOpen = 0;
        $OlOpen = 0;
        foreach ($arr as $key => $line) {
            #$arr[$key] = preg_replace("#^<br />$#sUi", '', $arr[$key]);
            $arr[$key] = preg_replace('#^====== (.*) ======#sUi', '<div class="wikiH6">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#^===== (.*) =====#sUi', '<div class="wikiH5">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#^==== (.*) ====#sUi', '<div class="wikiH4">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#^=== (.*) ===#sUi', '<div class="wikiH3">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#^== (.*) ==#sUi', '<div class="wikiH2">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#^= (.*) =#sUi', '<div class="wikiH1">\\1</div>', $arr[$key]);
            $arr[$key] = preg_replace('#\\[\\[Bild:(.*)\\]\\]#sUi', '<img src="ext_inc/wiki/\\1" alt="\\1">', $arr[$key]);
            $arr[$key] = preg_replace('#\\[(http://[^ ]*) ([^\\]]*)\\]#sUi', '<a target="_blank" href="\\1" rel="nofollow">\\2</a>', $arr[$key]);
            $arr[$key] = preg_replace('#\\[\\[([^\\|\\]]*)\\]\\]#sUi', '<a href="index.php?mod=wiki&action=show&name=\\1">\\1</a>', $arr[$key]);
            $arr[$key] = preg_replace('#\\[\\[([^\\|]*)\\|([^\\]]*)\\]\\]#sUi', '<a href="index.php?mod=wiki&action=show&name=\\1">\\2</a>', $arr[$key]);
            $arr[$key] = preg_replace("#'''(.*)'''#sUi", "<b>\\1</b>", $arr[$key]);

            if ($UlOpen) {
                $arr[$key] = preg_replace("#^\\* (.*)<br />#sUi", "<li>\\1</li>", $arr[$key]);
                $arr[$key] = preg_replace("#^([^\\*].(.*))<br />#sUi", "</ul>\\1", $arr[$key], -1, $count);
                if ($count) {
                    $UlOpen = 0;
                }
            } else {
                $arr[$key] = preg_replace("#^\\* (.*)<br />#sUi", "<ul><li>\\1</li>", $arr[$key], -1, $count);
                if ($count) {
                    $UlOpen = 1;
                }
            }

            if ($OlOpen) {
                $arr[$key] = preg_replace("|^\\# (.*)<br />|sUi", "<li>\\1</li>", $arr[$key]);
                $arr[$key] = preg_replace("|^([^\\#].(.*))<br />|sUi", "</ol>\\1", $arr[$key], -1, $count);
                if ($count) {
                    $OlOpen = 0;
                }
            } else {
                $arr[$key] = preg_replace("|^\\# (.*)<br />|sUi", "<ol><li>\\1</li>", $arr[$key], -1, $count);
                if ($count) {
                    $OlOpen = 1;
                }
            }

            if ($COpen) {
                $arr[$key] = preg_replace("#^([^ ].)#sUi", "[/c]\\1", $arr[$key], -1, $count);
                if ($count) {
                    $COpen = 0;
                }
                $arr[$key] = preg_replace("#^ #sUi", "", $arr[$key]);
                $arr[$key] = preg_replace("#<br />$#sUi", "", $arr[$key]);
            } else {
                $arr[$key] = preg_replace("#^ #sUi", "[c]", $arr[$key], -1, $count);
                if ($count) {
                    $COpen = 1;
                    $arr[$key] = preg_replace("#<br />$#sUi", "", $arr[$key]);
                }
            }
        }

        $string = implode("\n", $arr);

        if ($UlOpen) {
            $string .= '</ul>';
        }
        if ($OlOpen) {
            $string .= '</ol>';
        }
        if ($COpen) {
            $string .= '[/c]';
        }

        return $this->Text2HTML($string, 2);
    }

    public function Entity2Uml($string)
    {
        $string = str_replace('&uuml;', 'ü', $string);
        $string = str_replace('&Uuml;', 'Ü', $string);
        $string = str_replace('&auml;', 'ä', $string);
        $string = str_replace('&Auml;', 'Ä', $string);
        $string = str_replace('&ouml;', 'ö', $string);
        $string = str_replace('&Ouml;', 'Ö', $string);
        $string = str_replace('&szlig;', 'ß', $string);
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace('&quot;', '"', $string);

        return $string;
    }


  #### Check Var Content ####
    public function check_var($var, $type, $min_length, $max_length)
    {
        if (($type == "integer" or $type == "double" or $type == "string" or $type == "boolean" or $type == "object" or $type == "array") and (isset($min_length) == false or gettype($min_length) == "integer") and (isset($max_length) == false or gettype($max_length) == "integer") and (isset($var) == true)) {
            if ((gettype($var) == $type) and (strlen($var) >= $min_length) and (strlen($var) <= $max_length)) {
                return true;
            } else {
                return false;
            }
        } else {
            echo "Error: Function check_var needs defined: var, datatype (may be integer, double, string, boolean, object or array), [optionally: min_length], [optionally: max_length] <br/> For more information please visit the lansuite programmers docu";
        }
    }

    public function checkIP($ip)
    {
        if (strlen($ip) < 5 or strlen($ip) > 15) {
            return 0;
        }

        $IPParts = explode(".", $ip);
        if (count($IPParts) != 4) {
            return 0;
        }
        if ($IPParts[0] == 0) {
            return 0;
        }

        for ($i=0; $i<=3; $i++) {
            if (ereg("[^0-9]", $IPParts[$i])) {
                return 0;
            }
            if ($IPParts[$i] > 255 or $IPParts[$i] < 0) {
                return 0;
            }
        }
        return 1;
    }


  #### Misc ####
  // Types:  1 = Info, 2 = Warning, 3 = Error (be careful with 3)
    public function log_event($message, $type = 1, $sort_tag = '', $target_id = '')
    {
        global $db, $auth;

        if ($message == '') {
            echo("Function log_event needs message defined! - Invalid arguments supplied!");
        } else {
            if ($sort_tag == '') {
                $sort_tag = $_GET['mod'];
            }
            $entry = $db->qry("INSERT INTO %prefix%log SET
             userid = %int%,
             description=%string%,
             type=%string%,
             sort_tag = %string%,
             target_id = %int%,
             script = %string%,
             referer = %string%,
             ip = INET6_ATON(%string%)
             ", $auth['userid'], $message, $type, $sort_tag, $target_id, $_SERVER["REQUEST_URI"], $this->internal_referer, $_SERVER['REMOTE_ADDR']);
            if ($entry == 1) {
                return 1;
            }
        }
          return 0;
    }

  // Better use MasterSearch..
    public function page_split($current_page, $max_entries_per_page, $overall_entries, $working_link, $var_page_name)
    {
        if ($max_entries_per_page > 0 and $overall_entries >= 0 and $working_link != "" and $var_page_name != "") {
            if ($current_page == "") {
                $page_sql = "LIMIT 0," . (int)$max_entries_per_page;
                $page_a = 0;
                $page_b = $max_entries_per_page;
            }
            if ($current_page == "all") {
                $page_sql = "";
                $page_a = 0;
                $page_b = $overall_entries;
            } else {
                $page_sql = ("LIMIT " . ($current_page * $max_entries_per_page) . ", " . (int)($max_entries_per_page));
                $page_a = ($current_page * $max_entries_per_page);
                $page_b = ($max_entries_per_page);
            }
            if ($overall_entries > $max_entries_per_page) {
                $page_output = ("Seiten: ");
                if ($current_page != "all" && ($current_page + 1) > 1) {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&" . $var_page_name . "=" . ($current_page - 1) . "&orderby=" . $orderby . "\">" ."<b>" . "<" . "</b>" . "</a>");
                }
                $i = 0;
                while ($i < ($overall_entries / $max_entries_per_page)) {
                    if ($current_page == $i && $current_page != "all") {
                        $page_output .= (" " . ($i + 1));
                    } else {
                        $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&" . $var_page_name . "=" . $i . "\">" ."<b>" . ($i + 1) . "</b>" . "</a>");
                    }
                    $i++;
                }
                if ($current_page != "all" && ($current_page + 1) < ($overall_entries/$max_entries_per_page)) {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link ."&" . $var_page_name . "=" . ($current_page + 1) . "\">" ."<b>" . ">" . "</b>" . "</a>");
                }
                if ($current_page != "all") {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link ."&" . $var_page_name . "=all" . "\">" ."<b>" . "Alle" . "</b>" . "</a>");
                }
                if ($current_page == "all") {
                    $page_output .= " Alle";
                }
            }

            $output["html"] = $page_output;
            $output["sql"] = $page_sql;
            $output["a"] = $page_a;
            $output["b"] = $page_b;

            return($output);

            // ?!?! unset($output); unset($working_link); unset($page_sql); unset($page_output);
        } else {
            echo("Error: Function page_split needs defined: current_page, max_entries_per_page,working_link, page_varname For more information please visit the lansuite programmers docu");
        }
    }

    public function FileUpload($source_var, $path, $name = null)
    {
        global $config;

        switch ($_FILES[$source_var]['error']) {
            case 1:
                echo "Fehler: Die hochgeladene Datei überschreitet die in der Anweisung upload_max_filesize in php.ini festgelegte Größe";
                return 0;
            break;
            case 2:
                echo "Fehler: Die hochgeladene Datei überschreitet die in dem HTML Formular mittels der Anweisung MAX_FILE_SIZE angegebene maximale Dateigröße";
                return 0;
            break;
            case 3:
                echo "Fehler: Die Datei wurde nur teilweise hochgeladen";
                return 0;
            break;
            case 4:
                #echo "Fehler: Es wurde keine Datei hochgeladen";
                return 0;
            break;
            default:
                if ($_FILES[$source_var]['tmp_name'] == '') {
                    return false;
                }

                if (strrpos($path, '/') + 1 != strlen($path)) {
                      $path .= "/";
                }
                if (!file_exists($path)) {
                      mkdir($path);
                }
                if ($name) {
                      // Auto-Add File-Extension
                    if (!strpos($name, ".")) {
                        $name .= substr($_FILES[$source_var]['name'], strrpos($_FILES[$source_var]['name'], "."), 5);
                    }
                        $target = $path . $name;
                } else {
                      $target = $path . $_FILES[$source_var]['name'];
                }

                  // Change .php to .php.txt
                switch (substr($target, strrpos($target, "."), strlen($target))) {
                    // Script extentions
                    case '.php':
                    case '.php2':
                    case '.php3':
                    case '.php4':
                    case '.php5':
                    case '.phtml':
                    case '.pwml':
                    case '.inc':
                    case '.asp':
                    case '.aspx':
                    case '.ascx':
                    case '.jsp':
                    case '.cfm':
                    case '.cfc':
                    case '.pl':
                    case '.bat':
                    case '.vbs':
                    case '.reg':
                    case '.cgi':
                    case '.shtml':
                        // Harmless extentions, but better to view with .txt
                    case '.html':
                    case '.htm':
                    case '.js':
                    case '.css':
                        $target .= '.txt';
                        break;
                }

                  $i = '';
                do {
                      $targetStart = substr($target, 0, strrpos($target, "."));
                      $targetEnd = substr($target, strrpos($target, "."), strlen($target));
                      $targetUniq = $targetStart . $i . $targetEnd;
                      $i++;
                } while (file_exists($targetUniq));
                if (move_uploaded_file($_FILES[$source_var]['tmp_name'], $targetUniq)) {
                      chmod($targetUniq, octdec($config["lansuite"]["chmod_file"]));
                      return $targetUniq;
                } else {
                      echo "Fehler: Datei konnte nicht hochgeladen werden." . HTML_NEWLINE;
                      print_r($_FILES);
                      return 0;
                }
                break;
        }
    }

    public function CreateDir($dir)
    {
        global $config;

        if (!is_dir($dir)) {
            mkdir($dir, octdec($config["lansuite"]["chmod_dir"]));
            #chmod($dir, octdec($config["lansuite"]["chmod_dir"]));
        }
    }

    public function ping($host, $timeout = 200000)
    {
        // Öffne Socket zum Server
        $handle=fsockopen('udp://'.$host, 7, $errno, $errstr);
        if (!$handle) {
            return false;
        } else {
            //Set read timeout
            socket_set_timeout($handle, 0, $timeout);
            //Time the responce
            list($usec, $sec) = explode(" ", microtime(true));
            $start=(float)$usec + (float)$sec;

            //send somthing
            $write=fwrite($handle, "echo this\n");
            if (!$write) {
                fclose($handle);
                return false;
            }
            //Try to read. the server will most likely respond with a "ICMP Destination Unreachable" and end the read. But that is a responce!
            fread($handle, 1024);

            //Work out if we got a responce and time it
            list($usec, $sec) = explode(" ", microtime(true));
            $laptime=((float)$usec + (float)$sec)-$start;
            if (($laptime*1000000)>($timeout*0.9)) {
                fclose($handle);
                return false;
            } else {
                fclose($handle);
                return true;
            }
        }
    }

    public function FormatFileSize($size)
    {
        $i = 0;
        $iec = array("Byte", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $i++;
        }
        return round($size, 2) .' '. $iec[$i];
    }

    public function GetDirList($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $ret = array();
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if ((substr($file, 0, 1)  != '.') and ($file != '.svn')) {
                $ret[] = strtolower($file);
            }
        }
        closedir($handle);

        sort($ret);
        return $ret;
    }

  /**
   * func::chk_img_path() Check for a valid Picturepath
   *
   * @param  mixed    Path to test for validity
   * @return boolean  Path OK an Picture exists
   * @static
   */
    public function chk_img_path($imgpath)
    {
        if ($imgpath != '' and $imgpath != 'none' and $imgpath != '0') {
            if (is_file($imgpath)) {
                return 1;
            } else {
                return 0;
            }
        }
    }

  /**
   * Read DB and shows if a Superadmin exists
   *
   * @return boolean
   * @static
   */
    public function admin_exists()
    {
        global $db, $config;

        // If the system is not configured at all, quit.
        // Otherwise the query below would throw an
        //      Table 'lansuite.user' doesn't exist
        // error
        if (!$config['environment']['configured']) {
            return false;
        }

        if (is_object($db) and $db->success==1) {
            $res = $db->qry("SELECT userid FROM %prefix%user WHERE type = 3 LIMIT 1");
            if ($db->num_rows($res) > 0) {
                $found = 1;
            } else {
                $found = 0;
            }
            $db->free_result($res);
            return $found;
        }

        return false;
    }

    public function CutString($str, $SoftLimit, $HardLimit = false)
    {
        if ($HardLimit === false) {
            $HardLimit = $SoftLimit + 6;
        }
        if ($HardLimit and strlen($str) > $HardLimit) {
            return substr($str, 0, $HardLimit - 2) . '...';
        } elseif (strlen($str) > $SoftLimit) {
            preg_match('/[^a-zA-Z0-9]/', substr($str, $SoftLimit, strlen($str)), $ret, PREG_OFFSET_CAPTURE);
            return substr($str, 0, $SoftLimit + $ret[0][1]) . '...';
        } else {
            return $str;
        }
    }

    public function CheckNewPosts($last_change, $table, $entryid, $userid = 0)
    {
        global $db, $auth;

      // Older, than one week
        if ($last_change < (time() - 60 * 60 * 24 * 7)) {
            return 0;
        }

      // If logged out, everyting in the last week is considered new
        if (!$userid) {
            $userid = $auth['userid'];
        }
        if (!$userid) {
            return 1;
        } // If logged in
        else {
            $last_read = $db->qry_first('SELECT UNIX_TIMESTAMP(date) AS date FROM %prefix%lastread
          WHERE userid = %int% AND tab = %string% AND entryid = %int%', $userid, $table, $entryid);

            // Older, than one week
            if ($last_change < (time() - 60 * 60 * 24 * 7)) {
                  return 0;
            } // No entry -> Thread completely new
            elseif (!$last_read['date']) {
                    return 1;
            } // Entry exists
            else {
                  // The posts date is newer than the mark -> New
                if ($last_read['date'] < $last_change) {
                      return 1;
                } // The posts date is older than the mark -> Old
                else {
                      return 0;
                }
            }
        }
    }

    public function SetRead($table, $entryid, $userid = 0)
    {
        global $db, $auth;

        if (!$userid) {
            $userid = $auth['userid'];
        }

        $search_read = $db->qry_first("SELECT 1 AS found FROM %prefix%lastread WHERE tab = %string% AND entryid = %int% AND userid = %int%", $table, $entryid, $userid);
        if ($search_read["found"]) {
            $db->qry_first("UPDATE %prefix%lastread SET date = NOW() WHERE tab = %string% AND entryid = %int% AND userid = %int%", $table, $entryid, $userid);
        } else {
            $db->qry_first("INSERT INTO %prefix%lastread SET date = NOW(), tab = %string%, entryid = %int%, userid = %int%", $table, $entryid, $userid);
        }
    }


    public function CreateSignonBar($guests, $paid_guests, $max_guests)
    {
        $max_bars = 100;

        // Angemeldet länge ausrechnen.
        if ($max_guests * $guests) {
            $curuser = round($max_bars / $max_guests * $guests);
        }
        if ($curuser > $max_bars) {
            $curuser = $max_bars;
        }

        // Bezahlt länge ausrechnen.
        if ($max_guests * $paid_guests) {
            $gesamtpaid = round($max_bars / $max_guests * $paid_guests);
        }
        if ($gesamtpaid > $max_bars) {
            $gesamtpaid = $max_bars;
        }

        // Wirkliche Bildanzahl ausrechenn
        $pixelges = $max_bars - $curuser;
        $pixelcuruser = $curuser - $gesamtpaid;
        $pixelpaid = $gesamtpaid;

      // Bar erzeugen
        if ($pixelpaid > 0) {
            $bar = '<ul class="BarOccupied infolink" style="width:'. $pixelpaid .'px;">&nbsp;<span class="infobox">'. t('Angemeldet und Bezahlt') .': '. $paid_guests .'</span></ul>';
        }
        if ($pixelcuruser > 0) {
            $bar .= '<ul class="BarMarked infolink" style="width:'. $pixelcuruser .'px;">&nbsp;<span class="infobox">'. t('Nur Angemeldet') .': '. ($guests - $paid_guests) .'</span></ul>';
        }
        if ($pixelges > 0) {
            $bar .= '<ul class="BarFree infolink" style="width:'. $pixelges .'px;">&nbsp;<span class="infobox">'. t('Frei') .': '. ($max_guests - $paid_guests) .'</span></ul>';
        }
        $bar .= '<ul class="BarClear">&nbsp;</ul>';

        return $bar;
    }

    public function getActiveModules()
    {
        global $db;

        $this->ActiveModules = array();
        $res = $db->qry('SELECT name, caption FROM %prefix%modules WHERE active = 1');
        while ($row = $db->fetch_array($res)) {
            $this->ActiveModules[$row['name']] = $row['caption'];
        }
        $db->free_result($res);
        $this->ActiveModules['helplet'] = 'Helplets';
        $this->ActiveModules['popups'] = 'Popups';
        $this->ActiveModules['auth'] = 'Auth';
    }

    public function isModActive($mod, &$caption = '')
    {
        $caption = $this->ActiveModules[$mod];
        return array_key_exists($mod, $this->ActiveModules);
    }
}
