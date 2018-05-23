<?php

namespace LanSuite\Module\Party;

class Party
{
    /**
     * @var int
     */
    public $party_id = 0;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var array
     */
    public $data = [];

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

    /**
     * Read PartyInfo into vars
     *
     * @return void
     */
    private function UpdatePartyArray()
    {
        global $db;

        if ($db->success) {
            // Count Parties
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

    /**
     * @param $id
     * @return void
     */
    private function set_party_id($id)
    {
        global $db;

        $row = $db->qry_first_rows("SELECT * FROM %prefix%partys WHERE party_id = %int%", $id);
        if ($row['number'] == 1) {
            $this->party_id = $id;
        }
        $this->UpdatePartyArray();
    }

    /**
     * @param int $show_old
     * @param string $link
     * @return void
     */
    public function get_party_dropdown_form($show_old = 0, $link = '')
    {
        global $dsp, $db, $func;

        if ($link == '') {
            $link = "index.php?" . $_SERVER['QUERY_STRING'];
        }

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
     * Adds an user to a party.
     * The function checks if the user already signed up and if so, replaces the data.
     *
     * @param int $user_id
     * @param string $price_id
     * @param string $paid
     * @param string $checkin
     * @return void
     */
    public function add_user_to_party($user_id, $price_id = "0", $paid = "NULL", $checkin = "NULL")
    {
        global $db, $cfg;

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

            $db->qry("
              INSERT INTO %prefix%party_user
              SET
                user_id = %int%,
                party_id = %int%,
                price_id = %int%,
                checkin = %string%,
                paid = %int%,
                seatcontrol = %string%,
                signondate = %string%", $user_id, $this->party_id, $price_id, $checkin, $paid, $seatcontrol, $timestamp);
        } else {
            $this->update_user_at_party($user_id, $paid, $price_id, $checkin);
        }
    }

    /**
     * Change the payment status.
     *
     * @param int $user_id
     * @param string $paid
     * @param string $price_id
     * @param string $checkin
     * @param string $checkout
     * @param string $seatcontrol
     * @return void
     */
    private function update_user_at_party($user_id, $paid, $price_id = "0", $checkin = "0", $checkout = "0", $seatcontrol = "NULL")
    {
        global $db, $func;
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
                    party_id = {$this->party_id}";
        $msg = str_replace("%PARTY%", $this->party_id, str_replace("%ID%", $user_id, str_replace("%PIRCEID%", $price_id, str_replace("%SEATCONTROL%", $seatcontrol, str_replace("%CHECKOUT%", $checkout, str_replace("%CHECKIN%", $checkin, str_replace("%PAID%", $paid, t('Die Anmeldung von %ID% bei der Party %PARTY% wurde geändert. Neu: Bezahlt = %PAID%, Checkin = %CHECKIN%, Checkout = %CHECKOUT%, Pfand = %SEATCONTROL%, Preisid = %PIRCEID%'))))))));
        $func->log_event($msg, 1);
        $db->qry('UPDATE %prefix%party_user SET %plain%', $query);
    }

    /**
     * Sign off a user from a party
     *
     * @param $user_id
     * @return void
     */
    public function delete_user_from_party($user_id)
    {
        global $db, $cfg;

        $timestamp = time();
        if ($checkin == "1" || $cfg["signon_autocheckin"] == "1") {
            $checkin = $timestamp;
        } else {
            $checkin = "0";
        }

        $db->qry("
          DELETE FROM %prefix%party_user
          WHERE
            user_id = %int%
            AND party_id = %int%", $user_id, $this->party_id);
    }

    /**
     * @param string $group_id
     * @param int $nogroub
     * @param int $select_id
     * @param bool $javascript
     * @return bool
     */
    public function get_user_group_dropdown($group_id = "NULL", $nogroub = 0, $select_id = 0, $javascript = false)
    {
        global $db, $dsp;

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
     * Add a user group.
     *
     * @param string $group
     * @param string $description
     * @param string $selection
     * @param string $select_opts
     * @return void
     */
    public function add_user_group($group, $description, $selection, $select_opts)
    {
        global $db;

        $db->qry("
            INSERT %prefix%party_usergroups
            SET
                group_name = %string%,
                description = %string%,
                selection = %string%,
                select_opts = %string%", $group, $description, $selection, $select_opts);
    }

    /**
     * Change a user group
     *
     * @param int $group_id
     * @param string $group
     * @param string $description
     * @param string $selection
     * @param string $select_opts
     * @return void
     */
    public function update_user_group($group_id, $group, $description, $selection, $select_opts)
    {
        global $db;

        $db->qry("
          UPDATE %prefix%party_usergroups
          SET
            group_name = %string%,
            description = %string%,
            selection = %string%,
            select_opts = %string%
          WHERE group_id = %int%", $group, $description, $selection, $select_opts, $group_id);
    }

    /**
     * Delete a group.
     * While doing this all users are assigned to a new group.
     *
     * @param string $del_group
     * @param string $set_group
     * @return void
     */
    public function delete_usergroups($del_group, $set_group)
    {
        global $db;
        $db->qry("UPDATE %prefix%user  SET group_id=%string% WHERE group_id=%string%", $set_group, $del_group);
        $db->qry("DELETE FROM %prefix%party_usergroups WHERE group_id=%string%", $del_group);
    }
}
