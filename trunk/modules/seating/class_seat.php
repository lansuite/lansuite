<?php

class seat2 {

	function FlipVertical($text) {
		$res = '';
		for($i = 0; $i <= strlen($text); $i++) {
			$res .= substr($text, $i, 1) . '<br />';
		}
		$res = substr($res, 0, strlen($res) - 6);

		return $res;
	}

  function SeatNameLink($userid, $MaxBlockLength = 0, $break = '<br />'){
    global $db, $config, $party;
  
    // Unterscheidung Bezahlt oder Unbezahlt (aber nur 1 res. Platz)
    $seat_paid = $db->query_first("SELECT paid FROM {$config['tables']['party_user']}
                                   WHERE  party_id=$party->party_id AND user_id=$userid");
    if ($seat_paid['paid']>0) {$seat_status = 2;} else {$seat_status = 3;};

    if (!$userid) return '';
    else $row = $db->query_first("SELECT b.blockid, b.name, b.orientation, s.col, s.row FROM {$config['tables']['seat_block']} AS b
      LEFT JOIN {$config['tables']['seat_seats']} AS s ON b.blockid = s.blockid
      WHERE b.party_id = ". (int)$party->party_id ." AND s.userid = $userid AND s.status = $seat_status");
  
    if (!$row['blockid']) return '';
    else {
      $LinkText = $row['name'] .' '. $break . $this->CoordinateToName($row['col'] + 1, $row['row'], $row['orientation'], $MaxBlockLength);
  	  return "<a href=\"#\" onclick=\"javascript:var w=window.open('index.php?mod=seating&action=popup&design=popup&function=usrmgr&id={$row['blockid']}&userarray[]=$userid&l=1','_blank','width=596,height=678,resizable=yes');\" class=\"small\">$LinkText</a>";
  	}
  }

	function SeatOfUser($userid, $MaxBlockLength = 0, $LinkIt = 0) {
	  global $db, $config, $party; 
      // Unterscheidung Bezahlt oder Unbezahlt (aber nur 1 res. Platz)
      $seat_paid = $db->query_first("SELECT paid FROM {$config['tables']['party_user']}
                                     WHERE  party_id=$party->party_id AND user_id=$userid");
      if ($seat_paid['paid']>0) {$seat_status = 2;} else {$seat_status = 3;};

		$row = $db->query_first("SELECT s.row, s.col, b.blockid, b.name FROM {$config['tables']['seat_seats']} AS s
			LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b.blockid
			WHERE s.userid='$userid' AND s.status = $seat_status AND b.party_id = ". (int)$party->party_id);

    if ($row['blockid']) return $this->CoordinateToBlockAndName($row['col'] + 1, $row['row'], $row['blockid'], $MaxBlockLength, $LinkIt, $userid);
    else return false;
	}

  function CoordinateToBlockAndName($x, $y, $blockid, $MaxBlockLength = 0, $LinkIt = 0, $userid = 0) {
    global $db, $config;
    
		$row = $db->query_first("SELECT name, orientation FROM {$config['tables']['seat_block']} WHERE blockid = $blockid");

		if (!$row['name']) return false;
		else {
      if ($MaxBlockLength > 4 and strlen($row['name']) > $MaxBlockLength) $row['name'] = substr($row['name'], 0, $MaxBlockLength - 3) . '...';
      		
		  $LinkText = $row['name'] .' - '. $this->CoordinateToName($x, $y, $row['orientation']);
	    if ($LinkIt == 1) return "<a href=\"index.php?mod=seating&action=show&step=2&blockid=$blockid&col=$x&row=$y\">$LinkText</a>";
	    if ($LinkIt == 2) return "<a href=\"#\" onclick=\"javascript:var w=window.open('index.php?mod=seating&action=popup&design=popup&function=usrmgr&id=$blockid&userarray[]={$userid}&l=1','_blank','width=596,height=638,resizable=yes');\">$LinkText</a>";
	    else return $LinkText;
	  }
  }

	function CoordinateToName($x, $y, $orientation) {
		$out = '';
		if ($orientation) {
			if ($y > -1)				$out = ($y + 1);
			if ($x > -1 and $y > -1)	$out .= '-';
			if ($x > -1)				$out .= str_replace('@', '', chr(64 + floor(($x - 1) / 26))) . chr(65 + ($x - 1) % 26);
		} else {
			if ($y > -1)				$out = str_replace('@', '', chr(64 + floor(($y) / 26))) . chr(65 + ($y) % 26);
			if ($x > -1 and $y > -1)	$out .= '-';
			if ($x > -1)				$out .= ($x);
		}

		return $out;
	}

	function CreateSeatImage($name, $r, $g, $b, $percentage) {
		global $func, $gd, $auth;

    $target_dir = "ext_inc/auto_images/{$auth['design']}/seat/";
    $source_dir = "design/{$auth['design']}/images/";
    if (!file_exists($target_dir . $name .'.png')) {
  		$func->CreateDir($target_dir);
  		$gd->Colorize($source_dir .'seat.png', $r, $g, $b, $percentage);
  		$gd->PutImage($target_dir . $name .'.png', 'png');
  
  		// Create Image for selection
  		$gd->MergeImages($target_dir . $name .'.png', $source_dir .'seat_onclick.png', $target_dir . $name .'_onclick.png');
  	}
	}

	function U18Block($id, $idtype) {
		global $db;
		/*
		$id 	can be a userid or blockid
		$idtype can be
			"u" for userid (standard)
			"b" for blockid or

		TRUE - MEANS THAT IS A U18 BLOCK
		FALSE - MEANS THAT IS A over18 BLOCK OR !!! BLOCK DOESN'T EXIST
		*/

		if ($idtype == "b") $blockid = $id;
		elseif ($idtype != "b") {
      $row_seat = $db->query_first("SELECT blockid FROM {$GLOBALS['config']['tables']['seat_seats']} WHERE userid='$id'");
      $blockid = $row_seat['blockid'];
      if ($blockid == "") return FALSE;
		}

		$row_block = $db->query_first("SELECT u18, blockid FROM {$GLOBALS['config']['tables']['seat_block']} WHERE blockid='$blockid'");
		$blockid = $row_block['blockid'];
		if ($blockid == "") return FALSE;

		$u18 = $row_block["u18"];
		if ($u18 == TRUE) return TRUE;
		else return FALSE;
	}


	function DrawPlan($blockid, $mode, $linktarget = '', $selected_user = false) {
		global $db, $config, $dsp, $templ, $auth, $gd, $lang, $cfg, $party, $smarty;
		// $mode:
		// 0 = Normal display mode
		// 1 = With seperators
		// 2 = With checkboxes
		// 3 = Admin mode

    $smarty->assign('default_design', $auth['design']);

		// Create Images
		$this->CreateSeatImage('seat_free', 0, 250, 0, 60);
		$this->CreateSeatImage('seat_reserved', 250, 0, 0, 60);
		$this->CreateSeatImage('seat_checked_in', 100, 0, 0, 60);
		$this->CreateSeatImage('seat_checked_out', 0, 100, 0, 60);
		$this->CreateSeatImage('seat_marked', 200, 200, 0, 60);
		$this->CreateSeatImage('seat_myselfe', 0, 0, 200, 60);
		$this->CreateSeatImage('seat_clanmate', 0, 100, 200, 60);

		// Get Block data (side descriptions + number of rows + cols)
		$block = $db->query_first("SELECT * FROM {$config["tables"]["seat_block"]} WHERE blockid = '{$blockid}'");

		$smarty->assign('text_tl', $block['text_tl']);
		$smarty->assign('text_tc', $block['text_tc']);
		$smarty->assign('text_tr', $block['text_tr']);
		$smarty->assign('text_lt', $this->FlipVertical($block['text_lt']));
		$smarty->assign('text_lc', $this->FlipVertical($block['text_lc']));
		$smarty->assign('text_lb', $this->FlipVertical($block['text_lb']));
		$smarty->assign('text_rt', $this->FlipVertical($block['text_rt']));
		$smarty->assign('text_rc', $this->FlipVertical($block['text_rc']));
		$smarty->assign('text_rb', $this->FlipVertical($block['text_rb']));
		$smarty->assign('text_bl', $block['text_bl']);
		$smarty->assign('text_bc', $block['text_bc']);
		$smarty->assign('text_br', $block['text_br']);
		$smarty->assign('row_count', $block['rows'] + 1);
		$smarty->assign('col_count', $block['cols'] + 1);
    $smarty->assign('mode', $mode);

		// Get seperators
		$sep_cols = array();
		$sep_rows = array();
		$seperators = $db->query("SELECT orientation, value FROM {$config["tables"]["seat_sep"]} WHERE blockid = '$blockid'");
		while($seperator = $db->fetch_array($seperators)) {
			if ($seperator['orientation'] == 0) $sep_cols[$seperator['value']] = 1;	
			else $sep_rows[$seperator['value']] = 1;
		}
		$db->free_result($seperators);

		// Store seat-data in arrays
		$seat_state = array();
		$seat_ip = array();
		$seat_userid = array();
		$seats_qry = $db->qry('SELECT s.*, u.*, c.name AS clan, c.url AS clanurl, d.avatar_path FROM %prefix%seat_seats AS s
      LEFT JOIN %prefix%user AS u ON u.userid = s.userid
      LEFT JOIN %prefix%usersettings AS d ON d.userid = u.userid
      LEFT JOIN %prefix%clan AS c ON u.clanid = c.clanid
      WHERE blockid = %int%', $blockid);
		if (!$db->num_rows() == 0) {
#			for ($x = 0; $x <= $block['cols']; $x++) for ($y = 0; $y <= $block['rows']; $y++) $seat_state[$y][$x] = 1;
#		else {
			while ($seat_row = $db->fetch_array($seats_qry)) {
        if ($seat_row['userid']) $party_user = $db->query_first("SELECT checkin, checkout FROM {$config["tables"]["party_user"]}
          WHERE user_id = {$seat_row['userid']} AND party_id = {$party->party_id}");
          
				$seat_state[$seat_row['row']][$seat_row['col']] = $seat_row['status'];
				$seat_ip[$seat_row['row']][$seat_row['col']] = $seat_row['ip'];
				$seat_userid[$seat_row['row']][$seat_row['col']] = $seat_row['userid'];
				$seat_user_checkin[$seat_row['row']][$seat_row['col']] = $party_user['checkin'];
				$seat_user_checkout[$seat_row['row']][$seat_row['col']] = $party_user['checkout'];
				$user_info[$seat_row['row']][$seat_row['col']] = $seat_row;
			}
			$db->free_result($seats_qry);
		}

		// Get current users clanmates
		$my_clanmates = array();
		if ($auth['clanid']) {
			$clanmates = $db->query("SELECT userid FROM {$config["tables"]["user"]} WHERE clanid = '{$auth['clanid']}'");
			while ($clanmate = $db->fetch_array($clanmates)) array_push($my_clanmates, $clanmate['userid']);
			$db->free_result($clanmates);
		}

    // Has user paid?
		if ($auth['login']) $user_paid = $db->query_first("SELECT paid FROM {$config['tables']['party_user']} WHERE user_id = {$auth['userid']} AND party_id = {$party->party_id}");

		// Header-Row
		$head = array();
		for ($x = 0; $x <= $block['cols']; $x++) {
			if ($sep_rows[$x+1]) {
				$head[$x]['width'] = 28;
				$head[$x]['icon'] = "design/{$auth['design']}/images/arrows_seating_remove_sep_hor.gif";
			} else {
				$head[$x]['width'] = 14;
				$head[$x]['icon'] = "design/{$auth['design']}/images/arrows_seating_add_sep_hor.gif";
			}
		  $head[$x]['link'] = "index.php?mod=seating&action={$_GET['action']}&step=4&blockid=$blockid" . "&change_sep_row=".($x + 1);
			$head[$x]['name'] = $this->CoordinateToName($x + 1, -1, $block['orientation']);
		}
		$smarty->assign('head', $head);

		// Images
		if($mode == 2){
			$handel = opendir("ext_inc/seating_symbols/");
			while ($imagedata = readdir($handel)){
				if(!($imagedata == ".." || $imagedata == "." || is_dir($imagedata) || substr($imagedata,0,2) == "ls")){
					$imagename = substr($imagedata,0,strlen($imagedata)-4);
					$imageext = substr($imagedata,-4);
					if($imageext == ".jpg" || $imageext == "jpeg" || $imageext == ".gif" || $imageext == ".png"){
						$templ['seat']['seat_data_image'] .= "image[$imagename] = new Image();\n";
						$templ['seat']['seat_data_image'] .= "image[$imagename].src = \"ext_inc/seating_symbols/$imagedata\";\n";
					}
				}
			}
		}
		
		// Main-Table
		$templ['seat']['seat_data_array'] = '';
		$cell_nr = 0;
    $body = array();
		for ($y = 0; $y <= $block['rows']; $y++) {
			if ($sep_cols[$y+1]) {
				$body[$y]['height'] = 28;
				$body[$y]['icon'] = "design/{$auth['design']}/images/arrows_seating_remove_sep_ver.gif";
			} else {
				$body[$y]['height'] = 14;
				$body[$y]['icon'] = "design/{$auth['design']}/images/arrows_seating_add_sep_ver.gif";
			}
			$body[$y]['link'] = "index.php?mod=seating&action={$_GET['action']}&step=4&blockid=$blockid&change_sep_col=".($y + 1);

			$templ['seat']['cols'] = "";
			for ($x = 0; $x <= $block['cols']; $x++) {

				switch ($mode) {
          // Show plan				
					default:
						$templ['seat']['cell_nr'] = $cell_nr;
						
						$body[$y]['line'][$x]['title'] = $this->CoordinateToName($x + 1, $y, $block['orientation']);
						#$templ['seat']['img_title'] = $this->CoordinateToName($x + 1, $y, $block['orientation']);

						// Set seat link target
						$body[$y]['line'][$x]['link'] = '';
						if ($linktarget) $body[$y]['line'][$x]['link'] = "$linktarget&row=$y&col=$x";
						elseif ($auth['login']) {
							// If free and user has not paid-> Possibility to mark this seat
							if ($seat_state[$y][$x] == 1 and !$user_paid['paid'])
								$body[$y]['line'][$x]['link'] = "index.php?mod=seating&action=show&step=12&blockid=$blockid&row=$y&col=$x";
							// If free, or marked for another one -> Possibility to reserve this seat
							elseif ($seat_state[$y][$x] == 1 or ($seat_state[$y][$x] == 3 and $seat_userid[$y][$x] != $auth['userid']))
								$body[$y]['line'][$x]['link'] = "index.php?mod=seating&action=show&step=10&blockid=$blockid&row=$y&col=$x";
							// If assigned to me, or marked for me -> Possibility to free this seat again
							elseif (($seat_state[$y][$x] == 2 or $seat_state[$y][$x] == 3) and $seat_userid[$y][$x] == $auth['userid'])
								$body[$y]['line'][$x]['link'] = "index.php?mod=seating&action=show&step=20&blockid=$blockid&row=$y&col=$x";
							// If assigned and user is admin -> Possibility to free this seat
							elseif ($seat_state[$y][$x] == 2 and $auth['type'] > 1) {
								$body[$y]['line'][$x]['link'] = "index.php?mod=seating&action=show&step=30&blockid=$blockid&row=$y&col=$x";
              }
						}

						// Set seat image
						$body[$y]['line'][$x]['img_name'] = '';
						switch ($seat_state[$y][$x]) {
							case 0: // Empty
							break;
							case 1: // Seat free
								$body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_free.png";
							break;
							case 2: // Seat occupied
								if ($selected_user)	$userid = $selected_user;
								else $userid = $auth['userid'];
								// My Seat
								if ($seat_userid[$y][$x] == $userid) $body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_myselfe.png";
								// Clanmate
								elseif (in_array($seat_userid[$y][$x], $my_clanmates)) $body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_clanmate.png";
                // Checked out
								elseif ($seat_user_checkout[$y][$x] and $seat_user_checkout[$y][$x] != '0000-00-00 00:00:00') $body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_checked_out.png";
                // Checked in
								elseif ($seat_user_checkin[$y][$x] and $seat_user_checkin[$y][$x] != '0000-00-00 00:00:00') $body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_checked_in.png";
								// Normal occupied seat
								else $body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png";
							break;
							case 3: // Seat marked
								$body[$y]['line'][$x]['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_marked.png";
							break;
							case 7: // Seat reserved
								$body[$y]['line'][$x]['img_name'] = "ext_inc/seating_symbols/7.png";
							break;
							default: // Symbol
							  if (file_exists('ext_inc/seating_symbols/default/'. $seat_state[$y][$x] .'.png')) $body[$y]['line'][$x]['img_name'] = 'ext_inc/seating_symbols/default/'. $seat_state[$y][$x] .'.png';
							  else {
    						  $SymbolePath = 'ext_inc/seating_symbols/lsthumb_'. $seat_state[$y][$x];
    							if (file_exists($SymbolePath .'.png')) $body[$y]['line'][$x]['img_name'] = $SymbolePath .'.png';
    							elseif (file_exists($SymbolePath .'.gif')) $body[$y]['line'][$x]['img_name'] = $SymbolePath .'.gif';
    							elseif (file_exists($SymbolePath .'.jpg')) $body[$y]['line'][$x]['img_name'] = $SymbolePath .'.jpg';
    						}
							break;
						}

						$templ['seat']['cell_content'] = '';
						if ($body[$y]['line'][$x]['img_name']) {

              // Generate popup
      				if ($seat_state[$y][$x] == 2 and $seat_userid[$y][$x] == $auth['userid']) $s_state = 8;
      				elseif ($seat_state[$y][$x] == 2 and in_array($seat_userid[$y][$x], $my_clanmates)) $s_state = 9;
      				else $s_state = $seat_state[$y][$x];

              if ($seat_ip[$y][$x] == '') $seat_ip[$y][$x] = '<i>'. t('Keine zugeordnet') .'</i>';
              $body[$y]['line'][$x]['tooltip'] = '';
              switch ($s_state) {
                case "2":
                case "3":
                case "8":
                case "9":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('Benutzername') .': '. $user_info[$y][$x]['username'] . HTML_NEWLINE;
  							  if (!$cfg['sys_internet'] or $auth['type'] > 1 or ($auth['userid'] == $selected_user and $selected_user != false))
                    $body[$y]['line'][$x]['tooltip'] .= t('Name') .': '. $user_info[$y][$x]['firstname'] .' '. $user_info[$y][$x]['name'] . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('Clan') .': '. $user_info[$y][$x]['clan'] . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
  							  if (func::chk_img_path($user_info[$y][$x]['avatar_path']) and
                    ($cfg['seating_show_user_pics'] or !$cfg['sys_internet'] or $auth['type'] > 1 or ($auth['userid'] == $selected_user and $selected_user != false)))
    							  $body[$y]['line'][$x]['tooltip'] .= '<img src=&quot;'. $user_info[$y][$x]['avatar_path'] .'&quot; style=&quot;max-width:100%;&quot; />' . HTML_NEWLINE;
                break;
                case "1":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .' '. t('Frei'). HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
                break;
                case "7":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .' '. t('Gesperrt'). HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
                break;
                case "80":
                case "81":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('Beschreibung') .': '. t('WC') . HTML_NEWLINE;
                break;
                case "82":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('Beschreibung') .': '. t('Notausgang') . HTML_NEWLINE;
                break;
                case "83":
  							  $body[$y]['line'][$x]['tooltip'] .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
  							  $body[$y]['line'][$x]['tooltip'] .= t('Beschreibung') .': '. t('Catering') . HTML_NEWLINE;
                break;
              }
              $body[$y]['line'][$x]['tooltip'] = addslashes($body[$y]['line'][$x]['tooltip']);
						}
					break;

          // Edit plan
					case 2:
						$body[$y]['line'][$x]['symbol'] = '';
            $input_hidden = '<input type="hidden" id="cell'. ($x * 100 + $y) .'" name="cell['. ($x * 100 + $y) .']" value="'. $seat_state[$y][$x] .'" />'."\n";

						// Empty cell
						if ($seat_state[$y][$x] == 0) {
						  $body[$y]['line'][$x]['symbol'] = 'ext_inc/seating_symbols/lsthumb_100.png';
							$input_hidden_ret .= $input_hidden;

				    // Free seat cell
						} elseif ($seat_state[$y][$x] == 1) {
						  $body[$y]['line'][$x]['symbol'] = 'ext_inc/auto_images/'. $auth['design'] .'/seat/seat_free.png';
							$input_hidden_ret .= $input_hidden;

					  // Reserved seat cell
						} elseif ($seat_state[$y][$x] > 1 && $seat_state[$y][$x] < 7) {
						  $body[$y]['line'][$x]['symbol'] = 'ext_inc/auto_images/'. $auth['design'] .'/seat/seat_reserved.png';

            // Locked seat cell
						} elseif ($seat_state[$y][$x] == 7) {
						  $body[$y]['line'][$x]['symbol'] = 'ext_inc/seating_symbols/7.png';
							$input_hidden_ret .= $input_hidden;

            // Symbol cell
						} else {
						  if (file_exists('ext_inc/seating_symbols/default/'. $seat_state[$y][$x] .'.png')) $body[$y]['line'][$x]['symbol'] = 'ext_inc/seating_symbols/default/'. $seat_state[$y][$x] .'.png';
						  else {
  						  $SymbolePath = 'ext_inc/seating_symbols/'. $seat_state[$y][$x];
  							if (file_exists($SymbolePath .'.png')) $body[$y]['line'][$x]['symbol'] = $SymbolePath .'.png';
  							elseif (file_exists($SymbolePath .'.gif')) $body[$y]['line'][$x]['symbol'] = $SymbolePath .'.gif';
  							elseif (file_exists($SymbolePath .'.jpg')) $body[$y]['line'][$x]['symbol'] = $SymbolePath .'.jpg';
  						}
							$input_hidden_ret .= $input_hidden;
						}

					  $body[$y]['line'][$x]['cell_id'] = 'fcell'. ($x * 100 + $y);
					break;
					
					// IP-Input-Fields
					case 3:
						if ($seat_state[$y][$x] >= 1 and $seat_state[$y][$x] < 10) $body[$y]['line'][$x]['content'] = "<input type=\"text\" name=\"cell[". ($x * 100 + $y) ."]\" size=\"15\" maxlength=\"15\" value=\"". $seat_ip[$y][$x] ."\" />";
						else $body[$y]['line'][$x]['content'] = "&nbsp;";
					break;
				}
				$cell_nr++;
			}

			$body[$y]['desc'] = $this->CoordinateToName(-1, $y, $block['orientation']);
		}
    $smarty->assign('input_hidden', $input_hidden_ret);
    $smarty->assign('body', $body);
		$plan = $smarty->fetch('modules/seating/templates/plan.htm');

    $smarty->assign('free', t('Frei'));
    $smarty->assign('reserved', t('Besetzt'));
    $smarty->assign('clan', t('Platz eines Clanmates'));
    $smarty->assign('marked', t('Vorgemerkt'));
    $smarty->assign('locked', t('Gesperrter Platz'));
    $smarty->assign('checked_in', t('Besetzt (Eingecheckt)'));
    $smarty->assign('checked_out', t('Frei (Ausgecheckt)'));
		
		if ($selected_user) $smarty->assign('me', t('Ausgewählter User'));
		else $smarty->assign('me', t('Ihr Platz'));
				
		if ($mode == 0) $plan .= $smarty->fetch('modules/seating/templates/plan_legend.htm');
		return $plan;
	}


	// Seat management functions
  function ReserveSeatIfPaidAndOnlyOneMarkedSeat($userid) {
    global $db, $config, $party;
    
    $res = $db->query("SELECT s.seatid, s.status FROM {$config["tables"]["seat_block"]} AS b
			LEFT JOIN {$config["tables"]["seat_seats"]} AS s ON b.blockid = s.blockid
			WHERE b.party_id = {$party->party_id} AND s.userid = '$userid'
			");
		$row = $db->fetch_array($res);
		if ($db->num_rows($res) == 1 and $row['status'] == 3)
      $db->query("UPDATE {$config["tables"]["seat_seats"]} SET status = 2 WHERE seatid = {$row['seatid']}");    
  }
	
  function MarkSeatIfNotPaidAndSeatReserved($userid) {
    global $db, $config, $party;
    
    $row = $db->query_first("SELECT s.seatid, s.status FROM {$config["tables"]["seat_block"]} AS b
			LEFT JOIN {$config["tables"]["seat_seats"]} AS s ON b.blockid = s.blockid
			WHERE b.party_id = {$party->party_id} AND s.userid = '$userid' AND s.status = 2
			");
		if ($row['seatid'] > 0) $db->query("UPDATE {$config["tables"]["seat_seats"]} SET status = 3 WHERE seatid = {$row['seatid']}");    
  }
	
	function AssignSeat($userid, $blockid, $row, $col) {
		global $db, $config, $party;

		// Delete old seat, if exists
		$my_party_seat = $db->query_first("SELECT s.seatid FROM {$config["tables"]["seat_block"]} AS b
			LEFT JOIN {$config["tables"]["seat_seats"]} AS s ON b.blockid = s.blockid
			WHERE b.party_id = {$party->party_id} AND s.userid = '$userid' AND status = 2
			");
		if ($my_party_seat['seatid']) $db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1 WHERE seatid = {$my_party_seat['seatid']}");

		// Assign new seat
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = $userid, status = 2
			WHERE blockid = '$blockid' AND row = '$row' AND col = '$col'");
	}

	function MarkSeat($userid, $blockid, $row, $col) {
		global $db, $config, $party;

		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = $userid, status = 3
			WHERE blockid = '$blockid' AND row = '$row' AND col = '$col'");
	}

	function FreeSeat($blockid, $row, $col) {
		global $db, $config, $party;

		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1
			WHERE blockid = '$blockid' AND row = '$row' AND col = '$col'");
	}
}
?>
