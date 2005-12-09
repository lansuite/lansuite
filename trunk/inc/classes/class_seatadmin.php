<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		class_seatadmin.php
*	Module: 		seat
*	Main editor: 		raphael@one-network.org
*	Last change: 		18.03.2003 21:24
*	Description: 		class to assign seats
*	Remarks:
*
**************************************************************************/


class sadmin {

var $userid;
var $seatid;
var $blockid;
var $mode;

var $rebinduri;

/////////////////////////////////////////
// INITIALIZE VARS

	function sadmin($mod, $action) {
		global $vars;

			$this->step 	= $vars['step'];

			$this->mode 	= $vars['mode'];
			$this->userid 	= $vars['userid'];
			$this->blockid	= $vars['blockid'];
			$this->seatid	= $vars['seatid'];

			$this->mod	= $mod;
			$this->action	= $action;


			if($this->mode == "rebind")		$this->rebinduri = "&mode=rebind";
			elseif($this->mode == "new")		$this->rebinduri = "&mode=new";

	} // constructor

/////////////////////////////////////////
// UNSET SESSION VARS

	function sadmin_unset_svars() {

				unset($_SESSION['userarray']);
				unset($_SESSION['newseatarray']);
				unset($_SESSION['current_user']);
				unset($_SESSION['last_user']);
				unset($_SESSION['last_last_user']);
				unset($_SESSION['rebind']);
				unset($_SESSION['first_seat']);
				unset($_SESSION['first_user']);
				unset($_SESSION['first_block']);
				unset($_SESSION['bound']);

	} // function - sadmin_unset_svars()

/////////////////////////////////////////
// IF THIS IS FIRST USER SAVE THIS DATA IN SPECIAL SESSION VAR


		function sadmin_bind_firstuser() {

			global $db,$party;

				$row = $db->query_first("SELECT s.seatid, s.blockid FROM {$GLOBALS["config"]["tables"]["seat_seats"]} AS s LEFT JOIN {$GLOBALS["config"]["tables"]["seat_block"]} AS b USING(blockid) WHERE s.userid='$this->userid' AND b.party_id={$party->party_id}");

				if(isset($_SESSION['userarray'][$this->userid]) == FALSE)
			 		$_SESSION['userarray'][$this->userid]	= $row["seatid"];

				$_SESSION['first_block'] 	= $row["blockid"];
				$_SESSION['first_user'] 	= $this->userid;
				$_SESSION['first_seat'] 	= $row["seatid"];
				$_SESSION['current_user'] 	= $this->userid; 		// can be from step 1 (new) or step 4 (rebind)
				$_SESSION['current_username'] = $row["username"];

		} // function - sadmin_bind_firstuser

/////////////////////////////////////////
// SAVE USER DATA IN SESSION ARRAY

		function sadmin_rebind_user() {

				unset($_SESSION['userarray'][$_SESSION['first_user']]);

				$_SESSION['rebind'] = TRUE;

				$_SESSION['current_user'] = $_SESSION['last_user'];


		} // function - sadmin_rebind

/////////////////////////////////////////
// SAVE USER DATA AGAIN SESSION ARRAY

		function sadmin_newtry_user() {


				$current_user = $_SESSION['current_user'];
				$last_user    = $_SESSION['last_user'];

				unset($_SESSION['newseatarray'][$current_user]);
				unset($_SESSION['userarray'][$last_user]);

				$_SESSION['last_user'] = $_SESSION['last_last_user'];

		} // function - sadmin_rebind

/////////////////////////////////////////
// DIFFER SAVE CASE - first user/other user

		function sadmin_bind_users() {

				if($this->mode == "new")	{ $this->sadmin_newtry_user();	}
				elseif($this->mode == "rebind")	{ $this->sadmin_rebind_user(); 	}
				else			    	{ $this->sadmin_bind_firstuser();}

		} // function admin_bind_users

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

				foreach($_SESSION['userarray'] as $userid => $seatid) {

					$userarray[] = $userid;
				}



