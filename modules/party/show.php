<?php

function GetActiveState($id)
{
    global $cfg;

    if ($cfg['signon_partyid'] == $id) {
        return 'Aktive Party';
    } else {
        return '<a href="index.php?mod=party&action=show&step=10&party_id='. $id .'">Aktivieren</a>';
    }
}

function GetMinimumAgeString($minage)
{
    return ($minage == 0) ? t('Kein Mindestalter') : $minage;
}

// Set Active PartyID
if ($_GET['step'] == 10 and is_numeric($_GET['party_id'])) {
    $db->qry("UPDATE %prefix%config SET cfg_value = %int% WHERE cfg_key = 'signon_partyid'", $_GET['party_id']);
    $cfg['signon_partyid'] = $_GET['party_id'];
}

function GetGuests($max_guest)
{
    global $db, $func, $line;

    $row = $db->qry_first('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE party_id = %int%', $line['party_id']);
    $row2 = $db->qry_first('SELECT COUNT(*) AS anz FROM %prefix%party_user WHERE paid > 0 AND party_id = %int%', $line['party_id']);
    return $func->CreateSignonBar($row['anz'], $row2['anz'], $max_guest);
}

$dsp->NewContent(t('Unsere Partys'), t('Hier siehst du eine Liste aller geplanten Partys'));
switch ($_GET['step']) {
    default:
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('party');
    
        $ms2->query['from'] = "%prefix%partys AS p";
        $ms2->query['default_order_by'] = 'p.startdate DESC';
    
        $ms2->config['EntriesPerPage'] = 20;
    
        $ms2->AddResultField('Name', 'p.name');
        $ms2->AddResultField('Gäste', 'p.max_guest', 'GetGuests');
        $ms2->AddResultField('Von', 'p.startdate');
        $ms2->AddResultField('Bis', 'p.enddate');
        $ms2->AddResultField(t('Mindestalter'), 'p.minage', 'GetMinimumAgeString');
        if ($auth['type'] >= 2) {
            $ms2->AddResultField('Aktiv', 'p.party_id', 'GetActiveState');
        }

        $ms2->AddIconField('details', 'index.php?mod=party&action=show&step=1&party_id=', t('Details'));
        $ms2->AddIconField('signon', 'index.php?mod=usrmgr&action=party&user_id='. $auth['userid'] .'&party_id=', t('Partyanmeldung'));
        if ($auth['type'] >= 2) {
            $ms2->AddIconField('edit', 'index.php?mod=party&action=edit&party_id=', t('Editieren'));
        }
        if ($auth['type'] >= 2) {
            $ms2->AddIconField('delete', 'index.php?mod=party&action=delete&party_id=', t('Editieren'));
        }
        if ($auth['type'] >= 2) {
            $ms2->AddIconField('paid', 'index.php?mod=party&action=price&step=2&party_id=');
        }

    #if ($auth['type'] >= 3) $ms2->AddMultiSelectAction(t('Löschen'), 'index.php?mod=party&action=delete', 1);

        $ms2->PrintSearch('index.php?mod=party', 'p.party_id');

        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=party&action=edit'));
    
        if ($auth['type'] >= 2 and isset($_SESSION['party_id'])) {
            $func->information(t('Der Status "Aktiv" zeigt an, welche Party standardmäßig für alle aktiviert ist, die nicht selbst eine auf der Startseite, oder in der Party-Box ausgewählt haben. In deinem Browser ist jedoch aktuell die Party mit der ID %1 aktiv. Welche Party für dich persöhnlich die aktivie ist, kannst du auf der Startseite, oder in der Party-Box einstellen', $_SESSION['party_id']), NO_LINK);
        }
        break;

    case 1:
        $row = $db->qry_first("SELECT p.*, UNIX_TIMESTAMP(p.startdate) AS startdate, UNIX_TIMESTAMP(p.enddate) AS enddate, UNIX_TIMESTAMP(p.sstartdate) AS sstartdate, UNIX_TIMESTAMP(p.senddate) AS senddate FROM %prefix%partys AS p WHERE party_id=%int%", $_GET["party_id"]);

        $dsp->AddDoubleRow(t('Partyname'), $row['name']);
        $dsp->AddDoubleRow(t('Anzahl Plätze'), $row['max_guest']);
        $dsp->AddDoubleRow(t('Mindestalter'), GetMinimumAgeString($row['minage']));
        $dsp->AddDoubleRow(t('PLZ'), $row['plz']);
        $dsp->AddDoubleRow(t('Ort'), $row['ort']);
        $dsp->AddDoubleRow(t('Party startet am'), $func->unixstamp2date($row['startdate'], "datetime"));
        $dsp->AddDoubleRow(t('Party endet am'), $func->unixstamp2date($row['enddate'], "datetime"));
        $dsp->AddDoubleRow(t('Anmeldung startet am'), $func->unixstamp2date($row['sstartdate'], "datetime"));
        $dsp->AddDoubleRow(t('Anmeldung endet am'), $func->unixstamp2date($row['senddate'], "datetime"));
        $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Editieren'), "index.php?mod=party&action=edit&party_id={$_GET['party_id']}"));

        $dsp->AddBackButton('index.php?mod=party');
        break;
}
$dsp->AddContent();
