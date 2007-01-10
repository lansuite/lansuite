<?php
define('NO_LINK', -1);

class func {

	// Constructor
	// Referer ermitteln und in einen Internen Link wandeln
	function func() {
		$url_array = parse_url($_SERVER['HTTP_REFERER']);
		$this->internal_referer = "?".$url_array['query'].$url_array['fragment'];
    }


	function read_db_config() {
		global $db, $config;

		$get_conf = $db->query("SELECT cfg_value, cfg_key FROM {$config["tables"]["config"]}");
		while ($row=$db->fetch_array($get_conf)) $cfg["{$row['cfg_key']}"] = $row['cfg_value'];
		$db->free_result($get_conf);

		return $cfg;
	}

	
	function checkIP($ip) {
		if (strlen($ip) < 5 OR strlen($ip) > 15) return 0;

		$IPParts = explode(".", $ip);
		if (count($IPParts) != 4) return 0;
		if ($IPParts[0] == 0 ) return 0;

		for ($i=0; $i<=3; $i++) {
			if (ereg("[^0-9]", $IPParts[$i])) return 0;
			if ($IPParts[$i] > 255 or $IPParts[$i] < 0) return 0;
		}
        return 1;
	}

	function button_userdetails($userid, $target) {
		global $auth;

		if ($target == "new") $target = 'target="_blank"';
		return ' <a href="index.php?mod=usrmgr&action=details&userid='.$userid.'" '.$target.'><img src="design/'. $auth["design"] .'/images/arrows_user.gif" border="0"/></a>';
	}
	
	function gettemplate($template) {
	global $auth, $lang;

		if (is_file("design/".$auth["design"]."/templates/". $template. ".htm" ) ) {
			return str_replace("{default_design}", $auth["design"], str_replace("\"", "\\\"", implode("", file("design/".$auth["design"]."/templates/".$template.".htm"))));

		} elseif (is_file("design/templates/". $template. ".htm") ) {
			return str_replace("{default_design}", $auth["design"], str_replace("\"", "\\\"", implode("", file("design/templates/".$template.".htm"))));

		} else echo(HTML_FONT_ERROR. str_replace("%TEMPL%", "design/templates/$template.htm", $lang['class_func']['no_templ']) . HTML_FONT_END);
	}

	function templ_output($template) {
		echo $template;
	}

	function date2unixstamp($year,$month,$day,$hour,$minute,$second) {	
		$func_timestamp = @mktime($hour,$minute,$second,$month,$day,$year);
		return $func_timestamp;		
	} 
	
	function setainfo( $text, $userid, $priority, $item, $itemid) {
		global $db, $config, $lang;

		if ($priority != "0" AND $priority != "1" AND $priority != "2") {
			echo($lang['class_func']['seatinfo_priority']);
		} else { 
			$date = date("U");
			$db->query("INSERT INTO {$config["tables"]["infobox"]} SET userid='$userid', class='$item', id_in_class = '$itemid', text='$text', date='$date', priority='$priority'");
		}
	}
	
