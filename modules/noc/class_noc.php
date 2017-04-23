<?php

/*	Network Operations Centre
 *
 *	originally based on phpSMITH
 *
 *
 *	Maintainer: Joachim Garth <josch@one-network.org>
 *  Author: 	Marco Müller <marco@chuchi.tv>
 */

/* NOC CLASS -- Contains important functions
 *
 * function getSNMPValue( $Device, $ReadCommunity, $OID ); Reads a value from a SNMP device
 * function setSNMPValue( $Device, $WriteCommunity, $OID ); Sets a value on a SNMP device
 * function checkIP( $IP ); Checks an IP-Adress, if its correct. Later, subnet checking will be added
 * function checkSNMPDevice( $IP ); Checks for a working SNMP device
 * function getSNMPWalk ( $Device, $ReadCommunity, $OID ); Read a array form SNMP device
 */

#@snmp_set_quick_print(1);

class noc
{
    public function MACtoIP($SearchMAC, $IP, $ReadCommunity)
    {
        $walk = snmprealwalk($IP, $ReadCommunity, ".1.3.6.1.2.1.3.1.1.2");

        if ($walk) {
            $i = 0;

            foreach ($walk as $entry) {
                $oid = array_keys($walk);
                $value = $entry;

                $oid = $oid[$i];

                if (stristr("atPhysAddress", $oid[0])) {
                    strtok($oid, "atPhysAddress");
                    $tmp = strtok("atPhysAddress");
                    $tmp = substr($tmp, 5);
                }

                $IP = $tmp;
                $MAC = $value;

                if ($MAC = $SearchMAC) {
                    break;
                } else {
                    unset($MAC);
                    unset($IP);
                }

                $i++;
            }

            return $IP;
        } // END if( $walk )
    } // END Function MACtoIP

    // Returns a numeric Value ( 1 for Error, 0 for No Error) <= kinda illogical...
    public function checkSNMPDevice($IP, $ReadCommunity)
    {
        unset($error);

        if (!(@snmpget($IP, $ReadCommunity, ".1.3.6.1.2.1.1.1.0"))) {
            echo @snmpget($IP, $ReadCommunity, ".1.3.6.1.2.1.1.1.0");
            $error = 1;
        }
                    
        if ($error) {
            return 0;
        } else {
            return 1;
        }
    }
    
    public function getSNMPValue($Device, $ReadCommunity, $OID)
    {
        $SNMPAnswer = @snmpget($Device, $ReadCommunity, $OID);
        
        if (preg_match("/\b = \b/i", $SNMPAnswer) != 1) {
            $SNMPValue = $SNMPAnswer;
        } else {
            $tmp = strtok($SNMPAnswer, "=");
            $tmp = strtok("=");

            $SNMPValue = $tmp;
        }
        
        if (stristr($SNMPValue, "counter") || stristr($SNMPValue, "gauge") || stristr($SNMPValue, "string") || stristr($SNMPValue, "integer") ||  stristr($SNMPValue, "OID")) {
            $tmp = strtok($SNMPValue, ":");
            $tmp = ltrim(strtok(":"));

            while ($tmp2 = strtok(":")) {
                $tmp .= $tmp2;
            }

            $SNMPValue = $tmp;
        }

        if (stristr($SNMPValue, "timeticks")) {
            $tmp = strtok($SNMPValue, "\)");
            $tmp = ltrim(strtok("\)"));
        
            $SNMPValue = $tmp;
        }
                    
        return ltrim($SNMPValue);
    } // END FUNCTION getSNMPValue
    
    public function setSNMPValue($Device, $WriteCommunity, $OID, $Type, $Value)
    {
        $setvalue = @snmpset($Device, $WriteCommunity, $OID, $Type, $Value);

        return $setvalue;
    }

    public function getSNMPwalk($Device, $ReadCommunity, $OID)
    {
        $walkvalue = snmpwalk($Device, $ReadCommunity, $OID);
        
        foreach ($walkvalue as $value) {
            if (stristr($value, ":")) {
                $tmp = explode(":", $value);
                $data[] .= trim($tmp[1]);
            } else {
                $data[] .= trim($value);
            }
        }
        
        return $data;
    }
    
