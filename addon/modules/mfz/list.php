<?php
if (!$cfg["mfz_partyid"]) $func->error($lang["mfz"]["err_noid"], "");
else {
	$xml_file = "";
	if (!$handle = @fopen("http://www.lanparty.de/events/mfz/?id=". $cfg["mfz_partyid"], "r")) $func->error($lang["mfz"]["err_notfound"], "");
	else {
		while (!feof($handle)) $xml_file .= fgets($handle, 4096);
		@fclose ($handle);

		$templ['mfz']['rows']['row'] = "";

		$xml_id = $xml->get_tag_content_array("id", $xml_file);
		$xml_kategorie = $xml->get_tag_content_array("kategorie", $xml_file);
		$xml_start = $xml->get_tag_content_array("start", $xml_file);
		$xml_nick = $xml->get_tag_content_array("nick", $xml_file);
		$xml_date = $xml->get_tag_content_array("date", $xml_file);

		$dsp->NewContent($lang["mfz"]["list_caption"], $lang["mfz"]["list_subcaption"]);
		if ($xml_id[0] == "") $func->information($lang["mfz"]["err_nodata"], "");
		else {
			array_multisort ($xml_kategorie, SORT_ASC,
								$xml_start, SORT_ASC,
								$xml_date,
								$xml_nick,
								$xml_id);

			while ($akt_xml_id = array_shift($xml_id)) {
				$akt_xml_kategorie = array_shift($xml_kategorie);
				$akt_xml_start = array_shift($xml_start);
				$akt_xml_nick = array_shift($xml_nick);
				$akt_xml_date = array_shift($xml_date);

				$templ["mfz"]["rows"]["cat"] = $akt_xml_kategorie;
				$templ['mfz']['rows']['start'] = $akt_xml_start;
				$templ['mfz']['rows']['date'] = $func->unixstamp2date(strtotime($akt_xml_date), "shortdaytime");
				$templ['mfz']['rows']['driver'] = $akt_xml_nick;
				$templ['mfz']['rows']['details'] = "http://www.lanparty.de/events/?action=mfzshow&mfzid=". $akt_xml_id;
				$templ['mfz']['rows']['row'] .= $dsp->FetchModTpl("mfz", "row");
			}
			$dsp->AddDoubleRow("", $dsp->FetchModTpl("mfz", "case"));
		}
		$dsp->AddDoubleRow("", $dsp->FetchButton("http://www.lanparty.de/c/mfz/". $cfg["mfz_partyid"], "add", "", "_blank"));

		$dsp->AddBackButton("?mod=mfz", "mfz/list"); 
		$dsp->AddContent();
	}
}
?>