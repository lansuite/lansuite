<?php

	$dsp->NewContent($lang["map"]["headline"], $lang["map"]["subheadline"]);


	if($_GET["step"] != 2)
	{
		$dsp->SetForm("index.php?mod=map&action=show&step=2");

		if($_SESSION["auth"]["login"] == 1) {

			$res = $db->query("SELECT street, hnr, plz, city FROM {$config["tables"]["user"]} WHERE userid = {$_SESSION[auth][userid]}");
			$user = $db->fetch_array($res);

			$tmp = $user["street"];
			if($user["hnr"] != ""){
				$tmp = $user["street"] . " " . $user["hnr"];
			}
						
			$dsp->AddTextFieldRow("sstreet", $lang["map"]["street"],$tmp , "");
			$dsp->AddTextFieldRow("splz", $lang["map"]["plz"], $user["plz"], "");
			$dsp->AddTextFieldRow("scity", $lang["map"]["city"], $user["city"], "");

	
			$db->free_result($res);

		} else {

			$dsp->AddTextFieldRow("sstreet", $lang["map"]["street"], "", "");
			$dsp->AddTextFieldRow("splz", $lang["map"]["plz"], "", "");
			$dsp->AddTextFieldRow("scity", $lang["map"]["city"], "", "");

		}

		$dsp->AddFormSubmitRow("send");

	} else {

		$dsp->AddSingleRow($lang["map"]["single_row"]);

		
		if (!$cfg["sys_country"]) $cfg["sys_country"] = "DE";
		
		$templ["map"]["link"] = "http://link2.map24.com/?lid=67e5b331&maptype=JAVA&width=1500&action=route&dstreet=" . $cfg['map_street'] . " " . $cfg['map_hnr'] . "&dzip=" . $cfg['map_plz'] . "&dcity=" . $cfg['map_city'] ."&dcountry=". $cfg["sys_country"] ."&rtype=fast&sstreet=" . $_POST["sstreet"] . "&szip=" . $_POST["splz"] . "&scity=" . $_POST["scity"] . "&scountry=DE&x=23&y=4";

		// $templ["map"]["link"] = "http://link2.map24.com/?lid=5e8a53c0&maptype=JAVA&action=route&szip=" . $_POST["splz"] . "&scity=" . $_POST["scity"] . "&sstreet=" . $_POST["sstreet"] . "&dzip=" . $cfg['map_plz'] . "&dcity=" . $cfg['map_city'] . "&dstreet=" . $cfg['map_street'] . " " . $cfg['map_hnr'];
		$dsp->AddSingleRow($dsp->FetchModTpl("map", "map_window"));

	}



	$dsp->AddContent();
?>