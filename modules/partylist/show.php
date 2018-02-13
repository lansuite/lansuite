<?php

$xml = new xml();

switch ($_GET['step']) {
    case 10:
        $row = $db->qry_first("SELECT ls_url FROM %prefix%partylist WHERE partyid = %int%", $_GET['partyid']);
        if (substr($row['ls_url'], strlen($row['ls_url']) - 1, 1) != '/') {
            $row['ls_url'] .= '/';
        }
        if (substr($row['ls_url'], 0, 7) != 'http://') {
            $row['ls_url'] = 'http://'. $row['ls_url'];
        }
        header('Location: '. $row['ls_url'] . 'index.php?mod=signon');
        exit;
    break;
}

function GetSite($url)
{
    global $HTTPHeader;

    $url = @parse_url($url);
    if (!$url['port']) {
        $url['port'] = 80;
    }
    $url['host'] = trim($url['host']);
    $url['path'] = trim($url['path']);
    if (!$url['host'] or !$url['path']) {
        $HTTPHeader = t('Hostname, oder Pfad fehlt');
        return '';
    }
    try {
        # $ip = gethostbyname($url['host']);
        $fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 1);
    # $fp = stream_set_timeout($fp, 1);
    } catch (Exception $e) {
        // Ignore connection errors
    }

    if (!$fp) {
        $HTTPHeader = $errno.': '.$errstr;
        return '';
    } else {
        $cont = '';

        fputs($fp, "GET {$url['path']} HTTP/1.0\r\nHost: {$url['host']}\r\n\r\n");
        while (!feof($fp)) {
            $line = fgets($fp, 128);
            if ($line == '') {
                break;
            }
            $cont .= $line;
        }
        fclose($fp);

        $HTTPHeader = substr($cont, 0, strpos($cont, "\r\n\r\n"));

        $StatusCode = substr($HTTPHeader, strpos($HTTPHeader, ' ') + 1, 3);
        if ($StatusCode != 200) {
            return '';
        }

        return substr($cont, strpos($cont, "\r\n\r\n") + 4);
    }
}


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
#  $lines = @file($lsurl);
    $content = GetSite($lsurl);

#  if (!$lines) return t('infos.xml fehlt');
    if (!$content) {
        return '<div class="infolink" style="display:inline">'. t('infos.xml fehlt') .'<span class="infobox">'. $lsurl .HTML_NEWLINE.HTML_NEWLINE. str_replace("'", "\\'", str_replace('"', "'", str_replace("\r\n", HTML_NEWLINE, $HTTPHeader))) .'</span></div>';
    } else {
        #    $content = '';
    #    foreach ($lines as $line_num => $line) $content .= $line;

        $system = $xml->get_tag_content_array('system', $content);
        // Version 3.0 XML-File
        if ($system) {
            #    $name = $xml->get_tag_content('name', $system[0]);
        #    $link = $xml->get_tag_content('link', $system[0]);
        #    $language = $xml->get_tag_content('language', $system[0]);
            $current_party = $xml->get_tag_content('current_party', $system[0]);
        #    $users = $xml->get_tag_content('users', $system[0]);

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
      #     $startdate = $xml->get_tag_content('startdate', $p);
      #     $enddate = $xml->get_tag_content('enddate', $p);
      #     $sstartdate = $xml->get_tag_content('sstartdate', $p);
      #     $senddate = $xml->get_tag_content('senddate', $p);
                    $registered = $xml->get_tag_content('registered', $p);
                    $paid = $xml->get_tag_content('paid', $p);

            # Overview
                    if (!$_GET['partyid'] and $current_party == $partyid) {
                        $ret .= $func->CreateSignonBar($registered, $paid, $max_guest).'Max.: '.$max_guest;
                    }

            # Details
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
            $signon_start = $xml->get_tag_content('signon_start', $content);
            $signon_end = $xml->get_tag_content('signon_end', $content);

            return $func->CreateSignonBar($registered, $paid, $max_guest);
        }
    }
}

