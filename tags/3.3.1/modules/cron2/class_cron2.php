<?php

class cron2{
  function Run($jobid) {
    global $db, $config, $func;

    if (!$jobid) return false;

    $row = $db->query_first("SELECT name, function FROM {$config['tables']['cron']} WHERE jobid = ". (int)$jobid);
    $db->query_first($row['function']);
    $db->query_first("UPDATE {$config['tables']['cron']} SET lastrun = NOW() WHERE jobid = ". (int)$jobid);

    $func->log_event(t('Cronjob "%1" wurde ausgeführt', array($row['name'])), 1);

    return $row['function'];
  }

  function CheckJobs() {
    global $db, $config;

    $row = $db->query_first("SELECT jobid FROM {$config['tables']['cron']}
      WHERE UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(DATE(lastrun), INTERVAL 1 DAY)) + TIME_TO_SEC(runat)
      ");
    if ($row['jobid']) $this->Run($row['jobid']);
  }
}
?>