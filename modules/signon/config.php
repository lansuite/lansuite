<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		0.3
*	Filename: 			add.php
*	Module: 			anmeldung
*	Main editor: 		webmaster@ewep.de
*	Last change: 		10.09.2003 13:31
*	Description: 		Anmeldung für neue Benutzer
*	Remarks:
*
**************************************************************************/

// DEFINE GET/POST VARS:
$step        = $_GET['step'];


switch ($step) {
    default:
        $dsp->NewContent(t('Zur LAN-Party <b>%1</b> anmelden', $cfg["sys_lanpartyname"]), str_replace("%NAME%", $cfg["sys_lanpartyname"], t('Bitte fülle die folgenden Felder sorgfälltig aus')));
        $dsp->SetForm("index.php?mod=signon&action=config&step=2");
        $dsp->AddDoubleRow("username", "Pflichteingabe");
        $dsp->AddDoubleRow("email", "Pflichteingabe");

        $rows = $db->qry("SELECT * FROM %prefix%config WHERE cfg_group = 'Anmeldungsfelder' ORDER BY cfg_value DESC");
        while ($row = $db->fetch_array($rows)) {
            $option_array = array(t('Nicht anzeigen'), t('Optionale Eingabe'), t('Pflichteingabe'));
            $t_array = array();
            while (list($key, $val) = each($option_array)) {
                ($key == $row["cfg_value"]) ? $selected = "selected" : $selected = "";
                array_push($t_array, "<option $selected value=\"$key\">$val</option>");
            }
            $dsp->AddDropDownFieldRow($row["cfg_key"], $row["cfg_key"], $t_array, "", 1);
        }
        $db->free_result($rows);

        $dsp->AddFormSubmitRow(t('Hinzufügen'));
        $dsp->AddContent();
        break;

    case 2:
        while (list($key, $val) = each($_POST)) {
            $db->qry("UPDATE %prefix%config SET cfg_value = %string% WHERE cfg_key = %string%", $val, $key);
        }
        $func->confirmation(t('Einstellungen wurden erfolgreich geändert'), "index.php?mod=signon&action=config");
        break;
}
