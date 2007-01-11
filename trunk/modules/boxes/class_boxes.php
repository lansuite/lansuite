<?php
$LSCurFile = __FILE__;

class boxes {

	// Constructor (opens/closes the clicked box
	function boxes() {
		global $auth, $db, $config;

		// In LogOff state all boxes are visible (no ability to minimize them)
		if ($auth['login'] == "1") {

			// Change state, when Item is clicked
			if ($_GET['box_action'] == 'change' and $_GET['boxid'] != "") {
        if ($_SESSION['box_'. $_GET['boxid'] .'_active']) unset($_SESSION['box_'. $_GET['boxid'] .'_active']);
        else $_SESSION['box_'. $_GET['boxid'] .'_active'] = 1;
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
		global $templ, $dsp, $func;

		$templ['box']['row']['item'] = $item;
		if (strip_tags($caption) == $caption) $caption = $func->wrap($caption, 18);
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
		global $templ, $dsp, $func;

		$templ['box']['row']['hint'] = $hint;
		$templ['box']['row']['content'] = $caption;
		if (strip_tags($caption) == $caption) $caption = $func->wrap($caption, 18);
		$templ['box']['row']['link_cont'] = $this->LinkItem($link, $caption, $class);
		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", "engaged_row");
	}

	function AddTemplate($template) {
		global $templ, $dsp;

		$templ['box']['rows'] .= $dsp->FetchModTpl("boxes", $template);
	}

	function CreateBox($boxid, $caption = "") {
		global $func, $auth, $dsp, $templ;

		if (!$_SESSION['box_'. $boxid .'_active']) {
			$box_content = file("design/{$auth["design"]}/templates/box_case.htm");
			$content = $dsp->FetchModTpl("boxes", "box");
		} else $box_content = file("design/{$auth["design"]}/templates/box_case_closed.htm");

		$box_content = @implode("\n", $box_content);
		$box_content = str_replace("{default_design}", $auth["design"], $box_content);
		$box_content = str_replace("{title}", $boxid, $box_content);
		$box_content = str_replace("{caption}", $caption, $box_content);
		$box_content = str_replace("{content}", $content, $box_content);
		$box_content = str_replace("\"","\\\"",$box_content );
		$box_content = str_replace("{link_open_close}", "index.php?box_action=change&boxid=$boxid", $box_content);

		eval("\$box_content = \"".$box_content."\";");
		return $box_content;
	}
}



function PartyAvailible() {
  global $party;
  if ($party->count > 0) return 1;
  else return 0;
}

function MsgInIntMode() {
  global $cfg;
  if (!$cfg['sys_internet'] or $cfg['msgsys_alwayson']) return 1;
  else return 0;
}

function IsWWCLT() {
  global $db, $config, $party;
  if ($_GET['mod'] != 'tournament2') return 0;
  else {
    $row = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["tournament_tournaments"]} WHERE wwcl_gameid > 0 AND party_id = '{$party->party_id}'");
    if ($row['found']) return 1;
    else return 0;
  }
}

$box = new boxes();

// Fetach Boxes
$BoxRes = $db->query("SELECT boxid, name, place, source, module, callback FROM {$config["tables"]["boxes"]}
  WHERE active = 1
    AND (internet = 0 OR internet = {$cfg['sys_internet']} + 1)
    AND (login = 0 OR login = {$auth['login']} + 1)
  ORDER BY pos
  ");
while ($BoxRow = $db->fetch_array($BoxRes)) if (($BoxRow['module'] == '' or in_array($BoxRow['module'], $ActiveModules))
and ($BoxRow['callback'] == '' or call_user_func($BoxRow['callback'], ''))) {

  $templ['box']['rows'] = '';
  
  // Load file
  if (!$_SESSION['box_'. $BoxRow['boxid'] .'_active']) include_once('modules/boxes/'. $BoxRow['source'] .'.php');

  // Write content to template var
  if ($BoxRow['place'] == 0) $templ['index']['control']['boxes_letfside'] .= $box->CreateBox($BoxRow['boxid'], t($BoxRow['name']));
  elseif ($BoxRow['place'] == 1) $templ['index']['control']['boxes_rightside'] .= $box->CreateBox($BoxRow['boxid'], t($BoxRow['name']));
}
$db->free_result($BoxRes);

?>