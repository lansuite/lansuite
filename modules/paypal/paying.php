<?php

//posts transaction data using fsockopen.
function fsockPost($url, $data)
{

    //Parse url
    $web=parse_url($url);

    //build post string
    foreach ($data as $i => $v) {
        $postdata.= $i . "=" . urlencode($v) . "&";
    }

    $postdata.="cmd=_notify-validate";

    //Set the port number
    if ($web[scheme] == "https") {
        $web[port]="443";
        $ssl="ssl://";
    } else {
        $web[port]="80";
    }
    
    //Create paypal connection
    $fp=fsockopen($ssl . $web[host], $web[port], $errnum, $errstr, 30);

    //Error checking
    if (!$fp) {
        echo "$errnum: $errstr";
    } else {
	//Post Data
        fputs($fp, "POST $web[path] HTTP/1.1\r\n");
        fputs($fp, "Host: $web[host]\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $postdata . "\r\n\r\n");

        //loop through the response from the server
        while (!feof($fp)) {
            $info[]=@fgets($fp, 1024);
        }

        //close fp - we are done with it
        fclose($fp);

        //break up results into a string
        $info=implode(",", $info);
    }

    return $info;
}


function create_csv_file($file, $data)
{

    //check for array
    if (is_array($data)) {
        $post_values=array_values($data);

    //build csv data
        foreach ($post_values as $i) {
            $csv.="\"$i\",";
        }

    //remove the last comma from string
        $csv=substr($csv, 0, -1);

    //check for existence of file
        if (file_exists($file) && is_writeable($file)) {
            $mode="a";
        } else {
            $mode="w";
        }

    //create file pointer
        $fp=fopen($file, "a");

    //write to file
        fwrite($fp, $csv . "\n");

    //close file pointer
        fclose($fp);

        return true;
    } else {
        return false;
    }
}

function check_transaction($verify_file, $checked_file, $verify_id, $item_id)
{
    $data = @file($verify_file);
    $check = @file($checked_file);
    
    
    $data = array_reverse($data);
    
    // Check Files
    if (isset($data) && isset($check)) {
        // Check for Transfer
        foreach ($data as $line) {
            if (strstr($line, $verify_id) && strstr($line, $item_id)) {
                $found = true;
                break;
            }
        }
        
        // Check for double Transfer
        if ($found) {
            $fp = @fopen($checked_file, "a");
            $found_double = false;
            foreach ($check as $line) {
                if (strstr($line, $verify_id) && strstr($line, $item_id)) {
                    $found_double = true;
                }
            }
        }
        
        // Write for double check and get true
        if ($found == true && $found_double == false) {
            @fwrite($fp, $verify_id . " " . $item_id . "\n");
            @fclose($fp);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


switch ($_GET['step']) {
    case 2:
        if ($_POST['firstname'] == "") {
            $error_pay['firstname'] = t('Bitte geben sie einen Vornamen ein');
        }
        if ($_POST['lastname'] == "") {
            $error_pay['lastname'] = t('Bitte geben sie einen Namen ein');
        }
        if ($_POST['email'] == "") {
            $error_pay['email'] = t('Bitte geben sie eine Email ein');
        }
                    
        if (isset($error_pay)) {
            $_GET['step'] = 1;
        }
            
        break;
}



switch ($_GET['step']) {
    case 1:
        $_POST['price'] = unserialize(urldecode($_POST['price']));
        $_POST['depot'] = unserialize(urldecode($_POST['depot']));
        
    default:
        if (!isset($_POST['email']) && $auth['userid'] != 0) {
            $row = $db->qry_first("SELECT * FROM %prefix%user WHERE userid=%int%", $auth['userid']);
            $_POST['firstname'] = $row['firstname'];
            $_POST['lastname'] = $row['name'];
            $_POST['street'] = $row['street'];
            $_POST['city'] = $row['city'];
            $_POST['zip'] = $row['plz'];
            $_POST['email'] = $auth['email'];
        }
        $price = 0;
        // Party price
        if (isset($_POST['price'])) {
            $_SESSION['paypal']['price'] = $_POST['price'];
            foreach ($_POST['price'] as $price_id) {
                $price_db = $db->qry_first("SELECT * FROM %prefix%party_prices WHERE price_id=%int%", $price_id);
                $price = $price + $price_db['price'];
                if (isset($_POST['depot']) && in_array($price_id, $_POST['depot'])) {
                    $_SESSION['paypal']['depot'] = $_POST['depot'];
                    $price = $price + $price_db['depot_price'];
                }
            }
        }
        // Catering
        $price = $price + $_POST['catering'];
        // Donation
        $price = $price + $_POST['donation'];
            
        if ($price <= 0) {
            $func->error(t('Kein Preis ausgew&auml;hlt'), "\" OnClick=\"javascript: refreshParent()");
        } else {
            $dsp->NewContent(t('Daten eingeben'), t('Bitte geben sie ihre Zahlungsdaten ein.'));
            $dsp->AddSmartyTpl('javascript', 'paypal');
            $dsp->SetForm("index.php?mod=paypal&action=paying&design=base&step=2");
            $dsp->AddTextFieldRow("firstname", t('Vorname'), $_POST['firstname'], $error_pay['firstname']);
            $dsp->AddTextFieldRow("lastname", t('Nachname'), $_POST['lastname'], $error_pay['lastname']);
            $dsp->AddTextFieldRow("street", t('Strasse'), $_POST['street'], $error_pay['street']);
            $dsp->AddTextFieldRow("city", t('Stadt'), $_POST['city'], $error_pay['city']);
            $dsp->AddTextFieldRow("zip", t('PLZ'), $_POST['zip'], $error_pay['zip']);
            $dsp->AddTextFieldRow("email", t('E-Mail'), $_POST['email'], $error_pay['email']);
            $dsp->AddDoubleRow(t('Zu &uuml;berweisender Betrag'), $price . " " . $cfg['paypal_currency_code']);
            $dsp->AddSingleRow("<font color=\"red\">" .t('Achtung mit dem klicken auf weiter wird die Zahlung durchgef&uuml;hrt')    .
                            "</font><input type=\"hidden\" name=\"price_text\" value=\"$price\">
									<input type=\"hidden\" name=\"price\" value=\"" . urlencode(serialize($_POST['price'])) . "\">
									<input type=\"hidden\" name=\"depot\" value=\"" . urlencode(serialize($_POST['depot'])) . "\">
									<input type=\"hidden\" name=\"catering\" value=\"" . $_POST['catering'] . "\">
									<input type=\"hidden\" name=\"donation\" value=\"" . $_POST['donation'] . "\">");
            $dsp->AddFormSubmitRow(t('Weiter'));
            $dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
            $dsp->AddContent();
        }
            
            
        break;
        
    case 2:
        $item_number = rand();
        $_SESSION['paypal']['catering'] = $_POST['catering'];
        $_SESSION['paypal']['donation'] = $_POST['donation'];
        $_SESSION['paypal']['item_number'] = $item_number;
            
            
        $smarty->assign('action', "document.paypal_form.submit();");
        $dsp->NewContent(t('Senden der Daten'), t('Die Daten werden gesendet einen Moment bitte'));
        $dsp->SetForm($cfg['paypal_url'], "paypal_form");
        $dsp->AddSingleRow("<font color=\"red\">" . t('Einen Moment Bitte die Daten werden gesendet') . "</font>");
        $dsp->AddSingleRow("
			<!-- PayPal Configuration --> 
					<input type=\"hidden\" name=\"business\" value=\"{$cfg['paypal_business']}\"> 
					<input type=\"hidden\" name=\"cmd\" value=\"_xclick\"> 
					<input type=\"hidden\" name=\"image_url\" value=\"\">
					<input type=\"hidden\" name=\"return\" value=\"{$cfg['paypal_site_url']}/index.php?mod=paypal&action=paying&design=base&step=3\">
					<input type=\"hidden\" name=\"cancel_return\" value=\"{$cfg['paypal_site_url']}/index.php?mod=paypal&action=paying&design=base&step=10\">
					<input type=\"hidden\" name=\"notify_url\" value=\"{$cfg['paypal_site_url']}/index.php?mod=paypal&action=paying&design=base&step=5\">
					<input type=\"hidden\" name=\"rm\" value=\"0\">
					<input type=\"hidden\" name=\"currency_code\" value=\"{$cfg['paypal_currency_code']}\">
					<input type=\"hidden\" name=\"lc\" value=\"DE\">
					<input type=\"hidden\" name=\"bn\" value=\"toolkit-php\">
					<input type=\"hidden\" name=\"cbt\" value=\"Next >>\">
					<!-- Payment Page Information --> 
					<input type=\"hidden\" name=\"no_shipping\" value=\"1\">
					<input type=\"hidden\" name=\"no_note\" value=\"1\">
					<input type=\"hidden\" name=\"cn\" value=\"".t('Kommentar')."\">
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
        $check = check_transaction("ext_inc/paypal/ipn_success.txt.php", "ext_inc/paypal/ipn_checked.txt.php", $_POST['verify_sign'], $_POST['item_number']);
            
        if ($_POST['item_number'] == $_SESSION['paypal']['item_number'] && $check) {
            if (isset($_SESSION['paypal']['price'])) {
                foreach ($_SESSION['paypal']['price'] as $price_id) {
                    $db->qry("UPDATE %prefix%party_user SET paid='1' WHERE user_id=%int% AND price_id=%int%", $auth['userid'], $price_id);
                    if (isset($_SESSION['paypal']['depot']) && in_array($price_id, $_SESSION['paypal']['depot'])) {
                        $db->qry("UPDATE %prefix%party_user SET seatcontrol='1' WHERE user_id=%int% AND price_id=%int%", $auth['userid'], $price_id);
                    }
                }
            }
                
            if ($_SESSION['paypal']['catering'] > 0) {
                $db->qry(
                    "INSERT INTO %prefix%catering_accounting SET userID=%int%, actiontime=NOW(), comment=\"PAYPAL: %string% %string%\", movement=%string",
                    $auth["userid"],
                    $_POST['payment_date'],
                    $_POST['txn_id'],
                    $_SESSION['paypal']['catering']
                );
            }
                
            $dsp->NewContent(t('Zahlung erfolgreich'));
            $dsp->AddSmartyTpl('javascript', 'paypal');
            $dsp->AddSingleRow(t('Die Zahlung war erfolgreich. Wir danken f&uuml;r die Einzahlung.'));
            $dsp->AddDoubleRow(t('Vorname'), $_POST['first_name']);
            $dsp->AddDoubleRow(t('Nachname'), $_POST['last_name']);
            $dsp->AddDoubleRow(t('E-Mail'), $_POST['payer_email']);
            $dsp->AddDoubleRow(t('Zahlungsnummer'), $_POST['txn_id']);
            $dsp->AddDoubleRow(t('Zahlungsdatum'), $_POST['payment_date']);
            $dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
            $dsp->AddContent();
        } else {
            $dsp->NewContent(t('Transaktionsfehler oder unerlaubter Zugriff'));
            $dsp->AddSmartyTpl('javascript', 'paypal');
            $dsp->AddSingleRow("<font color=\"red\">" . t('Bitte melden sie sich beim einem Admin damit der die Zahlung pr&uuml;fen kann.') . "</font>");
            $dsp->AddDoubleRow(t('Vorname'), $_POST['first_name']);
            $dsp->AddDoubleRow(t('Nachname'), $_POST['last_name']);
            $dsp->AddDoubleRow(t('E-Mail'), $_POST['payer_email']);
            $dsp->AddDoubleRow(t('Zahlungsnummer'), $_POST['txn_id']);
            $dsp->AddDoubleRow(t('Zahlungsdatum'), $_POST['payment_date']);
            $dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
            $dsp->AddContent();
        }
        break;
        
    case 5:
        $result=fsockPost($cfg['paypal_url'], $_POST);
            
        if (eregi("VERIFIED", $result)) {
            create_csv_file("ext_inc/paypal/ipn_success.txt.php", $_POST);
        } else {
            create_csv_file("ext_inc/paypal/ipn_error.txt.php", $_POST);
        }
                
        break;
        
    case 10:
        $dsp->NewContent(t('Fehler'));
        $dsp->AddSingleRow(t('Die Transaktion konnte nicht durchgef&uuml;hrt werden.'));
        $dsp->AddBackButton("\" OnClick=\"javascript: refreshParent()");
        $dsp->AddContent();
        
        break;
}
    
echo $smarty->fetch('modules/paypal/templates/sendbox.htm');
