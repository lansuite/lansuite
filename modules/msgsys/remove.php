<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		remove.php
*	Module: 		Msgsys
*	Main editor: 		johannes@one-network.org
*	Last change: 		04.01.2003 17:32
*	Description:
*	Remarks:
*
**************************************************************************/

//
// Check queryid
//
if ($_GET[queryid]) {
    //
    // Step
    //
    switch ($_GET[step]) {
        //
        // Case question
        //
        default:
            $rowcheck = $db->qry("
   SELECT id
   FROM %prefix%buddys
   WHERE userid = %int%
   AND buddyid = %int%
   ", $auth['userid'], $_GET[queryid]);

            //
            // User in buddylist ?
            //
            if ($db->num_rows() != '0') {
                //
                // Get name
                //
                $row = $db->qry_first("
    SELECT username, name, firstname
    FROM %prefix%user
    WHERE userid = %int%
    ", $_GET[queryid]);

                //
                // Question
                //
                if ($cfg['sys_internet'] == 0) {
                    $func->question(t('Willst du den Benutzer <b>%1 (%2 %3)</b> wirklich aus deiner Buddy-Liste entfernen?', $row[name], $row[firstname], $row[username]), "index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2", "index.php");
                } else {
                    $func->question(t('Willst du den Benutzer <b>%1</b> wirklich aus deiner Buddy-Liste entfernen?', $row[username]), "index.php?mod=msgsys&action=removebuddy&queryid=$_GET[queryid]&step=2", "index.php");
                }
            } // if
            else {
                //
                // Error
                //
                $func->error(t('Dieser Benutzer befindet sich nicht in deiner Buddy-Liste'));
            } // else

            break;

        //
        // Case remove
        //
        case 2:
            //
            // Get name
            //
            $row1 = $db->qry_first("
   SELECT username, name, firstname
   FROM %prefix%user
   WHERE userid = %int%
   ", $_GET[queryid]);

            //
            // Remove
            //
            $row2 = $db->qry("
   DELETE
   FROM %prefix%buddys
   WHERE buddyid = %int%
   AND userid = %int%
   ", $_GET[queryid], $auth['userid']);

            //
            // Confirmation
            //
            if ($row2 == true) {
                if ($cfg['sys_internet'] == 1) {
                    $func->confirmation(t('Der Benutzer <b>%1</b> wurde aus deiner Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1[username]), "");
                } else {
                    $func->confirmation(t('Der Benutzer <b>%1 (%2 %3)</b> wurde aus deiner Buddy-Liste entfernt. Die &Auml;nderung wird beim n&auml;chsten Seitenaufruf wirksam.', $row1[name], $row1[firstname], $row1[username]), "");
                }
            }



            break;
    } // switch
} // if queryid
else {
    //
    // Error
    //
    $func->error(t('Du hast keinen Benutzer ausgew&auml;hlt'));
} // else queryid
