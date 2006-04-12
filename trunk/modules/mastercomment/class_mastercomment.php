<?php

class Mastercomment{
	
	
	var $config;
	var $vars;
	var $data;
	var $class;
	var $lang;
	
	
	
	// Konstruktor
	function Mastercomment($vars, $working_link,$modul,$objectid,$objectname) {
		global $language;
		$this->config['working_link']   = $working_link;
		$this->config['modul']			= $modul;
		$this->config['objectid']		= $objectid;
		$this->config['objectname']		= $objectname;
		$this->vars                     = $vars;
	
		if(file_exists("modules/mastercomment/language/mastercomment_lang_$language.php")){
			include("modules/mastercomment/language/mastercomment_lang_$language.php");
		}else{
			include("modules/mastercomment/language/mastercomment_lang_de.php");
		}
		
		$this->lang = $lang;
		
	}
	
	
	
	function action(){
		switch ($this->vars['mcact']){
			
			default:
				$this->show_comment();
				$_SESSION['add_comment_blocker'] = 0;
				return "show";
			break;	
		
			case "add":
				$this->add_comment("add");
				return "add";
			break;
				
			case "delete":
				$this->delete_comment();
				return "delete";
			break;
			
			case "change":
				$this->add_comment("change");
				return "change";
			break;
			
		
		}
	
	
	
	
	}
	
	
	
