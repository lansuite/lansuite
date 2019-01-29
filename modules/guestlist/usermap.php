<?php

$dsp->NewContent(t('Besucherkarte'), t('Hier siehst du aus welchen Gegenden Deutschlands Besucher zu dieser Party kommen'));

// Use Googlemaps
if ($cfg['guestlist_guestmap'] == 2) {
    $where_pid = '';
    if ($party->party_id) {
        $where_pid = "AND (p.party_id = {$party->party_id})";
    }

    $res = $db->qry("
      SELECT
        u.*
      FROM %prefix%user AS u
    LEFT JOIN %prefix%party_user AS p ON u.userid = p.user_id
    WHERE
      u.plz > 0
      AND u.type > 0 %plain%
    ORDER BY u.city, u.country, u.username ASC", $where_pid);
    if ($res->num_rows > 0) {
        $templ['addresses'] = '';
        $adresses = 'var adresses = [';
        $last_city='';
        $aggregated_text='';
        while ($row = $db->fetch_array($res)) {
            ($row['country'])? $country = $row['country'] : $country = $cfg['sys_country'];
            switch ($country) {
                case 'de':
                    $GCountry = 'Germany';
                    break;
                case 'at':
                    $GCountry = 'Austria';
                    break;
                case 'ch':
                    $GCountry = 'Swiss';
                    break;
                case 'en':
                    $GCountry = 'England';
                    break;
                case 'nl':
                    $GCountry = 'Netherlands';
                    break;
                case 'es':
                    $GCountry = 'Spain';
                    break;
                case 'it':
                    $GCountry = 'Italy';
                    break;
                case 'fr':
                    $GCountry = 'France';
                    break;
                default:
                    $GCountry = 'Germany';
                    break;
            }

            // Show detailed map to admins only, otherwise stick to user settings
            if ($row['show_me_in_map'] == 1 || $auth['type'] >= 2) {
                $text = "<b>{$row['username']}</b>";
                if ($cfg['guestlist_shownames']|| $auth['type'] >= 2) {
                    $text .= " {$row['firstname']} {$row['name']}";
                }
            } else {
                $text = "<i><b>anonymous</b></i>";
            }
            if ($func->chk_img_path($row['avatar_path'])) {
                $text .= sprintf('<br/><img src=\\"%s\\" alt=\\"%s\\" border=\\"0\\"></br></br>', $row["avatar_path"], '');
            }
            if ($row['city']!=$last_city) {
                //next (or first) area, flush current entry and prepare for the next one
                if (!empty($aggregated_text)) {
                    $adresses .= $aggregated_text . "'},\r\n";
                }
                $aggregated_text = "{'country':'$GCountry', 'city':'{$row['city']}', 'plz':'{$row['plz']}', 'street':'', 'hnr':'', 'text':'<h3>{$row['city']}</h3> $text";
                $last_city = $row['city'];
            } else {
                $aggregated_text  .= "<hr>$text";
            }
        }
        $adresses .= '];';
        $db->free_result($res);
        if (!empty($cfg['google_analytics_id'])) {
            $smarty->assign('apikey', 'key='. $cfg['google_analytics_id']);
            $smarty->assign('adresses', $adresses);
            $dsp->AddSingleRow($smarty->fetch('modules/guestlist/templates/googlemaps.htm'));
        } else {
            $func->error(t('Die Google Analytics ID wurde nicht konfiguriert, ist aber notwendig zur Benutzung von Google Maps-Diensten.'));
        }
    } else {
        $get_cur = $db->qry_first('SELECT COUNT(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE party_id=%int% AND (%plain%)', $party->party_id, ($cfg["guestlist_showorga"] == 0 ? "type = 1" : "type >= 1"));
        if ($get_cur["n"]>0) {
            $func->error(t('Leider hat noch keiner der angemeldeten Besucher dieser Party seine Postleitzahl angegeben. Es kann daher keine Karte angezeigt werden.'));
        } else {
            $func->error(t('Es hat sich noch niemand für die aktuelle Party angemeldet, daher ist eine Anzeige der Karte nicht möglich.'));
        }
    }
// Use Geofreedb
} else {
    $res = $db->qry("SELECT plz FROM %prefix%user LEFT JOIN %prefix%party_user ON userid = user_id WHERE (plz > 0) AND (party_id = %int%)", $party->party_id);
    $res3 = $db->qry_first("SELECT laenge, breite FROM %prefix%locations WHERE plz = %int%", $_SESSION['party_info']['partyplz']);
    $pi = pi();

    if ($db->num_rows($res) == 0) {
        $get_cur = $db->qry_first('SELECT COUNT(userid) as n FROM %prefix%user AS user LEFT JOIN %prefix%party_user AS party ON user.userid = party.user_id WHERE party_id=%int% AND (%plain%)', $party->party_id, ($cfg["guestlist_showorga"] == 0 ? "type = 1" : "type >= 1"));
        if ($get_cur["n"]>0) {
            $func->error(t('Leider hat noch keiner der angemeldeten Besucher dieser Party seine Postleitzahl angegeben. Es kann daher keine Karte angezeigt werden.'));
        } else {
            $func->error(t('Es hat sich noch niemand für die aktuelle Party angemeldet, daher ist eine Anzeige der Karte nicht möglich.'));
        }
    } else {
        $map_out = '<script type="text/javascript" src="ext_scripts/overlib421/Mini/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
<div id="tooltip" class="tooltip" style="position: absolute; width: auto; height: auto; z-index: 100; visibility: hidden; left: 0; top: 0;"></div><script src="modules/guestlist/templates/map.js" type="text/javascript"></script><map name="deutschland">';

        $x_start = 5.43;
        $y_start = 46.95;
        $x_end = 15.942;
        $y_end = 55.155;

        $size = GetImageSize("modules/guestlist/map.png");
        $img_width = $size[0];
        $img_height = $size[1];

        $xf = $img_width / (abs($x_start - $x_end));
        $yf = $img_height / (abs($y_start - $y_end));

        $res4 = $db->qry("
          SELECT
            user.userid,
            user.username,
            user.city,
            user.plz,
            COUNT(*) AS anz,
            locations.laenge,
            locations.breite
          FROM %prefix%user AS user
            INNER JOIN %prefix%locations AS locations ON user.plz = locations.plz
            INNER JOIN %prefix%party_user AS party ON user.userid = party.user_id
          WHERE
            (user.plz > 0)
            AND user.show_me_in_map = 1
            AND (party.party_id = %int%)
            AND user.type > 0
          GROUP BY locations.laenge, locations.breite", $party->party_id);
        $z = 0;
        while ($user = $db->fetch_array($res4)) {
            $z++;

            $kx = (int) ($xf * ($user['laenge'] - $x_start));
            $ky = (int) ($img_height - $yf * ($user['breite'] - $y_start));
            $size = floor(1 + 0.25 * $user['anz']);
            if ($size > 5) {
                $size = 5;
            }

            // Get list of all users with current plz
            $res2 = $db->qry("
              SELECT
                u.username,
                u.firstname,
                u.name
              FROM %prefix%user AS u
                INNER JOIN %prefix%party_user AS p ON u.userid = p.user_id
                INNER JOIN %prefix%locations AS locations ON u.plz = locations.plz
              WHERE
                (
                  laenge LIKE %string%
                  AND breite LIKE %string%
                )
                AND u.show_me_in_map = 1
                AND (p.party_id = %int%)
                AND u.type > 0
              GROUP BY u.userid", $user['laenge'], $user['breite'], $party->party_id);

            $UsersOut = '';

            while ($current_user = $db->fetch_array($res2)) {
                if ($auth['type'] < 2 and ($cfg['sys_internet'])) {
                    $current_user['firstname'] = '---';
                    $current_user['name'] = '---';
                }
                $UsersOut .= HTML_NEWLINE . $current_user['username'] .' ('. $current_user['firstname'] .' '. $current_user['name'] .')';
            }
            $db->free_result($res2);

            // Distance calculations
            $breite1 = $user['breite']/180*$pi;
            $breite2 = $res3['breite']/180*$pi;
            $laenge1 = $user['laenge']/180*$pi;
            $laenge2 = $res3['laenge']/180*$pi;

            $e = acos(sin($breite1)*sin($breite2) + cos($breite1)*cos($breite2)*cos($laenge2-$laenge1));
            $entfernung = round($e * 6378.137, 0);

            $hint = '<b>'. $user['plz'] .' '. $user['city'] .' ('. $user['anz'] . ' G&auml;ste) '. $entfernung .'km</b>'. $UsersOut;

            $map_out .= "<area name=\"point$z\" shape=\"rect\" coords=\"". ($kx-$size) .", ". ($ky-$size) .", ". ($kx+$size) .", ". ($ky+$size) ."\" onmouseover=\"return overlib('". $hint ."');\" onmouseout=\"return nd();\"' />";
        }
        $db->free_result($res4);
        $map_out .= "</map>";

        $dsp->AddSingleRow($map_out ."<img src=\"index.php?mod=guestlist&action=usermap_img&design=base\" usemap=\"#deutschland\" border=\"0\">");
    }

    $db->free_result($res);
}
