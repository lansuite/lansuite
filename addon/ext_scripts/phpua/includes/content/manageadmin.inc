<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    // !!! SECURITY !!!
    if ( !$_SESSION["authenticated"] ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_ACCESSDENIED"]);
    
    if ( !empty($_REQUEST["action"]) ) {
        switch ( $_REQUEST["action"] ) {
            case $phpUA["LANGUAGE"]["Add"]:
                if (empty($_REQUEST["new_name"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADUSERNAME"]);
                if (empty($_REQUEST["new_pswd"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPASSWORD"]);
                if (empty($_REQUEST["new_pswd_confirm"])) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADCONFIRMPASSWORD"]);
                if ($_REQUEST["new_pswd"] != $_REQUEST["new_pswd_confirm"]) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPASSWORDMATCH"]);
                configAddAdmin($_REQUEST["new_name"], md5($_REQUEST["new_pswd"]));
                break;
            /*
            case $phpUA["LANGUAGE"]["Modify"]:
                if ( $_REQUEST["admin_id"] == "" ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADADMINID"]);
                if ( empty($_REQUEST["admin_name"]) ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADUSERNAME"]);
                if ( $_REQUEST["admin_pswd"] != $_REQUEST["admin_pswd_confirm"] ) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADPASSWORDMATCH"]);
                // User is allowed to not enter a password so only the name gets changed.
                if ( empty($_REQUEST["admin_pswd"]) ) $_REQUEST["admin_pswd"] = $phpUA["CONFIG"]["ADMIN"][$_REQUEST["admin_name"]];
                configRemoveAdmin($_REQUEST["admin_id"]);
                configAddAdmin($_REQUEST["admin_name"], md5($_REQUEST["admin_pswd"]));
                break;
            */
            case $phpUA["LANGUAGE"]["Remove"]:
                if ($_REQUEST["admin_id"] == "") displayErrorMessage($phpUA["LANGUAGE"]["ERROR_BADADMINID"]);
                if (count($phpUA["CONFIG"]["ADMIN"]) == 1) displayErrorMessage($phpUA["LANGUAGE"]["ERROR_ATLEASTONEADMIN"]);
                configRemoveAdmin($_REQUEST["admin_id"]);
                break;
        }
    }
    
    // Create an array of existing admin
    $i = -1;
    foreach ($phpUA["CONFIG"]["ADMIN"] as $value) {
        $i++;
        // Decode admin line (USERNAME:PASSWORD)
        $temp = explode(":", $value);
        $admin_name = $temp[0];
        $admin_array[$i] = array("id"  =>$i,
                                 "name"=>$admin_name);
    }
    
    // Output template
    $phpUA["TEMPLATE"]->assign("ADMIN", $admin_array);
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/admin/manageadmin.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>