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

  function SeatNameLink($userid, $MaxBlockLength = 0){
    global $db, $config, $party;
  
    if (!$userid) return '';
    else $row = $db->query_first("SELECT b.blockid, b.name, b.orientation, s.col, s.row FROM {$config['tables']['seat_block']} AS b
      LEFT JOIN {$config['tables']['seat_seats']} AS s ON b.blockid = s.blockid
      WHERE b.party_id = {$party->party_id} AND s.userid = $userid");
  
    if (!$row['blockid']) return '';
    else {
      $LinkText = $row['name'] .'<br />'. $this->CoordinateToName($row['col'] + 1, $row['row'], $row['orientation'], $MaxBlockLength);
  	  return "<a href=\"#\" onclick=\"javascript:var w=window.open('base.php?mod=seating&function=usrmgr&id={$row['blockid']}&userarray[]=$userid&l=1','_blank','width=596,height=638,resizable=yes');\" class=\"small\">$LinkText</a>";
  	}
  }

	function SeatOfUser($userid, $MaxBlockLength = 0, $LinkIt = 0) {
	  global $db, $config, $party; 

		$row = $db->query_first("SELECT s.row, s.col, b.blockid, b.name FROM {$config['tables']['seat_seats']} AS s
			LEFT JOIN {$config['tables']['seat_block']} AS b ON s.blockid = b.blockid
			WHERE s.userid='$userid' AND s.status = 2 AND b.party_id = ". (int)$party->party_id);

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
	    if ($LinkIt == 2) return "<a href=\"#\" onclick=\"javascript:var w=window.open('base.php?mod=seating&function=usrmgr&id=$blockid&userarray[]={$userid}&l=1','_blank','width=596,height=638,resizable=yes');\">$LinkText</a>";
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

		$func->CreateDir("ext_inc/auto_images/{$auth['design']}/seat");
		$gd->Colorize("design/{$auth['design']}/images/seat.png", $r, $g, $b, $percentage);
		$gd->PutImage("ext_inc/auto_images/{$auth['design']}/seat/$name.png", 'png');

		// Create Image for selection
		$gd->MergeImages("ext_inc/auto_images/{$auth['design']}/seat/{$name}.png", "design/{$auth['design']}/images/seat_onclick.png", "ext_inc/auto_images/{$auth['design']}/seat/{$name}_onclick.png");
	}

	function DrawPlan($blockid, $mode, $linktarget = '', $selected_user = false) {
		global $db, $config, $dsp, $templ, $auth, $gd, $lang, $cfg, $party;
		// $mode:
		// 0 = Normal display mode
		// 1 = With seperators
		// 2 = With checkboxes
		// 3 = Admin mode

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

		$templ['seat']['text_tl'] = $block['text_tl'];
		$templ['seat']['text_tc'] = $block['text_tc'];
		$templ['seat']['text_tr'] = $block['text_tr'];
		$templ['seat']['text_lt'] = $this->FlipVertical($block['text_lt']);
		$templ['seat']['text_lc'] = $this->FlipVertical($block['text_lc']);
		$templ['seat']['text_lb'] = $this->FlipVertical($block['text_lb']);
		$templ['seat']['text_rt'] = $this->FlipVertical($block['text_rt']);
		$templ['seat']['text_rc'] = $this->FlipVertical($block['text_rc']);
		$templ['seat']['text_rb'] = $this->FlipVertical($block['text_rb']);
		$templ['seat']['text_bl'] = $block['text_bl'];
		$templ['seat']['text_bc'] = $block['text_bc'];
		$templ['seat']['text_br'] = $block['text_br'];
		$templ['seat']['row_count'] = $block['rows'] + 1;
		$templ['seat']['col_count'] = $block['cols'] + 1;

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
		$seats_qry = $db->query("SELECT * FROM {$config["tables"]["seat_seats"]} AS s
      LEFT JOIN {$config["tables"]["user"]} AS u ON u.userid = s.userid
      WHERE blockid = '$blockid'");
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
		if($auth['clan'] != ""){
			$clanmates = $db->query("SELECT userid FROM {$config["tables"]["user"]} WHERE clan = '{$auth['clan']}'");
			while ($clanmate = $db->fetch_array($clanmates)) array_push($my_clanmates, $clanmate['userid']);
			$db->free_result($clanmates);
		}
		
		// Header-Row
		$templ['seat']['plan_sep_row_head_cols'] = '';
		$templ['seat']['plan_sep_row_desc_x'] = '';
		if ($mode == 1) $templ['seat']['plan_sep_row_desc_x'] = '<td class="content"></td>';
		for ($x = 0; $x <= $block['cols']; $x++) {
			$templ['seat']['cur_col'] = $x;
			$templ['working_link'] = "index.php?mod=seating&action={$_GET['action']}&step=4&blockid=$blockid";
			$templ['sep_row_click_link'] = $templ['working_link'] . "&change_sep_row=".($x + 1);
			if ($sep_rows[$x+1]) {
				$templ['sep_width'] = 28;
				$templ['sep_ver'] = "design/{$auth['design']}/images/arrows_seating_remove_sep_hor.gif";
			} else {
				$templ['sep_width'] = 14;
				$templ['sep_ver'] = "design/{$auth['design']}/images/arrows_seating_add_sep_hor.gif";
			}

			if ($mode == 1) {
				if ($x < $block['cols']) $templ['seat']['plan_sep_row_head_cols'] .= $dsp->FetchModTpl('seating', 'plan_sep_row_head_cols');
				else {
					$templ['seat']['cell_content'] = '';
					$templ['seat']['plan_sep_row_head_cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
				}
			}elseif ($mode == 2){
				if ($x < $block['cols']) $templ['seat']['plan_sep_row_head_cols'] .= "<td style=\"font-size:8px; table-layout:fixed; width:{$templ['sep_width']}px;\"><img src=\"ext_inc/seating_symbols/100.png\" name=\"leerpic{$block['cols']}\" /></td>";
			}
			$templ['seat']['col_nr'] = $this->CoordinateToName($x + 1, -1, $block['orientation']);
#			($block['orientation'])? $templ['seat']['col_nr'] = chr(65 + $x) : $templ['seat']['col_nr'] = ($x + 1);
			$templ['seat']['plan_sep_row_desc_x'] .= $dsp->FetchModTpl('seating', 'plan_row_head');
		}
		$templ['seat']['plan_extra_top'] .= $dsp->FetchModTpl('seating', 'plan_sep_row_head');

		if($mode == 2){
			// Images
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
		for ($y = 0; $y <= $block['rows']; $y++) {
			$templ['seat']['cur_row'] = $y;
			$templ['working_link'] = "index.php?mod=seating&action={$_GET['action']}&step=4&blockid=$blockid";
			$templ['sep_row_click_link'] = $templ['working_link'] . "&change_sep_col=".($y + 1);
			if ($sep_cols[$y+1]) {
				$templ['sep_height'] = 28;
				$templ['sep_hor'] = "design/{$auth['design']}/images/arrows_seating_remove_sep_ver.gif";
			} else {
				$templ['sep_height'] = 14;
				$templ['sep_hor'] = "design/{$auth['design']}/images/arrows_seating_add_sep_ver.gif";
			}

			$templ['seat']['cols'] = "";
			for ($x = 0; $x <= $block['cols']; $x++) {

				// Generate JavaScript
				if ($seat_state[$y][$x] == 2 and $seat_userid[$y][$x] == $auth['userid']) $s_state = 8;
				elseif ($seat_state[$y][$x] == 2 and in_array($seat_userid[$y][$x], $my_clanmates)) $s_state = 9;
				else $s_state = $seat_state[$y][$x];
        
				if(!$cfg['sys_internet'] OR $auth['type'] > 1 OR ($auth['userid'] == $selected_user && $selected_user != false)){
					$templ['seat']['seat_data_array'] .= "seat['x$cell_nr'] = '{$user_info[$y][$x]['username']},{$user_info[$y][$x]['firstname']},{$user_info[$y][$x]['name']},{$user_info[$y][$x]['clan']},". $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .",0,{$s_state},{$seat_ip[$y][$x]},{$user_info[$y][$x]['clanurl']}';\r\n";
				} else {
					$templ['seat']['seat_data_array'] .= "seat['x$cell_nr'] = '{$user_info[$y][$x]['username']},,,{$user_info[$y][$x]['clan']},". $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .",0,{$s_state},{$seat_ip[$y][$x]},{$user_info[$y][$x]['clanurl']}';\r\n";
				}
				

										
				switch ($mode) {
					default:
						$templ['seat']['cell_nr'] = $cell_nr;
						$templ['seat']['img_title'] = $this->CoordinateToName($x + 1, $y, $block['orientation']);

						// Set seat link target
						$templ['seat']['link_href'] = '';
						if ($linktarget) $templ['seat']['link_href'] = "$linktarget&row=$y&col=$x";
						elseif ($auth['login']) {
							// If free, or marked for another one -> Possibility to reserve this seat
							if ($seat_state[$y][$x] == 1 or ($seat_state[$y][$x] == 3 and $seat_userid[$y][$x] != $auth['userid']))
								$templ['seat']['link_href'] = "index.php?mod=seating&action=show&step=10&blockid=$blockid&row=$y&col=$x";
							// If assigned to me, or marked for me -> Possibility to free this seat again
							elseif (($seat_state[$y][$x] == 2 or $seat_state[$y][$x] == 3) and $seat_userid[$y][$x] == $auth['userid'])
								$templ['seat']['link_href'] = "index.php?mod=seating&action=show&step=20&blockid=$blockid&row=$y&col=$x";
							// If free and user is admin -> Possibility to free this seat 
							elseif ($seat_state[$y][$x] == 2 and $auth['type'] > 1) {
								$templ['seat']['link_href'] = "index.php?mod=seating&action=show&step=30&blockid=$blockid&row=$y&col=$x";
              }
						}

						// Set seat image
						$templ['seat']['img_name'] = '';
						switch ($seat_state[$y][$x]) {
							case 0: // Empty
							break;
							case 1: // Seat free
								$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_free.png";
							break;
							case 2: // Seat occupied
								if ($selected_user)	$userid = $selected_user;
								else $userid = $auth['userid'];
								// My Seat
								if ($seat_userid[$y][$x] == $userid) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_myselfe.png";
								// Clanmate
								elseif (in_array($seat_userid[$y][$x], $my_clanmates)) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_clanmate.png";
                // Checked out
								elseif ($seat_user_checkout[$y][$x]) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_checked_out.png";
                // Checked in
								elseif ($seat_user_checkin[$y][$x]) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_checked_in.png";
								// Normal occupied seat
								else $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png";
							break;
							case 3: // Seat marked
								$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_marked.png";
							break;
// Geändert von HSE: 3 Zeilen hinzugefügt
							case 7: // Seat reserved
								$templ['seat']['img_name'] = "ext_inc/seating_symbols/7.png";
							break;
							default: // Symbol
								if (file_exists("ext_inc/seating_symbols/". $seat_state[$y][$x] .".png")) {
									$templ['seat']['img_name'] = "ext_inc/seating_symbols/lsthumb_". $seat_state[$y][$x] .".png";
								} elseif (file_exists("ext_inc/seating_symbols/lsthumb_". $seat_state[$y][$x] .".gif")) {
									$templ['seat']['img_name'] = "ext_inc/seating_symbols/lsthumb_". $seat_state[$y][$x] .".gif";
								} else {
									$templ['seat']['img_name'] = "ext_inc/seating_symbols/lsthumb_". $seat_state[$y][$x] .".jpg";
								}
							break;
						}

						$templ['seat']['cell_content'] = '';
						if ($templ['seat']['img_name']) {
							if ($templ['seat']['link_href']) {
								$templ['seat']['link_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
								$templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_link');
							} else $templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
						}
						$templ['seat']['cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
					break;

					case 1:
						$templ['seat']['cell_content'] = "<img src=\"ext_inc/auto_images/{$auth['design']}/seat/seat_free.png\" />";
						$templ['seat']['cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
					break;

					case 2:
						$templ['seat']['cell_content'] = '';
						if ($seat_state[$y][$x] == 0){
							$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/100.png); background-repeat:no-repeat; \" id=\"fcell". ($x * 100 + $y) ."\"></td>";
							$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
#						elseif ($seat_state[$y][$x] == $_POST['icon'])
#							$templ['seat']['cell_content'] = "<input type=\"checkbox\" name=\"cell[". ($x * 100 + $y) ."]\" value=\"". ($x * 100 + $y) ."\"checked />";
						}else {
							if ($seat_state[$y][$x] > 1 && $seat_state[$y][$x] < 7) {
							  $templ['seat']['cell_content'] = "<td style=\"background:url(ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png); height:14px; width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
//								$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png";
//								$templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
							} else {
						    // Free seat
								if ($seat_state[$y][$x] == 1) {
//									$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_free.png";
									$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/auto_images/{$auth['design']}/seat/seat_free.png); width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
                // Locked seat
								} elseif ($seat_state[$y][$x] == 7) {
									$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/7.png); width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
								} else{
									if(file_exists("ext_inc/seating_symbols/". $seat_state[$y][$x] .".png")){
										$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/{$seat_state[$y][$x]}.png); width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									}elseif (file_exists("ext_inc/seating_symbols/". $seat_state[$y][$x] .".gif")){	
										$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/{$seat_state[$y][$x]}.gif); width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									}else{
										$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/{$seat_state[$y][$x]}.jpg); width:14px; background-repeat:no-repeat;\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									}
									$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
								}
								//$templ['seat']['img_name'] = "ext_inc/seating_symbols/lsthumb_". $seat_state[$y][$x] .".png";

//								$templ['seat']['link_href'] = "index.php?mod=seating&action=edit&step=6&blockid={$_GET['blockid']}&deleteid=". ($x * 100 + $y);
//								$templ['seat']['link_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
//								$templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_link');
							}
						}
						$templ['seat']['cols'] .= $templ['seat']['cell_content'];
						$templ['seat']['seat_data_array'] = "";
						// $templ['seat']['cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
					break;
					
					// IP-Input-Fields
					case 3:
						if ($seat_state[$y][$x] >= 1 and $seat_state[$y][$x] < 10) $templ['seat']['cell_content'] = "<input type=\"text\" name=\"cell[". ($x * 100 + $y) ."]\" size=\"15\" maxlength=\"15\" value=\"". $seat_ip[$y][$x] ."\" />";
						else $templ['seat']['cell_content'] = "&nbsp;";
						$templ['seat']['cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
					break;
				}
				$cell_nr++;
			}

			if ($mode == 1) $templ['seat']['plan_extra_left'] = $dsp->FetchModTpl('seating', 'plan_sep_left_head');
			$templ['seat']['plan_sep_desc_y'] = $this->CoordinateToName(-1, $y, $block['orientation']);
#			($block['orientation'])? $templ['seat']['plan_sep_desc_y'] = $y : $templ['seat']['plan_sep_desc_y'] = chr(65 + $y);
			$templ['seat']['rows'] .= $dsp->FetchModTpl('seating', 'plan_row');
		}

		$plan = $dsp->FetchModTpl('seating', 'plan');
		$templ['seating']['legend']['free']		= $lang['seating']['free'];
		$templ['seating']['legend']['reserved']	= $lang['seating']['reserved'];
		$templ['seating']['legend']['clan']		= $lang['seating']['clan_seat'];
		$templ['seating']['legend']['marked']	= $lang['seating']['marked'];
		$templ['seating']['legend']['locked']	= $lang['seating']['locked'];
		$templ['seating']['legend']['checked_in']	= $lang['seating']['checked_in'];
		$templ['seating']['legend']['checked_out']	= $lang['seating']['checked_out'];    		
		
		if ($selected_user) $templ['seating']['legend']['me'] = $lang['seating']['selected'];
		else	$templ['seating']['legend']['me']			 = $lang['seating']['me'];
				
		if ($mode == 0) $plan .= $dsp->FetchModTpl('seating', 'plan_legend');
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
}
?>