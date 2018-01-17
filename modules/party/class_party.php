<?php
class party
{
    public $party_id = 0;
    public $count = 0;
    public $data = array();
    
    public function __construct()
    {
        global $cfg, $db;

    // Set new Session PartyID on GET or POST
        if (is_numeric($_GET['set_party_id'])) {
            $this->party_id = $_GET['set_party_id'];
        } elseif (is_numeric($_POST['set_party_id'])) {
            $this->party_id = $_POST['set_party_id'];
        } elseif (is_numeric($_SESSION['party_id'])) {
            // Look whether this partyId exists
            $row = $db->qry_first('SELECT 1 AS found FROM %prefix%partys WHERE party_id = %int%', $_SESSION['party_id']);
            if ($row['found']) {
                $this->party_id = $_SESSION['party_id'];
            } else {
                $this->party_id = $cfg['signon_partyid'];
                unset($_SESSION['party_id']);
            }
        } else {
            $this->party_id = $cfg['signon_partyid'];
        }

        $_SESSION['party_id'] = $this->party_id;
        $this->UpdatePartyArray();
    }

    // Read PartyInfo into Vars
    public function UpdatePartyArray()
    {
        global $cfg, $db;

        if ($db->success) {
            // Count Partys
            $res = $db->qry("SELECT * FROM %prefix%partys");
            $this->count = $db->num_rows($res);
            $db->free_result($res);

            $_SESSION['party_info'] = array();
            if ($this->count > 0) {
                $row = $db->qry_first("SELECT name, ort, plz, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate, max_guest FROM %prefix%partys WHERE party_id=%int%", $this->party_id);
                $this->data = $row;

                $_SESSION['party_info']['name']            = $row['name'];
                $_SESSION['party_info']['partyort']        = $row['ort'];
                $_SESSION['party_info']['partyplz']        = $row['plz'];
                $_SESSION['party_info']['partybegin']    = $row['startdate'];
                $_SESSION['party_info']['partyend']    = $row['enddate'];
                $_SESSION['party_info']['s_startdate']    = $row['sstartdate'];
                $_SESSION['party_info']['s_enddate']    = $row['senddate'];
                $_SESSION['party_info']['max_guest']    = $row['max_guest'];
            }
        }
    }

    public function get_party_id()
    {
        return $this->party_id;
    }

    public function set_party_id($id)
    {
        global $db;

        $row = $db->qry_first_rows("SELECT * FROM %prefix%partys WHERE party_id = %int%", $id);
        if ($row['number'] == 1) {
            $this->party_id = $id;
        }
        $this->UpdatePartyArray();
    }



