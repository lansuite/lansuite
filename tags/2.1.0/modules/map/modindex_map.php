<?php
	// Here is, where you define, which page should be loaded according to submitted action

	switch ($vars["action"]) {
	
		default:
			include ("modules/map/show.php");
		break;
	}
?>