	function unixstamp2date($func_timestamp,$func_art) {
		global $lang;

		switch($func_art) {
			case "year":		$func_date  = date("Y", $func_timestamp);		break;		
			case "month":		$func_date  = date("Y", $func_timestamp) ." - ". $this->translate_monthname(date("F", $func_timestamp));		break;		
			case "date":		$func_date  = date("d.m.Y", $func_timestamp);		break;		
			case "time":		$func_date  = date("H:i", $func_timestamp);		break;
			case "shorttime":	$func_date  = date("H:i", $func_timestamp);		break;
			case "datetime":	$func_date  = date("d.m.Y H:i", $func_timestamp);	break;
			case "daydatetime":
				$day[0] = $lang['class_func']['sunday'];
				$day[1] = $lang['class_func']['monday'];
				$day[2] = $lang['class_func']['tuesday'];
				$day[3] = $lang['class_func']['wednesdey'];
				$day[4] = $lang['class_func']['thursday'];
				$day[5] = $lang['class_func']['friday'];
				$day[6] = $lang['class_func']['saturday'];

				$func_date .= $day[date("w", $func_timestamp)];	
				$func_date .= ", ";	
				$func_date .= date("d.m.Y H:i", $func_timestamp);
			break;

			case "daydate":
				$day[0] = $lang['class_func']['sunday'];
				$day[1] = $lang['class_func']['monday'];
				$day[2] = $lang['class_func']['tuesday'];
				$day[3] = $lang['class_func']['wednesdey'];
				$day[4] = $lang['class_func']['thursday'];
				$day[5] = $lang['class_func']['friday'];
				$day[6] = $lang['class_func']['saturday'];
				$func_date .= date("d.m.Y", $func_timestamp) . " (". $day[date("w", $func_timestamp)] .")";
			break;

			case "shortdaytime":
				$day[0] = $lang['class_func']['sunday_short'];
				$day[1] = $lang['class_func']['monday_short'];
				$day[2] = $lang['class_func']['tuesday_short'];
				$day[3] = $lang['class_func']['wednesdey_short'];
				$day[4] = $lang['class_func']['thursday_short'];
				$day[5] = $lang['class_func']['friday_short'];
				$day[6] = $lang['class_func']['saturday_short'];

				$func_date .= $day[date("w", $func_timestamp)];	
				$func_date .= ", ";	
				$func_date .= date("H:i", $func_timestamp);
			break;
		}
		return $func_date;
	}


	function translate_weekdayname($name) {
		global $lang;

		$name = strtolower($name);
		return $lang['class_func'][$name];
	}

	
	function translate_monthname($name) {
		global $lang;

		$name = strtolower($name);
		return $lang['class_func'][$name];
	}

	
	// #### DIALOG FUNCTIONS ####
	function error($text, $link_target = '') {
		global $templ, $auth, $lang, $language, $dsp;
		
    // Close Layout table, if opened 
    $dsp->AddContent();

		if ($link_target == '') $link_target = $this->internal_referer;
		if ($link_target == NO_LINK) $link_target = '';
		if ($link_target) $templ['error']['info']['link'] = $dsp->FetchIcon($link_target, "back");

		switch($text) {
			case "ACCESS_DENIED":
				$templ['error']['info']['errormsg'] = $lang['class_func']['error_access_denied'];
			break;
			case "NO_LOGIN":
				$templ['error']['info']['errormsg'] = $lang['class_func']['error_no_login'];
			break;
			case "NOT_FOUND":
				$templ['error']['info']['errormsg'] = $lang['class_func']['error_not_found'];
			break;
			case "DEACTIVATED":
				$templ['error']['info']['errormsg'] = $lang['class_func']['error_deactivated'];
			break;
			case "NO_REFRESH":
				$templ['error']['info']['errormsg'] = $lang['class_func']['error_no_refresh'];
			break;
			default:
				$templ['error']['info']['errormsg'] = $text;
			break;
		}
    $dsp->AddContent();
    $dsp->AddTpl("design/templates/error.htm");
    $dsp->AddContent();
	}

	function confirmation($text, $link_target = '') {
		global $templ, $auth, $dsp, $language;

		if ($link_target == '') $link_target = $this->internal_referer;
		if ($link_target == NO_LINK) $link_target = '';
		if ($link_target) $templ['confirmation']['control']['link'] = $dsp->FetchIcon($link_target, "back");
		$templ['confirmation']['info']['confirmationmsg']	= $text;

    $dsp->AddContent();
    $dsp->AddTpl("design/templates/confirmation.htm");
    $dsp->AddContent();
	}

	function information($text, $link_target = '', $button_text = 'back') {
		global $templ, $auth, $dsp, $language;

		if ($link_target == '') $link_target = $this->internal_referer;
		if ($link_target == NO_LINK) $link_target = '';
		if ($link_target) $templ['confirmation']['control']['link'] = $dsp->FetchIcon($link_target, $button_text);
		$templ['confirmation']['info']['confirmationmsg'] = $text;

    $dsp->AddContent();
    $dsp->AddTpl("design/templates/information.htm");
    $dsp->AddContent();
	}

