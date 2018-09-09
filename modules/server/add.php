<?php

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

    $mf = new \LanSuite\MasterForm();
    if (!$_GET['serverid']) {
        if ($auth['type'] > 1) {
            $mf->AddDropDownFromTable(t('Besitzer'), 'owner', 'userid', 'username', 'user', '', 'type > 0');
        } else {
            $mf->AddFix('owner', $auth['userid']);
        }
    }
  
    $mf->AddField(t('Name'), 'caption');

    // Party-Liste
    if ($func->isModActive('party')) {
        $party_list = array('' => t('KEINE'));
        $row = $db->qry("SELECT party_id, name FROM %prefix%partys");
        while ($res = $db->fetch_array($row)) {
            $party_list[$res['party_id']] = $res['name'];
        }
        $db->free_result($row);
        $mf->AddField(t('Party'), 'party_id', \LanSuite\MasterForm::IS_SELECTION, $party_list, $party->party_id);
    }

    $selections = array();
    $selections['gameserver'] = t('Gameserver');
    $selections['ftp'] = t('FTP Server');
    $selections['irc'] = t('IRC Server');
    $selections['web'] = t('Web Server');
    $selections['proxy'] = t('Proxy Server');
    $selections['misc'] = t('Sonstiger Server');
    $mf->AddField(t('Servertyp'), 'type', \LanSuite\MasterForm::IS_SELECTION, $selections, \LanSuite\MasterForm::FIELD_OPTIONAL);

    if ($cfg['server_ip_auto_assign']) {
        $mf->AddFix('ip', $IPBase .'.'. $cfg['server_ip_next']);
    } else {
        $mf->AddField(t('IP / Domain'), 'ip', '', '', '', 'CheckIP');
    }
  
    $mf->AddField(t('Port'), 'port', '', '', '', 'CheckPort');
    $mf->AddField(t('MAC-Adresse'), 'mac', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL, 'CheckMAC');
    $mf->AddField(t('Betriebssystem'), 'os', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('CPU (MHz)'), 'cpu', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('RAM (MB)'), 'ram', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('HDD (GB)'), 'hdd', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Passwort geschützt'), 'pw', '', '', \LanSuite\MasterForm::FIELD_OPTIONAL);
    $mf->AddField(t('Beschreibung'), 'text', '', \LanSuite\MasterForm::LSCODE_ALLOWED, \LanSuite\MasterForm::FIELD_OPTIONAL);

    if ($mf->SendForm('index.php?mod=server&action=add', 'server', 'serverid', $_GET['serverid'])) {
        // Increase auto IP
        if ($cfg['server_ip_auto_assign']) {
            $db->qry('UPDATE %prefix%config SET cfg_value = %int% WHERE cfg_key = \'server_ip_next\'', $cfg['server_ip_next'] + 1);
        }
    };
}
