<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		0.6
*	Filename: 		class_seatuser.php
*	Module: 		seat
*	Main editor: 		webmaster@netec.org
*	Last change: 		11.09.2003 18:09
*	Description: 		class to assign user seats
*	Remarks:
*
**************************************************************************/


class sadmin {

var $email;
var $user_username;
var $paid;
var $userid;
var $seatid;
var $blockid;
var $mode;

var $rebinduri;

/////////////////////////////////////////
// INITIALIZE VARS

	function sadmin($mod, $action) {

			$this->step 	= $_GET['step'];
			$this->mode 	= $_GET['mode'];
			$this->userid 	= $auth['userid'];
			$this->blockid	= $_GET['blockid'];
			$this->seatid	= $_GET['seatid'];

			$this->mod	= $mod;
			$this->action	= $action;

	} // constructor



/////////////////////////////////////////
// CHECK IF THERE ARE FREE SEATS OR NOT

		function sadmin_check_seats() {

			global $db;

			$row = $db->query_first("SELECT count(*) as n FROM {$GLOBALS["config"]["tables"]["seat_seats"]} WHERE status='1'");
			$seats = $row["n"];

			if($seats == 0) return FALSE;
			else		return TRUE;


		} // function - sadmin_check_seats


/////////////////////////////////////////
// DISPLAY SEATING

		function sadmin_display_seat($nextstep) {
			global $func;
			
			if(is_array($_SESSION['userarray']))

			$userarray[] = $auth['userid'];

			$seat = new seat;

			if($this->mode != "free") {
				$seat->seat_linkstr[1] = '?mod='.$this->mod.'&action='.$this->action.'&step='.$nextstep.$this->rebinduri.'&seatid=%s';
			}

			$seat->seat_picstr[21] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
			$seat->seat_picstr[31] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
			$seat->seat_picstr[41] = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_highlighted.gif';
			$seat->seat_picstr[0]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_transparency.gif ';
			$seat->seat_picstr[1]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_free.gif';
			$seat->seat_picstr[2]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved.gif';
			$seat->seat_picstr[3]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved_you.gif';
			$seat->seat_picstr[4]  = 'design/'.$_SESSION["auth"]["design"].'/images/arrows_seating_reserved_clanmate.gif';



			eval("\$templ['seat']['general']['case']['control']['legend']  .= \"". $func->gettemplate("seating_show_row_legend_seatadmin")."\";");

			$seat->generate_highl_userarray($this->blockid, $userarray);

			$seat->generate_block_userdata($this->blockid);

			//if($this->mode == "rebind")
			$seat->field_status[$seat->field_col[$_SESSION['first_seat'] ]][$seat->field_row[$_SESSION['first_seat']]] = 1;


			$templ['seat']['seatadmin']['case']['control']['case']  .= $seat->view_block_highl_without_catch_data($this->blockid);

			return $templ['seat']['seatadmin']['case']['control']['case'];

		} // function - sadmin_display_seat

////////////////////////////////////////
// SET USERS FOR FREE
		function set_free() {

			$_SESSION['last_user'] = $this->userid;

		} // function - set_free()


		function sadmin_if_seatfree($seatid)
		{
			global $db;	
			$check_seat = $db->query_first("SELECT status FROM {$GLOBALS["config"]["tables"]["seat_seats"]} WHERE seatid = '$seatid'");
			if($check_seat["status"] == 1) { return true; } else { return false; }
		}
		
/////////////////////////////////////////
// UPDATE DB DATA

		function sadmin_db_update_seats($user_username, $user_email) 
		{
			global $db,$party;

				$to_change_user = $_SESSION["auth"]["userid"];
				
				// Change only active Blocks
				$block_query = "";
				$block = $db->query("SELECT blockid FROM {$GLOBALS["config"]["tables"]["seat_block"]} WHERE party_id={$party->party_id}");
				while ($block_data = $db->fetch_array($block)){
					if($block_query == ""){
						$block_query .= "AND (blockid={$block_data['blockid']}";
					}else {
						$block_query .= " OR blockid={$block_data['blockid']}";
					}
				}
				$block_query .= ")";
				$clear_seat = $db->query("UPDATE {$GLOBALS["config"]["tables"]["seat_seats"]} SET status = '1', userid = '0' WHERE userid = '$to_change_user' AND status = '2' $block_query");
				$set_seat = $db->query("UPDATE {$GLOBALS["config"]["tables"]["seat_seats"]} SET status = '2', userid = '$to_change_user' WHERE seatid='$this->seatid' AND status = '1'");

				$_SESSION['bound'] = TRUE;
									
				if($set_seat)
				{
					return true;
				}
				else
				{
					return false;
				}

		} // function - sadmin_db_update_seats()

} // class sadmin
