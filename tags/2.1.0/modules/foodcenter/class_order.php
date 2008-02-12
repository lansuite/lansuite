<?php




class ordered {
	
	var $orders;
	
	function read_orders($userid){
		global $db,$config;
		
		$result = $db->query("SELECT * FROM {$config['tables']['food_ordering']} WHERE userid={$userid} GROUP BY ordertime ORDER BY ordertime");	

		while ($row = $db->fetch_array($result)){
			$orders[] .= $row['ordertime'];
		}
		
		
	}
	
	function read_user_ordered($userid){
		$query = $db->query("SELECT * FROM {$config['tables']['food_ordering']} WHERE status = 1 AND userid={$auth['userid']}");	
		
	}
}


?>