<?php

if (!$cfg["equipment_shopid"]) $func->error($lang["equipment"]["err_noid"], "");
else {
    $dsp->NewContent($lang["equipment"]["admin_caption"], $lang["equipment"]["admin_subcaption"]);
    $dsp->AddSingleRow($lang["equipment"]["admin_text"]);
    $dsp->AddContent();
}
?>