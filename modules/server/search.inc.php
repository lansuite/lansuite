<?php
include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2();

function PWIcon($pw)
{
    global $dsp, $templ;
  
    if ($pw) {
        return $dsp->FetchIcon('', 'locked', t('Geschützt'));
    } else {
        return $dsp->FetchIcon('', 'unlocked', t('Nicht geschützt'));
    }
}

function ServerType($type)
{
    switch ($type) {
        default:
            return "???";
        break;
        case "gameserver":
            return "Game";
        break;
        case "ftp":
            return "FTP";
        break;
        case "irc":
            return "IRC";
        break;
        case "web":
            return "Web";
        break;
        case "proxy":
            return "Proxy";
        break;
        case "misc":
            return "Misc";
        break;
    }
}

function ServerStatus()
{
    global $cfg, $line;

    // Wenn Intranetversion, erreichbarkeit testen
    if ($cfg["sys_internet"] == 0 and (!get_cfg_var("safe_mode"))) {
        include_once("modules/server/ping_server.inc.php");
        ping_server($line['ip'], $line['port']);

        if ($line['available'] == 1) {
            return "<div class=\"tbl_green\">Online</div>";
        } elseif ($line['available'] == 2) {
            return "<div class=\"tbl_red\">Port Offline</div>";
        } else {
            return "<div class=\"tbl_red\">IP Offline</div>";
        }
    } else {
        return "-";
    }
}


$ms2->query['from'] = "%prefix%server AS s LEFT JOIN %prefix%user AS u ON s.owner = u.userid";

$ms2->config['EntriesPerPage'] = 30;

$ms2->AddTextSearchField(t('Name'), array('s.caption' => 'like', 's.ip' => 'like'));
$ms2->AddTextSearchField(t('Besitzer'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));
$ms2->AddTextSearchDropDown(t('Servertyp'), 's.type', array('' => t('Alle'), 'gameserver' => 'Game', 'ftp' => 'FTP', 'irc' => 'IRC', 'web' => 'Web', 'proxy' => 'Proxy', 'misc' => 'Misc'));
$ms2->AddTextSearchDropDown('PW', 's.pw', array('' => t('Alle'), '0' => t('Nein'), '1' => t('Ja')));

$ms2->AddSelect('u.userid');
$ms2->AddResultField(t('Name'), 's.caption');
$ms2->AddResultField(t('Servertyp'), 's.type', 'ServerType');
$ms2->AddResultField(t('IP-Adresse / Domain'), 's.ip');
$ms2->AddResultField(t('Port'), 's.port');
$ms2->AddResultField(t('Besitzer'), 'u.username', 'UserNameAndIcon');
$ms2->AddResultField('PW', 's.pw', 'PWIcon');
$ms2->AddResultField(t('Status'), 's.available', 'ServerStatus');

$ms2->AddIconField('details', 'index.php?mod=server&action=show_details&serverid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=server&action=change&step=2&serverid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=server&action=delete&step=2&serverid=', t('Löschen'));
}

$ms2->PrintSearch('index.php?mod=server&action=show', 's.serverid');
