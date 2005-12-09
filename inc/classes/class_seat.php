<?php
/*************************************************************************
*
*	Lansuite-Professional - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.1
*	File Version:		2.1
*	Filename: 		class_seat.php
*	Module: 		seat
*	Main editor: 		raphael@one-network.org
*	Last change: 		04.04.2003 16:22
*	Description: 		seat functions
*	Remarks:
*
**************************************************************************/

class seat{

var $block_design;

var $blockid;
var $seat_name;
var $seat_rows;
var $seat_cols;
var $seat_orientation;
var $seat_u18;
var $seat_text_tl;
var $seat_text_tc;
var $seat_text_tr;
var $seat_text_lt;
var $seat_text_lc;
var $seat_text_lb;
var $seat_text_rt;
var $seat_text_rc;
var $seat_text_rb;
var $seat_text_bl;
var $seat_text_bc;
var $seat_text_br;

var $seat_ip 	 = array();
var $seat_ip_new = array();

var $field_seatid 	= array();
var $field_status   	= array();
var $field_jsstatus   	= array();
var $field_userid   	= array();
var $field_username   	= array();
var $field_firstname   	= array();
var $field_name  = array();
var $field_clan  = array();
var $field_seat  = array();
var $field_row   = array();
var $field_col   = array();

var $highl_userarray 	  = array();
var $seat_sep_orientation = array();

var $seat_jsstr   = array();
var $seat_js   	  = array();
var $seat_this_jsstr;
var $seat_linkstr = array();
var $seat_linkurl = array();
var $seat_picurl  = array();
var $seat_picstr  = array();

var $field_js_seatid 	= array();
var $field_userid_index = array();
var $field_seatid_index = array();




