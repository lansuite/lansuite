<?php

/**
 * @param string $lsurl
 * @param int $show_history
 * @return string
 */
function AddSignonStatus($lsurl, $show_history = 0)
{
    global $xml, $dsp, $HTTPHeader, $func;

    if (substr($lsurl, strlen($lsurl) - 1, 1) != '/') {
        $lsurl .= '/';
    }
    if (substr($lsurl, 0, 7) != 'http://') {
        $lsurl = 'http://'. $lsurl;
    }
    $lsurl .= 'ext_inc/party_infos/infos.xml';
    $content = GetSite($lsurl);

    if (!$content) {
        return '<div class="infolink" style="display:inline">'. t('infos.xml fehlt') .'<span class="infobox">'. $lsurl .HTML_NEWLINE.HTML_NEWLINE. str_replace("'", "\\'", str_replace('"', "'", str_replace("\r\n", HTML_NEWLINE, $HTTPHeader))) .'</span></div>';
    } else {
        $system = $xml->get_tag_content_array('system', $content);
        // Version 3.0 XML-File
        if ($system) {
            $current_party = $xml->get_tag_content('current_party', $system[0]);

            $partys = $xml->get_tag_content_array('party', $content);
            $ret = '';
            if (!$partys) {
                return t('Noch keine Party angelegt');
            } else {
                foreach ($partys as $p) {
                    $partyid = $xml->get_tag_content('partyid', $p);
                    $partyname = $xml->get_tag_content('name', $p);
                    $max_guest = $xml->get_tag_content('max_guest', $p);
                    $ort = $xml->get_tag_content('ort', $p);
                    $plz = $xml->get_tag_content('plz', $p);
                    $registered = $xml->get_tag_content('registered', $p);
                    $paid = $xml->get_tag_content('paid', $p);

                    // Overview
                    if (!$_GET['partyid'] and $current_party == $partyid) {
                        $ret .= $func->CreateSignonBar($registered, $paid, $max_guest).'Max.: '.$max_guest;
                    }

                    // Details
                    if ($_GET['partyid']) {
                        if (!$show_history and $current_party == $partyid) {
                            $ret .= $func->CreateSignonBar($registered, $paid, $max_guest);
                        } elseif ($show_history and $current_party != $partyid) {
                            $dsp->AddDoubleRow($partyname .HTML_NEWLINE. $plz .' '. $ort, $func->CreateSignonBar($registered, $paid, $max_guest));
                        }
                    }
                }
            }
            return $ret;

            // Old version
        } else {
            $guests = $xml->get_tag_content('guests', $content);
            $paid_guests = $xml->get_tag_content('paid_guests', $content);
            $max_guests = $xml->get_tag_content('max_guests', $content);

            return $func->CreateSignonBar($guests, $paid_guests, $max_guests);
        }
    }
}
