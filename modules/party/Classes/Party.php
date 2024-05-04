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

    public function __construct($party_id = null)
    {
        global $cfg, $database, $request;

        $setPartyIDGETParameter = $request->query->get('set_party_id');
        $setPartyIDPOSTParameter = $request->request->get('set_party_id');
        if (empty($party_id)) {
            // Set new Session PartyID on GET or POST
            if (is_numeric($setPartyIDGETParameter)) {
                $this->party_id = $setPartyIDGETParameter;
            } elseif (is_numeric($setPartyIDPOSTParameter)) {
                $this->party_id = $setPartyIDPOSTParameter;
            } elseif (array_key_exists('party_id', $_SESSION) && is_numeric($_SESSION['party_id'])) {
                // Look whether this partyId exists
                $row = $database->queryWithOnlyFirstRow('SELECT 1 AS found FROM %prefix%partys WHERE party_id = ?', [$_SESSION['party_id']]);
                if (is_array($row) && $row['found']) {
                    $this->party_id = $_SESSION['party_id'];
                } else {
                    $this->party_id = $cfg['signon_partyid'];
                    unset($_SESSION['party_id']);
                }
            } else {
                $this->party_id = $cfg['signon_partyid'];
            }
        } else {
            // use the provided ID
            $this->party_id = $party_id;
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
        global $db, $database;

        if ($db->success) {
            // Count Parties
            $res = $db->qry("SELECT * FROM %prefix%partys");
            $this->count = $db->num_rows($res);
            $db->free_result($res);

            $_SESSION['party_info'] = [];
            if ($this->count > 0) {
                $row = $database->queryWithOnlyFirstRow("SELECT name, ort, plz, UNIX_TIMESTAMP(enddate) AS enddate, UNIX_TIMESTAMP(sstartdate) AS sstartdate, UNIX_TIMESTAMP(senddate) AS senddate, UNIX_TIMESTAMP(startdate) AS startdate, max_guest FROM %prefix%partys WHERE party_id = ?", [$this->party_id]);
                $this->data = $row;

                $_SESSION['party_info'] = [
                    'name' => $row['name'],
                    'partyort' => $row['ort'],
                    'partyplz' => $row['plz'],
                    'partybegin' => $row['startdate'],
                    'partyend' => $row['enddate'],
                    's_startdate' => $row['sstartdate'],
                    's_enddate' => $row['senddate'],
                    'max_guest' => $row['max_guest'],
                ];
            }
        }
    }

    /**
     * @param $id
     * @return void
     */
    private function set_party_id($id)
    {
        global $db, $database;

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
        $list_array = [];
        global $dsp, $db, $database, $func;

        if ($link == '') {
            $link = "index.php?" . $_SERVER['QUERY_STRING'];
        }

        if ($show_old == 0) {
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
                    $list_array[] = "<option $selected value='{$res['party_id']}'>{$res['name']} $start_date - $end_date</option>";
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
        global $db, $database, $cfg;

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
            $prices = $database->queryWithOnlyFirstRow("SELECT * FROM %prefix%party_prices WHERE price_id = ?", [$price_id]);
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
        global $db, $database, $func;
        $timestamp = time();

        if ($checkin == "1") {
            $checkin = $timestamp;
        }

        if ($checkout == "1") {
            $checkout = $timestamp;
        }

        if ($price_id != 0) {
            $prices = $database->queryWithOnlyFirstRow("SELECT * FROM %prefix%party_prices WHERE price_id = ?", [$price_id]);
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

        $query .= " checkin = {$checkin},
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
        $checkin = null;
        global $db, $database, $cfg;

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
        $data = [];
        global $db, $database, $dsp;

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
                    $data[] = "<option $selected value='{$res['group_id']}'>{$res['group_name']}</option>";
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
        global $db, $database;

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
     * @param int $groupId Id of the user group to change
     * @param string $groupName The (new) name of the group
     * @param string $description Description of the group
     * @param string $selection
     * @param string $select_opts
     * @return void
     */
    public function update_user_group($groupId, $groupName, $description, $selection, $select_opts)
    {
        global $database;

        $database->query("
          UPDATE %prefix%party_usergroups
          SET
            group_name = ?,
            description = ?,
            selection = ?,
            select_opts = ?
          WHERE group_id = ?", [$groupName, $description, $selection, $select_opts, $groupId]
        );
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
        global $database;
        $database->query("UPDATE %prefix%user SET group_id = ? WHERE group_id = ?", [$set_group, $del_group]);
        $database->query("DELETE FROM %prefix%party_usergroups WHERE group_id = ?", [$del_group]);
    }

    /**
     * Returns the amount of users registered for a party.
     *
     * @param int $partyId The ID of the party to calculate this for (uses object value otherwise)
     * @param
     * @return array Result array with elements "qty" and "paid"
    */
    public function getGuestQty($partyId = null, $showOrga = null)
    {
        global $cfg, $cache, $database;

        $partyIdParameter = $partyId ?? $this->party_id;
        $showOrgaParameter = $showOrga ?? $cfg["guestlist_showorga"];

        $partyCache = $cache->getItem('party.guestcount.' . $partyIdParameter);
        if (!$partyCache->isHit()) {
            // Include Admins or not
            if ($showOrgaParameter) {
                $querytype = "type >= 1";
            } else {
                $querytype = "type = 1";
            }
            // Fetch amounts from DB
            $guestCounts = $database->queryWithOnlyFirstRow('SELECT COUNT(*) as qty, party.paid as paid FROM %prefix%user as user LEFT JOIN %prefix%party_user as party ON user.userid = party.user_id WHERE party_id= ? AND ' . $querytype . ' GROUP BY paid ORDER BY paid DESC;', [$partyIdParameter]);
            $partyCache->set($guestCounts);
            $cache->save($partyCache);
        }
        return $partyCache->get();
    }

    /**
     * Get details about this users participation at the party.
     * Most prominently the name and price of the entrance ticket
     * @param int $userid The userid to look the status up for
     * @return array Array with party & Price information
     */
    public function getUserParticipationData($userId = null)
    {
        global $database, $auth;

        $userIdParameter = $userId ?? $auth['userid'];
        $data= $database->queryWithOnlyFirstRow("SELECT * FROM %prefix%party_user AS pu LEFT JOIN %prefix%party_prices AS price ON price.price_id=pu.price_id WHERE user_id= ? and pu.party_id =?", [$userIdParameter, $this->party_id]);
        return $data;
    }

}
