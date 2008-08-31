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
		global $db, $config, $dsp, $templ, $auth, $gd, $lang, $cfg, $party, $smarty, $framework;
		// $mode:
		// 0 = Normal display mode
		// 1 = With seperators
		// 2 = With checkboxes
		// 3 = Admin mode

    $smarty->assign('default_design', $auth['design']);

		// Get Block data (side descriptions + number of rows + cols)
		$block = $db->query_first("SELECT * FROM {$config["tables"]["seat_block"]} WHERE blockid = '{$blockid}'");

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
    if ($mode == 3) {
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

    } else {
      if ($mode == 2) {
        $XStartPlan = 50;
        $YStartPlan = 150;
        $XStartPlanFrame = 0;
        $YStartPlanFrame = 105;
      } else {
        $XStartPlan = 50;
        $YStartPlan = 50;
        $XStartPlanFrame = 0;
        $YStartPlanFrame = 5;
      }
      $SVGWidth = $XStartPlanFrame + 14 * $block['cols'] + 100;
      $SVGHeight = $YStartPlanFrame + 14 * $block['rows'] + 100;
      if ($mode == 2 and $SVGWidth < 600) $SVGWidth = 600;
      ($SVGWidth < 250)? $SVGImgWidth = 250 : $SVGImgWidth = $SVGWidth;
  		$smarty->assign('SVGWidth', $SVGImgWidth);
  		$smarty->assign('SVGHeight', $SVGHeight + 50);

      $HiddenFields = array();
      for ($x = 0; $x <= $block['cols']; $x++) for ($y = 0; $y <= $block['rows']; $y++) {
        $k = $x * 100 + $y;
        $HiddenFields[$k] = $seat_state[$y][$x];
      }
  		$smarty->assign('HiddenFields', $HiddenFields);
  		
  		// Main-Table
  		$framework->main_header_metatags .= '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
  		$framework->main_header_jscode .= '<script type="text/javascript" src="ext_scripts/SVG2VMLv1_1.js"></script>
      <script type="text/javascript" src="ext_scripts/ls_svg2vml.js"></script>
      <script type="text/javascript" src="seating.js"></script>';

  		$framework->main_header_jscode .= '<script>
  			function go() {
  				vectorModel = new VectorModel();
  				container = document.getElementById("SeatPlanSVGContet");
  				mySvg = vectorModel.createElement("svg");
  				container.appendChild(mySvg);
  				mySvg.setAttribute("version", "1.1");
  			  myG = vectorModel.createElement("g");
  				mySvg.appendChild(myG);
      ';
  
      // Icon selection in mode 2
      if ($mode == 2){

        $framework->main_header_jscode .= "CreateText('Auswahl:', 0, 14);\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(19, 0, 14, 'javascript:UpdateCurrentDrawingSymbol(\"19\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(14, 14, 14, 'javascript:UpdateCurrentDrawingSymbol(\"14\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(18, 28, 14, 'javascript:UpdateCurrentDrawingSymbol(\"18\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(15, 0, 28, 'javascript:UpdateCurrentDrawingSymbol(\"15\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(16, 14, 28, 'javascript:UpdateCurrentDrawingSymbol(\"16\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(13, 28, 28, 'javascript:UpdateCurrentDrawingSymbol(\"13\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(20, 0, 42, 'javascript:UpdateCurrentDrawingSymbol(\"20\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(12, 14, 42, 'javascript:UpdateCurrentDrawingSymbol(\"12\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(17, 28, 42, 'javascript:UpdateCurrentDrawingSymbol(\"17\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(103, 42, 14, 'javascript:UpdateCurrentDrawingSymbol(\"103\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(107, 56, 14, 'javascript:UpdateCurrentDrawingSymbol(\"107\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(104, 70, 14, 'javascript:UpdateCurrentDrawingSymbol(\"104\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(108, 42, 28, 'javascript:UpdateCurrentDrawingSymbol(\"108\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(111, 56, 28, 'javascript:UpdateCurrentDrawingSymbol(\"111\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(109, 70, 28, 'javascript:UpdateCurrentDrawingSymbol(\"109\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(105, 42, 42, 'javascript:UpdateCurrentDrawingSymbol(\"105\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(110, 56, 42, 'javascript:UpdateCurrentDrawingSymbol(\"110\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(106, 70, 42, 'javascript:UpdateCurrentDrawingSymbol(\"106\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(22, 84, 14, 'javascript:UpdateCurrentDrawingSymbol(\"22\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(21, 98, 14, 'javascript:UpdateCurrentDrawingSymbol(\"21\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(23, 84, 28, 'javascript:UpdateCurrentDrawingSymbol(\"108\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(24, 98, 28, 'javascript:UpdateCurrentDrawingSymbol(\"24\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(10, 84, 42, 'javascript:UpdateCurrentDrawingSymbol(\"10\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(11, 98, 42, 'javascript:UpdateCurrentDrawingSymbol(\"11\")', '');\n";
    
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(148, 112, 14, 'javascript:UpdateCurrentDrawingSymbol(\"148\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(149, 126, 14, 'javascript:UpdateCurrentDrawingSymbol(\"149\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(150, 112, 28, 'javascript:UpdateCurrentDrawingSymbol(\"150\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(151, 126, 28, 'javascript:UpdateCurrentDrawingSymbol(\"151\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(102, 112, 42, 'javascript:UpdateCurrentDrawingSymbol(\"102\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(101, 126, 42, 'javascript:UpdateCurrentDrawingSymbol(\"101\")', '');\n";
        
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(136, 140, 14, 'javascript:UpdateCurrentDrawingSymbol(\"136\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(137, 154, 14, 'javascript:UpdateCurrentDrawingSymbol(\"137\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(138, 140, 28, 'javascript:UpdateCurrentDrawingSymbol(\"138\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(139, 154, 28, 'javascript:UpdateCurrentDrawingSymbol(\"139\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(1, 140, 42, 'javascript:UpdateCurrentDrawingSymbol(\"1\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(100, 154, 42, 'javascript:UpdateCurrentDrawingSymbol(\"100\")', '');\n";
        
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(140, 168, 14, 'javascript:UpdateCurrentDrawingSymbol(\"140\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(141, 182, 14, 'javascript:UpdateCurrentDrawingSymbol(\"141\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(142, 168, 28, 'javascript:UpdateCurrentDrawingSymbol(\"142\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(143, 182, 28, 'javascript:UpdateCurrentDrawingSymbol(\"143\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(7, 168, 42, 'javascript:UpdateCurrentDrawingSymbol(\"7\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(153, 182, 42, 'javascript:UpdateCurrentDrawingSymbol(\"153\")', '');\n";
        
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(144, 196, 14, 'javascript:UpdateCurrentDrawingSymbol(\"144\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(145, 210, 14, 'javascript:UpdateCurrentDrawingSymbol(\"145\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(146, 196, 28, 'javascript:UpdateCurrentDrawingSymbol(\"146\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(147, 210, 28, 'javascript:UpdateCurrentDrawingSymbol(\"147\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(154, 196, 42, 'javascript:UpdateCurrentDrawingSymbol(\"154\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(156, 210, 42, 'javascript:UpdateCurrentDrawingSymbol(\"156\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(112, 224, 14, 'javascript:UpdateCurrentDrawingSymbol(\"112\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(113, 238, 14, 'javascript:UpdateCurrentDrawingSymbol(\"113\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(114, 224, 28, 'javascript:UpdateCurrentDrawingSymbol(\"114\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(115, 238, 28, 'javascript:UpdateCurrentDrawingSymbol(\"115\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(132, 224, 42, 'javascript:UpdateCurrentDrawingSymbol(\"132\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(152, 238, 42, 'javascript:UpdateCurrentDrawingSymbol(\"152\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(133, 252, 14, 'javascript:UpdateCurrentDrawingSymbol(\"133\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(134, 252, 28, 'javascript:UpdateCurrentDrawingSymbol(\"134\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(135, 252, 42, 'javascript:UpdateCurrentDrawingSymbol(\"135\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(224, 266, 14, 'javascript:UpdateCurrentDrawingSymbol(\"224\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(226, 280, 14, 'javascript:UpdateCurrentDrawingSymbol(\"226\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(227, 294, 14, 'javascript:UpdateCurrentDrawingSymbol(\"227\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(223, 266, 28, 'javascript:UpdateCurrentDrawingSymbol(\"223\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(222, 280, 28, 'javascript:UpdateCurrentDrawingSymbol(\"222\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(225, 294, 28, 'javascript:UpdateCurrentDrawingSymbol(\"225\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(100, 266, 42, 'javascript:UpdateCurrentDrawingSymbol(\"100\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(228, 280, 42, 'javascript:UpdateCurrentDrawingSymbol(\"228\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(100, 294, 42, 'javascript:UpdateCurrentDrawingSymbol(\"100\")', '');\n";

        $framework->main_header_jscode .= "DrawClearSeatingSymbol(206, 308, 14, 'javascript:UpdateCurrentDrawingSymbol(\"206\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(212, 322, 14, 'javascript:UpdateCurrentDrawingSymbol(\"212\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(213, 336, 14, 'javascript:UpdateCurrentDrawingSymbol(\"213\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(207, 308, 28, 'javascript:UpdateCurrentDrawingSymbol(\"207\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(201, 322, 28, 'javascript:UpdateCurrentDrawingSymbol(\"201\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(216, 336, 28, 'javascript:UpdateCurrentDrawingSymbol(\"216\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(208, 308, 42, 'javascript:UpdateCurrentDrawingSymbol(\"208\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(220, 322, 42, 'javascript:UpdateCurrentDrawingSymbol(\"220\")', '');\n";
        $framework->main_header_jscode .= "DrawClearSeatingSymbol(209, 336, 42, 'javascript:UpdateCurrentDrawingSymbol(\"209\")', '');\n";

        $x = 0;
        $y = 56;
        for ($i = 300; $i <= 371; $i++) {
  	      $framework->main_header_jscode .= "DrawClearSeatingSymbol($i, $x, $y, 'javascript:UpdateCurrentDrawingSymbol(\"$i\")', 'Test');\n";
  
          $x += 14;
          if ($x > 580) {
            $x = 0;
            $y += 14;
          }
        }
      }
  
      $framework->main_header_jscode .= "CreateRect(4, $YStartPlanFrame, ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_tl'] ."', ". (($SVGWidth / 6 * 1) - strlen($block['text_tl']) * 4) .", ". ($YStartPlanFrame + 15) .", '');\n";
      $framework->main_header_jscode .= "CreateRect(". (($SVGWidth / 3) + 4) .", $YStartPlanFrame, ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_tc'] ."', ". (($SVGWidth / 6 * 3) - strlen($block['text_tc']) * 4) .", ". ($YStartPlanFrame + 15) .", '');\n";
      $framework->main_header_jscode .= "CreateRect(". ((($SVGWidth / 3) * 2) + 4) .", $YStartPlanFrame, ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_tr'] ."', ". (($SVGWidth / 6 * 5) - strlen($block['text_tr']) * 4) .", ". ($YStartPlanFrame + 15) .", '');\n";
  
      $framework->main_header_jscode .= "CreateRect(4, ". ($YStartPlanFrame + 27) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_lt']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_lt'], $i, 1) ."', 12, ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 1 + ($YStartPlanFrame + 27)) - strlen($block['text_lt']) * 5 + 10 * $i) .", '');\n";
  		}
      $framework->main_header_jscode .= "CreateRect(4, ". (($SVGHeight - $YStartPlanFrame - 70) / 3 + 4 + ($YStartPlanFrame + 27)) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_lc']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_lc'], $i, 1) ."', 12, ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 3 + ($YStartPlanFrame + 27)) - strlen($block['text_lc']) * 5 + 10 * $i) .", '');\n";
  		}
      $framework->main_header_jscode .= "CreateRect(4, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) * 2 + 4 + ($YStartPlanFrame + 27)) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_lb']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_lb'], $i, 1) ."', 12, ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 5 + ($YStartPlanFrame + 27)) - strlen($block['text_lb']) * 5 + 10 * $i) .", '');\n";
  		}
  
      $framework->main_header_jscode .= "CreateRect(". ($SVGWidth - 25) .", ". ($YStartPlanFrame + 27) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_rt']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_rt'], $i, 1) ."', ". ($SVGWidth - 17) .", ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 1 + ($YStartPlanFrame + 27)) - strlen($block['text_rt']) * 5 + 10 * $i) .", '');\n";
  		}
      $framework->main_header_jscode .= "CreateRect(". ($SVGWidth - 25) .", ". (($SVGHeight - $YStartPlanFrame - 70) / 3 + 4 + ($YStartPlanFrame + 27)) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_rc']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_rc'], $i, 1) ."', ". ($SVGWidth - 17) .", ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 3 + ($YStartPlanFrame + 27)) - strlen($block['text_rc']) * 5 + 10 * $i) .", '');\n";
  		}
      $framework->main_header_jscode .= "CreateRect(". ($SVGWidth - 25) .", ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) * 2 + 4 + ($YStartPlanFrame + 27)) .", 20, ". ((($SVGHeight - $YStartPlanFrame - 70) / 3) - 8) .", '#d6d6d6 ', '#9d9d9d', '');\n";
  		for ($i = 0; $i <= strlen($block['text_rb']); $i++) {
  		  $framework->main_header_jscode .= "CreateText('". substr($block['text_rb'], $i, 1) ."', ". ($SVGWidth - 17) .", ". ((($SVGHeight - $YStartPlanFrame - 70) / 6 * 5 + ($YStartPlanFrame + 27)) - strlen($block['text_rb']) * 5 + 10 * $i) .", '');\n";
  		}
  
      $framework->main_header_jscode .= "CreateRect(4, ". ($SVGHeight - 35) .", ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_bl'] ."', ". (($SVGWidth / 6 * 1) - strlen($block['text_bl']) * 4) .", ". ($SVGHeight - 20) .", '');\n";
      $framework->main_header_jscode .= "CreateRect(". (($SVGWidth / 3) + 4) .", ". ($SVGHeight - 35) .", ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_bc'] ."', ". (($SVGWidth / 6 * 3) - strlen($block['text_bc']) * 4) .", ". ($SVGHeight - 20) .", '');\n";
      $framework->main_header_jscode .= "CreateRect(". ((($SVGWidth / 3) * 2) + 4) .", ". ($SVGHeight - 35) .", ". (($SVGWidth / 3) - 8) .", 20, '#d6d6d6 ', '#9d9d9d', '');\n";
      $framework->main_header_jscode .= "CreateText('". $block['text_br'] ."', ". (($SVGWidth / 6 * 5) - strlen($block['text_br']) * 4) .", ". ($SVGHeight - 20) .", '');\n";
    }

		$cell_nr = 0;
    $body = array();
    $sepY = 0;
		for ($y = 0; $y <= $block['rows']; $y++) {

			if ($sep_cols[$y]) $sepY++;
			$YOffset = $y * 14 + $sepY * 7 + $YStartPlan;

			$body[$y]['desc'] = $this->CoordinateToName(-1, $y, $block['orientation']);
			if ($mode != 3) $framework->main_header_jscode .= "CreateText('". $this->CoordinateToName(-1, $y, $block['orientation']) ."', ". ($XStartPlan - 10) .", ". ($YOffset + 9) .", '');\n";			
			if ($mode == 1) {
			  if ($sep_cols[$y+1]) $framework->main_header_jscode .= "CreateSmallText('^', ". ($XStartPlan - 20) .", ". ($YOffset + 9 + 7) .", 'index.php?mod=seating&action=edit&step=4&blockid=". $_GET['blockid'] ."&change_sep_col=". ($y + 1) ."');\n";
			  else  $framework->main_header_jscode .= "CreateSmallText('v', ". ($XStartPlan - 20) .", ". ($YOffset + 9 + 7) .", 'index.php?mod=seating&action=edit&step=4&blockid=". $_GET['blockid'] ."&change_sep_col=". ($y + 1) ."');\n";
      }

			if ($sep_cols[$y+1]) {
				$body[$y]['height'] = 28;
				$body[$y]['icon'] = "design/{$auth['design']}/images/arrows_seating_remove_sep_ver.gif";
			} else {
				$body[$y]['height'] = 14;
				$body[$y]['icon'] = "design/{$auth['design']}/images/arrows_seating_add_sep_ver.gif";
			}
			$body[$y]['link'] = "index.php?mod=seating&action={$_GET['action']}&step=4&blockid=$blockid&change_sep_col=".($y + 1);

			$templ['seat']['cols'] = "";
      $sepX = 0;
			for ($x = 0; $x <= $block['cols']; $x++) {

  			if ($sep_rows[$x]) $sepX++;
				$XOffset = $x * 14 + $sepX * 7 + $XStartPlan;

				switch ($mode) {
          // Show plan				
					default:
						$templ['seat']['cell_nr'] = $cell_nr;
						
						if ($y == 1) $framework->main_header_jscode .= "CreateText('". $this->CoordinateToName($x + 1, -1, $block['orientation']) ."', ". ($XOffset - 2) .", ". ($YStartPlan - 6) .", '');\n";
						if ($y == 1 and $mode == 1) {
						  if ($sep_rows[$x+1]) $framework->main_header_jscode .= "CreateSmallText('<', ". ($XOffset - 2 + 9) .", ". ($YStartPlan - 16) .", 'index.php?mod=seating&action=edit&step=4&blockid=". $_GET['blockid'] ."&change_sep_row=". ($x + 1) ."');\n";
						  else  $framework->main_header_jscode .= "CreateSmallText('>', ". ($XOffset - 2 + 9) .", ". ($YStartPlan - 16) .", 'index.php?mod=seating&action=edit&step=4&blockid=". $_GET['blockid'] ."&change_sep_row=". ($x + 1) ."');\n";
            }

						// Set seat link target
						$link = '';
						switch ($mode) {
						  default:
    						if ($linktarget) $link = "$linktarget&row=$y&col=$x";
    						elseif ($auth['login']) {
    							// If free and user has not paid-> Possibility to mark this seat
    							if ($seat_state[$y][$x] == 1 and !$user_paid['paid'])
    								$link = "index.php?mod=seating&action=show&step=12&blockid=$blockid&row=$y&col=$x";
    							// If free, or marked for another one -> Possibility to reserve this seat
    							elseif ($seat_state[$y][$x] == 1 or ($seat_state[$y][$x] == 3 and $seat_userid[$y][$x] != $auth['userid']))
    								$link = "index.php?mod=seating&action=show&step=10&blockid=$blockid&row=$y&col=$x";
    							// If assigned to me, or marked for me -> Possibility to free this seat again
    							elseif (($seat_state[$y][$x] == 2 or $seat_state[$y][$x] == 3) and $seat_userid[$y][$x] == $auth['userid'])
    								$link = "index.php?mod=seating&action=show&step=20&blockid=$blockid&row=$y&col=$x";
    							// If assigned and user is admin -> Possibility to free this seat
    							elseif ($seat_state[$y][$x] == 2 and $auth['type'] > 1) {
    								$link = "index.php?mod=seating&action=show&step=30&blockid=$blockid&row=$y&col=$x";
                  }
    						}
						  break;
						  case 1: break;
						  case 2:
						    // Seat only changeble, if noone sits there
                if ($seat_state[$y][$x] > 1 and $seat_state[$y][$x] < 7) $link = "javascript:alert(\"". t('Es können nur freie Sitzplätze geändert werden') ."\")";
                else $link = "javascript:ChangeSeatingPlan(\"cell". ($x * 100 + $y) ."\", $XOffset, $YOffset)";
              break;
						}

            // Generate popup
    				if ($seat_state[$y][$x] == 2 and $seat_userid[$y][$x] == $auth['userid']) $s_state = 8;
    				elseif ($seat_state[$y][$x] == 2 and in_array($seat_userid[$y][$x], $my_clanmates)) $s_state = 9;
    				else $s_state = $seat_state[$y][$x];

            if ($seat_ip[$y][$x] == '') $seat_ip[$y][$x] = '<i>'. t('Keine zugeordnet') .'</i>';
            $tooltip = '';
            switch ($s_state) {
              case "2":
              case "3":
              case "8":
              case "9":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
							  $tooltip .= t('Benutzername') .': '. $user_info[$y][$x]['username'] . HTML_NEWLINE;
							  if (!$cfg['sys_internet'] or $auth['type'] > 1 or ($auth['userid'] == $selected_user and $selected_user != false))
                  $tooltip .= t('Name') .': '. $user_info[$y][$x]['firstname'] .' '. $user_info[$y][$x]['name'] . HTML_NEWLINE;
							  $tooltip .= t('Clan') .': '. $user_info[$y][$x]['clan'] . HTML_NEWLINE;
							  $tooltip .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
							  if (func::chk_img_path($user_info[$y][$x]['avatar_path']) and
                  ($cfg['seating_show_user_pics'] or !$cfg['sys_internet'] or $auth['type'] > 1 or ($auth['userid'] == $selected_user and $selected_user != false)))
  							  $tooltip .= '<img src=&quot;'. $user_info[$y][$x]['avatar_path'] .'&quot; style=&quot;max-width:100%;&quot; />' . HTML_NEWLINE;
              break;
              case "1":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .' '. t('Frei'). HTML_NEWLINE;
							  $tooltip .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
              break;
              case "7":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) .' '. t('Gesperrt'). HTML_NEWLINE;
							  $tooltip .= t('IP') .': '. $seat_ip[$y][$x] . HTML_NEWLINE;
              break;
              case "80":
              case "81":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
							  $tooltip .= t('Beschreibung') .': '. t('WC') . HTML_NEWLINE;
              break;
              case "82":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
							  $tooltip .= t('Beschreibung') .': '. t('Notausgang') . HTML_NEWLINE;
              break;
              case "83":
							  $tooltip .= t('Block') .': '. $this->CoordinateToBlockAndName($x + 1, $y, $blockid) . HTML_NEWLINE;
							  $tooltip .= t('Beschreibung') .': '. t('Catering') . HTML_NEWLINE;
              break;
            }
            $tooltip = addslashes($tooltip);

						// Set seat image
						$body[$y]['line'][$x]['img_name'] = '';

					  switch ($seat_state[$y][$x]) {
					    case 0:
					    case 100:
					      if ($mode == 1) $framework->main_header_jscode .= "DrawSeatingSymbol(0, $XOffset, $YOffset, '$link', '$tooltip');\n";
					      elseif ($mode == 2) $framework->main_header_jscode .= "ClearArea($XOffset, $YOffset, 14, 14, '$link');\n";
					    break;
					    case 2:
								if ($selected_user)	$userid = $selected_user;
								else $userid = $auth['userid'];
								
								if ($seat_userid[$y][$x] == $userid) $seat_state[$y][$x] = 4; // My Seat
								elseif (in_array($seat_userid[$y][$x], $my_clanmates)) $seat_state[$y][$x] = 5; // Clanmate
								elseif ($seat_user_checkout[$y][$x] and $seat_user_checkout[$y][$x] != '0000-00-00 00:00:00') $seat_state[$y][$x] = 6; // Checked out
								elseif ($seat_user_checkin[$y][$x] and $seat_user_checkin[$y][$x] != '0000-00-00 00:00:00') $seat_state[$y][$x] = 8; // Checked in
								// else = 2 -> Normal occupied seat

              // No Break!
					    default:
					      if ($mode == 2) $framework->main_header_jscode .= "ClearArea($XOffset, $YOffset, 14, 14, '$link');\n";
					      $framework->main_header_jscode .= "DrawSeatingSymbol({$seat_state[$y][$x]}, $XOffset, $YOffset, '$link', '$tooltip');\n";
					    break;
					      
					  }

						$templ['seat']['cell_content'] = '';
          break;

					// IP-Input-Fields
					case 3:
						if ($seat_state[$y][$x] >= 1 and $seat_state[$y][$x] < 10) $body[$y]['line'][$x]['content'] = "<input type=\"text\" name=\"cell[". ($x * 100 + $y) ."]\" size=\"15\" maxlength=\"15\" value=\"". $seat_ip[$y][$x] ."\" />";
						else $body[$y]['line'][$x]['content'] = "&nbsp;";
					break;
				}
				$cell_nr++;
			}
		}

    if ($mode == 3) $smarty->assign('body', $body);
		$plan = $smarty->fetch('modules/seating/templates/plan.htm');

    if ($mode == 0) {    
      $framework->main_header_jscode .= "DrawSeatingSymbol(1, 0, ". ($YOffset + 50) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Frei') ."', ". 14 .", ". ($YOffset + 58) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(2, 0, ". ($YOffset + 64) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Besetzt') ."', ". 14 .", ". ($YOffset + 72) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(4, 0, ". ($YOffset + 78) .", '', '');\n";
      if ($selected_user) $framework->main_header_jscode .= "CreateText('". t('Auswahl') ."', ". 14 .", ". ($YOffset + 86) .", '');\n";
      else $framework->main_header_jscode .= "CreateText('". t('Ihr Platz') ."', ". 14 .", ". ($YOffset + 86) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(3, 0, ". ($YOffset + 92) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Vorgemerkt') ."', ". 14 .", ". ($YOffset + 100) .", '');\n";

      $framework->main_header_jscode .= "DrawSeatingSymbol(6, 100, ". ($YOffset + 50) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Frei (Ausgecheckt)') ."', ". 114 .", ". ($YOffset + 58) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(8, 100, ". ($YOffset + 64) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Besetzt (Eingecheckt)') ."', ". 114 .", ". ($YOffset + 72) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(5, 100, ". ($YOffset + 78) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Clanmate') ."', ". 114 .", ". ($YOffset + 86) .", '');\n";
      $framework->main_header_jscode .= "DrawSeatingSymbol(7, 100, ". ($YOffset + 92) .", '', '');\n";
      $framework->main_header_jscode .= "CreateText('". t('Gesperrt') ."', ". 114 .", ". ($YOffset + 100) .", '');\n";
  	}

    if ($mode != 3) $framework->main_header_jscode .= '
			}
		</script>
    ';
    
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
