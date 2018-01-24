<?php
$xml = new xml();

$xml_file = @fopen("design/" . $auth["design"] . "/design.xml", "r");
$xml_content = @fread($xml_file, filesize("design/".$auth["design"]."/design.xml"));

$design_name = $xml->get_tag_content("name", $xml_content);

$dsp->NewContent("Designinfo zu '<b>$design_name</b>'", "Auf dieser Seite erh�lst du Informationen &uuml;ber das derzeitige Lansuite-Erscheinungsbild");
$dsp->AddDoubleRow("Name", $design_name);
$dsp->AddDoubleRow("Version", $xml->get_tag_content("version", $xml_content));
$dsp->AddDoubleRow("Beschreibung", $xml->get_tag_content("description", $xml_content));
$dsp->AddDoubleRow("Autor", $xml->get_tag_content("author", $xml_content));
$dsp->AddDoubleRow("Kontakt", $xml->get_tag_content("contact", $xml_content));
$dsp->AddDoubleRow("Website", $xml->get_tag_content("website", $xml_content));
$dsp->AddDoubleRow("Kommentar", $xml->get_tag_content("comments", $xml_content));
$dsp->AddBackButton("index.php?mod=about", "about/design");
$dsp->AddContent();
