<?php


/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2005 by One-Network.Org
*
*	Lansuite Version:	2.0.2
*	File Version:		2.0.2
*	Filename: 			class_update.php
*	Module: 			Install
*	Author:				Genesis (marco@chuchi.tv)
*	Last change: 		24.02.05
*	Description: 		Klasse um update durchzuführen
*	Remarks:
*
**************************************************************************/

class update
{
    public function update_db($file)
    {
        global $db;
/*
        if(file_exists("modules/install/update/" . $file)){
            $fp2 = fopen("modules/install/update/" . $file, "r");
            $contents2 = fread ($fp2, 1024*256);
            fclose ($fp2);

            $querrys2 = explode(";", trim($contents2));
            while (list ($key, $val) = each ($querrys2)) if ($val) {
                $db->qry("%plain%", $val);
            }


            return true;
        }else{

            return false;

        }	*/
    }
}
