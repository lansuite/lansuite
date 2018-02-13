<?php

switch ($_GET['step']) {
  // Delete
    case 10:
        $md = new masterdelete();
        $md->MultiDelete('cron', 'jobid');
        break;

  // Run now
    case 20:
        include_once("modules/cron2/class_cron2.php");
        $cron2 = new cron2();

        $dsp->AddDoubleRow(t('Folgender SQL-Befehl wurde ausgeführt'), $cron2->Run($_GET['jobid']));
        $dsp->AddBackButton('index.php?mod=cron2');
        break;
  
    default:
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('cron2');

        $ms2->query['from'] = "%prefix%cron AS c";

        $ms2->AddResultField(t('Name'), 'c.name');
        $ms2->AddResultField(t('Geplant um'), 'runat');
        $ms2->AddResultField(t('Aktiv'), 'active', 'TrueFalse');
        $ms2->AddResultField(t('Letzte Ausführung'), 'UNIX_TIMESTAMP(c.lastrun) AS lastrun', 'MS2GetDate');

        $ms2->AddIconField('edit', 'index.php?mod=cron2&action=add&jobid=', t('Editieren'));
        $ms2->AddIconField('generate', 'index.php?mod=cron2&step=20&jobid=', t('Jetzt ausführen'));
        $ms2->AddMultiSelectAction('Löschen', 'index.php?mod=cron2&step=10&jobid=', 1);

        $ms2->PrintSearch('index.php?mod=cron2', 'c.jobid');

        $dsp->AddSingleRow($dsp->FetchSpanButton(t('Hinzufügen'), 'index.php?mod=cron2&action=add'));
        break;
}

$dsp->AddContent();
