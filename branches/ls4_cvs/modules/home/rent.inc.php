<?php

// Rent
	$user_id = $_SESSION["auth"]["userid"];
	$rentuser = $db->query("SELECT rs.caption, rs.comment FROM {$config["tables"]["rentuser"]} AS ru LEFT JOIN {$config["tables"]["rentstuff"]} AS rs ON rs.stuffid=ru.stuffid WHERE ru.userid=$user_id");
	if($db->num_rows($rentuser) > 0) {
		while($row = $db->fetch_array($rentuser)) {

		$text = $row["caption"];
		if($row["comment"]<>"") { $text = $text." (".$row["comment"].")"; }
		$templ['home']['show']['row']['control']['link']	= "";	// set var to NULL
		$templ['home']['show']['row']['info']['text']		= "";	// set var to NULL
		$templ['home']['show']['row']['info']['text2']		= $text;
	 
		$templ['home']['show']['rent']['control']['row'] .= $dsp->FetchModTpl("home", "show_row");
		eval("\$templ['home']['show']['rent']['control']['row'] .= \"". $func->gettemplate("home_show_row")."\";");	
		}
	$templ['home']['show']['case']['control']['row'] .= $dsp->FetchModTpl("home", "show_rent");
	}
// Rent ENDE		

?>
