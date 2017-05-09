<?php
include_once("modules/foodcenter/class_product.php");
include_once("modules/foodcenter/class_accounting.php");
$product_list = new product_list();

if ($auth['type'] < 2) {
    unset($_GET['step']);
}

switch ($_GET['step']) {
    case 3:
        $time = time();
        if ($_GET['status'] == 6 | $_GET['status'] == 7) {
            $db->qry("UPDATE %prefix%food_ordering SET status = %string%, lastchange = %string%, supplytime = %string%  WHERE id = %int%", $_GET['status'], $time, $time, $_GET['id']);
        } elseif ($_GET['status'] == 8) {
            $prodrow = $db->qry_first("SELECT * FROM %prefix%food_ordering WHERE id = %int%", $_GET['id']);
            
            if ($prodrow['pice'] > 1 && !isset($_POST['delcount'])) {
                $count_array[] = "<option selected value=\"{$prodrow['pice']}\">".t('Alle')."</option>";
                
                for ($i = $prodrow['pice']; $i > 0; $i--) {
                    $count_array[] .= "<option value=\"{$i}\">{$i}</option>";
                }
                $_GET['step'] = 10;
            } else {
                $price = 0;
                $account = new accounting($prodrow['userid']);
                if (stristr($prodrow['opts'], "/")) {
                    $values = explode("/", $prodrow['opts']);

                    foreach ($values as $number) {
                        if (is_numeric($number)) {
                            $optrow = $db->qry_first("SELECT price FROM %prefix%food_option WHERE id = %int%", $number);
                            $price += $optrow['price'];
                        }
                    }
                } else {
                    $optrow = $db->qry_first("SELECT price FROM %prefix%food_option WHERE id = %int%", $prodrow['opts']);
                    $price += $optrow['price'];
                }

                if (isset($_POST['delcount'])) {
                    $price = $price * $_POST['delcount'];
                } else {
                    $price = $price * $prodrow['pice'];
                }
                $account->change($price, t('Rückzahlung bei abbestellten Produkten') . " (" . $auth['username'] . ")", $prodrow['userid']);
                
                if (!isset($_POST['delcount']) || $_POST['delcount'] == $prodrow['pice']) {
                    $db->qry_first("DELETE FROM %prefix%food_ordering WHERE id = %int%", $_GET['id']);
                } else {
                    $pice = $prodrow['pice'] - $_POST['delcount'];
                    $db->qry_first("UPDATE %prefix%food_ordering SET pice = %int% WHERE id = %int%", $pice, $_GET['id']);
                }
            }
        } else {
            $db->qry("UPDATE %prefix%food_ordering SET status = %string%, lastchange = %string% WHERE id = %int%", $_GET['status'], $time, $_GET['id']);
            if ($_GET['status'] == 3) {
                $user_id = $db->qry_first("SELECT userid FROM %prefix%food_ordering WHERE id = %int%", $_GET['id']);
                $func->setainfo(t('Deine bestellten Produkte sind abholbereit'), $user_id['userid'], 2, "foodcenter", $_GET['id']);
            }
        }
        break;
}


switch ($_GET['step']) {
    default:
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('news');

        $ms2->query['from'] = "%prefix%food_ordering AS a
    	  LEFT JOIN %prefix%food_option AS o ON a.opts = o.id
		  LEFT JOIN %prefix%food_product AS p ON a.productid = p.id
		  LEFT JOIN %prefix%food_supp AS s ON p.supp_id = s.supp_id
		  LEFT JOIN %prefix%user AS u ON u.userid = a.userid";

    // Array Abfragen für DropDowns
        $status_list = array('' => 'Alle');
        $row = $db->qry("SELECT * FROM %prefix%food_status");
        while ($res = $db->fetch_array($row)) {
            $status_list[$res['id']] = $res['statusname'];
        }
        $db->free_result($row);
    
        $supp_list = array('' => 'Alle');
        $row = $db->qry("SELECT * FROM %prefix%food_supp");
        while ($res = $db->fetch_array($row)) {
            $supp_list[$res['supp_id']] = $res['name'];
        }
        $db->free_result($row);
    
        $party_list = array('' => 'Alle');
        $row = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($res = $db->fetch_array($row)) {
            $party_list[$res['party_id']] = $res['name'];
        }
        $db->free_result($row);
    
        $ms2->AddTextSearchDropDown('Status', 'a.status', $status_list, '1');
        $ms2->AddTextSearchDropDown('Lieferant', 's.supp_id', $supp_list);
        $ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);
