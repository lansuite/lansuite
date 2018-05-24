<?php

$framework->set_modus('ajax');
switch ($_GET['shout']) {
    case 'add':
        if ($_POST['captchaInputSend'] == $_SESSION['captcha'] and $_POST['captchaInputSend'] != "") {
            $captchaCheck = true;
        } else {
            $captchaCheck = false;
        }

        if (!$auth['login'] or !$captchaCheck) {
            // No Login -> Captcha
            include_once('ext_scripts/ascii_captcha.class.php');
            $captcha = new \ASCII_Captcha();
            $cap = $captcha->create($text);
            $_SESSION['captcha'] = $text;
            $data['response'] = 'captcha';
            $data['code'] = $text;
            $data['captcha'] = $cap;
        }

        if (($_POST['message'] and $auth['login']) or ($_POST['message'] and $captchaCheck)) {
            if ($auth['type']>=1) {
                $_POST['nickname'] = $auth['username'];
            }

            $result = $db->qry("
              INSERT INTO %prefix%shoutbox (userid, ip, name, message)
              VALUES (%int%, %string%, %string%, %string%)", $auth['userid'], $auth['ip'], $_POST["nickname"], $_POST["message"]);

            $resp =  $db->qry_first("SELECT id, created FROM %prefix%shoutbox WHERE id = %int%", $db->insert_id());

            $data['response'] = 'Good work';
            $data['nickname'] = $_POST['nickname'];
            $data['message'] = $_POST['message'];
            $data['time'] = strtotime($resp['created']);
            $data['id'] = $resp['id'];
        }
        break;

    case 'view':
        $data = array();

        if (!$_GET['lastid']) {
            $_GET['lastid'] = 0;
        }

        $qry = $db->qry('SELECT * FROM %prefix%shoutbox WHERE id > %int% ORDER BY ID DESC LIMIT %int%', $_GET['lastid'], $cfg['shout_entries']);
        while ($row = $db->fetch_array($qry)) {
            $data[] = array(
                "message"   => $row['message'],
                "nickname"  => $row['name'],
                "time"      => strtotime($row['created']),
                "id"        => $row['id']
            );
        }

        $data = array_reverse($data);
        $db->free_result($qry);
        break;
}

header("Content-Type: application/json; charset=utf-8");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo json_encode($data);