    public function get_party_dropdown_form($show_old = 0, $link = '')
    {
        global $dsp,$db,$lang,$templ,$func,$cfg;
            
            // Bei leerem String
        if ($link == '') {
            $link = "index.php?" . $_SERVER['QUERY_STRING'];
        }
            
            // Wenn nur eine Party aufgelistet ist nichts ausgeben
        if ($show_old = 0) {
            $row = $db->qry("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys WHERE enddate < %int%", time());
        } else {
            $row = $db->qry("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys");
        }

        if ($db->num_rows($row) >= 1) {
            while ($res = $db->fetch_array($row)) {
                $start_date = $func->unixstamp2date($res["startdate"], "date");
                $end_date = $func->unixstamp2date($res["enddate"], "date");

                if ($res['party_id'] == $this->party_id) {
                    $selected = "selected='selected'";
                } else {
                    $selected = "";
                }
                    
                if (is_array($list_array)) {
                    array_push($list_array, "<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
                } else {
                    $list_array = array("<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
                }
            }
            $dsp->SetForm($link);
            $dsp->AddDropDownFieldRow("set_party_id", t('Party auswählen'), $list_array, '');
            $dsp->AddFormSubmitRow(t('Ändern'));
        }
    }

        /**
         * Funktion zum hinzufügen eines Dropdownfeldes zur Klasse $dsp
         *
         * @param boolean $show_old
         */
    public function get_party_dropdown($show_old = 0)
    {
        global $dsp,$db,$lang,$templ,$func;
            
        // Bei leerem String
        if ($link == '') {
            $link = "index.php?" . $_SERVER['QUERY_STRING'];
        }
            
        // Wenn die Anzeige auf nur einer party steht dann nichts ausgeben
        if ($cfg['singon_multiparty'] == 1) {
            if ($archive = 0) {
                $row = $db->qry("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys WHERE UNIX_TIMESTAMP(enddate) < %int%", time());
            } else {
                $row = $db->qry("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys");
            }

            // Wenn nur eine Party aufgelistet ist nichts ausgeben
            if ($db->num_rows($row) > 1) {
                while ($res = $db->fetch_array($row)) {
                    $start_date = $func->unixstamp2date($res["statedate"], "date");
                    $end_date = $func->unixstamp2date($res["enddate"], "date");
                        
                    if ($res['party_id'] == $this->party_id) {
                        $selected = "selected='selected'";
                    } else {
                        $selected = "";
                    }
                    if (is_array($list_array)) {
                        array_push($list_array, "<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
                    } else {
                        $list_array = array("<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>");
                    }
                }
                $dsp->AddDropDownFieldRow("party_id", t('Party auswählen'), $list_array);
            }
        }
    }

        /**
         * Funktion zum hinzufüge einer Party
         *
         */
    public function add_party()
    {
        global $db,$func;
            
        $_POST['startdate']    = mktime($_POST["stime_value_hours"], $_POST["stime_value_minutes"], $_POST["stime_value_seconds"], $_POST["stime_value_month"], $_POST["stime_value_day"], $_POST["stime_value_year"]);
        $_POST['enddate']        = mktime($_POST["etime_value_hours"], $_POST["etime_value_minutes"], $_POST["etime_value_seconds"], $_POST["etime_value_month"], $_POST["etime_value_day"], $_POST["etime_value_year"]);
        $_POST['sstartdate']    = mktime($_POST["sstime_value_hours"], $_POST["sstime_value_minutes"], $_POST["sstime_value_seconds"], $_POST["sstime_value_month"], $_POST["sstime_value_day"], $_POST["sstime_value_year"]);
        $_POST['senddate']        = mktime($_POST["setime_value_hours"], $_POST["setime_value_minutes"], $_POST["setime_value_seconds"], $_POST["setime_value_month"], $_POST["setime_value_day"], $_POST["setime_value_year"]);
            
            
        $db->qry("INSERT INTO %prefix%partys SET
        name = %string%,
        ort = %string%,
        plz = %string%,
        max_guest = %string%,
        startdate = %string%,
        enddate = %string%,
        sstartdate = %string%,
        senddate = %string%", $_POST['name'], $_POST['ort'], $_POST['plz'], $_POST['max_guest'], $_POST['startdate'], $_POST['enddate'], $_POST['sstartdate'], $_POST['senddate']);
                        
        $this->set_party_id($db->insert_id());
    }
        
        /**
         * Party ändern
         *
         */
    public function change_party()
    {
        global $db,$func;
            
        $_POST['startdate']    = mktime($_POST["stime_value_hours"], $_POST["stime_value_minutes"], $_POST["stime_value_seconds"], $_POST["stime_value_month"], $_POST["stime_value_day"], $_POST["stime_value_year"]);
        $_POST['enddate']        = mktime($_POST["etime_value_hours"], $_POST["etime_value_minutes"], $_POST["etime_value_seconds"], $_POST["etime_value_month"], $_POST["etime_value_day"], $_POST["etime_value_year"]);
        $_POST['sstartdate']    = mktime($_POST["sstime_value_hours"], $_POST["sstime_value_minutes"], $_POST["sstime_value_seconds"], $_POST["sstime_value_month"], $_POST["sstime_value_day"], $_POST["sstime_value_year"]);
        $_POST['senddate']        = mktime($_POST["setime_value_hours"], $_POST["setime_value_minutes"], $_POST["setime_value_seconds"], $_POST["setime_value_month"], $_POST["setime_value_day"], $_POST["setime_value_year"]);
            
        $db->qry("UPDATE %prefix%partys SET
        name = %string%,
        ort = %string%,
        plz = %string%,
        max_guest = %string%,
        startdate = %string%,
        enddate = %string%,
        sstartdate = %string%,
        senddate = %string%
        WHERE party_id = %int%", $_POST['name'], $_POST['ort'], $_POST['plz'], $_POST['max_guest'], $_POST['startdate'], $_POST['enddate'], $_POST['sstartdate'], $_POST["senddate"], $this->party_id);
    }
        
        
        /**
         * Party löschen und auf Standardparty einstellen
         *
         */
    public function delete_party()
    {
        global $db,$func,$cfg;
        // Party löschen
        $db->qry("DELETE FROM %prefix%partys 
        WHERE party_id = %int%", $this->party_id);
            
        // Preise zur Party löschen
        $db->qry("DELETE FROM %prefix%party_prices 
        party_id = %int%
        ", $this->party_id);
            
        // User zur Party löschen
        $db->qry("DELETE FROM %prefix%party_user 
        party_id = %int%
        ", $this->party_id);
            
        $this->set_party_id($cfg['signon_partyid']);
    }
        
        
        /**
         * Preise zählen
         */
        
    public function get_price_count($groupid = false)
    {
        global $db;
            
        if ($groupid) {
            $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% AND group_id=%int%", $this->party_id, $groupid);
        } else {
            $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int%", $this->party_id);
        }
        return $db->num_rows($row);
    }
        /**
         * Funktion um ein Dorpdownfeld mit Preisen zur Party auszugeben
         *
         */
    public function get_price_dropdown($group_id = 0, $price_id = 0, $dropdown = false)
    {
        global $db,$dsp,$lang,$cfg;

        if ($group_id !== "NULL") {
            $subquery = " AND group_id='{$group_id}'";
        }
        if ($price_id == "NULL") {
            $price_id = 0;
        }

        $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% %plain%", $this->party_id, $subquery);
        $anzahl = $db->num_rows($row);

        if ($anzahl == 0) {
            $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% AND group_id='0'", $this->party_id);
        }

        if ($anzahl >1 || $dropdown == true) {
            while ($res = $db->fetch_array($row)) {
                if ($price_id == $res['price_id']) {
                    $selected = "selected='selected'";
                } else {
                    $selected = "";
                }

                if (is_array($data)) {
                    array_push($data, "<option $selected value='{$res['price_id']}'>{$res['price_text']} / {$res['price']} {$cfg['sys_currency']}</option>");
                } else {
                    $data = array("<option $selected value='{$res['price_id']}'>{$res['price_text']} / {$res['price']} {$cfg['sys_currency']}</option>");
                }
            }
            $dsp->AddDropDownFieldRow("price_id", t('Preis auswählen'), $data, '');
        } else {
            $res = $db->fetch_array($row);
            $dsp->AddDoubleRow(t('Preis auswählen'), $res['price_text'] . "  / {$res['price']} {$cfg['sys_currency']}<input name='price_id' type='hidden' value='{$res['price_id']}' />");
        }
    }

    public function GetPriceDropdown($group_id = 0, $price_id = 0)
    {
        global $db,$lang,$cfg,$mf;

        $selections = array();
      
        if ($group_id !== "NULL") {
            $subquery = " AND group_id='{$group_id}'";
        }
        if ($price_id == "NULL") {
            $price_id = 0;
        }

        $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% %plain%", $this->party_id, $subquery);
        $anzahl = $db->num_rows($row);

        if ($anzahl == 0) {
            $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% AND group_id='0'", $this->party_id);
        }

        while ($res = $db->fetch_array($row)) {
            $selections[$res['price_id']] = $res['price_text'] .' / '. $res['price'] .' '. $cfg['sys_currency'];
        }
        $mf->AddField(t('Preis auswählen'), 'price_id', IS_SELECTION, $selections);
        $res = $db->free_result($res);
    }

    public function get_party_javascript()
    {
        global $db,$cfg;
        $row = $db->qry("SELECT * FROM %prefix%party_prices WHERE party_id = %int% ORDER BY group_id", $this->party_id);
        $option = "var option = new Array();\n";
        $prices = "var price = new Array();\n";
        while ($data = $db->fetch_array($row)) {
            if ($temp_group != $data['group_id']) {
                $temp_group = $data['group_id'];
                $option .= "option[{$data['group_id']}] = new Array();\n";
                $prices .= "price[{$data['group_id']}] = new Array();\n";
                $i = 0;
            }
            $option .= "option[{$data['group_id']}][$i] = \"{$data['price_text']} / {$data['price']} {$cfg['sys_currency']}\";\n";
            $prices .= "price[{$data['group_id']}][$i] = \"{$data['price_id']}\";\n";
            $i++;
        }
            
        return $option . $prices;
    }
        
        /**
         * Funktion um einen Preis hizuzufügen
         *
         * @param string $price_text
         * @param int $price
         * @param string $depot_desc
         * @param int $depot_price
         * @param int $usergroup
         */
    public function add_price($price_text, $price, $depot_desc = "", $depot_price = 0, $usergroup = 0)
    {
        global $db;
            
        $db->qry("INSERT %prefix%party_prices SET 
        party_id = %int%,
        price_text = %string%,
        price = %string%,
        depot_desc = %string%,
        depot_price = %string%,
        group_id = %string%
        ", $this->party_id, $price_text, $price, $depot_desc, $depot_price, $usergroup);
    }
        

        
        
        /**
         * Funktion um einen Preis zu ändern
         *
         * @param int $price_id
         * @param string $price_text
         * @param int $price
         * @param string $depot_desc
         * @param int $depot_price
         * @param int $usergroup
         */
    public function update_price($price_id, $price_text, $price, $depot_desc = "", $depot_price = 0, $usergroup = 0)
    {
        global $db;
            
        $db->qry("UPDATE %prefix%party_prices SET 
        price_text = %string%,
        price = %string%,
        depot_desc = %string%,
        depot_price = %string%,
        group_id = %string%
        WHERE price_id = %int%
        ", $price_text, $price, $depot_desc, $depot_price, $usergroup, $price_id);
    }
        
        
        /**
         * Funktion zum hinzufügen eines Users zu einer Party
         * Die Funktion prüft ob der User schon an der Party angemeldet ist und ersetzt gegebenenfalls den Eintrag.
         *
         * @param int $user_id
         * @param int $price_id
         * @param int $checkin
         */
    public function add_user_to_party($user_id, $price_id = "0", $paid = "NULL", $checkin = "NULL")
    {
        global $db,$cfg;
            
        $timestamp = time();
            
        if ($checkin == "1" || $cfg["signon_autocheckin"] == "1") {
            $checkin = "$timestamp";
        } else {
            $checkin = "0";
        }
            
        if (($cfg["signon_autopaid"] == "1" && $paid == "NULL")) {
            $paid = "1";
        } elseif ($paid == "NULL") {
            $paid = "0";
        }

        $row = $db->qry("SELECT * FROM %prefix%party_user WHERE user_id=%int% AND party_id=%int%", $user_id, $this->party_id);
        if ($db->num_rows($row) < 1) {
            $prices = $db->qry_first("SELECT * FROM %prefix%party_prices WHERE price_id=%int%", $price_id);
            if ($prices['depot_price'] == 0) {
                $seatcontrol = 1;
            } else {
                $seatcontrol = 0;
            }

            $db->qry("INSERT INTO %prefix%party_user SET
         user_id = %int%,
         party_id = %int%,
         price_id = %int%,
         checkin = %string%,
         paid = %int%,
         seatcontrol = %string%,
         signondate = %string%
         ", $user_id, $this->party_id, $price_id, $checkin, $paid, $seatcontrol, $timestamp);
        } else {
            $this->update_user_at_party($user_id, $paid, $price_id, $checkin);
        }
    }
        
        

        /**
         * Funktion um einen Bezahlungsstatus zu ändern
         *
         * @param int $user_id
         * @param bool $paid
         * @param int $price_id
         * @param bool $checkin
         * @param bool $checkout
         */
    public function update_user_at_party($user_id, $paid, $price_id = "0", $checkin = "0", $checkout = "0", $seatcontrol = "NULL")
    {
        global $db,$func,$lang;
        $timestamp = time();

        if ($checkin == "1") {
            $checkin = $timestamp;
        }
            
        if ($checkout == "1") {
            $checkout = $timestamp;
        }

        if ($price_id != 0) {
            $prices = $db->qry_first("SELECT * FROM %prefix%party_prices WHERE price_id=%int%", $price_id);
            if ($prices['depot_price'] == 0) {
                $seatcontrol = 1;
            }
        }

        $query = "";
            
        if ($paid != "") {
            $query .= "paid = {$paid},";
        }
            
            
        if ($price_id != "0" && $price_id != "") {
            $query .= "price_id = {$price_id},";
        }
            
        if ($seatcontrol !== "NULL") {
            $query .= "seatcontrol = {$seatcontrol},";
        }
            
        $query .= "	checkin = {$checkin},
						checkout = {$checkout}
						WHERE user_id = {$user_id} AND
						party_id = {$this->party_id}
						";
        $msg = str_replace("%PARTY%", $this->party_id, str_replace("%ID%", $user_id, str_replace("%PIRCEID%", $price_id, str_replace("%SEATCONTROL%", $seatcontrol, str_replace("%CHECKOUT%", $checkout, str_replace("%CHECKIN%", $checkin, str_replace("%PAID%", $paid, t('Die Anmeldung von %ID% bei der Party %PARTY% wurde geändert. Neu: Bezahlt = %PAID%, Checkin = %CHECKIN%, Checkout = %CHECKOUT%, Pfand = %SEATCONTROL%, Preisid = %PIRCEID%'))))))));
        $func->log_event($msg, 1);
        $db->qry('UPDATE %prefix%party_user SET %plain%', $query);
    }
            

        /**
         * User von einer Party abmelden
         *
         * @param int $user_id
         */
    public function delete_user_from_party($user_id)
    {
        global $db,$cfg;
        $timestamp = time();
        if ($checkin == "1" || $cfg["signon_autocheckin"] == "1") {
            $checkin = $timestamp;
        } else {
            $checkin = "0";
        }
            
            
        $db->qry("DELETE FROM %prefix%party_user 
        WHERE user_id = %int% AND
        party_id = %int%
        ", $user_id, $this->party_id);
    }


        /**
         * Funktion um ein Dropdownfeld mit Benutzergruppen hinzuzufügen.
         *
         */
    public function GetUserGroupDropdown($group_id = "NULL", $nogroub = 0, $select_id = 0, $javascript = false)
    {
        global $db,$mf,$lang;

        if ($group_id == "NULL") {
            $res = $db->qry("SELECT * FROM %prefix%party_usergroups");
        } else {
            $res = $db->qry("SELECT * FROM %prefix%party_usergroups WHERE group_id = %int%", $group_id);
        }

        $selections = array();
        $selections[] = t('Ohne Gruppe');

        if ($res) {
            while ($row = $db->fetch_array($res)) {
                $selections[$row['group_id']] = $row['group_name'];
            }
        }
        $mf->AddField(t('Benutzergruppe'), 'group_id', IS_SELECTION, $selections);
        return true;
    }


    public function get_user_group_dropdown($group_id = "NULL", $nogroub = 0, $select_id = 0, $javascript = false)
    {
        global $db,$dsp,$lang;
            
        if ($group_id == "NULL") {
            $row = $db->qry("SELECT * FROM %prefix%party_usergroups");
        } else {
            $row = $db->qry("SELECT * FROM %prefix%party_usergroups WHERE group_id = %int%", $group_id);
        }
            
        if ($nogroub == 1) {
            if ($select_id == 0) {
                $data = array("<option selected value='0'>".t('Ohne Gruppe')."</option>");
            } else {
                $data = array("<option value='0'>".t('Ohne Gruppe')."</option>");
            }
        }
            
        $anzahl = $db->num_rows($row);
            
        if ($anzahl == 0) {
            $dsp->AddDoubleRow(t('Benutzergruppe'), t('Keine Benutzergruppe vorhanden') . "<input name='group_id' value='0' type='hidden' />");
            return false;
        } elseif ($nogroub == 0 && $anzahl == 1) {
            $res = $db->fetch_array($row);
            $dsp->AddDoubleRow(t('Benutzergruppe'), $res['group_name'] . "<input name='group_id' value='{$res['group_id']}' type='hidden' />");
        } else {
            while ($res = $db->fetch_array($row)) {
                if ($res['group_id'] == $select_id) {
                    $selected = "selected='selected'";
                } else {
                    $selected = "";
                }
                    
                if (is_array($data)) {
                    array_push($data, "<option $selected value='{$res['group_id']}'>{$res['group_name']}</option>");
                } else {
                    $data = array("<option $selected value='{$res['group_id']}'>{$res['group_name']}</option>");
                }
            }
            if ($javascript) {
                $dsp->AddDropDownFieldRow("group_id\" onchange=\"change_group(this.options[this.options.selectedIndex].value)", t('Benutzergruppe'), $data, '');
            } else {
                $dsp->AddDropDownFieldRow("group_id", t('Benutzergruppe'), $data, '');
            }
        }
        return true;
    }
        
        
        /**
         * Funktion um Benutzergruppen hinzuzufügen
         *
         * @param string $group
         * @param string $description
         */
    public function add_user_group($group, $description, $selection, $select_opts)
    {
        global $db;
            
        $db->qry("INSERT %prefix%party_usergroups SET
        group_name = %string%,
        description = %string%,
        selection = %string%,
        select_opts = %string%
        ", $group, $description, $selection, $select_opts);
    }
        
        /**
         * Funktion um Benutzergruppen zu ändern
         *
         * @param string $group
         * @param string $description
         * @param int $group_id
         */
    public function update_user_group($group_id, $group, $description, $selection, $select_opts)
    {
        global $db;
            
        $db->qry("UPDATE %prefix%party_usergroups SET
        group_name = %string%,
        description = %string%,
        selection = %string%,
        select_opts = %string%
        WHERE group_id = %int%
        ", $group, $description, $selection, $select_opts, $group_id);
    }
        
        
    public function price_seatcontrol($price_id)
    {
        global $db;
        $prices = $db->qry_first("SELECT * FROM %prefix%party_prices WHERE price_id=%int%", $price_id);
        return $prices['depot_price'];
    }
        
        /**
         * Platzpfand abfragen
         *
         * @param int $user_id
         * @return int
         */
    public function get_seatcontrol($user_id)
    {
        global $db;
        $row = $db->qry_first("SELECT * FROM %prefix%party_user WHERE user_id=%int% AND party_id=%int%", $user_id, $this->party_id);
        return $row['seatcontrol'];
    }
        
        /**
         * Platzpfand setzten
         *
         * @param int $user_id
         * @param int $seatcontrol
         */
    public function set_seatcontrol($user_id, $seatcontrol)
    {
        global $db;
        $db->qry("UPDATE %prefix%party_user  SET seatcontrol=%string% WHERE user_id=%int% AND party_id=%int%", $seatcontrol, $user_id, $this->party_id);
    }
                
        /**
         * Preise löschen, dabei werden alle Benutzer die diesen Preis haben auf einen neuen Preis gesetzt
         *
         * @param int $del_price
         * @param int $set_price
         */
    public function delete_price($del_price, $set_price)
    {
        global $db;
        $db->qry("UPDATE %prefix%party_user  SET price_id=%string% WHERE price_id=%string%", $set_price, $del_price);
        $db->qry("DELETE FROM %prefix%party_prices WHERE price_id=%string%", $del_price);
    }
        
        
        /**
         * Gruppe löschen, dabei werden alle Benutzer die in dieser Gruppe sind auf einen neue Gruppe setzt.
         *
         * @param unknown_type $del_group
         * @param unknown_type $set_group
         */
    public function delete_usergroups($del_group, $set_group)
    {
        global $db;
        $db->qry("UPDATE %prefix%user  SET group_id=%string% WHERE group_id=%string%", $set_group, $del_group);
        $db->qry("DELETE FROM %prefix%party_usergroups WHERE group_id=%string%", $del_group);
    }
        
        
        
    public function get_next_party()
    {
        global $db;
            
        $time = time();
        $row = $db->qry_first_rows("SELECT *, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate FROM %prefix%partys WHERE startdate > %int% ORDER BY startdate ASC", $time);
            
        if ($row['number'] > 0) {
            $data['party_id']        = $row['party_id'];
            $data['name']            = $row['name'];
            $data['partyort']        = $row['ort'];
            $data['partyplz']        = $row['plz'];
            $data['partybegin']    = $row['startdate'];
            $data['partyend']        = $row['enddate'];
            $data['s_startdate']    = $row['sstartdate'];
            $data['s_enddate']        = $row['senddate'];
            $data['max_guest']        = $row['max_guest'];
                            
            return $data;
        } else {
            return false;
        }
    }
}