	function multiquestion($questionarray, $linkarray, $text) {
		global $templ, $dsp;

		if (!$text) $templ['multiquestion']['info']['text'] = "Bitte w&auml;hlen Sie eine M&ouml;glichkeit aus";
		else $templ['multiquestion']['info']['text']	= $text;

		if (is_array($questionarray)) foreach($questionarray as $ind => $question) {
			$templ['multiquestion']['row']['text']	= $question;
			$templ['multiquestion']['row']['link']	= $linkarray[$ind];

      $templ['multiquestion']['control']['row'] .= $dsp->FetchTpl("design/templates/multiquestion_row.htm", $templ);
		}
    $dsp->AddContent();
    $dsp->AddTpl("design/templates/multiquestion.htm");
    $dsp->AddContent();
	}

	function dialog($dialogarray, $linkarray, $picarray) {
		global $templ, $gd, $dsp;

		if ($dialogarray[0]=="") $dialogarray[0]="question";
		if ($dialogarray[1]=="") $dialogarray[1]="Frage";

		$templ['dialog']['info']['icon']		= $dialogarray[0]; // using the pic filename w/o "icon_" & ".gif" !
		$templ['dialog']['info']['caption']		= $dialogarray[1];
		$templ['dialog']['info']['questionmsg']	= $dialogarray[2];

		if (is_array($linkarray)) foreach ($linkarray as $ind => $link)
      $templ['dialog']['control']['row'] .= $dsp->FetchButton($link, $picarray[$ind]);

    $dsp->AddContent();
    $dsp->AddTpl("design/templates/dialog.htm");
    $dsp->AddContent();
	}

	function question($text, $link_target_yes, $link_target_no = '') {
		global $templ, $auth, $dsp, $language;

		if ($link_target_no == '') $link_target_no = $this->internal_referer;

		$templ['question']['info']['questionmsg']	= $text;
		$templ['question']['control']['link']['yes'] = $dsp->FetchIcon($link_target_yes, "yes");
		$templ['question']['control']['link']['no'] = $dsp->FetchIcon($link_target_no, "no");

    $dsp->AddContent();
    $dsp->AddTpl("design/templates/question.htm");
    $dsp->AddContent();
	}

	function no_items($object,$link_target,$type) {
		global $templ, $auth, $lang, $dsp, $language;

		switch($type) {
			case "rlist":	$templ['no_item']['info']['no_itemmsg']	= str_replace("%OBJECT%",$object,$lang['class_func']['no_item_rlist']); break;
			case "search":	$templ['no_item']['info']['no_itemmsg']	= str_replace("%OBJECT%",$object,$lang['class_func']['no_item_search']); break;
			case "free":	$templ['no_item']['info']['no_itemmsg']	= $object; break;
		}

		if ($link_target) $templ['confirmation']['control']['link'] = $dsp->FetchButton($link_target, "back");

    $dsp->AddContent();
    $dsp->AddTpl("design/templates/no_item.htm");
    $dsp->AddContent();
	}
	
