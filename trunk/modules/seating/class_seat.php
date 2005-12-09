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

	function CoordinateToName($x, $y, $orientation) {
		$out = '';
		if ($orientation) {
			if ($y > -1)				$out = ($y + 1);
			if ($x > -1 and $y > -1)	$out .= '-';
			if ($x > -1)				$out .= str_replace('@', '', chr(64 + floor($x / 26))) . chr(65 + $x % 26);
		} else {
			if ($y > -1)				$out = str_replace('@', '', chr(64 + floor($y / 26))) . chr(65 + $y % 26);
			if ($x > -1 and $y > -1)	$out .= '-';
			if ($x > -1)				$out .= ($x + 1);
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

	function DrawPlan($blockid, $mode, $linktarget = '') {
		global $db, $config, $dsp, $templ, $auth, $gd;
		// $mode:
		// 0 = Normal display mode
		// 1 = With seperators
		// 2 = With checkboxes
		// 3 = Admin mode

		// Create Images
		$this->CreateSeatImage('seat_free', 0, 200, 0, 50);
		$this->CreateSeatImage('seat_reserved', 200, 0, 0, 50);
		$this->CreateSeatImage('seat_marked', 200, 200, 0, 50);
		$this->CreateSeatImage('seat_myselfe', 0, 0, 200, 50);
		$this->CreateSeatImage('seat_clanmate', 0, 100, 200, 50);

		// Get Block data (side descriptions + number of rows + cols)
		$block = $db->query_first("SELECT * FROM {$config["tables"]["seat_block"]} WHERE blockid = '$blockid'");
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
		$seats_qry = $db->query("SELECT * FROM {$config["tables"]["seat_seats"]} AS s LEFT JOIN {$config["tables"]["user"]} AS u ON u.userid = s.userid WHERE blockid = '$blockid'");
		if (!$db->num_rows() == 0) {
#			for ($x = 0; $x <= $block['cols']; $x++) for ($y = 0; $y <= $block['rows']; $y++) $seat_state[$y][$x] = 1;
#		else {
			while ($seat_row = $db->fetch_array($seats_qry)) {
				$seat_state[$seat_row['row']][$seat_row['col']] = $seat_row['status'];
				$seat_ip[$seat_row['row']][$seat_row['col']] = $seat_row['ip'];
				$seat_userid[$seat_row['row']][$seat_row['col']] = $seat_row['userid'];
				$user_info[$seat_row['row']][$seat_row['col']] = $seat_row;
			}
			$db->free_result($seats_qry);
		}

		// Get current users clanmates
		$my_clanmates = array();
		$clanmates = $db->query("SELECT userid FROM {$config["tables"]["user"]} WHERE clan = '{$auth['clan']}'");
		while ($clanmate = $db->fetch_array($clanmates)) array_push($my_clanmates, $clanmate['userid']);
		$db->free_result($clanmates);

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
			}
			$templ['seat']['col_nr'] = $this->CoordinateToName($x, -1, $block['orientation']);
#			($block['orientation'])? $templ['seat']['col_nr'] = chr(65 + $x) : $templ['seat']['col_nr'] = ($x + 1);
			$templ['seat']['plan_sep_row_desc_x'] .= $dsp->FetchModTpl('seating', 'plan_row_head');
		}
		$templ['seat']['plan_extra_top'] .= $dsp->FetchModTpl('seating', 'plan_sep_row_head');

		// Images
		$handel = opendir("ext_inc/seating_symbols/");
		
		while ($imagedata = readdir($handel)){
			if(!($imagedata == ".." || $imagedata == "." || is_dir($imagedata) || substr($imagedata,0,2) == "ls")){
				$imagedata = substr($imagedata,0,strlen($imagedata)-4);
				$templ['seat']['seat_data_image'] .= "image[$imagedata] = new Image();\n";
				$templ['seat']['seat_data_image'] .= "image[$imagedata].src = \"ext_inc/seating_symbols/$imagedata.png\";\n";
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
				$templ['sep_height'] = 0;
				$templ['sep_hor'] = "design/{$auth['design']}/images/arrows_seating_add_sep_ver.gif";
			}

			$templ['seat']['cols'] = "";
			for ($x = 0; $x <= $block['cols']; $x++) {

				// Generate JavaScript
				if ($seat_state[$y][$x] == 2 and $seat_userid[$y][$x] == $auth['userid']) $s_state = 8;
				elseif ($seat_state[$y][$x] == 2 and in_array($seat_userid[$y][$x], $my_clanmates)) $s_state = 9;
				else $s_state = $seat_state[$y][$x];
//				$user_info = $db->query_first("SELECT username, firstname, name, clan, clanurl FROM {$config["tables"]["user"]} WHERE userid = '{$seat_userid[$y][$x]}'");
//				$templ['seat']['seat_data_array'] .= "seat['x$cell_nr'] = '{$user_info['username']},{$user_info['firstname']},{$user_info['name']},{$user_info['clan']},{$block['name']},". $this->CoordinateToName($x, $y, $block['orientation']). ",0,{$s_state},{$seat_ip[$y][$x]},{$user_info['clanurl']}';\r\n";
				$templ['seat']['seat_data_array'] .= "seat['x$cell_nr'] = '{$user_info[$y][$x]['username']},{$user_info[$y][$x]['firstname']},{$user_info[$y][$x]['name']},{$user_info['clan']},{$block['name']},". $this->CoordinateToName($x, $y, $block['orientation']). ",0,{$s_state},{$seat_ip[$y][$x]},{$user_info[$y][$x]['clanurl']}';\r\n";

										
				switch ($mode) {
					default:
						$templ['seat']['cell_nr'] = $cell_nr;
						$templ['seat']['img_title'] = $this->CoordinateToName($x, $y, $block['orientation']);

						// Set seat link target
						$templ['seat']['link_href'] = '';
						if ($linktarget) $templ['seat']['link_href'] = "$linktarget&row=$y&col=$x";
						elseif ($auth['login']) {
							// If free, or marked for another one
							if ($seat_state[$y][$x] == 1 or ($seat_state[$y][$x] == 3 and $seat_userid[$y][$x] != $auth['userid']))
								$templ['seat']['link_href'] = "index.php?mod=seating&action=show&step=10&blockid=$blockid&row=$y&col=$x";
							// If assigned to me, or marked for me
							elseif (($seat_state[$y][$x] == 2 or $seat_state[$y][$x] == 3) and $seat_userid[$y][$x] == $auth['userid'])
								$templ['seat']['link_href'] = "index.php?mod=seating&action=show&step=20&blockid=$blockid&row=$y&col=$x";
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
								// My Seat
								if ($seat_userid[$y][$x] == $auth['userid']) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_myselfe.png";
								// Clanmate
								elseif (in_array($seat_userid[$y][$x], $my_clanmates)) $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_clanmate.png";
								// Other ones seat
								else $templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png";
							break;
							case 3: // Seat marked
								$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_marked.png";
							break;
							default: // Symbol
								$templ['seat']['img_name'] = "ext_inc/seating_symbols/". $seat_state[$y][$x] .".png";
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
							$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/100.png);height=14px;width=14px\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
							$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
#						elseif ($seat_state[$y][$x] == $_POST['icon'])
#							$templ['seat']['cell_content'] = "<input type=\"checkbox\" name=\"cell[". ($x * 100 + $y) ."]\" value=\"". ($x * 100 + $y) ."\"checked />";
						}else {
							if ($seat_state[$y][$x] > 1 && $seat_state[$y][$x] < 10) {
								$templ['seat']['cell_content'] = "<td style=\"background:url(ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png);height=14px;width=14px\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
//								$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_reserved.png";
//								$templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
							} else {
								if ($seat_state[$y][$x] == 1) {
//									$templ['seat']['img_name'] = "ext_inc/auto_images/{$auth['design']}/seat/seat_free.png";
									$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/auto_images/{$auth['design']}/seat/seat_free.png);height=14px;width=14px\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
								} else{
									$templ['seat']['cell_content'] = "<td onClick=\"changeImage(this); return false\" onMousemove=\"changeImage(this); return false\" style=\"background:url(ext_inc/seating_symbols/{$seat_state[$y][$x]}.png);height=14px;width=14px\" id=\"fcell". ($x * 100 + $y) ."\"></td>";
									$templ['seat']['input_hidden'] .= "<input type=\"hidden\" id=\"cell". ($x * 100 + $y) ."\" name=\"cell[" . ($x * 100 + $y) . "]\" value=\"" . $seat_state[$y][$x] . "\"/>\n";
								}
								//$templ['seat']['img_name'] = "ext_inc/seating_symbols/". $seat_state[$y][$x] .".png";

//								$templ['seat']['link_href'] = "index.php?mod=seating&action=edit&step=6&blockid={$_GET['blockid']}&deleteid=". ($x * 100 + $y);
//								$templ['seat']['link_content'] = $dsp->FetchModTpl('seating', 'plan_cell_img');
//								$templ['seat']['cell_content'] = $dsp->FetchModTpl('seating', 'plan_cell_link');
							}
						}
						$templ['seat']['cols'] .= $templ['seat']['cell_content'];
						$templ['seat']['seat_data_array'] = "";
						// $templ['seat']['cols'] .= $dsp->FetchModTpl('seating', 'plan_cell');
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
		if ($mode == 0) $plan .= $dsp->FetchModTpl('seating', 'plan_legend');
		return $plan;
	}


	// Seat management functions

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