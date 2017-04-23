<?php

class cron2
{
    public function Run($jobid)
    {
        global $db, $func;

        if (!$jobid) {
            return false;
        }

        $row = $db->qry_first("SELECT name, type, function FROM %prefix%cron WHERE jobid = %int%", $jobid);
    
        if ($row['type'] == 'sql') {
            $db->qry('%plain%', $func->AllowHTML($row['function']));
        } elseif ($row['type'] == "php") {
            require_once 'ext_scripts/'.$row['function'];
        }
        $db->qry("UPDATE %prefix%cron SET lastrun = NOW() WHERE jobid = %int%", $jobid);

        $func->log_event(t('Cronjob "%1" wurde ausgeführt', array($row['name'])), 1);

        return $row['function'];
    }

    public function CheckJobs()
    {
        global $db;

        $row = $db->qry_first("SELECT jobid FROM %prefix%cron
      WHERE UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(DATE(lastrun), INTERVAL 1 DAY)) + TIME_TO_SEC(runat)
      ");
        if ($row['jobid']) {
            $this->Run($row['jobid']);
        }
    }
}
