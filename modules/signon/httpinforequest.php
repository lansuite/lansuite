<?php

switch ($_GET["info"]) {
    default:
        die("none");
    break;

    case "max_guest":
        die($_SESSION["party"]["max_guest"]);
    break;
    
    case "sign_guest":
        $get_cur = $db->qry_first("SELECT count(userid) as n FROM %prefix%user WHERE type = 1");
        die($get_cur["n"]);

    break;
    
    case "paid_guest":
        $get_cur = $db->qry_first("SELECT count(userid) as n FROM %prefix%user WHERE type = 1 AND paid = 1");
        die($get_cur["n"]);
    
    break;
}
