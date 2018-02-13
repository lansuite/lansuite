<?php

include_once("modules/mail/class_mail.php");
$mail = new mail();

$usrmgr = new UsrMgr();

class UsrMgr
{
    public function SendVerificationEmail($id)
    {
        global $cfg, $db, $mail, $func, $framework, $CurentURL;
    
        $verification_code = '';
        for ($x=0; $x<=24; $x++) {
            $verification_code .= chr(mt_rand(65, 90));
        }
        $db->qry('UPDATE %prefix%user SET fcode=%string% WHERE userid = %int%', $verification_code, $id);
            
        $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
        || $_SERVER['SERVER_PORT'] == 443) {
            $proto = 'https';
        } else {
            $proto = 'http';
        }
        $verification_link = "{$proto}://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}{$path}index.php?mod=usrmgr&action=verify_email&verification_code=$verification_code";

        $row = $db->qry_first('SELECT firstname, name, email FROM %prefix%user WHERE userid = %int%', $id);
        if (!$_POST['firstname']) {
            $_POST['firstname'] = $row['firstname'];
        }
        if (!$_POST['name']) {
            $_POST['name'] = $row['name'];
        }
        if (!$_POST['email']) {
            $_POST['email'] = $row['email'];
        }

        if (!$mail->create_inet_mail($_POST['firstname'].' '.$_POST['name'], $_POST['email'], t_no_html('Verifizierung deiner eMail Adresse auf %1', $_SERVER['SERVER_NAME']), t_no_html("Du hast sich soeben bei uns auf %1 angemeldet.\r\nDamit du dich bei uns Einloggen kannst, müssen wir jedoch zuerst sicherstellen, dass deine Email korrekt ist.\r\nKlicke zum Verifizieren deiner Email-Adresse bitte auf den folgenden Link\n%2 \n\nErst nach diesem Schritt wird es möglich sein sich auf unserer Seite einzuloggen.", $_SERVER['SERVER_NAME'], $verification_link), $cfg["sys_party_mail"])) {
            $func->error(t('Es ist ein Fehler beim Versand der Verifikations-Email aufgetreten.'));
            return 0;
        }
    
