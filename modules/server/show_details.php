<?php
$serverid = $_GET["serverid"];

$server = $database->queryWithOnlyFirstRow("
  SELECT
    a.serverid,
    a.owner,
    a.caption,
    a.text,
    INET6_NTOA(a.ip) AS ip,
    a.mac,
    a.port,
    a.os,
    a.cpu,
    a.ram,
    a.hdd,
    a.type,
    a.pw,
    b.userid,
    b.username
  FROM %prefix%server AS a
  LEFT JOIN %prefix%user AS b ON a.owner = b.userid
  WHERE
    serverid = ?", [$serverid]);
     
if ($server == "") {
    $func->error(t('Der von dir aufgerufene Server existiert nicht'), "index.php?mod=server&action=show");
} else {
    $func->SetRead('server', $_GET["serverid"]);

    // Just show details if the user is not adding, deleting or chaning his comment
    $mcactParameter = $_GET["mcact"] ?? '';
    if ($mcactParameter == "" || $mcactParameter == "show") {
        $dsp->NewContent(t('Serverdetails'), t('Auf dieser Seite siehst du alle Details zum Server <b>%1</b>. Durch eine Klick auf den Zur&uuml;ck-Button gelangst du zur Übersicht zur&uuml;ck', $server["caption"]));

        $dsp->AddDoubleRow(t('Name'), $server["caption"]);
        $dsp->AddDoubleRow(t('Besitzer'), $dsp->FetchUserIcon($server['userid'], $server["username"]));

        $type_descriptor["gameserver"] = t('Gameserver');
        $type_descriptor["ftp"] = t('FTP-Server');
        $type_descriptor["irc"] = t('IRC-Server');
        $type_descriptor["voice"] = t('Voice-Server');
        $type_descriptor["web"] = t('Webserver');
        $type_descriptor["proxy"] = t('Proxy / Gateway');
        $type_descriptor["misc"] = t('Sonstiges');
        $dsp->AddDoubleRow(t('Servertyp'), $type_descriptor[$server["type"]]);

        // Wenn Intranetversion, Servererreichbarkeit testen
        if ($cfg["sys_internet"] == 0) {
            PingServer($server["ip"], $server["port"]);

            // Gescannte Daten neu auslesen
            $server_scan = $database->queryWithOnlyFirstRow('
              SELECT
                special_info,
                available,
                success,
                scans,
                UNIX_TIMESTAMP(lastscan) AS lastscan
              FROM %prefix%server
              WHERE serverid = ?', [$serverid]);

            ($server_scan["available"] == 1) ?
                $serverstatus = "<div class=\"tbl_green\">".t('Dienst erreichbar')."</div>" : $serverstatus = "<div class=\"tbl_red\">".t('Dienst nicht ereichbar')."</div>";

            ($server_scan["scans"] >= 1) ?
                $accessibleness = round((($server_scan["success"])/($server_scan["scans"])*100), 1)."%"
                : $accessibleness = t('Noch nicht getestet');

            $dsp->AddDoubleRow(t('Status'), $serverstatus);
            $dsp->AddDoubleRow(t('Erreichbarkeit'), $accessibleness);
            $dsp->AddDoubleRow(t('Gescannte Infos'), $server_scan["special_info"]);
            $dsp->AddDoubleRow(t('Letzter Scan'), $func->unixstamp2date($server_scan["lastscan"], "datetime"));
        } else {
            // Im Internet Server nicht testen
            $dsp->AddDoubleRow(t('Status'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
            $dsp->AddDoubleRow(t('Erreichbarkeit'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
            $dsp->AddDoubleRow(t('Gescannte Infos'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
            $dsp->AddDoubleRow(t('Letzter Scan'), t('Diese Funktion ist erst auf der Party verf&uuml;gbar'));
        }

        $dsp->AddDoubleRow(t('IP-Adresse / Domain'), $server["ip"]);
        $dsp->AddDoubleRow(t('MAC Adresse'), $server["mac"]);
        $dsp->AddDoubleRow(t('Port'), $server["port"]);

        if ($server["os"] == "") {
            $server["os"] = "<i>". t('Keine Angabe') ."</i>";
        }
        $dsp->AddDoubleRow(t('Betriebssystem'), $server["os"]);

        ($server["cpu"] == "0") ? $server["cpu"] = "<i>". t('Keine Angabe') ."</i>" : $server["cpu"] = $server["cpu"]." Gigaherz";
        ($server["ram"] == "0") ? $server["ram"] = "<i>". t('Keine Angabe') ."</i>" : $server["ram"] = $server["ram"]." Gigabyte";
        ($server["hdd"] == "0") ? $server["hdd"] = "<i>". t('Keine Angabe') ."</i>" : $server["hdd"] = $server["hdd"]." Gigabyte";
        $dsp->AddDoubleRow("CPU", $server["cpu"]);
        $dsp->AddDoubleRow("RAM", $server["ram"]);
        $dsp->AddDoubleRow("HDD", $server["hdd"]);

        ($server["pw"] == 1) ? $password = t('Ja') : $password = t('Nein');
        $dsp->AddDoubleRow(t('Passwort gesch&uuml;tzt'), $password);

        $dsp->AddDoubleRow(t('Beschreibung'), $func->text2html($server["text"]));

        $buttons = "";
        if ($auth['type'] > \LS_AUTH_TYPE_USER or $auth['userid'] == $server["owner"]) {
            $buttons .= $dsp->FetchSpanButton(t('Editieren'), "index.php?mod=server&action=change&step=2&serverid=$serverid") ." ";
            $buttons .= $dsp->FetchSpanButton(t('Löschen'), "index.php?mod=server&action=delete&step=2&serverid=$serverid") ." ";
        }
        if ($server["type"] == "web") {
            $buttons .= $dsp->FetchSpanButton(t('Öffnen'), "http://{$server['ip']}:{$server['port']}", t('Webseite &ouml;ffnen'), "_blank") ." ";
        }
        if ($buttons) {
            $dsp->AddDoubleRow("", $buttons);
        }

        $dsp->AddBackButton("index.php?mod=server&action=show", "server/show");
    }

    if ($auth['login'] == 1) {
        new \LanSuite\MasterRate('server', $_GET['serverid']);
        new \LanSuite\MasterComment('server', $_GET['serverid'], array('server' => 'serverid'));
    }
}
