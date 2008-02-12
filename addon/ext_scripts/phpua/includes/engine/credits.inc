<?php

    startBenchmark(__FILE__, __FUNCTION__);
    
    function addCredits($task, $name)
    {
        startBenchmark(__FILE__, __FUNCTION__);
        global $phpUA;
        $phpUA["CREDITS"][] = array("task"=>$task,
                                    "name"=>$name);
        endBenchmark(__FILE__, __FUNCTION__);
    }
    
    $phpUA["CREDITS"] = array();
    
    endBenchmark(__FILE__, __FUNCTION__);
?>