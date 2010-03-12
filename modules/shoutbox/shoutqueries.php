<?php
$framework->set_modus('ajax');

switch($_GET['shout']) {
    case 'add': 
      $_POST['nickname'] = htmlentities($_POST['nickname']);
      $_POST['message'] = htmlentities($_POST['message']); 
     
     if($_POST['nickname'] and $_POST['message'])
     {
     	$result = $db->qry("INSERT INTO %prefix%shoutbox (name, message) VALUES (%string%,%string%)",$_POST["nickname"],$_POST["message"]);
		$resp =  $db->qry_first("SELECT created FROM %prefix%shoutbox WHERE id = %int%",$db->insert_id());
		
		$data['response'] = 'Good work';
		$data['nickname'] = $_POST['nickname'];
		$data['message'] = $_POST['message'];
		$data['time'] = strtotime($resp['created']);
      }
    break;
    
    case 'view':
      $data = array();

      if(!$_GET['time'])
        $_GET['time'] = 0;
  
  		$mysqldate = date( 'Y-m-d H:i:s', $_GET['time'] );
      	$qry = $db->qry('SELECT * FROM %prefix%shoutbox WHERE created > %string% ORDER BY ID DESC LIMIT %int%',$mysqldate,$cfg['shout_entries']);

		while ($row = $db->fetch_array($qry)) {
			$data[] = array(
					"message"=>$row['message'],
					"nickname"=>$row['name'],
					"time"=>strtotime($row['created']),
					"cfgentries"=>$cfg['shout_entries']
					);
		}
		$data = array_reverse($data);
		$db->free_result($qry);   
    break;
  }
  
  require_once('ext_scripts/json/json.php');
  $json = new Services_JSON();
  $out = $json->encode($data);
  print $out;
?>