/*
    $userquery = $db->qry("SELECT * FROM %prefix%food_ordering AS a LEFT JOIN %prefix%user AS u ON a.userid=u.userid");
    $user_array[''] = t('');
    while ($userrows = $db->fetch_array($userquery)) {
        $user_array[$userrows['userid']] = $userrows['username'];
    }
    $ms2->AddTextSearchDropDown('Besteller', 'a.userid', $user_array);
*/
        $ms2->AddSelect('u.userid');
        $ms2->AddResultField('Titel', 'p.caption');
    //$ms2->AddResultField('Option', 'o.caption');
        $ms2->AddResultField('Einheit', 'o.unit');
        $ms2->AddResultField('Anzahl', 'a.pice');
        $ms2->AddResultField('Lieferant', 's.name');
        $ms2->AddResultField('Besteller', 'u.username', 'UserNameAndIcon');
        $ms2->AddResultField('Bestellt', 'a.ordertime', 'MS2GetDate');
        $ms2->AddResultField('Geliefert', 'a.supplytime', 'MS2GetDate');

        $ms2->AddIconField('details', 'index.php?mod=foodcenter&action=statchange&step=2&id=', t('Details'));
    
        $fc_ordered_status_quest[0]    = t('Status ändern: Abgeholt');
        $fc_ordered_status_quest[1]    = t('Status ändern: Abholbereit');
        $fc_ordered_status_quest[2]    = t('Status ändern: Lieferung erwartet');
        $fc_ordered_status_quest[3]    = t('Status ändern: An Platz geliefert');
        $fc_ordered_status_quest[4]    = t('Produkt abbestellen und Geld zurücküberweisen.');
        $fc_ordered_status_quest[5]    = t('Zurück');
    
        $ms2->AddMultiSelectAction($fc_ordered_status_quest[0], 'index.php?mod=foodcenter&action=statchange&step=2&status=6', 1);
        $ms2->AddMultiSelectAction($fc_ordered_status_quest[1], 'index.php?mod=foodcenter&action=statchange&step=2&status=5', 1);
        $ms2->AddMultiSelectAction($fc_ordered_status_quest[2], 'index.php?mod=foodcenter&action=statchange&step=2&status=3', 1);
        $ms2->AddMultiSelectAction($fc_ordered_status_quest[3], 'index.php?mod=foodcenter&action=statchange&step=2&status=7', 1);
        $ms2->AddMultiSelectAction($fc_ordered_status_quest[4], 'index.php?mod=foodcenter&action=statchange&step=2&status=8', 1);

        switch ($_POST['search_dd_input'][0]) {
            case 1:
                $dsp->NewContent(t('Bestellte Produkte'), '');
                $ms2->NoItemsText = t('Keine aktuellen Bestellungen vorhanden.');
                break;

            case 2:
                $dsp->NewContent(t('Produkte die bestellt werden'), '');
                $ms2->NoItemsText = t('Es müssen keine Produkte bestellt werden.');
                break;

            case 3:
                $dsp->NewContent(t('Diese Produkte wurden bestellt. Auf die Lieferung wird gewartet.'), '');
                $ms2->NoItemsText = t('Es wird auf keine Lieferung gewartet.');
                break;

            case 4:
                $dsp->NewContent(t('Fertiggestellte Küchengerichte zur Abholung/Lieferung'), '');
                $ms2->NoItemsText = t('Derzeit gibt es keine fertiggestellten Gerichte aus der Küche.');
                break;
        
            case 5:
                $dsp->NewContent(t('Abgeholt'), '');
                $ms2->NoItemsText = t('Du hast alle Produkte abgeholt.');
                break;
        }

        $ms2->PrintSearch('index.php?mod=foodcenter&action=statchange', 'a.id');


        $handle = opendir("ext_inc/foodcenter_templates");
        while ($file = readdir($handle)) {
            if (($file != ".") and ($file != "..") and ($file != ".svn") and (!is_dir($file))) {
                if ((substr($file, -3, 3) == "htm") && (substr($file, -7, 7) != "row.htm") || (substr($file, -4, 4) == "html") && (substr($file, -8, 8) != "row.html")) {
                    $file_array[] .= "<option value=\"$file\">$file</option>";
                }
            }
        }
        $dsp->SetForm("index.php?mod=foodcenter&action=print&design=base\" target=\"_blank\"", "print");
        $dsp->AddDropDownFieldRow("file", t('Bitte Template auswählen:'), $file_array, "");

        $MainContent .= "<input type=\"hidden\" name=\"search_input[0]\" value=\"{$_POST['search_input'][0]}\">\n";
        $MainContent .= "<input type=\"hidden\" name=\"search_dd_input[0]\" value=\"{$_POST['search_dd_input'][0]}\">\n";
        $MainContent .= "<input type=\"hidden\" name=\"search_dd_input[1]\" value=\"{$_POST['search_dd_input'][1]}\">\n";
        $MainContent .= "<input type=\"hidden\" name=\"search_dd_input[2]\" value=\"{$_POST['search_dd_input'][2]}\">\n";
        
        $dsp->AddFormSubmitRow(t('Drucken'));
        $dsp->AddContent();
        break;
    case 2:
        if ($_POST['action']) {
            include_once("modules/seating/class_seat.php");
            $seat2 = new seat2();

            $time = time();
            $totprice = 0;
            foreach ($_POST["action"] as $item => $val) {
                if ($_GET["status"] == 6 | $_GET["status"] == 7) {
                    $db->qry("UPDATE %prefix%food_ordering SET status = %string%, lastchange = %string%, supplytime = %string%  WHERE id = %string%", $_GET["status"], $time, $time, $item);

    //sitzplan popup einbinden
    //change by jan für sitzplatz popup $item = id in food_ordering table
                    //unit food_option (größe)
                    $abfrage = $db->qry_first("SELECT %prefix%food_ordering.userid AS userid,%prefix%food_ordering.pice AS pice,unit, %prefix%food_product.caption AS caption, username, name, firstname
				FROM %prefix%food_ordering,%prefix%food_option, %prefix%food_product, %prefix%user
				WHERE %prefix%food_ordering.id = ".$item." 
				AND lastchange=".$time." 
				AND supplytime=".$time." 
				AND %prefix%food_product.id = %prefix%food_option.parentid 
				AND %prefix%food_ordering.productid = %prefix%food_product.id
				AND %prefix%user.userid = %prefix%food_ordering.userid");
                    //$dsp->AddDoubleRow('Ergebnis', $seat2->SeatOfUser($abfrage['userid'], 0, 2));
                    $dsp->AddDoubleRow('Was -> Wohin', $abfrage['pice'].' x '.$abfrage['caption']. ' ('.$abfrage['unit']. ') -> '.$abfrage['username'].' ('.$abfrage['firstname'].' '.$abfrage['name'].') '.$seat2->SeatOfUser($abfrage['userid'], 0, 2));
                
                
                    //change ende
                } elseif ($_GET["status"] == 8) {
                    $totprice = 0;
                    $prodrow = $db->qry_first("SELECT * FROM %prefix%food_ordering WHERE id = %string%", $item);
                
                    unset($account);
                    $account = new accounting($prodrow['userid']);
                    $price = 0;
                    $tempdesc = "";
                    if (stristr($prodrow['opts'], "/")) {
                        $values = explode("/", $prodrow['opts']);

                        foreach ($values as $number) {
                            if (is_numeric($number)) {
                                $optrow = $db->qry_first("SELECT price, caption FROM %prefix%food_option WHERE id = %int%", $number);
                                $price += $optrow['price'];
                                $tempdesc .= $optrow['caption'];
                            }
                        }
                    } else {
                        $optrow = $db->qry_first("SELECT price, caption FROM %prefix%food_option WHERE id = %int%", $prodrow['opts']);
                        $price += $optrow['price'];
                        $tempdesc .= $optrow['caption'];
                    }
                    $totprice += $price * $prodrow['pice'];
                    $tempsession = $_SESSION;
                    $account->change(
                        $totprice,
                        t('Rückzahlung bei abbestellten Produkten') . " (" . $auth['username'] . ") Artikel:".$tempdesc,
                        $prodrow['userid']
                    );
                    $_SESSION = $tempsession;
                    unset($tempsession);
                    $db->qry_first("DELETE FROM %prefix%food_ordering WHERE id = %int%", $item);
                } else {
                    $db->qry("UPDATE %prefix%food_ordering SET status = %string%, lastchange = %string%  WHERE id = %string%", $_GET["status"], $time, $item);
                    if ($_GET["status"] == 3) {
                        $user_id = $db->qry_first("SELECT userid FROM %prefix%food_ordering WHERE id = %string%", $item);
                        $func->setainfo(t('Deine bestellten Produkte sind abholbereit'), $user_id['userid'], 2, "foodcenter", $item);
                    }
                }
            }
            $fc_ordered_status_ask[4] = t('Produkte abbestellt');
            $fc_ordered_status_ask[5] = t('Status auf abgeholt gesetzt');
            $fc_ordered_status_ask[3] = t('Status auf Abholbereit gesetzt');
            $fc_ordered_status_ask[2] = t('Status auf bestellt gesetzt');
            $fc_ordered_status_ask[1] = t('Status auf bestellen gesetzt');
            $func->confirmation($fc_ordered_status_ask[$_GET["status"]], "index.php?mod=foodcenter&action=statchange");
        } else {
            $link_array[0] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=6";
            $link_array[1] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=5";
            $link_array[2] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=3";
            $link_array[3] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=7";
            $link_array[4] = "index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=8";
            $link_array[5] = "index.php?mod=foodcenter&action=statchange";
            $func->multiquestion($fc_ordered_status_quest, $link_array, t('Status setzen'));
        }
        break;
    
    case 10:
        $dsp->NewContent(t('Produkt abbestellen'), t('Bitte wählen sie die Produktanzahl die abbestellt werden soll.'));
        $dsp->SetForm("index.php?mod=foodcenter&action=statchange&step=3&id={$_GET['id']}&status=4");
        $dsp->AddDropDownFieldRow("delcount", t('Anzahl'), $count_array, "");
        $dsp->AddFormSubmitRow(t('Weiter'));
        $dsp->AddContent();
        break;
}
