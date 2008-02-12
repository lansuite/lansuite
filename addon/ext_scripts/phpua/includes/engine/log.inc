<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    function writeLog($event)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ($handle = @fopen(ABSOLUTEPATH . LOGFILE, "a")) {
            fwrite($handle, date("r",time()) . "\t" . $_SESSION["username"] . " [" . $_SERVER["REMOTE_ADDR"] . "(" . $_SERVER["REMOTE_HOST"] . ")]" . "\t" . $event . "\n");
            fclose($handle);
        } else {
            displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILEERROR"] . LOGFILE);
        }
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    function clearLog()
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        if ($handle = @fopen(ABSOLUTEPATH . LOGFILE, "w")) {
            fwrite($handle, "<?php header(\"Location: " . INDEXFILE . "\"); exit; ?>" . "\n");
            fwrite($handle, "# " . "\n");
            fwrite($handle, "# " . LOGFILE . "\n");
            fwrite($handle, "# phpUA Event Log File" . "\n");
            fwrite($handle, "# NOTE: This file is NOT subject to Copyright." . "\n");
            fwrite($handle, "# " . "\n");
            fwrite($handle, "\n");
            fclose($handle);
            writeLog($phpUA["LANGUAGE"]["LogCleared"]);
        } else {
            displayErrorMessage($phpUA["LANGUAGE"]["ERROR_FILEERROR"] . LOGFILE);
        }
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    endBenchmark(__FILE__, __FUNCTION__);
?>