function EditAllowed()
{
    global $line, $auth;

    if ($line['userid'] == $auth['userid'] or $auth['type'] >= 2) {
        return true;
    } else {
        return false;
    }
}

function NameAndMotto($name)
{
    global $line, $auth;

    return $name .HTML_NEWLINE. $line['motto'];
}


if (!$_GET['partyid']) {
    if ($_GET['action'] == 'history') {
        $where = 'p.end < NOW()';
    } else {
        $where = 'p.end >= NOW()';
    }

    $dsp->NewContent(t('Party-Liste'), t('Partys, die Lansuite verwenden'));

    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2('');

    $ms2->query['from'] = "%prefix%partylist AS p";
    $ms2->query['where'] = $where;
    $ms2->query['default_order_by'] = 'p.start ASC';
    $ms2->config['EntriesPerPage'] = 20;

    $ms2->AddSelect('p.motto');
    $ms2->AddSelect('p.userid');
    $ms2->AddResultField(t('Partyname'), 'p.name', 'NameAndMotto');
    $ms2->AddResultField(t('Begin'), 'UNIX_TIMESTAMP(p.start) as start', 'MS2GetDate');
    $ms2->AddResultField(t('Ende'), 'UNIX_TIMESTAMP(p.end) as end', 'MS2GetDate');
    $ms2->AddResultField(t('Anmelde-Status'), 'ls_url', 'AddSignonStatus');

    $ms2->AddIconField('details', 'index.php?mod=partylist&action='. $_GET['action'] .'&partyid=', t('Details'));
    if ($_GET['action'] != 'history') {
        $ms2->AddIconField('signon', 'nofollow.php?mod=partylist&step=10&design=base&partyid=', t('Anmelden'));
    }
    $ms2->AddIconField('edit', 'index.php?mod=partylist&action=add&partyid=', t('Editieren'), 'EditAllowed');
    if ($auth['type'] >= 3) {
        $ms2->AddIconField('delete', 'index.php?mod=partylist&action=delete&partyid=', t('Löschen'));
    }

    $ms2->PrintSearch('index.php?mod=partylist&action='. $_GET['action'], 'p.partyid');
} else {
    $row = $db->qry_first("SELECT u.username, p.*, UNIX_TIMESTAMP(p.start) AS start, UNIX_TIMESTAMP(p.end) AS end FROM %prefix%partylist AS p
   LEFT JOIN %prefix%user AS u on p.userid = u.userid
 WHERE p.partyid = %int%", $_GET['partyid']);
    $framework->AddToPageTitle($row["name"]);

    if (substr($row['url'], 0, 7) != 'http://') {
        $row['url'] = 'http://'. $row['url'];
    }

    $dsp->NewContent($row['name'], $row['motto']);
    $dsp->AddDoubleRow(t('Datum'), $func->unixstamp2date($row['start'], 'datetime') .' bis '. $func->unixstamp2date($row['end'], 'datetime'));
    $dsp->AddDoubleRow(t('Adresse'), $row['street'] .' '. $row['hnr'] .', '. $row['plz'] .' '. $row['city']);
    $dsp->AddDoubleRow(t('Webseite'), '<a href="'. $row['url'] .'" target="_blank">'. $row['url'] .'</a> ' . $dsp->FetchIcon('nofollow.php?mod=partylist&step=10&design=base&partyid='. $_GET['partyid'], 'signon'));
    $dsp->AddDoubleRow(t('Anmeldestatus'), AddSignonStatus($row['ls_url']));
    $dsp->AddDoubleRow(t('Zusätzliche Infos'), $func->text2html($row['text']));
    $dsp->AddDoubleRow(t('Eingetragen durch'), $dsp->FetchUserIcon($row['userid'], $row['username']));

    $dsp->AddFieldsetStart('Vergangene Veranstaltungen');
    $history = AddSignonStatus($row['ls_url'], 1);
    $dsp->AddFieldsetEnd();

    $dsp->AddBackButton('index.php?mod=partylist&action='. $_GET['action']);
}
$dsp->AddContent();