    public function getMacAddress($Device, $ReadComunity, $device_id, $modell)
    {
        global $db;
        
        $ports = $this->getSNMPwalk($Device, $ReadComunity, ".1.3.6.1.2.1.17.4.3.1.2");
        $Addresses = $this->getSNMPwalk($Device, $ReadComunity, ".1.3.6.1.2.1.17.4.3.1.1");

        //Umrechnung der Portnummer für 3Com
        if (stristr($modell, "3com")) {
            for ($i = 0; $i < count($ports); $i++) {
                $ports[$i] = 100 + $ports[$i];
            }
        }
        // Array mit Ports und Adressen zusammenfügen
        for ($i = 0; $i < count($ports); $i++) {
            if ($data[$ports[$i]] == "") {
                $data[$ports[$i]] = $Addresses[$i];
            } else {
                $data[$ports[$i]] .= " \n " . $Addresses[$i];
            }
        }
        
        // Alle MAC-Addressen für den Switch neu Setzen
    
        if (is_array($data)) {
            // Alte Adressen löschen
            $db->qry("UPDATE %prefix%noc_ports SET mac='0' WHERE deviceid =%int%", $device_id);
            // Neue Adresse setzen
            foreach ($data as $key => $value) {
                $db->qry("UPDATE %prefix%noc_ports SET mac=%string% WHERE deviceid =%int% AND portnr=%string%", $value, $device_id, $key);
            }
        }
    }
        
    public function IPtoMAC_arp($ip)
    {
        global $db,$dsp,$lang,$func;
        // Host anpingen um seine MAC-Adresse in den Speicher zu laden.
        $func->ping($ip);
        if (stristr(strtolower($_SERVER['SERVER_SOFTWARE']), "win") == "") {
            if (shell_exec("/sbin/arp -n") == "") {
                $dsp->AddSingleRow(t('Kann den Befehl arp nicht ausf&uuml;hren'));
            }
            @exec("/sbin/arp -n $ip | grep $ip", $arp_output);
            $result = array();
            preg_match("/.{2}:.{2}:.{2}:.{2}:.{2}:.{2}/i", implode("", $arp_output), $result);
        } else {
            if (shell_exec("arp -a") == "") {
                $dsp->AddSingleRow(t('Kann den Befehl arp nicht ausf&uuml;hren'));
            }
            @exec("arp -a $ip", $arp_output);
            $result = array();
            preg_match("/.{2}-.{2}-.{2}-.{2}-.{2}-.{2}/i", implode("", $arp_output), $result);
            for ($i = 0; $i < count($result); $i++) {
                $result[$i] = str_replace("-", ":", $result[$i]);
            }
        }
        // Jede gefundene MAC-Adresse zuordnen und im Netzwerk suchen
        if ($result[0] != '') {
            for ($i = 0; $i < count($result); $i++) {
                $dsp->AddDoubleRow(t('MAC-Addresse'), $result[$i]);
                $dsp->AddHRuleRow();
                $string = str_replace(":", "%", $result[$i]);
                $query = $db->qry("SELECT * FROM %prefix%noc_ports WHERE mac LIKE %string%", '%'. $string .'%');
                if ($db->num_rows($query) > 0) {
                    while ($row = $db->fetch_array($query)) {
                        $device = $db->qry_first("SELECT name,ip,id FROM %prefix%noc_devices WHERE id = %int%", $row['deviceid']);
                        $dsp->AddDoubleRow(t('Device und IP'), "<a href='index.php?mod=noc&action=details_device&deviceid={$device['id']}'>" . $device['name'] . " " . $device['ip'] . "</a>");
                        $dsp->AddDoubleRow(t('Portnummer'), "<a href='index.php?mod=noc&action=port_details&portid={$row['portid']}'>{$row['portnr']}</a>");
                        $dsp->AddHRuleRow();
                    }
                } else {
                    $dsp->AddSingleRow(t('Die Adresse konnte nicht gefunden werden.HTML_NEWLINEDie Adressen werden bei der Ansicht des Device aktuallisiert.'));
                }
            }
        } else {
            $dsp->AddSingleRow(t('Die Adresse konnte nicht gefunden werden.HTML_NEWLINEDie Adressen werden bei der Ansicht des Device aktuallisiert.'));
        }
    }
}
