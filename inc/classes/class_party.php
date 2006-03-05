<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0.3
*	File Version:		1.0
*	Filename: 			class_party.php
*	Main editor: 		Genesis marco@chuchi.tv
*	Last change: 		25.02.2005
*	Description: 		Klasse für die Verwaltung mehrerer Partys
*	Remarks:			
*
**************************************************************************/


class party{

	/**
	 * Party_id bezeichnet die momentane Party 
	 *
	 * @var int
	 */
	
	var $party_id;
	
		
		/**
		 * Klasse um mehrere Party zu verwalten
		 *
		 * @return party
		 */
		function party(){
			global $cfg,$db,$config;
			
			if(!isset($_SESSION['party_info'])){
				$_SESSION['party_info'] = array();
			}
			
			if($cfg['signon_multiparty'] == 0){
				$this->party_id = $cfg['signon_partyid'];
			}else{
				if(isset($_GET['party_id'])){
					$this->party_id = $_GET['party_id'];
				}elseif (isset($_POST['party_id']) && is_numeric($_POST['party_id'])){
					$this->party_id = $_POST['party_id'];
				}elseif (isset($_SESSION['party_id'])){
					$this->party_id = $_SESSION['party_id'];
				}else{
					$this->party_id = $cfg['signon_partyid'];
				}
				$_SESSION['party_id'] =  $this->party_id;
			}
			
		}
	
		// Partyid abfragen
		/**
		 * Funktion zum ausgeben der Party_id
		 *
		 * @return int
		 */
		function get_party_id(){
			return $this->party_id;
		}
		
