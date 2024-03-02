<?php

namespace LanSuite\Module\Cron2;

class Cron2
{

    const LS_CRON_MAX_ERRORS = 3; //Maximum amount of errors for a job

    /**
     * @param int $jobid
     * @return bool
     */
    public function Run($jobid)
    {
        global $database, $func, $config;

        if (!$jobid) {
            return false;
        }

        $row = $database->queryWithOnlyFirstRow(
            "SELECT name, type, `function`, error_runs
            FROM %prefix%cron
            WHERE jobid = ?",
            [$jobid]
        );

        if ($row != false) {
            try {
                $runtime = microtime(true);
                if ($row['type'] == 'sql') {// run SQL query
                    $sql = str_replace('%prefix%', $config['database']['prefix'], $row['function']);
                    $database->query($func->AllowHTML($sql), []);
                } elseif ($row['type'] == "php") { // run PHP code
                    require 'ext_scripts/'.$row['function'];
                }
                $runtime = microtime(true) - $runtime;

                $database->query(
                    "UPDATE %prefix%cron
                    SET lastrun = NOW(),
                    last_error = '',
                    error_runs = 0,
                    runtime = ?
                    WHERE jobid = ?",
                    [$runtime, $jobid]
                );
                $func->log_event(t('Cronjob "%1" wurde ausgeführt', array($row['name'])), 1);
                return $row['function'];
            }
            catch (\mysqli_sql_exception | \Error $e) {
                // Execution ran into an issue, mark and handle
                $database->query(
                    'UPDATE %prefix%cron
                    SET
                        lastrun = NOW(),
                        error_runs = error_runs + 1,
                        last_error = ?
                    WHERE jobid = ?',
                    [$e->getMessage(), $jobid]
                );
                $func->log_event(t('Die Ausführung von Job %1 ist fehlgeschlagen', $row['name']));

                if ($row['error_runs'] >= $this::LS_CRON_MAX_ERRORS) {
                    // more than maximum amount of tries, deactivate job
                    $this->deactivateJob($jobid);
                    $func->log_event(t('Job %1 wurde auf Grund zu vieler Fehler deaktiviert', $row['name']), 2);
                }
            }
        }
        return false;
    }

    /**
     * Checks configured cron jobs if and what the next job to execute it and runs it
     * @return void
     */
    public function CheckJobs()
    {
        global $database;

        $row = $database->queryWithOnlyFirstRow(
            "SELECT
                jobid
            FROM %prefix%cron
            WHERE
                UNIX_TIMESTAMP(NOW()) > UNIX_TIMESTAMP(DATE_ADD(DATE(lastrun), INTERVAL 1 DAY)) + TIME_TO_SEC(runat) AND
                active = 1
            ",
            []
        );
        if ($row && $row['jobid']) {
            $this->Run($row['jobid']);
        }
    }

    /**
     * Deactivates a job with the given ID
     *
     * @param int $jobid ID of the job to be deactivated
     * @return bool true if job was found, false if not
     */
    public function deactivateJob(int $jobid = 0)
    {
        global $database;

        $result = $database->query('UPDATE %prefix%cron SET active = 0 WHERE jobid = ?', [$jobid]);
        return $result->affected_rows == 1;
    }
}