	function text2html($string) {
		global $db, $config;

		$img_start = "<img src=\"design/".$_SESSION["auth"]["design"]."/images/";
		$img_start2 = "<img src=\"ext_inc/smilies/";
		$img_end   = "\" border=\"0\" />";

		$string = str_replace("&", "&amp;", $string);
		$string = str_replace("\"", "&quot;", $string);
		$string = str_replace("<", "&lt;", $string);
		$string = str_replace(">", "&gt;", $string);
		
		$string = str_replace("&lt;!--", "<!--", $string);
		$string = str_replace("--&gt;", "-->", $string);
		$string = str_replace("&lt;?", "<?", $string);
		$string = str_replace("?&gt;", '?'.'>', $string);
		$string = strip_tags($string);

		$string = eregi_replace('\[img\]([^\[]*)\[/img\]', '<img src="\1" border="1" class="img" alt="" />', $string);

		$string = eregi_replace("([ \n])http://([^ ,\n]*)", "\\1[url]http://\\2[/url]", $string);
		$string = eregi_replace("([ \n])ftp://([^ ,\n]*)", "\\1[url]ftp://\\2[/url]", $string);
		$string = eregi_replace("([ \n])www\\.([^ ,\n]*)", "\\1[url]http://www.\\2[/url]", $string);
		$string = eregi_replace("^http://([^ ,\n]*)", "[url]http://\\1[/url]", $string);
		$string = eregi_replace("^ftp://([^ ,\n]*)", "[url]ftp://\\1[/url]", $string);
		$string = eregi_replace("^www\\.([^ ,\n]*)", "[url]http://www.\\1[/url]", $string);
 		$string = eregi_replace('\[url\]www.([^\[]*)\[/url\]', '<a href="http://www.\1" target="_blank">\1</a>', $string);
		$string = eregi_replace('\[url\]([^\[]*)\[/url\]', '<a href="\1" target="_blank">\1</a>', $string);
		$string = eregi_replace('\[mailurl\]([^\[]*)\[/mailurl\]', '<a href="\1">Link</a>', $string);
		$string = eregi_replace('\[url=www.([^\[]*)\]([^\[]*)\[/url\]', '<a href="http://www.\1" target="_blank">\2</a>', $string); 
		$string = eregi_replace('\[url=([^\[]*)\]([^\[]*)\[/url\]', '<a href="\1" target="_blank">\2</a>', $string);
		$string = eregi_replace('\[url_www.([^\[]*)\]([^\[]*)\[/url\]', '<a href="http://www.\1" target="_self">\2</a>', $string); 
		$string = eregi_replace('\[url_([^\[]*)\]([^\[]*)\[/url\]', '<a href="\1" target="_self">\2</a>', $string);

		$string = str_replace("\n", '<br />', $string);
		$string = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $string);

		$string = str_replace('[b]', '<b>', $string);
		$string = str_replace('[/b]', '</b>', $string);
		$string = str_replace('[i]', '<i>', $string);
		$string = str_replace('[/i]', '</i>', $string);
		$string = str_replace('[u]', '<u>', $string);
		$string = str_replace('[/u]', '</u>', $string);

 		$string = preg_replace('#\[c\](.*)\[/c\]#sUi', '<blockquote><div class="tbl_small">Code:</div><table width="100%" cellspacing="1" cellpadding="2" class="tbl_4"><tr class="tbl_7"><td>\\1</td></tr></table></blockquote>', $string);
 		$string = preg_replace('#\[quote\](.*)\[/quote\]#sUi', '<blockquote><div class="tbl_small">Zitat:</div><table width="70%" cellspacing="1" cellpadding="2" class="tbl_4"><tr class="tbl_7"><td>\\1</td></tr></table></blockquote>', $string);
 		$string = preg_replace('#\[size=([0-9]+)\](.*)\[/size\]#sUi', '<font style="font-size:\1px">\2</font>', $string);
 		$string = preg_replace('#\[color=([a-z]+)\](.*)\[/color\]#sUi', '<font color="\1">\2</font>', $string);

		$res = $db->query("SELECT shortcut, image FROM {$config["tables"]["smilies"]}");
		while ($row = $db->fetch_array($res)) $string = str_replace($row['shortcut'], $img_start2 . $row['image'] . $img_end, $string);
    $db->free_result($res);

