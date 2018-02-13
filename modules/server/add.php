<?php
function CheckIP($ip)
{
    global $cfg;

    if ($cfg['sys_internet'] == 0) {
        $ip_address = gethostbyname($ip);
    } else {
        $ip_address = $ip;
    }

    $explode = explode('.', $ip_address);
    $count = count($explode);
    if ($count != 4) {
        return t('Bitte gib eine gültige IP Adresse ein');
    } elseif ($explode[0] > 255 or $explode[1] > 255 or $explode[2] > 255 or $explode[3] > 255) {
        return t('Bitte gib eine gültige IP Adresse ein');
    }

    return false;
}

function CheckMAC($mac)
{
    if ($mac) {
        $explode = explode('-', $mac);
        $count = count($explode);
        if ($count != 6) {
            return t('Bitte gib eine gültige MAC Adresse ein');
        }
    }
    return false;
}

function CheckPort($port)
{
    if ($port < 1 or $port > 65535) {
        return t('Der Port muss zwischen 1 und 65535 liegen');
    }
    return false;
}

if ($auth['type'] <= 1) {
    $get_paid = $db->qry_first('SELECT paid FROM %prefix%party_user WHERE user_id = %int% AND party_id = %int%', $auth['userid'], $party->party_id);
}
if ($cfg['server_ip_auto_assign']) {
    $IPBase = substr($cfg['server_ip_auto_assign'], 0, strrpos($cfg['server_ip_auto_assign'], '.'));
    $IPArea = substr($cfg['server_ip_auto_assign'], strrpos($cfg['server_ip_auto_assign'], '.') + 1, strlen($cfg['server_ip_auto_assign']));
    $IPStart = substr($IPArea, 0, strrpos($IPArea, '-'));
    if (!$cfg['server_ip_next']) {
        $cfg['server_ip_next'] = $IPStart;
    }
    $IPEnd = substr($IPArea, strrpos($IPArea, '-') + 1, strlen($IPArea));
}

if ($cfg['server_ip_auto_assign'] and $cfg['server_ip_next'] > $IPEnd) {
    $func->information(t('Es sind keine freien IPs mehr vorhanden. Bitte einen Administrator darum den vorgesehenen Bereich zu erhöhren'), "index.php?mod=server");
} elseif ($cfg["server_admin_only"] and $auth['type'] <= 1) {
    $func->information(t('Nur Adminsitratoren dürfen Server hinzufügen'), "index.php?mod=server");
} elseif (!$get_paid['paid'] and $auth["type"] <= 1) {
    $func->information(t('Du musst zuerst bezahlen, um Server hinzufügen zu dürfen'), "index.php?mod=server");
} else {
    $dsp->NewContent(t('Server'), t('Hinzufügen und Aendern der Server'));

    $mf = new masterform();

    if (!$_GET['serverid']) {
        if ($auth['type'] > 1) {
            $mf->AddDropDownFromTable(t('Besitzer'), 'owner', 'userid', 'username', 'user', '', 'type > 0');
        } else {
            $mf->AddFix('owner', $auth['userid']);
        }
    }
  
    $mf->AddField(t('Name'), 'caption');

  //Party-Liste
    if ($func->isModActive('party')) {
        $party_list = array('' => t('KEINE'));
        $row = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($res = $db->fetch_array($row)) {
            $party_list[$res['party_id']] = $res['name'];
        }
        $db->free_result($row);
        $mf->AddField(t('Party'), 'party_id', IS_SELECTION, $party_list, $party->party_id);
    }

    $selections = array();
    $selections['gameserver'] = t('Gameserver');
    $selections['ftp'] = t('FTP Server');
    $selections['irc'] = t('IRC Server');
    $selections['web'] = t('Web Server');
    $selections['proxy'] = t('Proxy Server');
    $selections['misc'] = t('Sonstiger Server');
    $mf->AddField(t('Servertyp'), 'type', IS_SELECTION, $selections, FIELD_OPTIONAL);
  

    if ($cfg['server_ip_auto_assign']) {
        $mf->AddFix('ip', $IPBase .'.'. $cfg['server_ip_next']);
    } else {
        $mf->AddField(t('IP / Domain'), 'ip', '', '', '', 'CheckIP');
    }
  
    $mf->AddField(t('Port'), 'port', '', '', '', 'CheckPort');
    $mf->AddField(t('MAC-Adresse'), 'mac', '', '', FIELD_OPTIONAL, 'CheckMAC');
    $mf->AddField(t('Betriebssystem'), 'os', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('CPU (MHz)'), 'cpu', '', '', FIELD_OPTIONAL);
    $mf->AddField(t('RAM (MB)'), 'ram', '', '', FIELD_OPTIONA);
    $mf->AddField(t('HDD (GB)'), 'hdd', '', '', FIELD_OPTIONA);
    $mf->AddField(t('Passwort geschützt'), 'pw', '', '', FIELD_OPTIONA);
    $mf->AddField(t('Beschreibung'), 'text', '', LSCODE_ALLOWED, FIELD_OPTIONA);

    if ($mf->SendForm('index.php?mod=server&action=add', 'server', 'serverid', $_GET['serverid'])) {
        // Increase auto IP
        if ($cfg['server_ip_auto_assign']) {
            $db->qry('UPDATE %prefix%config SET cfg_value = %int% WHERE cfg_key = \'server_ip_next\'', $cfg['server_ip_next'] + 1);
        }
    };
}
