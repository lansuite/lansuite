<?php
$id 		= $_GET['id'];
$function 	= $_GET['function'];
$userarray 	= $_GET['userarray'];
$legend 	= $_GET['l'];

$dsp->NewContent("");
$dsp->AddSingleRow($seat2->DrawPlan($id,0,'',$userarray[0]));
$dsp->AddContent();

$func->templ_output($dsp->FetchTpl("design/templates/base_index.htm",$templ));
?>