		return $string;
	}
			
	function generate_error_template($template_name,$formfield_name,$error_text) {
		// THIS CODE FITS THE NEW TEMPL VARS
		$templ_names = explode("_",$template_name);

		foreach($templ_names AS $templ_name_piece) $templ_name .= "[\"" . $templ_name_piece . "\"]";

		$to_return .= (
			"\$templ" . $templ_name . "[\"error\"][\"$formfield_name\"][\"bgrd\"] = \"_error\";" .
			"\$templ" . $templ_name . "[\"error\"][\"$formfield_name\"][\"msg\"] = " . "\"" . $error_text . "\";"
		);

		return ($to_return);
	}		

	function log_event($message, $type, $sort_tag = '', $target_id = '') {
		global $db, $config, $auth, $CurentURLMod;

		if ($message == '' or $type == '') echo("Function log_event needs message and type defined! - Invalid arguments supplied!");

		// Types:  1 = Info, 2 = Warning, 3 = Error (be careful with 3)
		else {
			if ($sort_tag == '') $sort_tag = $CurentURLMod;
			$atuser = $auth["userid"];
			if($atuser == "") $atuser = "0";
			$timestamp = date("U");
			$entry = $db->query("INSERT INTO {$config["tables"]["log"]} SET
        userid='$atuser',
        description='". $this->escape_sql($message) ."',
        type='$type',
        date='$timestamp',
        sort_tag = '$sort_tag',
        target_id = '$target_id'
        ");

			if ($entry == 1) return(1); else return(0);
		}
								
	}
	

	function page_split($current_page, $max_entries_per_page, $overall_entries, $working_link, $var_page_name) {
		if ($max_entries_per_page > 0 and $overall_entries >= 0 and $working_link != "" and $var_page_name != "") {
			if($current_page == "") {
				$page_sql = "LIMIT 0," . $max_entries_per_page;
				$page_a = 0;
				$page_b = $max_entries_per_page;
			}
			if($current_page == "all") {
				$page_sql = "";
				$page_a = 0;
				$page_b = $overall_entries;
			} else	{
				$page_sql = ("LIMIT " . ($current_page * $max_entries_per_page) . ", " . ($max_entries_per_page));
				$page_a = ($current_page * $max_entries_per_page);
				$page_b = ($max_entries_per_page);
			}
			if($overall_entries > $max_entries_per_page) {
				$page_output = ("Seiten: ");
				if( $current_page != "all" && ($current_page + 1) > 1 ) {
					$page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&" . $var_page_name . "=" . ($current_page - 1) . "&orderby=" . $orderby . "\">" ."<b>" . "<" . "</b>" . "</a>");
				}
				$i = 0;					
				while($i < ($overall_entries / $max_entries_per_page)) {
					if($current_page == $i && $current_page != "all") {
						$page_output .= (" " . ($i + 1));
					} else {
						$page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&" . $var_page_name . "=" . $i . "\">" ."<b>" . ($i + 1) . "</b>" . "</a>");
					}
					$i++;
				}
				if($current_page != "all" && ($current_page + 1) < ($overall_entries/$max_entries_per_page)) {
					$page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link ."&" . $var_page_name . "=" . ($current_page + 1) . "\">" ."<b>" . ">" . "</b>" . "</a>");
				}
				if($current_page != "all") {
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
	
		} else echo ("Error: Function page_split needs defined: current_page, max_entries_per_page,working_link, page_varname For more information please visit the lansuite programmers docu");
	}

	function check_var($var, $type, $min_length, $max_length) {
		if (($type == "integer" OR $type == "double" OR $type == "string" OR $type == "boolean" OR $type == "object" OR $type == "array") AND (isset($min_length) == FALSE OR gettype($min_length) == "integer") AND (isset($max_length) == FALSE OR gettype($max_length) == "integer") AND (isset($var) == TRUE))
		{
			if((gettype($var) == $type) AND (strlen($var) >= $min_length) AND (strlen($var) <= $max_length)) return TRUE;
			else return FALSE;
		} else echo "Error: Function check_var needs defined: var, datatype (may be integer, double, string, boolean, object or array), [optionally: min_length], [optionally: max_length] <br/> For more information please visit the lansuite programmers docu";
	}

	// Old. Use $func->text2html instead
	function db2text2html($text) {
//			$text = $this->db2text($text);
			$text = $this->text2html($text);
			return $text;
	}

	// Old. Do not use any more
	function text2db($text) {
			$text = trim($text);
//			$text = addslashes($text);
			return $text;
	}

	// Add slashes at any non GPC-variable
	// This function musst be used, if ' come from other sources, than $_GET, or $_POST
	// for example language-files
	function escape_sql($text) {
		#if (!get_magic_quotes_gpc())
		$text = addslashes(stripslashes($text));
		return $text;
	} 

	// Old. Do not use any more
	function db2text($text) {
//			$text = stripslashes($text);
			return $text;
	} 

	function check_exist($checktype, $id) {
		global $db, $config;

		switch($checktype) {
			default:
			case "userid":
				$row = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["user"]} WHERE userid = '$id'"); 
			break;
			case "seatid":
				$row = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["seat_seats"]} WHERE seatid = '$id'"); 
			break;
			case "blockid":
				$row = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["seat_block"]} WHERE blockid = '$id'");
			break;
			case "pollid":
				$row = $db->query_first("SELECT count(*) as n FROM {$config["tables"]["polls"]} WHERE pollid = '$id'");
			break;
		}

		if ($row["n"] == 1) return TRUE;
		else return FALSE;
	}

	function show_debug() {
		global $debug, $cfg, $config, $vars, $templ, $auth, $func, $db;

		if ($cfg["sys_debug_allinfo"]) {
			$this->debug_parse_array($vars, '$vars');
			$this->debug_parse_array($auth, '$auth');
			$this->debug_parse_array($cfg, '$cfg');
			$this->debug_parse_array($_ENV, '$_ENV');
			$this->debug_parse_array($_COOKIE, '$_COOKIE');
			$this->debug_parse_array($_SESSION, '$_SESSION');
			$this->debug_parse_array($_SERVER, '$_SERVER');
			$this->debug_parse_array($_FILES["importdata"], '$_FILES[importdata]');
			$debug[] = "<br/>MySQL-Server Info: ".$db->get_host_info();
			$debug[] = "<br/>Benötigte Abfragen für diese Seite: ".$db->querys_count;
		}

		if ($debug and $cfg["sys_showdebug"] and ($auth["type"] >= 2 or $cfg["sys_showdebug_alluser"])) {
			$debug_content = implode("", $debug);
			eval("\$templ['index']['debug']['content'] .= \"". $this->gettemplate("debug_case")."\";");
			return true;
		}
	}

	function debug_parse_array($array, $caption = NULL) {
		global $debug;

		if ($caption) $debug[] = HTML_NEWLINE . "<b>$caption</b>";
		if ($array) foreach($array as $key => $value) {
			if(is_array($value)){
				$this->debug_parse_array($value,"Array => $key");
			}else{
				if (strlen($value) > 80) $value = $this->wrap($value, 80);
				$debug[] = HTML_NEWLINE . "$key = $value";
			}
		}
		$debug[] = HTML_NEWLINE . "------------------------------";
	}

    function set_change_log($modul, $value1, $value2 = NULL, $value3 = NULL, $value4 = NULL, $value5 = NULL) {
     	global $db;

        // TODO: MODUL-Kürzel prüfen !

        if ($modul == "" or $value1 == "") {
			$this->set_change_log_error = "modul OR first value empty";
			return false;
		}

        $insert = $db->query("INSERT INTO {$db->tables['change_log']} SET modul = '$modul',
                                                                          value1 = '$value1',
                                                                          value2 = '$value2',
                                                                          value3 = '$value3',
                                                                          value4 = '$value4',
                                                                          value5 = '$value5'");
        if ($insert) {
         	echo "OK:".$insert;
            $i =  $db->insert_id();
         	return $i;
        } else return false;

    }

    // Läd den Log-Eintrag zur übergebenen ID
    function get_change_log($clogid) {
    }

    function get_last_change_log($modul) {
    }

	// Gibt das aktuelle Alter zurück
	function age($gebtimestamp) {
		$yeardiff = date("Y") - date("Y", $gebtimestamp);
		$monthdiff = date("m") - date("m", $gebtimestamp);
		$daydiff = date("j") - date("j", $gebtimestamp);

		if (($monthdiff < 0) || ($monthdiff == 0 && $daydiff < 0)) $age = $yeardiff - 1;
		else $age = $yeardiff;

		return $age;
	}

	// Gibt das Alter bei der LanParty zurück
	function age_at_lan($gebtimestamp) {
/*	Funktioniert so leider noch nicht
		$yeardiff = date("Y", mktime($cfg["signon_partybegin"])) - date("Y", $gebtimestamp);
		$monthdiff = date("m", mktime($cfg["signon_partybegin"])) - date("m", $gebtimestamp);
		$daydiff = date("j", mktime($cfg["signon_partybegin"])) - date("j", $gebtimestamp);

		if (($monthdiff < 0) || ($monthdiff == 0 && $daydiff < 0)) $age = $yeardiff - 1;
		else $age = $yeardiff;

		return $age;
*/
	}

	function FileUpload($source_var, $path, $name = NULL) {
		global $config;

    if ($_FILES[$source_var]['tmp_name'] == '') return false;
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
				echo "Fehler: Es wurde keine Datei hochgeladen";
				return 0;
			break;
			default:
				if (strrpos($path, '/') + 1 != strlen($path)) $path .= "/";
				if ($name) {
					// Auto-Add File-Extension
					if (!strpos($name, ".")) $name .= substr($_FILES[$source_var]['name'], strrpos($_FILES[$source_var]['name'], "."), 5);
					$target = $path . $name;
				} else $target = $path . $_FILES[$source_var]['name'];

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

				if (file_exists($target)) unlink($target);
				if (move_uploaded_file($_FILES[$source_var]['tmp_name'], $target)) {
					chmod ($target, octdec($config["lansuite"]["chmod_file"]));
					return $target;
				} else {
					echo "Fehler: Datei konnte nicht hoch geladen werden." . HTML_NEWLINE;
					print_r($_FILES);
					return 0;
				}
			break;
		}
	}

	function CreateDir($dir) {
		global $config;

		if (!is_dir($dir)) {
			mkdir($dir, octdec($config["lansuite"]["chmod_dir"]));
			#chmod($dir, octdec($config["lansuite"]["chmod_dir"]));
		}
	}

	function ping($host, $timeout = 200000){
		// Öffne Socket zum Server
		$handle=fsockopen('udp://'.$host, 7, $errno, $errstr);
		if (!$handle){
     		return false;
		}else{
			//Set read timeout
			socket_set_timeout($handle, 0 , $timeout);
			//Time the responce
			list($usec, $sec) = explode(" ", microtime(true));
			$start=(float)$usec + (float)$sec;

			//send somthing
			$write=fwrite($handle,"echo this\n");
			if(!$write){
				fclose($handle);
				return false;
			}
			//Try to read. the server will most likely respond with a "ICMP Destination Unreachable" and end the read. But that is a responce!
			fread($handle,1024);

			//Work out if we got a responce and time it
			list($usec, $sec) = explode(" ", microtime(true));
			$laptime=((float)$usec + (float)$sec)-$start;
			if(($laptime*1000000)>($timeout*0.9)){
				fclose($handle);
				return false;
			}else{
				fclose($handle);
				return true;
			}

		}
	}

	function translate($in) {
		global $LSCurFile; #, $db, $config, $language;

    $buffer = $LSCurFile;
    $LSCurFile = 'DB';
    $return = t($in);
    $LSCurFile = $buffer;

    return $return;
/*
    if ($language == 'de') return $in;
		else $out = $db->query_first("SELECT $language FROM {$config["tables"]["translations"]} WHERE de = '". $this->escape_sql($in) ."'");

		if ($out) return $out[$language];
		else return $in;
*/
	}
	
	function FormatSize($size) {
		if ($size < 1024) return $size . ' B';
		$units = array("kB", "MiB", "GB", "TB");
		foreach ($units as $unit) {
			$size = round($size / 1024, 1);
			if ($size < 1024) break;
		}
		return $size .' '. $unit;
	}

  function wrap($text, $maxlength, $spacer = "<br />\n") {
    $textarr = explode(' ', $text);
    $i = 0;
    foreach($textarr as $textpart) {
      if (strlen($textpart) > $maxlength) $textarr[$i] = chunk_split($textpart, $maxlength, $spacer);
      $i++;
    }
    return implode (' ', $textarr);
  }
}
?>
