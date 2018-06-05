<?php

namespace LanSuite\Module\Cron2;

class Cron2
{
    /**
     * @param int $jobid
     * @return bool
     */
    public function Run($jobid)
    {
        global $db, $func;

        if (!$jobid) {
            return false;
        }
        // Initialize status variable to be used as return value.
        $status = false;
        // Fetch job data 
        $row = $db->qry_first("SELECT name, type, function FROM %prefix%cron WHERE jobid = %int%", $jobid);
    
        if ($row['type'] == 'sql') {
            $db->qry('%plain%', $func->AllowHTML($row['function']));
            // @TODO: Add return status once DB class allows access to error data
        } elseif ($row['type'] == "php") {
            if (is_readable('ext_scripts/'.$row['function'])){
            require_once 'ext_scripts/'.$row['function'];
            // Script should set $status and $message at the end to be able to set this in the DB
            } else {
                $status = false;
                $message = 'Could not execute PHP script "'. $row['function'] . '". Check that it exists in ext_scripts and is accessible by the PHP user';
            }
        }
        $db->qry("UPDATE %prefix%cron SET lastrun = NOW(), laststate=%bool%, lastmessage=%text% WHERE jobid = %int%", $jobid, $status, $message);

        $func->log_event(t('Cronjob "%1" wurde ausgeführt', array($row['name'])), 1);

        return $row['function'];
    }

    /**
     * @return void
     */
    public function CheckJobs()
    {
        global $db;

        $row = $db->qry_first("
          SELECT
            jobid
          FROM %prefix%cron
          WHERE
            UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(DATE(lastrun), INTERVAL 1 DAY)) + TIME_TO_SEC(runat)");
        if ($row['jobid']) {
            $this->Run($row['jobid']);
        }
    }
}
