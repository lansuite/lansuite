<?php

    $phpUA["BENCHMARK"]["DATA"] = array();
    $phpUA["BENCHMARK"]["DEPTH"] = -1;
    
    // Define __FUNCTION__ constant for PHP < 4.3.0 (they don't get the function name)
    if (!defined("__FUNCTION__")) define("__FUNCTION__", "");
    
    function startBenchmark($file, $function)
    {
        if (DEBUG == 0) return;
        global $phpUA;
        
        $name = substr($file, strlen(ABSOLUTEPATH));
        if (!empty($function)) $name .= " -> " . $function;
        
        $phpUA["BENCHMARK"]["DEPTH"]++;
        $i = count($phpUA["BENCHMARK"]["DATA"]);
        $phpUA["BENCHMARK"]["DATA"][$i]["NAME"] = $name;
        $phpUA["BENCHMARK"]["DATA"][$i]["START"] = microtime();
        $phpUA["BENCHMARK"]["DATA"][$i]["DEPTH"] = $phpUA["BENCHMARK"]["DEPTH"];
        
        if (DEBUG > 1) echo "<!-- " . $name . " -->";
    }
    
    function endBenchmark($file, $function)
    {
        if (DEBUG == 0) return;
        global $phpUA;
        
        $name = substr($file, strlen(ABSOLUTEPATH));
        if (!empty($function)) $name .= " -> " . $function;
        
        $phpUA["BENCHMARK"]["DEPTH"]--;
        
        foreach ($phpUA["BENCHMARK"]["DATA"] as $key => $value) {
            if (($phpUA["BENCHMARK"]["DATA"][$key]["NAME"] == $name) && (empty($phpUA["BENCHMARK"]["DATA"][$key]["FINISH"]))) {
                $phpUA["BENCHMARK"]["DATA"][$key]["FINISH"] = microtime();
                $phpUA["BENCHMARK"]["DATA"][$key]["START"]  = array_sum(explode(" ", $phpUA["BENCHMARK"]["DATA"][$key]["START"]));
                $phpUA["BENCHMARK"]["DATA"][$key]["FINISH"] = array_sum(explode(" ", $phpUA["BENCHMARK"]["DATA"][$key]["FINISH"]));
                $phpUA["BENCHMARK"]["DATA"][$key]["RESULT"] = number_format($phpUA["BENCHMARK"]["DATA"][$key]["FINISH"] - $phpUA["BENCHMARK"]["DATA"][$key]["START"], 2);
                break;
            }
        }
    }
    
    function finishBenchmark()
    {
        if (DEBUG == 0) return;
        global $phpUA;
        
        foreach ($phpUA["BENCHMARK"]["DATA"] as $key => $value) {
            endBenchmark(ABSOLUTEPATH . $phpUA["BENCHMARK"]["DATA"][$key]["NAME"], "");
        }
    }
    
    function printBenchmark()
    {
        if (DEBUG == 0) return;
        global $phpUA;
        
        finishBenchmark();
        print "<table align=\"center\">";
        print "<tr><td colspan=\"2\"><b>Start of Benchmark Debug</b></td></tr>";
        foreach ($phpUA["BENCHMARK"]["DATA"] as $value) {
            print "<tr>";
            print "<td>" . str_repeat("...", $value["DEPTH"]) . " " . $value["NAME"] . ":" . "</td>";
            print "<td>" . $value["RESULT"] . " sec" . "</td>";
            print "<tr>";
        }
        print "<tr><td colspan=\"2\"><b>End of Benchmark Debug</b><br><br></td></tr>";
        print "</table>";
    }
?>