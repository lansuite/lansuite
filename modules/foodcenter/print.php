<?php

$print = new foodcenter_print();

class foodcenter_print{

	var $output = "";
	var $path = "ext_inc/foodcenter_templates/";
	var $row_file = "";
	var $row_temp = "";

	function foodcenter_print(){
		global $func, $auth;
		if(!file_exists($this->path . $_POST['file']) || $_POST['file'] == ""){
			header("HTTP/1.0 404 Not Found");
			exit();
		}

		$handle 	= fopen ($this->path . $_POST['file'], "rb");
		$temp_file	= fread ($handle, filesize ($this->path . $_POST['file']));
		fclose ($handle);

		
		list($file, $ext) = explode(".",$_POST['file']);
		$this->row_file = $file . "_row." . $ext;

		if(!file_exists($this->path . $this->row_file)){
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		$temp_file = str_replace("\"","\\\"", $temp_file );
		
		$time = time();
		$this->sql();
		$temp['content'] = $this->row_temp;		
		$temp['supp'] = $this->GetSupp($_POST['search_dd_input'][1]);
		$temp['user'] = $this->GetUsername((int)$auth['userid']);
		$temp['time'] = $func->unixstamp2date( $time , "datetime");
		
		eval("\$this->output .= \"" .$temp_file. "\";");		
		
		echo $this->output;
	}



	function fetch_row($temp){
		$handle 	= fopen ($this->path . $this->row_file, "rb");
		$tmp	= fread ($handle, filesize ($this->path . $this->row_file));
		fclose ($handle);
		
		$tmp = str_replace("\"","\\\"", $tmp );
		
		eval("\$this->row_temp .= \"" .$tmp. "\";");
	}


	function GetSupp($value){
		global $lang, $db, $config;
		
		if($value == ""){
			return t('Verschiedene');		
		}else{
			$supp = $db->query_first("SELECT name FROM {$config['tables']['food_supp']} WHERE supp_id = " . $value);
			return $supp['name'];
		}
		
	}
	
	
	
	function GetFoodoption($value){
		global $func, $cfg, $db, $config, $lang;
		
		
		if(stristr($value,"/")){
			$values = split("/",$value);

			foreach ($values as $number){
				if(is_numeric($number)){
					$data = $db->query_first("SELECT caption, unit FROM {$config['tables']['food_option']} WHERE id = " . $number);
					if($data['caption'] == ""){
						$out .= $data['unit'] . "<br />";
					}else{
						$out .= $data['caption'] . "<br />";
					}
				}
		
			}
		}else {
			$data = $db->query_first("SELECT caption,unit FROM {$config['tables']['food_option']} WHERE id = " . $value);
			if($data['caption'] == ""){
				$out .= $data['unit'] . "<br />";
			}else{
				$out .= $data['caption'] . "<br />";
			}			
		}
		return $out;	
		
	}

	function GetUsername( $userid )	{
		global $db, $config, $lang;
		if($userid == 'all'){
			return t('Verschiedene');	
		}else {
			$get_username = $db->query_first("SELECT username FROM {$config["tables"]["user"]} WHERE userid = '$userid'");
			return $get_username["username"];
		}
	}
	
	function GetUserdata( $userid )	{
		global $db, $config, $lang, $party;
		if($userid == 'all'){
			return t('Verschiedene');	
		}else {
	$get_userdata = $db->query_first("SELECT u.*, s.ip FROM {$config["tables"]["user"]} AS u
      								LEFT JOIN {$config["tables"]["seat_seats"]} AS s ON s.userid = u.userid
      								LEFT JOIN {$config["tables"]["seat_block"]} AS b ON b.blockid = s.blockid
      WHERE u.userid = '$userid' AND b.party_id = '$party->party_id'");
	return $get_userdata;
		}
	}
		
	function GetDate( $time ) {
		global $func;

		if ($this->config['datetime_format']=='') return $func->unixstamp2date( $time , "datetime"); 
		else return $func->unixstamp2date( $time , $this->config['datetime_format']); 
	}

	function sql(){
		global $db, $config;
		// Suchstring erstellen
		if($_POST['search_input'][0] != ""){
			$config['search_fields'][]  = "p.caption";
			$config['search_type'][]    = "like";
			$config['search_fields'][]  = "s.supp_id";
			$config['search_type'][]    = "exact";
			$config['search_fields'][]  = "a.status";
			$config['search_type'][]    = "exact";
			$config['search_fields'][]  = "a.userid";
			$config['search_type'][]    = "exact";

			$search .= "(";

			$key_1337 = $key;
			$key_1337 = str_replace ("o", "(o|0)", $key_1337);
			$key_1337 = str_replace ("O", "(O|0)", $key_1337);
			$key_1337 = str_replace ("l", "(l|1|\\\\||!)", $key_1337);
			$key_1337 = str_replace ("L", "(L|1|\\\\||!)", $key_1337);
			$key_1337 = str_replace ("e", "(e|3|€)", $key_1337);
			$key_1337 = str_replace ("E", "(E|3|€)", $key_1337);
			$key_1337 = str_replace ("t", "(t|7)", $key_1337);
			$key_1337 = str_replace ("T", "(T|7)", $key_1337);
			$key_1337 = str_replace ("a", "(a|@)", $key_1337);
			$key_1337 = str_replace ("A", "(A|@)", $key_1337);
			$key_1337 = str_replace ("s", "(s|5|$)", $key_1337);
			$key_1337 = str_replace ("S", "(S|5|$)", $key_1337);
			$key_1337 = str_replace ("z", "(z|2)", $key_1337);
			$key_1337 = str_replace ("Z", "(Z|2)", $key_1337);

			$d = 0;
			foreach ($config['search_fields'] as $col) {
				switch ($config['search_type'][$d]) {
					case "exact": $search .= "($col = '$key') OR "; break;
					case "1337": $search .= "($col REGEXP '$key_1337') OR "; break;
					default: $search .= "($col LIKE '%$key%') OR "; break;
				}
				$d ++;
			}
			$search = substr($search, 0, strlen($search) - 4);

			$search .= ") AND ";
		}


		if (strtolower($_POST['search_dd_input'][0]) != ""){
			$search .= "a.status = " . $_POST['search_dd_input'][0] . " AND ";
		}
		
		if (strtolower($_POST['search_dd_input'][1]) != ""){
			$search .= "s.supp_id = " . $_POST['search_dd_input'][1] . " AND ";
		}

		if (strtolower($_POST['search_dd_input'][2]) != ""){
			$search .= "a.partyid = " . $_POST['search_dd_input'][2] . " AND ";
		}

		$search .= "1";

		$sql = "SELECT a.*, p.*, s.* FROM {$config['tables']['food_ordering']} AS a
								LEFT JOIN {$config['tables']['food_product']} AS p ON a.productid = p.id
								LEFT JOIN {$config['tables']['food_supp']} AS s ON p.supp_id = s.supp_id
				WHERE $search
				ORDER BY p.caption ASC";

		$result = $db->query($sql);

		while ($data = $db->fetch_array($result)) {
			unset($row_temp);
			unset($userdata);
			$userdata = $this->GetUserdata($data['userid']);
			$row_temp['supp_name'] 			= $data['name'];
			$row_temp['supp_info'] 			= $data['supp_infos'];
			$row_temp['product_caption'] 	= $data['caption']; 
			$row_temp['username'] 			= $userdata['username'];
			$row_temp['userip'] 			= $userdata['ip'];
			$row_temp['usercomment']		= $userdata['comment'];
			$row_temp['product_option'] 	= $this->GetFoodoption($data['opts']);
			$row_temp['order_count']		= $data['pice'];
			$row_temp['ordertime']			= $this->GetDate($data['ordertime']);
			$row_temp['lastchange']			= $this->GetDate($data['lastchange']);
			$row_temp['supplytime']			= $this->GetDate($data['supplytime']);
			$row_temp['status']				= $data['status'];

			$this->fetch_row($row_temp);
		}
	}
}


?>