<?php 

#eval("\$index .= \"". $func->gettemplate("helplet_show_index")."\";");

include("modules/{$_GET['module']}/docu/{$language}_{$_GET['helpletid']}.php");

$dsp->NewContent($helplet['modul'] .' ('. $helplet['action'] .')', $helplet['info']);
$dsp->AddHruleRow();

if ($helplet['key']) foreach ($helplet['key'] as $key) {
	$value = array_shift($helplet['value']);
	if ($key) $dsp->AddDoubleRow($key, $value);
}
$dsp->AddContent();

echo $func->FetchMasterTmpl("design/templates/helplet_show_index.htm", $templ);
?>