			$seat = new seat;

			if($this->mode != "free") {
				$seat->seat_linkstr[1] = '?mod='.$this->mod.'&action='.$this->action.'&step='.$nextstep.$this->rebinduri.'&seatid=%s';
			}
				$seat->seat_linkstr[2] = '?mod='.$this->mod.'&action='.$this->action.'&step='.$nextstep.$this->rebinduri.'&seatid=%s&userid=%s';
				$seat->seat_linkstr[3] = '?mod='.$this->mod.'&action='.$this->action.'&step='.$nextstep.$this->rebinduri.'&seatid=%s&userid=%s';
				$seat->seat_linkstr[4] = '?mod='.$this->mod.'&action='.$this->action.'&step='.$nextstep.$this->rebinduri.'&seatid=%s&userid=%s';


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

			if($this->mode == "rebind")
			$seat->field_status[$seat->field_col[$_SESSION['first_seat'] ]][$seat->field_row[$_SESSION['first_seat']]] = 1;


			$templ['seat']['seatadmin']['case']['control']['case']  .= $seat->view_block_highl_without_catch_data($this->blockid);

			return $templ['seat']['seatadmin']['case']['control']['case'];

		} // function - sadmin_display_seat

////////////////////////////////////////
// SET USERS FOR FREE
		function set_free() {

			$_SESSION['last_user'] = $this->userid;

		} // function - set_free()


////////////////////////////////////////
// SHOW POSSIBILITIES

		function sadmin_show_possibilities($last_step, $second_step) {

			global $func;
			global $db;

			$current_user = $_SESSION['current_user'];			// set the current chosen userid
			$_SESSION['newseatarray'][$current_user] = $this->seatid; 	// set the new seat of the user

			$_SESSION['last_last_user'] = $_SESSION['last_user'];
			$_SESSION['last_user'] = $this->userid;				// the userid whose seat was chosen
			$_SESSION['userarray'][$this->userid] = $this->seatid; 		// thats the old seat that was reserved and must rebind

				$co_user = $db->query_first("SELECT username FROM {$GLOBALS["config"]["tables"]["user"]} WHERE userid='$this->userid'");
				$chosen_username = $co_user["username"];
				$cu_user = $db->query_first("SELECT username FROM {$GLOBALS["config"]["tables"]["user"]} WHERE userid='$current_user'");
				$current_username = $cu_user["username"];

				$questionarr[1] = "<strong>\"$current_username\"</strong> ".$lang['class_seatadmin']['search_new_seat'];
				$linkarr[1]	= "index.php?mod=".$this->mod."&action=".$this->action."&step=".($this->step - 2)."&userid=".$current_user."&mode=new".$this->rebinduri;

			if($_SESSION['rebind'] == FALSE AND $_SESSION['first_seat'] != "") {

				$questionarr[2] = str_replace("%CURRENTUSERNAME%", $current_username, str_replace("%USERNAME%", $chosen_username, $lang['class_seatadmin']['swap_seat']));
				$linkarr[2]	= "index.php?mod=".$this->mod."&action=".$this->action."&step=".$last_step."&mode=exchange".$this->rebinduri;

			}
				$questionarr[3] = str_replace("%CURRENTUSERNAME%", $current_username, str_replace("%USERNAME%", $chosen_username, $lang['class_seatadmin']['set_filled_seat']));
				$linkarr[3]	= "index.php?mod=".$this->mod."&action=".$this->action."&step=".$last_step."&mode=free";

				$questionarr[4] = str_replace("%CURRENTUSERNAME%", $current_username, str_replace("%USERNAME%", $chosen_username, $lang['class_seatadmin']['set_filled_seat2']));
				$linkarr[4]	= "index.php?mod=".$this->mod."&action=".$this->action."&step=".$second_step."&mode=rebind".$this->rebinduri;

				$questionarr[5] = $lang['class_seatadmin']['cancel_changes'];
				$linkarr[5]	= "index.php?mod=".$this->mod."&action=".$this->action;


			$func->multiquestion($questionarr,$linkarr,"");

		} // function - sadmin_show_possibilities

