<?php

    
    startBenchmark(__FILE__, __FUNCTION__);
    
    $phpUA["TEMPLATE"]->assign("CREDITS", $phpUA["CREDITS"]);
    $phpUA["TEMPLATE"]->display($phpUA["STYLE"] . "/credits.tpl");
    
    endBenchmark(__FILE__, __FUNCTION__);
?>