        return 1;
    }

    public function LockAccount($userid)
    {
        global $db;

        $db->qry("UPDATE %prefix%user SET locked = 1 WHERE userid=%int%", $userid);
        $db->qry('DELETE FROM %prefix%stats_auth WHERE userid=%int%', $userid);
    }

    public function UnlockAccount($userid)
    {
        global $db;

        $db->qry("UPDATE %prefix%user SET locked = 0 WHERE userid=%int%", $userid);
    }

    public function GeneratePassword()
    {
        return rand(10000, 99999);
    }


    public function CheckPerso($code)
    {
        $perso_block = explode("<", $code);
        $perso_citycode = substr($perso_block[0], 0, 4);
        $perso_id = substr($perso_block[0], 4, 5);
        $perso_cs1 = substr($perso_block[0], 9, 1);
        $perso_country = substr($perso_block[0], 10, 1);
        $perso_birth = substr($perso_block[2], 0, 6);
        $perso_cs2 = substr($perso_block[2], 6, 1);
        $perso_expiration = substr($perso_block[3], 0, 6);
        $perso_cs3 = substr($perso_block[3], 6, 1);
        $perso_cs4 = substr($perso_block[10], 0, 1);

        // Length Check
        if ((strlen($code) != 36) ||
            (strlen($perso_block[0]) != 11) || (strlen($perso_block[2]) != 7) || (strlen($perso_block[3]) != 7) || (strlen($perso_block[10]) != 1)) {
            return 2;
        } // Chechsum Check
        else {
            $multiplier = array("7", "3", "1");

            $cs1 = 0;
            for ($z = 0; $z <= 8; $z ++) {
                $cs1 += (substr($perso_block[0], $z, 1) * $multiplier[$z % 3]);
            }
            $cs1 = $cs1 % 10;

            $cs2 = 0;
            for ($z = 0; $z <= 5; $z ++) {
                $cs2 += (substr($perso_block[2], $z, 1) * $multiplier[$z % 3]);
            }
            $cs2 = $cs2 % 10;

            $cs3 = 0;
            for ($z = 0; $z <= 5; $z ++) {
                $cs3 += (substr($perso_block[3], $z, 1) * $multiplier[$z % 3]);
            }
            $cs3 = $cs3 % 10;

            $cs4 = 0;
            $perso_all = substr($perso_block[0], 0, 10) . $perso_block[2] . $perso_block[3];
            for ($z = 0; $z <= 24; $z ++) {
                $cs4 += (substr($perso_all, $z, 1) * $multiplier[$z % 3]);
            }
            $cs4 = $cs4 % 10;

            if (($cs1 != $perso_cs1) || ($cs2 != $perso_cs2) || ($cs3 != $perso_cs3) || ($cs4 != $perso_cs4)) {
                return 3;

            // Expiration Check
            } else {
                $perso_expir_timestamp = mktime(0, 0, 0, substr($perso_expiration, 2, 2), substr($perso_expiration, 4, 2), substr($perso_expiration, 0, 2));
                if (time() > $perso_expir_timestamp) {
                    return 4;
                }
            }
        }
        return 1;
        // Return Values:
        // 1 = OK
        // 2 = Wrong length
        // 3 = Checksum error
        // 4 = Expired
    }


    public function SendSignonMail($type = 0)
    {
        global $cfg, $func, $templ, $dsp, $mail, $db, $auth;

        switch ($type) {
      // Register-Mail
            default:
                    $message = $cfg["signon_signonemail_text_register"];

                  $message = str_replace('%USERNAME%', $_POST['username'], $message);
                  $message = str_replace('%EMAIL%', $_POST['email'], $message);
                  $message = str_replace('%PASSWORD%', $_SESSION['tmp_pass'], $message);
                if ($_POST['clan']) {
                    $row = $db->qry_first("SELECT name FROM %prefix%clan WHERE clanid = %int%", $_POST['clan']);
                    $clan = $row['name'];
                } else {
                    $clan = $_POST['clan_new'];
                }
                        $message = str_replace('%CLAN%', $clan, $message);
                        $message = str_replace('%PARTYNAME%', $_SESSION['party_info']['name'], $message);
                        $message = str_replace('%PARTYURL%', $cfg['sys_partyurl'], $message);
                        $message = str_replace('%PAGE_TITLE%', $cfg['sys_page_title'], $message);
                if ($mail->create_inet_mail($_POST["firstname"]." ".$_POST["name"], $_POST["email"], $cfg["signon_signonemail_subject"], $message, $cfg["sys_party_mail"])) {
                    return true;
                } else {
                    return false;
                }
                break;

      // Signon-Mail
            case 1:
                if ($_POST['InsertControll'.$_GET[mf_id]]) {
                    $message = $cfg["signon_signonemail_text"];
                    $subject = $cfg["signon_signonemail_subject"];
                } else {
                      $message = $cfg["signon_signoffemail_text"];
                      $subject = $cfg["signon_signoffemail_subject"];
                }

                if (!$_GET['user_id']) {
                      $_GET['user_id'] = $auth['userid'];
                }
                if ($_GET['user_id']) {
                      $row = $db->qry_first("SELECT firstname, name, username, email FROM %prefix%user WHERE userid = %int%", $_GET['user_id']);
                      $message = str_replace('%USERNAME%', $row['username'], $message);
                      $message = str_replace('%EMAIL%', $row['email'], $message);
                      $message = str_replace('%PARTYNAME%', $_SESSION['party_info']['name'], $message);
                      $message = str_replace('%MAXGUESTS%', $_SESSION['party_info']['max_guest'], $message);
                      $anmelde_schluss = '';
                    if ($_SESSION['party_info']['s_enddate'] > 0) {
                        $anmelde_schluss = "Anmeldeschluss: ". $func->unixstamp2date($_SESSION['party_info']['s_enddate'], date);
                    }
                      $message = str_replace('%SIGNON_DEADLINE%', $anmelde_schluss, $message);
                      $message = str_replace('%PARTYURL%', $cfg['sys_partyurl'], $message);
                    if ($mail->create_inet_mail($row["firstname"]." ".$row["name"], $row["email"], $subject, $message, $cfg["sys_party_mail"])) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                      return false;
                }
                break;
        }
    }

    public function WriteXMLStatFile()
    {
        global $cfg, $db, $config;

        $xml = new xml();
        $output = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\r\n";

        $system = $xml->write_tag('version', $config['lansuite']['version'], 2);
        $system .= $xml->write_tag('name', $cfg['feed_partyname'], 2);
        $system .= $xml->write_tag('link', $cfg['sys_partyurl'], 2);
        $system .= $xml->write_tag('language', 'de-de', 2);
        $system .= $xml->write_tag('current_party', $cfg['signon_partyid'], 2);

        $row = $db->qry_first("SELECT COUNT(*) AS anz FROM %prefix%user WHERE type > 0");
        $system .= $xml->write_tag('users', $row['anz'], 2);

        $lansuite = $xml->write_master_tag('system', $system, 1);

        $res = $db->qry("SELECT party_id, name, max_guest, ort, plz, startdate, enddate FROM %prefix%partys");
        $partys = '';
        while ($row = $db->fetch_array($res)) {
            $party = $xml->write_tag('partyid', $row['party_id'], 3);
            $party .= $xml->write_tag('name', $row['name'], 3);
            $party .= $xml->write_tag('max_guest', $row['max_guest'], 3);
            $party .= $xml->write_tag('ort', $row['ort'], 3);
            $party .= $xml->write_tag('plz', $row['plz'], 3);
            $party .= $xml->write_tag('startdate', $row['startdate'], 3);
            $party .= $xml->write_tag('enddate', $row['enddate'], 3);
            $party .= $xml->write_tag('sstartdate', $row['sstartdate'], 3);
            $party .= $xml->write_tag('senddate', $row['senddate'], 3);

            $row2 = $db->qry_first("SELECT count(userid) as anz FROM %prefix%user AS user
        LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id
        WHERE party_id=%int% AND (type >= 1)", $row['party_id']);
            $party .= $xml->write_tag('registered', $row2['anz'], 3);

            $row2 = $db->qry_first("SELECT count(userid) as anz FROM %prefix%user AS user
        LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id
        WHERE (party.paid > 0) AND party_id=%int% AND (type >= 1)", $row['party_id']);
            $party .= $xml->write_tag('paid', $row2['anz'], 3);
        
            $partys .= $xml->write_master_tag('party', $party, 2);
        }
        $db->free_result($res);
        $lansuite .= $xml->write_master_tag('partys', $partys, 1);

        $output .= $xml->write_master_tag('lansuite', $lansuite, 0);

        if (is_writable('ext_inc/party_infos/')) {
            if ($fp = @fopen('ext_inc/party_infos/infos.xml', 'w')) {
                if (!@fwrite($fp, $output)) {
                    return false;
                }
                @fclose($fp);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}
