<?php 

eval("\$index .= \"". $func->gettemplate("helplet_show_index")."\";");

include("doc/online/$language/" . $_GET['helpletid'] . ".php");

$dsp->NewContent($helplet['modul'] .' ('. $helplet['action'] .')', $helplet['info']);
$dsp->AddHruleRow();

if ($helplet['key']) foreach ($helplet['key'] as $key) {
	$value = array_shift($helplet['value']);
	if ($key) $dsp->AddDoubleRow($key, $value);
}
$dsp->AddContent();
$index .= $templ['index']['info']['content'];
$func->templ_output($index);

?>