<?php

$dsp->NewContent(t('Turnier - Pausenverwaltung'), t('Trage Startzeitpunkt und dauer der Pause ein. Lansuite wird entsprechend spätere Rundenzeiten berechnen'));

switch ($_GET['step']) {
    // Delete
    case 10:
        $md = new masterdelete();
        $md->Delete('t2_breaks', 'breakid', $_GET['breakid']);
        break;
    
    default:
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('tournament');
        $ms2->query['from'] = '%prefix%t2_breaks';
        $ms2->query['where'] = 'tournamentid = '. (int)$_GET['tournamentid'];
        $ms2->AddResultField(t('Start'), 'start');
        $ms2->AddResultField(t('Dauer'), 'duration');
        $ms2->AddIconField('edit', 'index.php?mod=tournament2&action=breaks&tournamentid='. (int)$_GET['tournamentid'] .'&breakid=', t('Editieren'));
        $ms2->AddIconField('delete', 'index.php?mod=tournament2&action=breaks&tournamentid='. (int)$_GET['tournamentid'] .'&step=10&breakid=', t('Löschen'));
        $ms2->PrintSearch('index.php?mod=tournament2&action=breaks&tournamentid='. (int)$_GET['tournamentid'], 'breakid');
        
        $t = $db->qry_first('SELECT name FROM %prefix%tournament_tournaments WHERE tournamentid = %int%', $_GET['tournamentid']);
          
        $dsp->AddFieldSetStart(t('Pause für Turnier %1 festlegen', $t['name']));
        $mf = new masterform();
        $mf->AddFix('tournamentid', $_GET['tournamentid']);
        $mf->AddField(t('Pause beginnen um'), 'start');
        $mf->AddField(t('Dauer der Pause (in Minuten)'), 'duration');
        $mf->SendForm('', 't2_breaks', 'breakid', $_GET['breakid']);
        $dsp->AddFieldSetEnd();
        break;
}

$buttons = "";
$buttons .= $dsp->FetchSpanButton(t('Paarungen'), "index.php?mod=tournament2&action=games&step=2&tournamentid=". $_GET['tournamentid']);
$buttons .= " ". $dsp->FetchSpanButton(t('Spielbaum'), "index.php?mod=tournament2&action=tree&step=2&tournamentid=". $_GET['tournamentid']);
$dsp->AddDoubleRow("", $buttons);
$dsp->AddContent();