		// Partyid setzen
		/**
		 * Funktion um eine neue Party zu setzen es wird geprüft ob die Party existiert
		 *
		 * @param int $id
		 */
		function set_party_id($id){
			global $db,$config;
			$row = $db->query_first_rows("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$id}");	
			if($row['number'] == 1){
				$this->party_id = $id;
				// $_SESSION['party_id'] = $id;
			}
			$this->write_party_infos();
		}
		
		
		/**
		 * Funktion zum hinzufügen eines Formulars zur Partyauswahl
		 *
		 * @param boolean $show_old
		 * @param string $link
		 */
		function get_party_dropdown_form($show_old = 0, $link = ''){
			global $dsp,$db,$lang,$templ,$func,$cfg,$config;
			
			// Bei leerem String
			if ($link == ''){
				$link = "index.php?" . $_SERVER['QUERY_STRING'];
			}
			
			// Wenn die Anzeige auf nur einer party steht dann nichts ausgeben
			if($cfg['signon_multiparty'] == 1){
				if($show_old = 0){
					$query = "SELECT * FROM {$config['tables']['partys']} WHERE enddate < " . time();
				}else{
					$query = "SELECT * FROM {$config['tables']['partys']}";
				}
				
				// Wenn nur eine Party aufgelistet ist nichts ausgeben
				$row = $db->query($query);
				if($db->num_rows($row) >= 1){
					
					while($res = $db->fetch_array($row)){
						$start_date = $func->unixstamp2date($res["startdate"], "date");
						$end_date = $func->unixstamp2date($res["enddate"], "date");
						
						if($res['party_id'] == $this->party_id){
							$selected = "selected='selected'";
						}else{
							$selected = "";
						}
						if(is_array($list_array)){
							array_push($list_array,"<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
						}else{
							$list_array = array("<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
						}
					}
		        	$dsp->SetForm($link);
					$dsp->AddDropDownFieldRow("party_id",$lang['class_party']['drowpdown_name'],$list_array,'');
		        	$dsp->AddFormSubmitRow("send");
				}
			
			}
		}

		/**
		 * Funktion zum hinzufügen eines Dropdownfeldes zur Klasse $dsp 
		 *
		 * @param boolean $show_old
		 */
		function get_party_dropdown($show_old = 0){
			global $dsp,$db,$lang,$templ,$func,$config;
			
			// Bei leerem String
			if ($link == ''){
				$link = "index.php?" . $_SERVER['QUERY_STRING'];
			}
			
			// Wenn die Anzeige auf nur einer party steht dann nichts ausgeben
			if($cfg['singon_multiparty'] == 1){
				if($archive = 0){
					$query = "SELECT * FROM {$config['tables']['partys']} WHERE enddate < " . time();
				}else{
					$query = "SELECT * FROM {$config['tables']['partys']}";
				}
			
				// Wenn nur eine Party aufgelistet ist nichts ausgeben
				$row = $db->query($query);
				if($db->num_rows($row) > 1){
					while($res = $db->fetch_array($row)){
						$start_date = $func->unixstamp2date($res["statedate"], "date");
						$end_date = $func->unixstamp2date($res["enddate"], "date");
						
						if($res['party_id'] == $this->party_id){
							$selected = "selected='selected'";
						}else{
							$selected = "";
						}
						if(is_array($list_array)){
							array_push($list_array,"<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
						}else{
							$list_array = array("<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
						}
					}
		        	$dsp->AddDropDownFieldRow("party_id",$lang['class_party']['drowpdown_name'],$list_array);
		        }
			
			}
		}

		/**
		 * Funktion zum hinzufüge einer Party
		 *
		 */
		function add_party(){
			global $db,$func,$config;
			
			$_POST['startdate'] 	= mktime($_POST["stime_value_hours"], $_POST["stime_value_minutes"], $_POST["stime_value_seconds"], $_POST["stime_value_month"], $_POST["stime_value_day"], $_POST["stime_value_year"]);
			$_POST['enddate']		= mktime($_POST["etime_value_hours"], $_POST["etime_value_minutes"], $_POST["etime_value_seconds"], $_POST["etime_value_month"], $_POST["etime_value_day"], $_POST["etime_value_year"]);
			$_POST['sstartdate']	= mktime($_POST["sstime_value_hours"], $_POST["sstime_value_minutes"], $_POST["sstime_value_seconds"], $_POST["sstime_value_month"], $_POST["sstime_value_day"], $_POST["sstime_value_year"]);
			$_POST['senddate']		= mktime($_POST["setime_value_hours"], $_POST["setime_value_minutes"], $_POST["setime_value_seconds"], $_POST["setime_value_month"], $_POST["setime_value_day"], $_POST["setime_value_year"]);
			
			
			$db->query("INSERT INTO {$config['tables']['partys']} SET
								name = '{$_POST['name']}',
								ort = '{$_POST['ort']}',
								plz = '{$_POST['plz']}',
								max_guest = '{$_POST['max_guest']}',
								startdate = {$_POST['startdate']},
								enddate = {$_POST['enddate']},
								sstartdate = {$_POST['sstartdate']},
								senddate = {$_POST['senddate']}");
						
			$this->set_party_id($db->insert_id());
		}
		
		/**
		 * Party ändern 
		 *
		 */
		function change_party(){
			global $db,$func,$config;
			
			$_POST['startdate'] 	= mktime($_POST["stime_value_hours"], $_POST["stime_value_minutes"], $_POST["stime_value_seconds"], $_POST["stime_value_month"], $_POST["stime_value_day"], $_POST["stime_value_year"]);
			$_POST['enddate']		= mktime($_POST["etime_value_hours"], $_POST["etime_value_minutes"], $_POST["etime_value_seconds"], $_POST["etime_value_month"], $_POST["etime_value_day"], $_POST["etime_value_year"]);
			$_POST['sstartdate']	= mktime($_POST["sstime_value_hours"], $_POST["sstime_value_minutes"], $_POST["sstime_value_seconds"], $_POST["sstime_value_month"], $_POST["sstime_value_day"], $_POST["sstime_value_year"]);
			$_POST['senddate']		= mktime($_POST["setime_value_hours"], $_POST["setime_value_minutes"], $_POST["setime_value_seconds"], $_POST["setime_value_month"], $_POST["setime_value_day"], $_POST["setime_value_year"]);
			
			$db->query("UPDATE {$config['tables']['partys']} SET
								name = '{$_POST['name']}',
								ort = '{$_POST['ort']}',
								plz = '{$_POST['plz']}',
								max_guest = '{$_POST['max_guest']}',
								startdate = {$_POST['startdate']},
								enddate = {$_POST['enddate']},
								sstartdate = {$_POST['sstartdate']},
								senddate = {$_POST["senddate"]}
								WHERE party_id = {$this->party_id}");
						
		}
		
		
		/**
		 * Party löschen und auf Standartparty einstellen
		 *
		 */
		function delete_party(){
			global $db,$func,$config,$cfg;
			// Party löschen
			$db->query("DELETE FROM {$config['tables']['partys']} 
								WHERE party_id = {$this->party_id}");
			
			// Preise zur Party löschen
			$db->query("DELETE FROM {$config['tables']['party_prices']} 
								party_id = {$this->party_id}
								");
			
			// User zur Party löschen
			$db->query("DELETE FROM {$config['tables']['party_user']} 
								party_id = {$this->party_id}
								");
			
			$this->set_party_id($cfg['signon_partyid']);
						
		}
		
		
		/**
		 * Party in die Session schreiben Interne Funktion
		 *
		 */
		function write_party_infos(){
			global $db,$config;
			
			if(is_numeric($this->party_id)){
			// Lese Partydaten
			$row = $db->query_first("SELECT * FROM {$config['tables']['partys']} WHERE party_id={$this->party_id}");	
			
			$_SESSION['party_info']['name']			= $row['name'];
			$_SESSION['party_info']['partyort']		= $row['ort'];
			$_SESSION['party_info']['partyplz']		= $row['plz'];
			$_SESSION['party_info']['partybegin'] 	= $row['startdate'];
			$_SESSION['party_info']['partyend'] 	= $row['enddate'];
			$_SESSION['party_info']['s_startdate'] 	= $row['sstartdate'];
			$_SESSION['party_info']['s_enddate'] 	= $row['senddate'];
			$_SESSION['party_info']['max_guest'] 	= $row['max_guest']; 
			}
			
		}
		
		
		/**
		 * Preise zählen
		 */
		
		function get_price_count($groupid = false){
			global $db,$config;
			
			if($groupid){
				$row = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$this->party_id} AND group_id='$groupid'");
				
			}else{
				$row = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$this->party_id}");	
			
			}
			return $db->num_rows($row);
			
		}
		/**
		 * Funktion um ein Dorpdownfeld mit Preisen zur Party auszugeben
		 *
		 */
		function get_price_dropdown($group_id = 0,$price_id = 0,$dropdown = false){
			global $db,$dsp,$config,$lang,$cfg;
			
			if($group_id !== "NULL") $subquery = " AND group_id='{$group_id}'";
			if($price_id == "NULL") $price_id = 0;
			
			$row = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$this->party_id} $subquery");
			$anzahl = $db->num_rows($row);
			
			if($anzahl == 0){
				$row = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$this->party_id} AND group_id='0'");
			}
			
			if($anzahl >1 || $dropdown == true){		
				while ($res = $db->fetch_array($row)){
						if($price_id == $res['price_id']){
							$selected = "selected='selected'";	
						}else{
							$selected = "";
						}
						
						if(is_array($data)){
							array_push($data,"<option $selected value='{$res['price_id']}'>{$res['price_text']} / {$res['price']} {$cfg['sys_currency']}</option>");
						}else{
						 $data = array("<option $selected value='{$res['price_id']}'>{$res['price_text']} / {$res['price']} {$cfg['sys_currency']}</option>");
						}
				}
				$dsp->AddDropDownFieldRow("price_id",$lang['class_party']['drowpdown_price'],$data,'');
			}else{
				$res = $db->fetch_array($row);
				$dsp->AddDoubleRow($lang['class_party']['drowpdown_price'],$res['price_text'] . "  / {$res['price']} {$cfg['sys_currency']}<input name='price_id' type='hidden' value='{$res['price_id']}' />");	
			}
			
		}
		
		function get_party_javascript(){
			global $db,$config,$cfg;
			$row = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = {$this->party_id} ORDER BY group_id");
			$option = "var option = new Array();\n";
			$prices = "var price = new Array();\n";
			while ($data = $db->fetch_array($row)){
				if($temp_group != $data['group_id']){
					$temp_group = $data['group_id'];
					$option .= "option[{$data['group_id']}] = new Array();\n";
					$prices .= "price[{$data['group_id']}] = new Array();\n";
					$i = 0;
				}
				$option .= "option[{$data['group_id']}][$i] = \"{$data['price_text']} / {$data['price']} {$cfg['sys_currency']}\";\n";
				$prices .= "price[{$data['group_id']}][$i] = \"{$data['price_id']}\";\n";
				$i++;
			}
			
			return $option . $prices;
		
		}
		
		/**
		 * Funktion um einen Preis hizuzufügen
		 *
		 * @param string $price_text
		 * @param int $price
		 * @param string $depot_desc
		 * @param int $depot_price
		 * @param int $usergroup
		 */
		function add_price($price_text,$price,$depot_desc = "",$depot_price = 0,$usergroup = 0){
			global $db,$config;
			
			$db->query("INSERT {$config['tables']['party_prices']} SET 
								party_id = {$this->party_id},
								price_text = '$price_text',
								price = '$price',
								depot_desc = '$depot_desc',
								depot_price = '$depot_price',
								group_id = '$usergroup'
								");
		}
		

		
		
		/**
		 * Funktion um einen Preis zu ändern
		 *
		 * @param int $price_id
		 * @param string $price_text
		 * @param int $price
		 * @param string $depot_desc
		 * @param int $depot_price
		 * @param int $usergroup
		 */
		function update_price($price_id,$price_text,$price,$depot_desc = "",$depot_price = 0,$usergroup = 0){
			global $db,$config;
			
			$db->query("UPDATE {$config['tables']['party_prices']} SET 
								price_text = '$price_text',
								price = '$price',
								depot_desc = '$depot_desc',
								depot_price = '$depot_price',
								group_id = '$usergroup'
								WHERE price_id = {$price_id}
								");
		}
		
		
		/**
		 * Funktion zum hinzufügen eines Users zu einer Party
		 * Die Funktion prüft ob der User schon an der Party angemeldet ist und ersetzt gegebenenfalls den Eintrag.
		 *
		 * @param int $user_id
		 * @param int $price_id
		 * @param int $checkin
		 */
		function add_user_to_party($user_id,$price_id = "0",$paid = "NULL",$checkin = "NULL"){
			global $db,$cfg,$config;
			
			$timestamp = time();
			
			if($checkin == "1" || $cfg["signon_autocheckin"] == "1"){
				$checkin = "$timestamp";
			}else{
				$checkin = "0";
			}
			
			if(($cfg["signon_autopaid"] == "1" && $paid == "NULL")){
				$paid = "1";
			}elseif ($paid == "NULL"){
				$paid = "0";	
			}
			
			$row = $db->query("SELECT * FROM {$config['tables']['party_user']} WHERE user_id={$user_id} AND party_id={$this->party_id}");
			if($db->num_rows($row) < 1){
				$prices = $db->query_first("SELECT * FROM {$config['tables']['party_prices']} WHERE price_id=$price_id");
				if($prices['depot_price'] == 0){
					$seatcontrol = 1;	
				}else {
					$seatcontrol = 0;	
				}
				
				$db->query("INSERT INTO {$config['tables']['party_user']} SET
									user_id = {$user_id},
									party_id = {$this->party_id},
									price_id = {$price_id},
									checkin = {$checkin},
									paid = {$paid},
									seatcontrol = {$seatcontrol},
									signondate = $timestamp
									");
			}else{
				$this->update_user_at_party($user_id,$paid,$price_id,$checkin);
			}
		}
		
		

		/**
		 * Funktion um einen Bezahlungsstatus zu ändern
		 *
		 * @param int $user_id
		 * @param bool $paid
		 * @param int $price_id
		 * @param bool $checkin
		 * @param bool $checkout
		 */
		function update_user_at_party($user_id, $paid, $price_id = "0", $checkin = "0",$checkout = "0",$seatcontrol = "NULL"){
			global $db,$config,$func,$lang;
			$timestamp = time();
			
			if($checkin == "1"){
				$checkin = $timestamp;
			}
			
			if($checkout == "1"){
				$checkout = $timestamp;
			}

			if($price_id != 0){
				 $prices = $db->query_first("SELECT * FROM {$config['tables']['party_prices']} WHERE price_id=$price_id");
				if($prices['depot_price'] == 0){
					$seatcontrol = 1;	
				}
			}

			$query = "UPDATE {$config['tables']['party_user']} SET ";
			
			if($paid != ""){
				$query .= "paid = {$paid},";
			}
			
			
			if($price_id != "0" && $price_id != ""){
				$query .= "price_id = {$price_id},";
			}
			
			if($seatcontrol !== "NULL"){
				$query .= "seatcontrol = {$seatcontrol},";
			}
			
			$query .= "	checkin = {$checkin},
						checkout = {$checkout}
						WHERE user_id = {$user_id} AND
						party_id = {$this->party_id}
						";
			$msg = str_replace("%PARTY%",$this->party_id,str_replace("%ID%",$user_id,str_replace("%PIRCEID%",$price_id,str_replace("%SEATCONTROL%",$seatcontrol,str_replace("%CHECKOUT%",$checkout,str_replace("%CHECKIN%",$checkin,str_replace("%PAID%",$paid,$lang['class_party']['logevent'])))))));
			$func->log_event($msg,1);
			$db->query($query);
		
			
		}
			

		/**
		 * User von einer Party abmelden
		 *
		 * @param int $user_id
		 */
		function delete_user_from_party($user_id){
			global $db,$cfg,$config;
			$timestamp = time();
			if($checkin == "1" || $cfg["signon_autocheckin"] == "1"){
				$checkin = $timestamp;
			}else{
				$checkin = "0";
			}
			
			
			$db->query("DELETE FROM {$config['tables']['party_user']} 
								WHERE user_id = $user_id AND
								party_id = {$this->party_id}
								");
				
		}

			
		/**
		 * Funktion um ein Dropdownfeld mit Benutzergruppen hinzuzufügen.
		 *
		 */
		function get_user_group_dropdown($group_id = "NULL",$nogroub = 0,$select_id = 0,$javascript = false){
			global $db,$dsp,$config,$lang;
			
			if($group_id == "NULL"){
				$row = $db->query("SELECT * FROM {$config['tables']['party_usergroups']}");
			}else{
				$row = $db->query("SELECT * FROM {$config['tables']['party_usergroups']} WHERE group_id = {$group_id}");	
			}
			
			if($nogroub == 1){
				if($select_id == 0){
					$data = array("<option selected value='0'>{$lang['class_party']['drowpdown_no_group']}</option>");
				}else{
					$data = array("<option value='0'>{$lang['class_party']['drowpdown_no_group']}</option>");
				}
			}
			
			$anzahl = $db->num_rows($row);
			
			if($anzahl == 0){
				$dsp->AddDoubleRow($lang['class_party']['drowpdown_user_group'],$lang['class_party']['no_user_group'] . "<input name='group_id' value='0' type='hidden' />");		
				return false;
			}elseif($nogroub == 0 && $anzahl == 1){
				$res = $db->fetch_array($row);
				$dsp->AddDoubleRow($lang['class_party']['drowpdown_user_group'],$res['group_name'] . "<input name='group_id' value='{$res['group_id']}' type='hidden' />");		
			}else{
				while ($res = $db->fetch_array($row)){
						if($res['group_id'] == $select_id){
							$selected = "selected='selected'";
						}else{
							$selected = "";
						}
					
						if(is_array($data)){
							array_push($data,"<option $selected value='{$res['group_id']}'>{$res['group_name']}</option>");
						}else{
						 $data = array("<option $selected value='{$res['group_id']}'>{$res['group_name']}</option>");
						}
				}
				if($javascript){
					$dsp->AddDropDownFieldRow("group_id\" onchange=\"change_group(this.options[this.options.selectedIndex].value)",$lang['class_party']['drowpdown_user_group'],$data,'');
				}else {
					$dsp->AddDropDownFieldRow("group_id",$lang['class_party']['drowpdown_user_group'],$data,'');
				}
			}
			return true;
						
		}
		
		
		/**
		 * Funktion um Benutzergruppen hinzuzufügen
		 *
		 * @param string $group
		 * @param string $description
		 */
		function add_user_group($group,$description,$selection,$select_opts){
			global $db,$config;
			
			$db->query("INSERT {$config['tables']['party_usergroups']} SET
								group_name = '{$group}',
								description = '{$description}',
								selection = '{$selection}',
								select_opts = '{$select_opts}'
								");
			
		}
		
		/**
		 * Funktion um Benutzergruppen zu ändern
		 *
		 * @param string $group
		 * @param string $description
		 * @param int $group_id
		 */
		function update_user_group($group_id,$group,$description,$selection,$select_opts){
			global $db,$config;
			
			$db->query("UPDATE {$config['tables']['party_usergroups']} SET
								group_name = '{$group}',
								description = '{$description}',
								selection = '{$selection}',
								select_opts = '{$select_opts}'
								WHERE group_id = '{$group_id}'
								");
			
		}		
		
		
		function price_seatcontrol($price_id){
			global $db, $config;
			$prices = $db->query_first("SELECT * FROM {$config['tables']['party_prices']} WHERE price_id=$price_id");
			return $prices['depot_price'];
		}
		
		/**
		 * Platzpfand abfragen
		 *
		 * @param int $user_id
		 * @return int
		 */
		function get_seatcontrol($user_id){
			global $db, $config;
				$row = $db->query_first("SELECT * FROM {$config['tables']['party_user']} WHERE user_id=$user_id AND party_id={$this->party_id}");
				return $row['seatcontrol'];
		}
		
		/**
		 * Platzpfand setzten
		 *
		 * @param int $user_id
		 * @param int $seatcontrol
		 */
		function set_seatcontrol($user_id,$seatcontrol){
			global $db, $config;
				$db->query("UPDATE {$config['tables']['party_user']}  SET seatcontrol=$seatcontrol WHERE user_id=$user_id AND party_id={$this->party_id}");
		
		}
				
		/**
		 * Preise löschen, dabei werden alle Benuter die diesen Preis haben auf einen neuen Preis gesetzt
		 *
		 * @param int $del_price
		 * @param int $set_price
		 */
		function delete_price($del_price, $set_price){
			global $db, $config;
				$db->query("UPDATE {$config['tables']['party_user']}  SET price_id='$set_price' WHERE price_id=$del_price");
				$db->query("DELETE FROM {$config['tables']['party_prices']} WHERE price_id=$del_price");
		}
		
		
		/**
		 * Gruppe löschen, dabei werden alle Benutzer die in dieser Gruppe sind auf einen neue Gruppe setzt.
		 *
		 * @param unknown_type $del_group
		 * @param unknown_type $set_group
		 */
		function delete_usergroups($del_group,$set_group){
			global $db, $config;
				$db->query("UPDATE {$config['tables']['user']}  SET group_id='$set_group' WHERE group_id=$del_group");
				$db->query("DELETE FROM {$config['tables']['party_usergroups']} WHERE group_id=$del_group");
		}
		
		
		
		function get_next_party(){
			global $db,$config;
			
			$time = time();
			$row = $db->query_first_rows("SELECT * FROM {$config['tables']['partys']} WHERE startdate > $time ORDER BY startdate ASC");	
			
			if($row['number'] > 0){
				$data['party_id']		= $row['party_id'];
				$data['name']			= $row['name'];
				$data['partyort']		= $row['ort'];
				$data['partyplz']		= $row['plz'];
				$data['partybegin'] 	= $row['startdate'];
				$data['partyend'] 		= $row['enddate'];
				$data['s_startdate'] 	= $row['sstartdate'];
				$data['s_enddate'] 		= $row['senddate'];
				$data['max_guest'] 		= $row['max_guest']; 
							
				return $data;
			}else{
				return false;
			}
		}
		/*
		function delete_party($party_id){
			global $db, $config;
				// Sitzplan löschen
				if(isset($config['tables']['seat_block'])){
					$db->query("DELETE FROM {$config['tables']['seat_block']} WHERE party_id=$party_id");
				}
				// Turniere löschen
				if(isset($config['tables']['seat_block'])){
					$db->query("DELETE FROM {$config['tables']['seat_block']} WHERE party_id=$party_id");
				}
			
			
		}
		*/
		
		
}


?>