	function show_comment(){
		global $func, $config, $dsp, $db, $templ, $auth;
		
		$found_entries = $db->query("SELECT commentid
						FROM {$config["tables"]["comments"]} 
						WHERE relatedto_id = '{$this->config['objectid']}'
						AND relatedto_item = '{$this->config['modul']}'");	
	
		$count_entrys =  $db->num_rows($found_entries);	
		
		$pages = $func->page_split($this->vars['comment_page'],$config["size"]["comments"],$count_entrys,$this->config['working_link'],"comment_page");

		$get_comments = $db->query("SELECT A.commentid, A.text, A.date, A.creatorid, B.username, B.userid, C.avatar_path, C.signature
							FROM {$config["tables"]["comments"]} AS A
							LEFT JOIN {$config["tables"]["user"]} AS B ON A.creatorid = B.userid
							LEFT JOIN {$config["tables"]["usersettings"]} AS C ON B.userid = C.userid
							WHERE A.relatedto_id = '{$this->config['objectid']}'
								AND A.relatedto_item = '{$this->config['modul']}'
								{$pages["sql"]}
								");
		
		$dsp->NewContent(str_replace("%MODULE%", $this->config['modul'], str_replace("%ITEM%", $this->config['objectname'], $this->lang['mastercomment']['comment_caption'])));

		if ($count_entrys > 0){
			if ($pages["html"]) $dsp->AddSingleRow($pages["html"] ,"align=\"right\"");
		
			$i = 1;
			while ($row = $db->fetch_array($get_comments)){

				$templ['mastercomment']['show']['row']['info']['text'] = $func->text2html($row["text"]);

				if($row["signature"] != "")
				$templ['mastercomment']['show']['row']['info']['text'] .= HTML_NEWLINE . HTML_NEWLINE . HTML_NEWLINE . $func->text2html($row["signature"]);

				$templ['mastercomment']['show']['row']['info']['number'] 	= $i++;
				$templ['mastercomment']['show']['row']['info']['username'] 	= $row["username"];
				$templ['mastercomment']['show']['row']['info']['date'] 		= $func->unixstamp2date($row["date"],"datetime");
				$templ['mastercomment']['show']['row']['info']['userid'] 	= $row["userid"];
				$templ['mastercomment']['show']['row']['info']['commentid'] 	= $row["commentid"];

				if($row["avatar_path"] != ""){
					$templ['mastercomment']['show']['row']['info']['avatar'] 	= "<img src=\"ext_inc/avatare/" . $row["avatar_path"] . "\"</img>";
				}else{
					$templ['mastercomment']['show']['row']['info']['avatar'] = "";
				}

				unset($templ['mastercomment']['show']['row']['control']['button_edit']);
				unset($templ['mastercomment']['show']['row']['control']['button_delete']);

				// Adding Buttons for deleting and changing (admin and oper only)
				if ($auth["type"] > 1 OR $row["userid"] == $auth["userid"]) {
					$templ['mastercomment']['show']['row']['control']['button_edit'] = $dsp->FetchButton($this->config['working_link'] . "&mcact=change&commentid=" . $row["commentid"],"edit");
					$templ['mastercomment']['show']['row']['control']['button_delete'] = $dsp->FetchButton($this->config['working_link'] . "&mcact=delete&commentid=" . $row["commentid"],"delete");
				}
				$dsp->AddModTpl("mastercomment", "mastercomment_show_row");
			}

			if ($pages["html"]) $dsp->AddSingleRow($pages["html"] ,"align=\"right\"");
			$dsp->AddSingleRow($this->lang['mastercomment']['count_comments'] . " $count_entrys");
		} else $dsp->AddSingleRow($this->lang['mastercomment']['no_comments']);
		
		if($_SESSION["auth"]["login"] != 0)
		$dsp->AddDoubleRow($this->lang['mastercomment']['add_comment'], $dsp->FetchButton($this->config['working_link'] . "&mcact=add","add"));
		$dsp->AddContent();
		
	}
	
	function add_comment($action){
		global $func,$db,$dsp,$config;
		// Error Check
		switch ($this->vars['mcstep']){
			default:
				if($action == "change"){
					$comment = $db->query_first("SELECT commentid, text FROM {$config["tables"]["comments"]}
							      							WHERE commentid='{$this->vars["commentid"]}'");	
					
					$this->vars["comment_comment"] = $comment["text"];
				}
			
			break;
			case 2:
			if(strlen($this->vars["comment_comment"]) > 1500) {

				$error_text = $this->lang['mastercomment']['error_text'];

				$this->vars['mcstep'] = 1;

			}


			if($this->vars["comment_comment"] == "") {

				$error_text = $this->lang['mastercomment']['error_text2'];

				$this->vars['mcstep'] = 1;
			}
		
		}
		
		
		switch ($this->vars['mcstep']){
			
			default:
				$dsp->NewContent(str_replace("%ITEM%",$this->config['objectname'],$lang['mastercomment']['add_comment_capt']));
				$dsp->SetForm($this->config['working_link'] . "&mcact=$action&mcstep=2&commentid={$this->vars["commentid"]}");
				$dsp->AddTextAreaPlusRow("comment_comment",$this->lang['mastercomment']['comment'],$this->vars["comment_comment"],$error_text);
				$dsp->AddFormSubmitRow("add");
				$dsp->AddBackButton($this->config['working_link']);
				$dsp->AddContent();
			break;
			
			case 2:
			if ($_SESSION['add_comment_blocker'] != 1) {

				$date = date("U");

				$userid = $_SESSION['auth']['userid'];
				if($action == "change"){
					$db->query("UPDATE {$config["tables"]["comments"]} SET text = '{$this->vars["comment_comment"]}'
										WHERE commentid = '{$this->vars["commentid"]}'");
		
					$func->confirmation($this->lang['mastercomment']['change_comment_ok'],$this->config['working_link']);
					
				}else{
					$db->query("INSERT INTO {$config["tables"]["comments"]} SET relatedto_id = '{$this->config['objectid']}',
												    relatedto_item = '{$this->config['modul']}',
												    creatorid = '$userid',
												    date = '$date',
												    text = '{$this->vars["comment_comment"]}'");

					$func->confirmation($this->lang['mastercomment']['add_comment_ok'],$this->config['working_link']);
				}

				$_SESSION['add_comment_blocker'] = 1;

			}

			else $func->error("NO_REFRESH","");
		
		}
		
	}
	
	function delete_comment(){
		global $func,$db,$config;
		
		switch($this->vars["mcstep"]) {

			default:

			$link_yes = ($this->config['working_link'] . "&commentid=" . $this->vars["commentid"] . "&mcstep=2&mcact=delete");

			$func->question($this->lang['mastercomment']['del_comment_quest'],$link_yes,$this->config['working_link']);

			break;


			case 2:

			$delete = $db->query("DELETE FROM {$config["tables"]["comments"]} WHERE commentid = '{$this->vars["commentid"]}'");

			if($delete == 1) {

				$func->confirmation($this->lang['mastercomment']['add_comment_del'],$this->config['working_link']);

			}

			else $func->error($this->lang['mastercomment']['comment_not_exist'],"");

			break;

		}
	}
	
	
}
?>