/////////////////////////////////////////
// IF MODE == EXCHANGE , EXCHANGE USERDATA

		function sadmin_exchange_seats($firstid) {

			if($this->mode == "exchange") {

				$current_user = $_SESSION['current_user'];

				if($current_user == "NEW") $current_user = $firstid;

				$last_user = $_SESSION['last_user'];

				$_SESSION['newseatarray'][$last_user] = $_SESSION['userarray'][$current_user];

			}

		} // function - sadmin_exchange_seats()

/////////////////////////////////////////
// IF MODE == FREE , ASSIGN USER 1 AND FREE USER 2

		function sadmin_free_seats() {

			if($this->mode == "free") {

				$last_user = $_SESSION['last_user'];

				if($last_user != 0) $_SESSION['newseatarray'][$last_user] = 0;

			}

		} // function - sadmin_exchange_seats()


/////////////////////////////////////////
// ADD USER TO SESSION ARRAY THAT WILL BE ADDED IN DATABASE

		function sadmin_add_user($last_step) {

			global $db;

			$row = $db->query_first("SELECT status, userid FROM {$GLOBALS["config"]["tables"]["seat_seats"]} WHERE seatid='$this->seatid'");

			$status = $row["status"];

			if($_SESSION['bound'] == TRUE) { $this->step = $last_step; }
			elseif($status == 1 OR $this->seatid == $_SESSION['first_seat']) {

				$this->step = $last_step;

				$this->mode = "bind";
				$current_user = $_SESSION['current_user'];
				$_SESSION['newseatarray'][$current_user] = $this->seatid;
			}

		} // function - sadmin_add_user


/////////////////////////////////////////
// UPDATE DB DATA

		function sadmin_db_update_seats($firstid) {

			global $db,$func,$party;

			$this->sadmin_exchange_seats($firstid);
			$this->sadmin_free_seats();

			if($_SESSION['bound'] == FALSE) {

				if(is_array($_SESSION['newseatarray'])) {

					foreach($_SESSION['newseatarray'] as $userid => $seatid) {

						if($userid == "NEW") $userid = $firstid;
						$row = $db->query_first("SELECT s.seatid FROM {$GLOBALS["config"]["tables"]["seat_block"]} AS b LEFT JOIN {$GLOBALS["config"]["tables"]["seat_seats"]} AS s USING(blockid) WHERE party_id={$party->party_id} AND s.userid=$userid");

						if(count($row['seatid']) > 0){
							$db->query("UPDATE {$GLOBALS["config"]["tables"]["seat_seats"]} SET status = 1, userid = 0  WHERE seatid={$row['seatid']} ");
						}

						if($seatid != 0) $db->query("UPDATE {$GLOBALS["config"]["tables"]["seat_seats"]} SET status = 2, userid = '$userid' WHERE seatid='$seatid'");

					} // foreach


					if($this->mod == "seating") {
					$func->confirmation("
							$lang['class_seatadmin']['change_confirm']
							", "index.php?mod=".$this->mod."&action=".$this->action);
					}

					$_SESSION['bound'] = TRUE;

				} else {
					$func->error("
					$lang['class_seatadmin']['nothing_to_change']
					", "index.php?mod=seating&action=".$this->action);
				}


			} // if($_SESSION['bound'] == FALSE)


			elseif($this->mod == "seating"){

				if($this->action == "free_seat" or $this->action == "free_user") {
					$func->error("
					$lang['class_seatadmin']['seat_changed']
					", "index.php?mod=seating&action=".$this->action);
				}
				else {
					$func->error("
					$lang['class_seatadmin']['user_has_seat']
					", "index.php?mod=seating&action=".$this->action);
				}

			} // elseif


		} // function - sadmin_db_update_seats()

} // class sadmin