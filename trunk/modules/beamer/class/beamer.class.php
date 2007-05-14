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
  	  	$update = $db->query( "UPDATE {$config['tables']['beamer_content']} SET lastView = '0' WHERE bcid = '$bcid' LIMIT 1");
  }
 
 
  function toggleActive ( $bcid ) {
  global $config, $db;
  	$row = $db->query_first( "SELECT active FROM {$config["tables"]["beamer_content"]} WHERE bcID = '$bcid' ");
	$active = $row['active'];
	
	if( $active == "1" ) {
		$insert_sql = $db->query("UPDATE {$config["tables"]["beamer_content"]} SET active = '0' WHERE bcID = '$bcid' LIMIT 1");
	} else {
		$insert_sql = $db->query("UPDATE {$config["tables"]["beamer_content"]} SET active = '1' WHERE bcID = '$bcid' LIMIT 1");	
	}
  }


  function toggleBeamerActive ( $bcid , $beamerid ) {
  global $config, $db;
    if( $beamerid == "" ) { return; }
  	$row = $db->query_first( 'SELECT b'.$beamerid.' As active FROM ' . $config["tables"]["beamer_content"] . ' WHERE bcID = ' . $bcid );
	$active = $row['active'];
	if( $active == "1" ) {
		$insert_sql = $db->query("UPDATE {$config["tables"]["beamer_content"]} SET b$beamerid = '0' WHERE bcID = $bcid LIMIT 1");
	} else {
		$insert_sql = $db->query("UPDATE {$config["tables"]["beamer_content"]} SET b$beamerid = '1' WHERE bcID = $bcid LIMIT 1");	
	}
  }

  function deleteContent ( $bcid ) {
  global $db, $config;
  
  	$delete =  $db->query("DELETE FROM {$config["tables"]["beamer_content"]} WHERE bcID = '$bcid' LIMIT 1");
  
  }
  

  function saveContent ( $c ) {
  global $config, $db;

	$lastview = time();
  	if ( !$c['bcid'] ) 
	{
	  	$insert = $db->query( 	"INSERT INTO {$config['tables']['beamer_content']} SET ".
								"caption = '{$c['caption']}', maxRepeats = '{$c['maxrepeats']}', ".
								"contentType = '{$c['type']}', lastView = '$lastview' , contentData = '{$c['text']}' ");
	} else {
	  	$update = $db->query( 	"UPDATE {$config['tables']['beamer_content']} SET contentData = '{$c['text']}' WHERE bcid = '$bcid' ");
	
	
	}
  
  }

  
  function getContent( $bcid ) {
  global $db, $config, $func;  
  	$row = $db->query_first(" SELECT * FROM {$config['tables']['beamer_content']} WHERE bcid = '$bcid' LIMIT 1");
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
	$update = $db->query('UPDATE ' . $config["tables"]["beamer_content"].' SET lastView = '.time().' WHERE bcID = '.$row['bcID'].' LIMIT 1');
	return $row['contentData'];
  
  
  }  
  
  
  
}


?>