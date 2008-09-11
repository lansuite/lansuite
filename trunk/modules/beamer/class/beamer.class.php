<?php

 if( !VALID_LS ) { die("Direct access not allowed!"); } // Direct-Call-Check

class beamer {


  // Constructor
  function __construct() {
/*
  	 echo " ACTION: ".$_GET['action'];
	 echo " BeamerID: ".$_GET['beamerid'];
	 * 
	 * 
*/  

  }
  
  function __destruct() {
  }
  
  
  function countSQL($where) {
  global $db, $config;
   $row = $db->query_first( "SELECT count(bcID) as n FROM {$config['tables']['beamer_content']} " . $where );
   return $row['n']; 
  }
  
  function countContent( $status = NULL, $beamerid = null ) { 			// Liefert die Anzahl der Inhalte mit Wahlmöglichkeit vom Aktiv-Status
	if( $beamerid ) { $beamerid_sql = " b$beamerid = '1' "; } else { $beamerid_sql = ""; }
  	switch ($status) {
		case ""  : $status_sql = ""; break;
	    case "1" : $status_sql = " active = '1' "; break;
		case "0" : $status_sql = " active = '0' "; break;
	}
	
	if( ($status_sql) AND ($beamerid_sql) ) $and_sql = " AND ";
	if( ($status_sql) OR ($beamerid_sql) ) {
		$add_sql = " WHERE " . $status_sql . $and_sql . $beamerid_sql;	
	} else { $add_sql = ""; }
	return $this->countSQL( $add_sql );
  }

 
  function set2first( $bcid ) {
  global $config, $db; 
  	  	$update = $db->qry("UPDATE %prefix%beamer_content SET lastView = '0' WHERE bcid = %int% LIMIT 1", $bcid);
  }
 
 
  function toggleActive ( $bcid ) {
  global $config, $db;
  	$row = $db->qry_first("SELECT active FROM %prefix%beamer_content WHERE bcID = %int%", $bcid);
	$active = $row['active'];
	
	if( $active == "1" ) {
		$insert_sql = $db->qry("UPDATE %prefix%beamer_content SET active = '0' WHERE bcID = %int% LIMIT 1", $bcid);
	} else {
		$insert_sql = $db->qry("UPDATE %prefix%beamer_content SET active = '1' WHERE bcID = %int% LIMIT 1", $bcid);	
	}
  }


  function toggleBeamerActive ( $bcid , $beamerid ) {
  global $config, $db;
    if( $beamerid == "" ) { return; }
  	$row = $db->query_first( 'SELECT b'.$beamerid.' As active FROM ' . $config["tables"]["beamer_content"] . ' WHERE bcID = ' . $bcid );
	$active = $row['active'];
	if( $active == "1" ) {
		$insert_sql = $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '0' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);
	} else {
		$insert_sql = $db->qry("UPDATE %prefix%beamer_content SET b%plain% = '1' WHERE bcID = %int% LIMIT 1", $beamerid, $bcid);	
	}
  }

  function deleteContent ( $bcid ) {
  global $db, $config;
  
  	$delete =  $db->qry("DELETE FROM %prefix%beamer_content WHERE bcID = %int% LIMIT 1", $bcid);
  
  }
  

  function saveContent ( $c ) {
  global $config, $db;
  
	$lastview = time();
  	if ( !$c['bcid'] ) 
	{
	  	$insert = $db->qry("INSERT INTO %prefix%beamer_content SET caption = %string%, maxRepeats = %string%, contentType = %string%, lastView = %string%, contentData = %string%",
  $c['caption'], $c['maxrepeats'], $c['type'], $lastview, $c['text']);
	} else {
	  	if ( $c['caption'] != "" ) { $caption_sql = " , caption = '{$c['caption']}' ";  }
		$update = $db->qry("UPDATE %prefix%beamer_content SET contentData = %string% %plain% WHERE bcid = %int%", $c['text'], $caption_sql, $c['bcid']);
	
	}
  
  }

  
  function getContent( $bcid ) {
  global $db, $config, $func;  
  	$row = $db->qry_first("SELECT * FROM %prefix%beamer_content WHERE bcid = %int% LIMIT 1", $bcid);
	return $row;
  
  }
  
  
  /*	System: Es wird immer der älteste Eintrag angezeigt und bei jedem Anzeigen der Counter gezählt. Ist der Counter auf Null bekommt der 
  * 	Eintrag den aktuellen Zeitstempler und der Counter geht wieder auf sein max-wert.
  *     Die Übergabe des Content muss hinsichtlich anderen Medien noch angepasst werden. Mit TEXT passt das erstmal so.
  */
  
  function getCurrentContent ( $beamerid ) {
  global $db, $config, $func;
  
  	$row = $db->query_first('SELECT * FROM ' . $config["tables"]["beamer_content"] . ' WHERE active = 1 AND b'.$beamerid.' = 1  '.
							'ORDER BY lastView ASC');
	$update = $db->query('UPDATE %prefix%beamer_content SET lastView = %int% WHERE bcID = %int% LIMIT 1', time(), $row['bcID']);
	
	
	switch ( $row['contentType'] ) {
	
		case 'text': 	return $row['contentData']; break;
		case 'wrapper':
							
							$arr = explode( "*" , $row['contentData'] );
							// $oframe = "<center><object data=\"{$arr[0]}\" type=\"application/x-shockwave-flash\"></center>"; // type="image/svg+xml" width="200" height="200"
							$iframe = "<center><iframe src=\"{$arr[0]}\" frameborder=\"0\" width=\"{$arr[2]}\" height=\"{$arr[1]}\"></iframe></center>";
							// $eframe = "<embed src=\"{$arr[0]}\" swLiveConnect=\"false\" type=\"application/x-shockwave-flash\" ".
							//		  "pluginspage=\"http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"></embed>";
							return $iframe;
		
						break;

		case 'turnier':		$t = "<center><h2>" . $row['caption'] . "</h2><img src=\"ext_inc/tournament_trees/tournament_{$row['contentData']}.png\" border=\"0\"><p />";
							return $t;
						break;
	
	}
  
  
  }  
  
  
  function getAllTournamentsAsOptionList() {
  global $db, $config, $func;
    
  	$result = $db->qry('SELECT tournamentid, name FROM %prefix%tournament_tournaments');
	while ($row = $db->fetch_array($result))
	{
		$tournaments[] = "<option value=\"{$row['tournamentid']}\">{$row['name']}</option>";
	}
	return $tournaments;
  }
  
  function getTournamentNamebyID( $ctid ) {
  global $db, $config, $func;  
   	$result = $db->query_first('SELECT name FROM ' . $config["tables"]["tournament_tournaments"] . " WHERE tournamentid ='$ctid'" );
    return $result['name'];
  }
  
}


?>
