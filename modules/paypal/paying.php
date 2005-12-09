<?php

//posts transaction data using fsockopen.
function fsockPost($url,$data) {

	//Parse url
	$web=parse_url($url);

	//build post string
	foreach($data as $i=>$v) {
		$postdata.= $i . "=" . urlencode($v) . "&";
	}

	$postdata.="cmd=_notify-validate";

	//Set the port number
	if($web[scheme] == "https") { $web[port]="443";  $ssl="ssl://"; } else { $web[port]="80"; }
	
	//Create paypal connection
	$fp=fsockopen($ssl . $web[host],$web[port],$errnum,$errstr,30);

	//Error checking
	if(!$fp) { echo "$errnum: $errstr"; }

	//Post Data
	else {

		fputs($fp, "POST $web[path] HTTP/1.1\r\n");
		fputs($fp, "Host: $web[host]\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $postdata . "\r\n\r\n");

		//loop through the response from the server
		while(!feof($fp)) { $info[]=@fgets($fp, 1024); }

		//close fp - we are done with it
		fclose($fp);

		//break up results into a string
		$info=implode(",",$info);

	}

	return $info;

}


function create_csv_file($file,$data) {

	//check for array
	if(is_array($data)) { $post_values=array_values($data);

	//build csv data
	foreach($post_values as $i) { $csv.="\"$i\","; }

	//remove the last comma from string
	$csv=substr($csv,0,-1);

	//check for existence of file
	if(file_exists($file) && is_writeable($file)) { $mode="a"; } else { $mode="w"; }

	//create file pointer
	$fp=fopen($file,"a");

	//write to file
	fwrite($fp,$csv . "\n");

	//close file pointer
	fclose($fp);

	return true;

	}

	else { return false; }

}

function check_transaction($verify_file,$checked_file,$verify_id,$item_id){
	$data = @file($verify_file);
	$check = @file($checked_file);
	
	
	$data = array_reverse($data);
	
	// Check Files
	if(isset($data) && isset($check)){
		// Check for Transfer
		foreach ($data as $line){
			if(strstr($line,$verify_id) && strstr($line,$item_id)){
				$found = true;
				break;
			}
		}
		
		// Check for double Transfer
		if($found){
			$fp = @fopen($checked_file,"a");
			$found_double = false;
			foreach ($check as $line){
				if(strstr($line,$verify_id) && strstr($line,$item_id)){
					$found_double = true;
				}
			}
		}
		
		// Write for double check and get true
		if($found == true && $found_double == false){
			@fwrite($fp,$verify_id . " " . $item_id . "\n");
			@fclose($fp);
			return true;
		}else{
			return false;
		}
		
	}else{
		return false;
	}
}


	switch ($_GET['action']){
		case 2:
			if($_POST['firstname'] == "") 	$error_pay['firstname'] = $lang['paypal']['error_firstname'];
			if($_POST['lastname'] == "") 	$error_pay['lastname'] = $lang['paypal']['error_lastname'];
			if($_POST['email'] == "") 		$error_pay['email'] = $lang['paypal']['error_email'];
					
			if(isset($error_pay)) $_GET['action'] = 1;
			
		break;	
		
	}



	switch ($_GET['action']){
		case 1:
			$_POST['price'] = unserialize(urldecode($_POST['price']));
			$_POST['depot'] = unserialize(urldecode($_POST['depot']));
		
		default:
			if(!isset($_POST['email']) && $auth['userid'] != 0){
				$row = $db->query_first("SELECT * FROM {$config["tables"]["user"]} WHERE userid={$auth['userid']}");
				$_POST['firstname'] = $row['firstname'];
				$_POST['lastname'] = $row['name'];
				$_POST['street'] = $row['street'];
				$_POST['city'] = $row['city'];
				$_POST['zip'] = $row['plz'];
				$_POST['email'] = $auth['email'];
			}
			$price = 0;
			// Party price
			if(isset($_POST['price'])){
				$_SESSION['paypal']['price'] = $_POST['price'];
				foreach ($_POST['price'] as $price_id){
					$price_db = $db->query_first("SELECT * FROM {$config["tables"]["party_prices"]} WHERE price_id='$price_id'");
					$price = $price + $price_db['price'];
					if(isset($_POST['depot']) && in_array($price_id,$_POST['depot'])){
						$_SESSION['paypal']['depot'] = $_POST['depot'];
						$price = $price + $price_db['depot_price'];
					}
				}
			}
			// Catering
			$price = $price + $_POST['catering'];
			// Donation
			$price = $price + $_POST['donation'];
			
			if($price == 0){
				$func->error($lang["paypal"]["no_price_error"],"\" OnClick=\"javascript: refreshParent()");
			}else{
				
				$dsp->NewContent($lang["paypal"]["data_caption"],$lang["paypal"]["data_subcaption"]);
				$dsp->AddModTpl("paypal","javascript");
				$dsp->SetForm("base.php?mod=paypal&action=2");
				$dsp->AddTextFieldRow("firstname",$lang["paypal"]["firstname"],$_POST['firstname'],$error_pay['firstname']);
				$dsp->AddTextFieldRow("lastname",$lang["paypal"]["lastname"],$_POST['lastname'],$error_pay['lastname']);
				$dsp->AddTextFieldRow("street",$lang["paypal"]["street"],$_POST['street'],$error_pay['street']);
				$dsp->AddTextFieldRow("city",$lang["paypal"]["city"],$_POST['city'],$error_pay['city']);
				$dsp->AddTextFieldRow("zip",$lang["paypal"]["zip"],$_POST['zip'],$error_pay['zip']);	
				$dsp->AddTextFieldRow("email",$lang["paypal"]["email"],$_POST['email'],$error_pay['email']);
				$dsp->AddDoubleRow($lang["paypal"]["price"],$price . " " . $cfg['paypal_currency_code']);
				$dsp->AddSingleRow("<font color=\"red\">" .$lang["paypal"]["send_warning"]	.
									"</font><input type=\"hidden\" name=\"price_text\" value=\"$price\">
									<input type=\"hidden\" name=\"price\" value=\"" . urlencode(serialize($_POST['price'])) . "\">
									<input type=\"hidden\" name=\"depot\" value=\"" . urlencode(serialize($_POST['depot'])) . "\">
									<input type=\"hidden\" name=\"catering\" value=\"" . $_POST['catering'] . "\">
									<input type=\"hidden\" name=\"donation\" value=\"" . $_POST['donation'] . "\">");
				$dsp->AddFormSubmitRow("next");
				$dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
				$dsp->AddContent();
			}
			
			
		break;
		
		case 2:
			$item_number = rand();
			$_SESSION['paypal']['catering'] = $_POST['catering'];
			$_SESSION['paypal']['donation'] = $_POST['donation'];
			$_SESSION['paypal']['item_number'] = $item_number;
			
			
			$templ['index']['info']['action'] = "document.paypal_form.submit();";
			$dsp->NewContent($lang["paypal"]["send_caption"],$lang["paypal"]["send_subcaption"]);
			$dsp->SetForm($cfg['paypal_url'],"paypal_form");
			$dsp->AddSingleRow("<font color=\"red\">" . $lang["paypal"]["on_moment"] . "</font>");
			$dsp->AddSingleRow("
			<!-- PayPal Configuration --> 
					<input type=\"hidden\" name=\"business\" value=\"{$cfg['paypal_business']}\"> 
					<input type=\"hidden\" name=\"cmd\" value=\"_xclick\"> 
					<input type=\"hidden\" name=\"image_url\" value=\"\">
					<input type=\"hidden\" name=\"return\" value=\"{$cfg['paypal_site_url']}/base.php?mod=paypal&action=3\">
					<input type=\"hidden\" name=\"cancel_return\" value=\"{$cfg['paypal_site_url']}/base.php?mod=paypal&action=10\">
					<input type=\"hidden\" name=\"notify_url\" value=\"{$cfg['paypal_site_url']}/base.php?mod=paypal&action=5\">
					<input type=\"hidden\" name=\"rm\" value=\"0\">
					<input type=\"hidden\" name=\"currency_code\" value=\"{$cfg['paypal_currency_code']}\">
					<input type=\"hidden\" name=\"lc\" value=\"DE\">
					<input type=\"hidden\" name=\"bn\" value=\"toolkit-php\">
					<input type=\"hidden\" name=\"cbt\" value=\"Next >>\">
					<!-- Payment Page Information --> 
					<input type=\"hidden\" name=\"no_shipping\" value=\"1\">
					<input type=\"hidden\" name=\"no_note\" value=\"1\">
					<input type=\"hidden\" name=\"cn\" value=\"{$lang['paypal']['comment']}\">
					<!-- Customer Information --> 
					<input type=\"hidden\" name=\"first_name\" value=\"{$_POST['firstname']}\">
					<input type=\"hidden\" name=\"last_name\" value=\"{$_POST['lastname']}\">
					<input type=\"hidden\" name=\"address1\" value=\"{$_POST['street']}\">
					<input type=\"hidden\" name=\"city\" value=\"{$_POST['city']}\">
					<input type=\"hidden\" name=\"zip\" value=\"{$_POST['zip']}\">
					<input type=\"hidden\" name=\"email\" value=\"{$_POST['email']}\">
					<!-- Product Information --> 
					<input type=\"hidden\" name=\"item_name\" value=\"{$cfg['paypal_desc_name']}\">
					<input type=\"hidden\" name=\"item_number\" value=\"$item_number\">
					<input type=\"hidden\" name=\"amount\" value=\"{$_POST['price_text']}\">");
			$dsp->CloseForm();
			$dsp->AddContent();
			
		break;

		case 3:

			$check = check_transaction("ext_inc/paypal/ipn_success.txt.php","ext_inc/paypal/ipn_checked.txt.php",$_POST['verify_sign'],$_POST['item_number']);
			
			if($_POST['item_number'] == $_SESSION['paypal']['item_number'] && $check){

				if(isset($_SESSION['paypal']['price'])){
					foreach ($_SESSION['paypal']['price'] as $price_id){
						$db->query("UPDATE {$config["tables"]["party_user"]} SET paid='1' WHERE user_id={$auth['userid']} AND price_id={$price_id}");
						if(isset($_SESSION['paypal']['depot']) && in_array($price_id,$_SESSION['paypal']['depot'])){
							$db->query("UPDATE {$config["tables"]["party_user"]} SET seatcontrol='1' WHERE user_id={$auth['userid']} AND price_id={$price_id}");
						}
					}
				}
				
				if($_SESSION['paypal']['catering'] > 0){
					$db->query("INSERT INTO {$config["tables"]["catering_accounting"]} SET userID='".$auth["userid"]."', actiontime=NOW(), comment=\"PAYPAL:  {$_POST['payment_date']} {$_POST['txn_id']}\", movement='".$_SESSION['paypal']['catering']."'");
				}
				
				$dsp->NewContent($lang["paypal"]["success_caption"]);
				$dsp->AddModTpl("paypal","javascript");
				$dsp->AddSingleRow($lang["paypal"]["success_text"]);
				$dsp->AddDoubleRow($lang["paypal"]["firstname"],$_POST['first_name']);
				$dsp->AddDoubleRow($lang["paypal"]["lastname"],$_POST['last_name']);
				$dsp->AddDoubleRow($lang["paypal"]["email"],$_POST['payer_email']);
				$dsp->AddDoubleRow($lang["paypal"]["ordernr"],$_POST['txn_id']);
				$dsp->AddDoubleRow($lang["paypal"]["date"],$_POST['payment_date']);
				$dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
				$dsp->AddContent();
			}else{
				$dsp->NewContent($lang["paypal"]["admin_caption"]);
				$dsp->AddModTpl("paypal","javascript");
				$dsp->AddSingleRow("<font color=\"red\">" . $lang["paypal"]["admin_text"] . "</font>");
				$dsp->AddDoubleRow($lang["paypal"]["firstname"],$_POST['first_name']);
				$dsp->AddDoubleRow($lang["paypal"]["lastname"],$_POST['last_name']);
				$dsp->AddDoubleRow($lang["paypal"]["email"],$_POST['payer_email']);
				$dsp->AddDoubleRow($lang["paypal"]["ordernr"],$_POST['txn_id']);
				$dsp->AddDoubleRow($lang["paypal"]["date"],$_POST['payment_date']);
				$dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
				$dsp->AddContent();
			}
		break;
		
		case 5:
			$result=fsockPost($cfg['paypal_url'],$_POST); 
			
			if(eregi("VERIFIED",$result)){ 
				create_csv_file("ext_inc/paypal/ipn_success.txt.php",$_POST);
			}else{ 
				create_csv_file("ext_inc/paypal/ipn_error.txt.php",$_POST);
			} 
				
		break;
		
		case 10:
			$dsp->NewContent($lang["paypal"]["error_caption"]);
			$dsp->AddSingleRow($lang["paypal"]["error_text"] );
			$dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
			$dsp->AddContent();
		
		break;
		
		
	}
	
$func->templ_output($dsp->FetchModTpl("paypal","sendbox"));
?>

