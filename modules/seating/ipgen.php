<?php

switch ($_GET['step']) {
    default:
        $_POST["ipgen_a"] = "10";
        $_POST["ipgen_b"] = "10";
        $_POST["ipgen_c"] = "0";
        $_POST["ip_offset"] = "0";
        $smarty->assign('ipgen_a', $_POST["ipgen_a"]);
        $smarty->assign('ipgen_b', $_POST["ipgen_b"]);
        $smarty->assign('ipgen_c', $_POST["ipgen_c"]);
        $smarty->assign('ip_offset', $_POST["ip_offset"]);

        $smarty->assign('form_action', "index.php?mod=seating&action=ipgen&step=10&blockid=". $_GET['blockid']);
        $smarty->assign('page_title', t('IP-Generierung'));

        $smarty->assign('case', $smarty->fetch('modules/seating/templates/ipgen_details.htm'));
        $dsp->AddSingleRow($smarty->fetch('modules/seating/templates/ipgen.htm'));
        break;

    case 10:
        $ip_a  = $_POST["ipgen_a"];
        $ip_b  = $_POST["ipgen_b"];
        $ip_c  = $_POST["ipgen_c"];
        $ip_offset  = $_POST['ipgen_offset'];

        $sort  = $_POST["ipgen_sort"];
        $br    = $_POST["ipgen_break"];
        $s_col = $_POST["ipgen_startcol"];
        $s_row = $_POST["ipgen_startrow"];

        $block_id = $_GET["blockid"];

        // Lösche alle IP einträge (sicher ist sicher)
        $del_ips = $db->qry("UPDATE %prefix%seat_seats SET ip = '' WHERE blockid=%int%", $block_id);

        // ermittle wieviele reihen und spalten der sitzplan hat
        $get_size = $db->qry_first("SELECT rows,cols FROM %prefix%seat_block WHERE blockid=%int%", $block_id);
        $max_row = $get_size["rows"];
        $max_col = $get_size["cols"];

        if ($br != 'rowcol' and $br != 'rowcol2') {
            $check_status = 'status < 10 AND status > 0 AND';
        } else {
            $check_status = '';
        }

        // Hole alle seatids nach reihen sortiert in ein array
        $count = 0;
        if ($sort=="col") {
            $order = "col ".$s_col.",row ".$s_row;
        } else {
            $order = "row ".$s_row.",col ".$s_col;
        }
        $query_sub = $db->qry("SELECT seatid, row, col FROM %prefix%seat_seats WHERE %plain% blockid=%int% ORDER BY %plain%", $check_status, $block_id, $order);
        while ($row_seat_ids = $db->fetch_array($query_sub)) {
            $seat_ids[$count] = $row_seat_ids["seatid"];
            $seat_row[$count] = $row_seat_ids["row"];
            $seat_col[$count] = $row_seat_ids["col"];
            $count++;
        } // while


        // Marco Müller
        // $ip_d = 0;
        $ip_d = ($ip_offset - 1);

        $durchg = 0;

        // zähle das array und update db
        for ($i=1; $i<=$count; $i++) {
            $durchg++;

            // aktuelle anzahl der Spalten falls Sitzplaetze deaktiviert
            $colakt = $seat_col[$i-1];
            $get_size = $db->qry("SELECT row FROM %prefix%seat_seats WHERE blockid=%int% and col=%string%", $block_id, $colakt);
            $max_row_durchg = $db->num_rows($get_size);

            // aktuelle anzahl der Zeilen falls Sitzplaetze deaktiviert
            $rowakt = $seat_row[$i-1];
            $get_size2 = $db->qry("SELECT col FROM %prefix%seat_seats WHERE blockid=%int% and row=%string%", $block_id, $rowakt);
            $max_col_durchg = $db->num_rows($get_size2);

            //ip hochzählen
            $ip_d++;

            // überlauf der 4. stelle abfangen und 3. hochzählen
            if ($ip_d>254) {
                $ip_c++;
                $ip_d = 1;
                // echo "<font color=red>normaler Überlauf</font><br><br>";
            }

            // seat_id aus array ermitteln
            $seat_id = $seat_ids[$i-1];

            // ip adresse aufbauen
            $ip = $ip_a.".".$ip_b.".".$ip_c.".".$ip_d;

            // db updaten
            $db->qry("UPDATE %prefix%seat_seats SET ip=%string% WHERE seatid=%int%", $ip, $seat_id);

            /*
            echo $i." zähler<br>";
            echo $ip." ip<br>";
            echo $seat_id." seatid<br>";
            echo $seat_row[$i-1]." seat row<br>";
            echo $seat_col[$i-1]." seat col<br>";
            echo $max_row_durchg . " max Rows Durchg.<br>";
            echo $max_col_durchg . " max Cols Durchg.<br>";
            */

            switch ($br) {
                case "rowcol":
                    if ($sort=="row") {
                        if ($durchg==$max_col_durchg) {
                            $ip_c++;
                            $ip_d = 0;
                            $durchg = 0;
                            // echo "<font color=red>Max COL = SEAT COL (Überlauf)</font><br><br>";
                        }
                    }

                    if ($sort=="col") {
                        if ($durchg==$max_row_durchg) {
                            $ip_c++;
                            $ip_d = 0;
                            $durchg = 0;
                            // echo "<font color=red>Max ROW = SEAT ROW (Überlauf)</font><br><br>";
                        }
                    }

                    break;


                case "rowcol2":
                    if ($sort=="row") {
                        if ($durchg==$max_col_durchg) {
                            if ($sp2 == false) {
                                $sp2 = true;
                                $durchg = 0;
                            } else {
                                $ip_c++;
                                $ip_d = 0;
                                $durchg = 0;
                                $sp2 = false;
                            }
                            // echo "<font color=red>Max COL = SEAT COL (Überlauf)</font><br><br>";
                        }
                    }

                    if ($sort=="col") {
                        if ($durchg==$max_row_durchg) {
                            if ($sp2 == false) {
                                $sp2 = true;
                                $durchg = 0;
                            } else {
                                $ip_c++;
                                $ip_d = 0;
                                $durchg = 0;
                                $sp2 = false;
                            }
                            // echo "<font color=red>Max ROW = SEAT ROW (Überlauf)</font><br><br>";
                        }
                    }
                    break;
            } // switch
        } //for loop

        $func->confirmation(t('Die IP Adressen wurden erfolgreich eingetragen.'), "index.php?mod=seating");
        break;

  // Delete IPs
    case 20:
        $func->question(t('IPs dieses Sitzblocks wirklich alle löschen?'), 'index.php?mod=seating&action=ipgen&step=21&blockid=' .$_GET['blockid'], 'index.php?mod=seating');
        break;

    case 21:
        $db->qry('UPDATE %prefix%seat_seats SET ip = \'\' WHERE blockid = %int%', $_GET['blockid']);
        $func->confirmation(t('Die IPs dieses Plans wurden erfolgreich gelöscht'), 'index.php?mod=seating');
        break;
} // switch
