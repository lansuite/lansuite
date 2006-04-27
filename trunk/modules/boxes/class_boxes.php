<?php
/*************************************************************************
* 
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:		2.0
*	File Version:			1.0
*	Filename: 				boxes.php
*	Module: 				boxes
*	Main editor: 			traxanos@madnet.info
*	2nd editor: 			knox@orgapage.net
*	Last change: 			15.10.2004
*	Description: 			Boxenverwaltung / -generierung
*	Remarks: 					
*	
**************************************************************************/

class boxes {
	var $box_status;

	// Constructor (fteches box states and opens/closes the clicked box
	function boxes() {
		global $auth, $db, $config;

		// In LogOff state all boxes are visible (no ability to minimize them)
		if ($auth['login'] == "1") {

			// Fetch box state
			$res = $db->query("SELECT box
					FROM {$config['tables']['boxes_closed']}
					WHERE userid = '{$auth['userid']}'
					");
			while ($row = $db->fetch_array($res)) {
				$this->box_status[$row["box"]] = 1;		// 1 = Closed
			}

			
			// Change state, when Item is clicked
			if ($_GET['boxid'] != "") {
				if ($this->box_status[$_GET['boxid']] == 1) {
					$this->box_status[$_GET['boxid']] = 0;
					$db->query("DELETE FROM {$config['tables']['boxes_closed']} WHERE box='{$_GET['boxid']}' AND userid='{$auth['userid']}'");
				} else {
					$this->box_status[$_GET['boxid']] = 1;
					$db->query("INSERT INTO {$config['tables']['boxes_closed']} SET box='{$_GET['boxid']}', userid='{$auth['userid']}'");
				}
			}
		}
	}


	function LinkItem($link, $caption, $class = "", $hint='') {
		global $templ, $dsp;

		if ($link != "") {
  		$templ['box']['row']['hint'] = $hint;
			$templ['box']['row']['link'] = $link;
			$templ['box']['row']['class'] = $class;

			$templ['box']['row']['content'] = $caption;
			return $dsp->FetchModTpl("boxes", "link_item");
		} else return $caption;
	}

	function ItemRow($item, $caption, $link = "", $hint = "", $class = "") {
		global $templ, $dsp;

		$templ['box']['row']['item'] = $item;
		if (strip_tags($caption) == $caption) $caption = wordwrap($caption, 18, "<br />\n");
		$templ['box']['row']['link_cont'] = $this->LinkItem($link, $caption, $class, $hint);
		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "item_row");
	}

	function HRuleRow() {
		global $templ, $dsp;

		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "hrule_row");
	}

	function HRuleEngagedRow() {
		global $templ, $dsp;

		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "hrule_row_engaged");
	}

	function DotRow($caption, $link = "", $hint = "", $class = "", $highlighted = "") {
		if ($highlighted) $item = "_active";

		$this->ItemRow($item, $caption, $link, $hint, $class);
	}

	function EmptyRow() {
		global $templ, $dsp;

		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "empty_row");
	}

	function EngangedRow($caption, $link = "", $hint = "", $class = "") {
		global $templ, $dsp;

		$templ['box']['row']['hint'] = $hint;
		$templ['box']['row']['content'] = $caption;
		if (strip_tags($caption) == $caption) $caption = wordwrap($caption, 18, "<br />\n");
		$templ['box']['row']['link_cont'] = $this->LinkItem($link, $caption, $class);
		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "engaged_row");
	}

	function AddTemplate($template) {
		global $templ, $dsp;

		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", $template);
	}

	function CreateBox($title, $caption = "") {
		global $func, $auth, $dsp, $templ;

		if ($this->box_status[$title] == 0 ) {
			$box_content = file("design/{$auth["design"]}/templates/box_case.htm");
			$content = $dsp->FetchModTpl("boxes", "box");
		} else $box_content = file("design/{$auth["design"]}/templates/box_case_closed.htm");

		$box_content = @implode("\n", $box_content);
		$box_content = str_replace("{default_design}", $auth["design"], $box_content);
		$box_content = str_replace("{title}", $title, $box_content);
		$box_content = str_replace("{caption}", $caption, $box_content);
		$box_content = str_replace("{content}", $content, $box_content);
		$box_content = str_replace("\"","\\\"",$box_content );
		$box_content = str_replace("{link_open_close}", "?boxid=$title", $box_content);

		eval("\$box_content = \"".$box_content."\";");
		return $box_content;
	}
}
?>