	function generate_block($blockid, $seat_mode) {

		global $func;

		$this->blockid = $blockid;

		for($ir = -1; $ir <= $this->seat_rows; $ir++) {

			$templ['seat']['general']['case']['control']['row_seats'] = "";



 			for($ic = -1; $ic <= $this->seat_cols; $ic++) {

 	/**
 	 * Separator
 	 **/
 			if($this->seat_sep_row[$ir] == TRUE AND $ic == -1) {

 					$i = sizeof($this->seat_sep_col)+$this->seat_cols+2;


				$templ['seat']['general']['case']['control']['col_sep'] = $i;

				eval("\$templ['seat']['general']['case']['control']['row_seats'] .= \"". $func->gettemplate("seating_col_sep")."\";");
    			} //if

    			if($this->seat_sep_col[$ic] == TRUE AND $ir == -1)
 				 {
 				 	$i = sizeof($this->seat_sep_row)+$this->seat_rows+3;


				$templ['seat']['general']['case']['control']['row_sep'] = $i;

				eval("\$templ['seat']['general']['case']['control']['row_seats'] .= \"". $func->gettemplate("seating_row_sep")."\";");

    			} //if

    	/**
 	 * END Separator
 	 **/

 				if($ir != -1 AND $ic != -1) {


 					switch($seat_mode) {

 						case 4:
 							$this->seat_linkurl[1] = sprintf($this->seat_linkstr[1], $this->field_js_seatid[$this->field_seatid[$ic][$ir]], $this->field_seatid[$ic][$ir]);
 							$this->seat_linkurl[2] = sprintf($this->seat_linkstr[2], $this->field_js_seatid[$this->field_seatid[$ic][$ir]], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[3] = sprintf($this->seat_linkstr[3], $this->field_js_seatid[$this->field_seatid[$ic][$ir]], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[4] = sprintf($this->seat_linkstr[4], $this->field_js_seatid[$this->field_seatid[$ic][$ir]], $this->field_userid[$ic][$ir]);

 							$this->seat_picurl[0]   = sprintf($this->seat_picstr[0], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_picurl[1]   = sprintf($this->seat_picstr[1], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_picurl[2]   = sprintf($this->seat_picstr[2], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_picurl[3]   = sprintf($this->seat_picstr[3], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_picurl[4]   = sprintf($this->seat_picstr[4], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_js[1] 	= sprintf($this->seat_jsstr[1], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_js[2]    	= sprintf($this->seat_jsstr[2], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_js[3]    	= sprintf($this->seat_jsstr[3], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);
 							$this->seat_js[4]    	= sprintf($this->seat_jsstr[4], $this->field_js_seatid[$this->field_seatid[$ic][$ir]]);

 				 		break;
 				 		case 1:
 							$this->seat_picurl[21]  = sprintf($this->seat_picstr[21], $this->field_seatid[$ic][$ir]);
 							$this->seat_picurl[31]  = sprintf($this->seat_picstr[31], $this->field_seatid[$ic][$ir]);
 							$this->seat_picurl[41]  = sprintf($this->seat_picstr[41], $this->field_seatid[$ic][$ir]);
 							$this->seat_linkurl[21] = sprintf($this->seat_linkstr[21], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[31] = sprintf($this->seat_linkstr[31], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[41] = sprintf($this->seat_linkstr[41], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);

 				 		default:
 				 			$this->seat_linkurl[1]  = sprintf($this->seat_linkstr[1], $this->field_seatid[$ic][$ir]);
 							$this->seat_linkurl[2]  = sprintf($this->seat_linkstr[2], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[3]  = sprintf($this->seat_linkstr[3], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);
 							$this->seat_linkurl[4]  = sprintf($this->seat_linkstr[4], $this->field_seatid[$ic][$ir], $this->field_userid[$ic][$ir]);
 							$this->seat_picurl[0]   = sprintf($this->seat_picstr[0], "");
 							$this->seat_picurl[1]   = sprintf($this->seat_picstr[1], "");
 							$this->seat_picurl[2]   = sprintf($this->seat_picstr[2], "");
 							$this->seat_picurl[3]   = sprintf($this->seat_picstr[3], "");
 							$this->seat_picurl[4]   = sprintf($this->seat_picstr[4], "");

 				 		break;
 				 	} // switch - seat_mode


 				/**
 	 			* FILL FIELDS WITH DATA
 	 			**/

 					switch($seat_mode) {

 						default: $seat_fieldstring = $this->generate_fieldstring($ic, $ir); 		break;
 						case 1:  $seat_fieldstring = $this->generate_fieldstring_highl($ic, $ir); 	break;
						case 2:	 $seat_fieldstring = $this->generate_fieldstring_create($ic, $ir); 	break;
						case 3:	 $seat_fieldstring = $this->generate_fieldstring_create_sep($ic, $ir); 	break;
						case 5:	 $seat_fieldstring = $this->generate_fieldstring_change_ip($ic, $ir); 	break;

					} // switch - seat_mode




 				} // if

 				/**
				 * SET INDEX FIELDS
				 **/


 				$templ_seat_index = $this->fetch_index($this->seat_orientation, $ic, $ir);




 				switch($seat_mode){
 					default: // show seating
 					 	$templ_seat_index['col'] = "<td width=\"14\" height=\"14\" valign=\"middle\" align=\"center\">".$templ_seat_index['col']."</td>\n";
 						$templ_seat_index['row'] = "<td width=\"14\" height=\"14\" valign=\"middle\" align=\"center\">".$templ_seat_index['row']."</td>\n";
 						$templ_seating_field_width_height = "20";
 						break;

 					case 2: // create seating
 						$templ_seat_index['col'] = "<td width=\"20\" height=\"20\" valign=\"middle\" align=\"center\" onclick=\"AllselectV($ic);\">".$templ_seat_index['col']."</td>\n";
 						$templ_seat_index['row'] = "<td width=\"20\" height=\"20\" valign=\"middle\" align=\"center\" onclick=\"AllselectH($ir);\">".$templ_seat_index['row']."</td>\n";
 						$templ_seating_field_width_height = "20";
 						break;

 					case 3: // create seating sep
 						$templ_seat_index['col'] = "<td width=\"20\" height=\"20\" valign=\"middle\" align=\"center\">".$templ_seat_index['col']."</td>\n";
 						$templ_seat_index['row'] = "<td width=\"20\" height=\"20\" valign=\"middle\" align=\"center\">".$templ_seat_index['row']."</td>\n";
 						$templ_seating_field_width_height = "20";
 						break;
				} // switch ( $seatmode



 				$td_string_first	= '<img src="design/%s/images/arrows_seating_transparency.gif" width="%s" height="%s"/>';
 				$td_string_field	= '<td width="%s" height="%s" %s>%s</td>';

 				if($ir == -1 AND $ic == -1) $templ['seat']['general']['case']['control']['row_seats'] .= sprintf($td_string_field, $templ_seating_field_width_height, $templ_seating_field_width_height, "", sprintf($tdstring_first, $_SESSION["auth"]["design"], $templ_seating_field_width_height, $templ_seating_field_width_height));
 		/* left */	if($ir != -1 AND $ic == -1) $templ['seat']['general']['case']['control']['row_seats'] .= $templ_seat_index['row'];
 		/* top */	if($ir == -1 AND $ic != -1) $templ['seat']['general']['case']['control']['row_seats'] .= $templ_seat_index['col'];


 				if($ir != -1 AND $ic != -1) $templ['seat']['general']['case']['control']['row_seats'] .= sprintf($td_string_field, $templ_seating_field_width_height, $templ_seating_field_width_height, $this->seat_this_jsstr, $seat_fieldstring)."\n";


 			unset($this->seat_this_jsstr);




 			} // for cols



    			eval("\$templ['seat']['general']['case']['control']['plan'] .= \"". $func->gettemplate("seating_rows")."\";");

    				$u18 =($this->seat_u18 == TRUE) ? ' <img src="design/'.$_SESSION["auth"]["design"].'/images/u18.gif" alt="'.$lang['class_seat']['u18_block'].'" title="'.$lang['class_seat']['u18_block'].'" border=0/>' : "";

    				$templ['seat']['general']['case']['info']['blockname'] = $this->seat_name . $u18;

		} // for rows

		switch($seat_mode){
			default:

				$templ['seat']['general']['case']['info']['caption_tl'] = $this->seat_text_tl;
				$templ['seat']['general']['case']['info']['caption_tc'] = $this->seat_text_tc;
				$templ['seat']['general']['case']['info']['caption_tr'] = $this->seat_text_tr;
   				$templ['seat']['general']['case']['info']['caption_lt'] = $this->seat_text_lt;
   				$templ['seat']['general']['case']['info']['caption_lc'] = $this->seat_text_lc;
   				$templ['seat']['general']['case']['info']['caption_lb'] = $this->seat_text_lb;
   				$templ['seat']['general']['case']['info']['caption_rt'] = $this->seat_text_rt;
   				$templ['seat']['general']['case']['info']['caption_rc'] = $this->seat_text_rc;
   				$templ['seat']['general']['case']['info']['caption_rb'] = $this->seat_text_rb;
   				$templ['seat']['general']['case']['info']['caption_bl'] = $this->seat_text_bl;
   				$templ['seat']['general']['case']['info']['caption_bc'] = $this->seat_text_bc;
   				$templ['seat']['general']['case']['info']['caption_br'] = $this->seat_text_br;


				$templ['seat']['general']['case']['control']['legend'] = $GLOBALS['templ']['seat']['general']['case']['control']['legend'];



				eval("\$this->block_design .= \"". $func->gettemplate("seating_plans")."\";");
			break;
			case 2:
			case 3:
			case 5:
				eval("\$this->block_design .= \"". $func->gettemplate("seating_plans_create")."\";");
			break;
		} // switch

	} // function generate_block





	function fetch_index($orientation, $ic, $ir) {


				if($orientation == 0) { 		// 0 -> Zahlen oben / Buchstaben links

 					if($ir > 25) { 	$templ_seat_index_number = 65+$ir-26;
 					 		$templ_seat_index['row'] = chr($templ_seat_index_number).chr($templ_seat_index_number);
 					}
 					else { 		$templ_seat_index_number = 65+$ir;
 							$templ_seat_index['row'] = chr($templ_seat_index_number);  // left
 					 }


 					$templ_seat_index['col'] = $ic+1; // top

 				} else { 				// 1 -> Zahlen links / Buchstaben oben

 					if($ic > 25){ 		$templ_seat_index_number = 65+$ic-26;
 					 			$templ_seat_index['col'] = chr($templ_seat_index_number).chr($templ_seat_index_number);
 					}
 					else { 			$templ_seat_index_number = 65+$ic;
 								$templ_seat_index['col'] = chr($templ_seat_index_number); // top
 					}

 					$templ_seat_index['row'] = $ir+1; // left
 				} // if seat_orientation

			return $templ_seat_index;

	} // function - fetch_index()




	function display_seat_index($orientation, $ic, $ir) {

		$ind = $this->fetch_index($orientation, $ic, $ir);

		if($orientation == 0) 	$display = $ind['row']."-".$ind['col'];
		else 			$display = $ind['col']."-".$ind['row'];

	 	return $display;

	} // function - display_seat_index()




	function generate_block_userdata($blockid) {

		global $db,$func, $cfg,$auth;

		/* ORIENTATION */

		$query = $db->query("SELECT orientation, value FROM {$GLOBALS["config"]["tables"]["seat_sep"]} WHERE blockid='$blockid'");

		while($row = $db->fetch_array($query)) {


			if($row["orientation"] == 1) {
				$this->seat_sep_col[$row["value"]] = 1;
			} else {
				$this->seat_sep_row[$row["value"]] = 1;
			} // if

		} // while



		/* SEATING DATA */

		$row = $db->query_first("SELECT name,
						  cols,
						  rows,
						  orientation,
						  /*remark,*/
						  u18,
						  text_tl,
						  text_tc,
						  text_tr,
						  text_lt,
						  text_lc,
						  text_lb,
						  text_rt,
						  text_rc,
						  text_rb,
						  text_bl,
						  text_bc,
						  text_br  FROM {$GLOBALS["config"]["tables"]["seat_block"]} WHERE blockid='$blockid'");

		$this->seat_rows = $row["rows"];
		$this->seat_cols = $row["cols"];
		$this->seat_orientation = $row["orientation"];

		$this->seat_u18		= $row["u18"];


		$this->seat_name    = $func->db2text($row["name"]);

		$this->seat_text_tl =($row["text_tl"]) ? $func->db2text($row["text_tl"]) : "&nbsp;";
		$this->seat_text_tc =($row["text_tc"]) ? $func->db2text($row["text_tc"]) : "&nbsp;";
		$this->seat_text_tr =($row["text_tr"]) ? $func->db2text($row["text_tr"]) : "&nbsp;";
   		$this->seat_text_lt =($row["text_lt"]) ? $func->db2text($row["text_lt"]) : "&nbsp;";
   		$this->seat_text_lc =($row["text_lc"]) ? $func->db2text($row["text_lc"]) : "&nbsp;";
   		$this->seat_text_lb =($row["text_lb"]) ? $func->db2text($row["text_lb"]) : "&nbsp;";
   		$this->seat_text_rt =($row["text_rt"]) ? $func->db2text($row["text_rt"]) : "&nbsp;";
   		$this->seat_text_rc =($row["text_rc"]) ? $func->db2text($row["text_rc"]) : "&nbsp;";
   		$this->seat_text_rb =($row["text_rb"]) ? $func->db2text($row["text_rb"]) : "&nbsp;";
   		$this->seat_text_bl =($row["text_bl"]) ? $func->db2text($row["text_bl"]) : "&nbsp;";
   		$this->seat_text_bc =($row["text_bc"]) ? $func->db2text($row["text_bc"]) : "&nbsp;";
   		$this->seat_text_br =($row["text_br"]) ? $func->db2text($row["text_br"]) : "&nbsp;";






   		/* CLANARRAY */
   				$row_user_clan = $db->query_first("SELECT clan FROM {$GLOBALS["config"]["tables"]["user"]} WHERE userid='{$GLOBALS[auth][userid]}'");

				$user_clan = $row_user_clan[clan];


				$clanarray = array();

				if($user_clan != "") {

					$query_clan = $db->query("SELECT userid FROM {$GLOBALS["config"]["tables"]["user"]} WHERE UPPER(LTRIM(RTRIM(clan)))='$user_clan'");

   					 		while($row_clanarray = $db->fetch_array($query_clan)) {
     								$clanarray[] = $row_clanarray[userid];
      							} // while row_clanarray
      				} // if($user_clan == "")

   		/* SEAT DATA */

   		$query = $db->query("SELECT 	A.seatid,
   						B.firstname,
   						B.name,
   						B.clan,
   						B.username,
   						A.col,
   						A.row,
   						A.status,
   						A.ip,
   						A.userid AS userid FROM
   		{$GLOBALS["config"]["tables"]["seat_seats"]} 	AS A 	LEFT OUTER JOIN
   		{$GLOBALS["config"]["tables"]["user"]} 		AS B 	ON A.userid=B.userid WHERE A.blockid = '$blockid' order by row,col");

   		while($row = $db->fetch_array($query)) {



   				$this->field_seatid_index[$row["seatid"]] = $row["userid"];
   				$this->field_userid_index[$row["userid"]] = $row["userid"];

				if($auth["type"] >= 2 OR $cfg["sys_internet"] == 0) {
					$this->field_firstname[$row["userid"]] = $row["firstname"];
					$this->field_name[$row["userid"]] = $row["name"];
				} else {
					$this->field_firstname[$row["userid"]] = $lang['class_seat']['not_shown'];
					$this->field_name[$row["userid"]] = $lang['class_seat']['not_shown'];
				}
   				$this->field_clan[$row["userid"]]      = $row["clan"];
				$this->field_username[$row["userid"]]  = $row["username"];

   				$this->seat_ip[$row["seatid"]]	       = $row["ip"];

   				if($this->seat_orientation == 0){ 	// 0 -> Zahlen oben / Buchstaben links
   					$this->field_seat[$row["seatid"]] = chr(65+$row["row"]).($row["col"]+1);
   				}
   				else {					// 1 -> Zahlen links / Buchstaben oben
   					$this->field_seat[$row["seatid"]] = chr(65+$row["col"]).($row["row"]+1);
   				}
   				$this->field_row[$row["seatid"]] = $row["row"];
   				$this->field_col[$row["seatid"]] = $row["col"];

   			$this->field_userid[$row["col"]][$row["row"]] = $row["userid"];



   		/*YOU*/	  if($row["userid"] == $GLOBALS['auth']['userid']){ 	$this->field_status[$row["col"]][$row["row"]] = 3;
   										$this->field_jsstatus[$row["seatid"]] = 3; }
   		/*CLAN*/  elseif(in_array($row["userid"],$clanarray)) {		$this->field_status[$row["col"]][$row["row"]] = 4;
   										$this->field_jsstatus[$row["seatid"]] = 4; }
   		/*OTHER*/ else {						$this->field_status[$row["col"]][$row["row"]] = $row["status"];
   										$this->field_jsstatus[$row["seatid"]] = $row["status"];	}


   			$this->field_seatid[$row["col"]][$row["row"]] = $row["seatid"];

   		} // while
   		$db->free_result($query);

	 }

	function generate_fieldstring($ic, $ir) {

					$display = $this->display_seat_index($this->seat_orientation, $ic, $ir);

					$alt = 'alt="'.$display.'"';
					$alt .=' title="'.$display.'"';

					$seat_fieldstringarr[0] = '<img src="%s" width="14" height="14" border="0"/>';

					for($i=1; $i<=4; $i++){
						if($this->seat_linkstr[$i] != ""){ $seat_fieldstringarr[$i] = '<a href="%s"><img src="%s" width="14" height="14" border="0"  '.$alt.'/></a>';	}
 						else			 	 { $seat_fieldstringarr[$i] = '%s<img src="%s" width="14" height="14" border="0"  '.$alt.'/>';			}
 					}// for


			switch($this->field_status[$ic][$ir]) {

 					default:		$seat_fieldstring = sprintf($seat_fieldstringarr[0], $this->seat_picurl[0]);				break;
 					case 1:	/* free     */ 	$seat_fieldstring = sprintf($seat_fieldstringarr[1], $this->seat_linkurl[1], $this->seat_picurl[1]);
 								$this->seat_this_jsstr = $this->seat_js[1];							break;
 					case 2:	/* occupied */	$seat_fieldstring = sprintf($seat_fieldstringarr[2], $this->seat_linkurl[2], $this->seat_picurl[2]);
 								$this->seat_this_jsstr = $this->seat_js[2];							break;
 					case 3:	/* you      */	$seat_fieldstring = sprintf($seat_fieldstringarr[3], $this->seat_linkurl[3], $this->seat_picurl[3]);
 								$this->seat_this_jsstr = $this->seat_js[3];							break;
 					case 4:	/* clanmate */	$seat_fieldstring = sprintf($seat_fieldstringarr[4], $this->seat_linkurl[4], $this->seat_picurl[4]);
 								$this->seat_this_jsstr = $this->seat_js[4];							break;


 			} // switch
 			//

		return $seat_fieldstring;

	} // function generate_fieldstring()







	function generate_fieldstring_highl($ic, $ir) {

					$display = $this->display_seat_index($this->seat_orientation, $ic, $ir);

					$alt = 'alt="'.$display.'"';
					$alt .=' title="'.$display.'"';

					$seat_fieldstringarr[0] = '<img src="%s" width="14" height="14" border="0"/>';

					for($i=1; $i<=4; $i++){
						if($this->seat_linkstr[$i] != "") 	{ $seat_fieldstringarr[$i] = '<a href="%s"><img src="%s" width="%d" height="%d" border="0" '.$alt.'/></a>';}
 						else			 		{ $seat_fieldstringarr[$i] = '%s<img src="%s" width="%d" height="%d" border="0" '.$alt.'/>';		 }
 					}// for
 					for($i=2; $i<=4; $i++){
						if($this->seat_linkstr[$i."1"] != "") 	{ $seat_fieldstringarr[$i."1"] = '<a href="%s"><img src="%s" width="%d" height="%d" border="0" '.$alt.'/></a>';}
 						else			 		{ $seat_fieldstringarr[$i."1"] = '%s<img src="%s" width="%d" height="%d" border="0" '.$alt.'/>';		 }
 					}// for

			switch($this->field_status[$ic][$ir]) {


 					default:		$seat_fieldstring = sprintf($seat_fieldstringarr[0], $this->seat_picurl[0]);	break;
 					case 1:	/* free     */ 	$seat_fieldstring = sprintf($seat_fieldstringarr[1], $this->seat_linkurl[1], $this->seat_picurl[1], 14, 14); 	break;


 					case 2:		//occupied
 						if($this->highl_userarray[$ic][$ir] == TRUE) {	$seat_fieldstring = sprintf($seat_fieldstringarr[21], $this->seat_linkurl[21], $this->seat_picurl[21], 14, 14); }
 						else {						$seat_fieldstring = sprintf($seat_fieldstringarr[2], $this->seat_linkurl[2], $this->seat_picurl[2], 14, 14);   }
 			 			break;

 					case 3:		// you
 				 		if($this->highl_userarray[$ic][$ir] == TRUE) {	$seat_fieldstring = sprintf($seat_fieldstringarr[31], $this->seat_linkurl[31], $this->seat_picurl[31], 14, 14); }
 						else {						$seat_fieldstring = sprintf($seat_fieldstringarr[3], $this->seat_linkurl[3], $this->seat_picurl[3], 14, 14);   }
						break;

 					case 4:		//clanmate
 						if($this->highl_userarray[$ic][$ir] == TRUE) {	$seat_fieldstring = sprintf($seat_fieldstringarr[41], $this->seat_linkurl[41], $this->seat_picurl[41], 14, 14); }
 						else {						$seat_fieldstring = sprintf($seat_fieldstringarr[4], $this->seat_linkurl[4], $this->seat_picurl[4], 14, 14);   }
 			 			break;

 			} // switch

		return $seat_fieldstring;

	} // function generate_fieldstring()


	function generate_fieldstring_create_sep($ic, $ir) {

 					$seat_fieldstring = '<img src="%s" width="20" height="20" border="0"/>';
 					$seat_fieldstring = sprintf($seat_fieldstring, $this->seat_picurl[0]);


		return $seat_fieldstring;

	} // function generate_fieldstring_create_sep($ic, $ir)


	function generate_fieldstring_create($ic, $ir) {

 			$seat_index_string = "$ic.$ir";

 			if(
 			/* if seat is checked, mark */ $this->field_status[$ic][$ir] >= 1
 			OR
 			/* mark all seats on startup */ isset($this->blockid) == FALSE
 			)  {

 				if($this->field_status[$ic][$ir] >= 2){ $seat_fieldstring = '<center><img src="%s" width="14" height="14" border="0"/><input type="hidden"/></center>'; 		$seat_fieldstring = sprintf($seat_fieldstring, $this->seat_picurl[0]);					}
 				else {					$seat_fieldstring = '<input type="checkbox" name="seating_status[%s]" value="%s" %s/>';  $seat_fieldstring = sprintf($seat_fieldstring, $seat_index_string, $seat_index_string, "checked");	}

 			} else {
 					$seat_fieldstring = '<input type="checkbox" name="seating_status[%s]" value="%s" %s/>';
 					$seat_fieldstring = sprintf($seat_fieldstring, $seat_index_string, $seat_index_string, "");
 			} // else

		return $seat_fieldstring;

	} // function generate_fieldstring_create()


	function generate_fieldstring_change_ip($ic, $ir) {

		switch($this->field_status[$ic][$ir]) {

 					default:
 							$seat_fieldstring = '';

 					break;
 					case 1:	   	// free
 					case 2:
 					case 3:
 					case 4:

 							$seat_index_string = "$ic.$ir";


							$seatid = $this->field_seatid[$ic][$ir];

							$ip_mask = $this->seat_ip[$seatid];
							$ip_new =  $this->seat_ip_new[$seatid];

							if($ip_new != "") $ip_mask = $ip_new;

 				 			$seat_fieldstring = '<input type="text" name="seating_ip[%s]" value="%s" %s/>';

 							if(isset($GLOBALS['templ']['general']['general']['form']['error'][$seatid]['msg'])) {
 									$additional = "size=13 maxlength=15 class=\"form_error\"";
 							} else {
 									$additional = "size=13 maxlength=15 class=\"form\"";
 							}

 							$seat_fieldstring = sprintf($seat_fieldstring, $seatid, $ip_mask, $additional);

 							$seat_fieldstring .= $GLOBALS['templ']['general']['general']['form']['error'][$seatid]['msg'];



 					break;
 		} // switch



		return $seat_fieldstring;


	} // function generate_fieldstring_change_ip($ic, $ir)



/**
 * generate highl userarray
 **/

	function generate_highl_userarray($blockid, $highl_userarr) {

		global $db;

		if(is_array($highl_userarr) == FALSE) $highl_userarr = array();

		foreach($highl_userarr as $arr_userid)  {

		$row = $db->query_first("SELECT col, row FROM {$GLOBALS["config"]["tables"]["seat_seats"]} WHERE userid='$arr_userid' AND blockid='$blockid'");

		$this->highl_userarray[$row["col"]][$row["row"]] = TRUE;

		}
	}

/**
 * to view a seating (seat_mode = default)
 **/


	function view_block($blockid) {

		$this->generate_block_userdata($blockid);

		$this->generate_block($blockid, 0);

		return  $this->block_design;
	}


/**
* to view a seating with highlighted seats (seat_mode = 1)
**/

	function view_block_highl($blockid, $highl_userarr) {

		$this->generate_highl_userarray($blockid, $highl_userarr);

		$this->generate_block_userdata($blockid);

		$this->generate_block($blockid, 1);


		return  $this->block_design;

	} // function view_block_highl($blockid, $userstring)


/**
 * to view a seating highlighted without fetch the userdata | for example to show changed userdata that aren't write into DB (seat_mode = 1)
 **/

	function view_block_highl_without_catch_data($blockid) {

		$this->generate_block($blockid, 1);

		return  $this->block_design;
	}

/**
 * to create or change a seating (seat_mode = 2)
 **/

	function view_block_create($blockid) {

		$this->generate_block($blockid, 2);

		return  $this->block_design;
	}




/**
 * to view a default seating without description and catch data(seat_mode = 3)
 **/


	function view_block_without_description_and_data($blockid) {

		$this->generate_block($blockid, 3);

		return  $this->block_design;
	}

/**
 * to view a seating without fetch the userdata | for example to create seating with javascript (seat_mode = 4)
 **/

	function view_block_without_catch_data($blockid) {

		$this->generate_block($blockid, 4);

		return  $this->block_design;
	}



/**
 * to view a seating with inputfolders | for example to change ip (seat_mode = 5)
 **/

	function view_block_with_input($blockid) {

		$this->generate_block_userdata($blockid);
		$this->generate_block($blockid, 5);

		return  $this->block_design;
	}


/**
 * define the js link string to open a popup with a seating
 **/

	function convert_js_string($function, $blockid, $userarray, $text, $l) {

		global $db;

			$row = $db->query_first("
				SELECT 		cols,rows
				FROM 		{$GLOBALS['config']['tables']['seat_block']}
				WHERE 		blockid	='$blockid'
				");
			$row_seps_h = $db->query_first("
				SELECT 		count(*) as number
				FROM 		{$GLOBALS['config']['tables']['seat_sep']}
				WHERE 		blockid	='$blockid' AND orientation='0'
				");
			$row_seps_v = $db->query_first("
				SELECT 		count(*) as number
				FROM 		{$GLOBALS['config']['tables']['seat_sep']}
				WHERE 		blockid	='$blockid' AND orientation='1'
				");
			$seps_h = $row_seps_h["number"];
			$seps_v = $row_seps_v["number"];

			$window_width = ($row["cols"]*16)+260+($seps_v*16);
			$window_height = ($row["rows"]*20)+118+($seps_h*20); // 380

			if(is_array($userarray)) {
				foreach($userarray as $user) {

					$userarraystring .= "&amp;userarray[]=$user";
				}
			}
			$simple_hyperlink = '<a href="#" onclick="%s">%s</a>';

			if($l == 1) 	{ $l = '&l=1'; $window_height += 180;}
			else		{ $l = '&l=0'; }

			$js_string = "javascript:var w=window.open('base.php?mod=seating&function=%s&id=%s%s%s','_blank','width=%s,height=%s,resizable=yes');";

			$js_string = sprintf($js_string, $function, $blockid, $userarraystring, $l, $window_width, $window_height);


		$link = sprintf($simple_hyperlink, $js_string, $text);

		return $link;

	} // convert js string


/**
 * calculate an IP by an array index for example by ordered seats in an array
 **/

	function index2ip($index, $point1, $point2) {

			$ii  = ceil($index / 254);
			$iii = $ii-1;
			$i   = $index - (254*($ii-1));


			$ip_scheme = '%s.%s.%s.%s';
			$ip = sprintf($ip_scheme, $point1, $point2, $iii, $i);

		return $ip;

	} // function index2ip


/**
 * display hyperlink with seatstatement to open a popup
 **/


	function display_seat_link($function,$user) {

		global $db,$func,$party;
		if($user != ""){
			$row_seat = $db->query_first("SELECT s.blockid, s.col, s.row FROM {$GLOBALS['config']['tables']['seat_seats']} AS s LEFT JOIN {$GLOBALS['config']['tables']['seat_block']} AS b USING(blockid) WHERE s.userid='$user' AND b.party_id={$party->party_id}");
			$blockid  = $row_seat["blockid"];
			if($blockid != "") {
				$row_block    = $db->query_first("SELECT orientation, name FROM {$GLOBALS['config']['tables']['seat_block']} WHERE blockid='$blockid'");
				$orientation  = $row_block["orientation"];
				$ic           = $row_seat["col"];
				$ir           = $row_seat["row"];
				$userarray[0] = $user;
				$seatindex    = $this->display_seat_index($orientation, $ic, $ir);
				$link         = $this->convert_js_string($function, $blockid, $userarray, $seatindex, 1);
				if($function == "mastersearch") {
					if(strlen($row_block["name"]) > 8)	{
						$blockname = $func->db2text(substr($row_block["name"], 0, 8))."...";
					} else {
						$blockname = $func->db2text($row_block["name"]);
					}
					$blockname = "<div title=\"{$row_block["name"]}\">$blockname - $link</div>";
				} else {
					$blockname = $func->db2text($row_block["name"])." - ".$link;
				}
				return $blockname;
			}
		}
	}

	function check_u18_block($id,$idtype) {
		global $db;
		/*
		$id 	can be a userid or blockid
		$idtype can be
			"u" for userid (standard)
			"b" for blockid or

		TRUE - MEANS THAT IS A U18 BLOCK
		FALSE - MEANS THAT IS A over18 BLOCK OR !!! BLOCK DOESN'T EXIST

		*/


		if($idtype == "b") 	{ $blockid = $id; }
		elseif($idtype != "b") {
					$row_seat = $db->query_first("SELECT blockid FROM {$GLOBALS['config']['tables']['seat_seats']} WHERE userid='$id'");
				   	$blockid = $row_seat['blockid'];

			if($blockid == "") return FALSE;
		}

		$row_block = $db->query_first("SELECT u18, blockid FROM {$GLOBALS['config']['tables']['seat_block']} WHERE blockid='$blockid'");

		$blockid = $row_block['blockid'];

		if($blockid == "") return FALSE;

		$u18 = $row_block["u18"];

		if($u18 == TRUE) return TRUE;
		else		 return FALSE;

	} // function check_u18_block

	function get_blockid($userid){
		global $db,$party;
			$row = $db->query_first("SELECT seat.blockid FROM {$GLOBALS['config']['tables']['seat_seats']} AS seat LEFT JOIN {$GLOBALS['config']['tables']['seat_block']} AS block ON seat.blockid=block.blockid WHERE userid='$userid' AND party_id={$party->party_id}");
			$blockid = $row["blockid"];

		return $blockid;
	}


} // class seat
?>