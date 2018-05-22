<?php

if ($auth['login']) {
    $_GET['user_id'] = $auth['userid'];
    include_once("modules/usrmgr/party.php");
} else {
    include_once("modules/usrmgr/add.php");
}
