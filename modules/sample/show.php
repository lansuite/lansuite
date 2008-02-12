<?php

	// This is how you start a new output
	// Please define each text-output in the $lang-array, so that translations into other languages are easy to perform
	// The values of the $lan-array are defined in language/sample_lang_de.php
	$dsp->NewContent($lang["sample"]["headline"], $lang["sample"]["subheadline"]);

	// This is how to simply output some text
	$dsp->AddSingleRow($lang["sample"]["single_row"]);


	// Lets start a form
	// The first argument is the target-php, which will be loaded after submitting
	// In this case it will just reload this page without any effect
	$dsp->SetForm("index.php?mod=sample&action=show&step=2");

	// Text-Input
	// 1st argument: the html-name of the text-input, so you cann access the input after submitting by $_POST["name"]
	// 2nd argument: here you can write a text, which gives the user a hint what information he has to write in this field
	// 3rd argument: the default vaule
	// 4th argument: an errortext. i.e. if the submitted information is incomplete
	$dsp->AddTextFieldRow("name", $lang["sample"]["name"], "value", "");

	// Use this to display the form-submit-link
	// "add" is the name of the button-grafic which will be displayed. you can also use "next", or "send", or any other button-grafic which will be generated when calling first and located in ext_inc/auto_images/{design}/{language}/button_*.png
	$dsp->AddFormSubmitRow("add");

	// This is how to load your own template, located in the 'templates'-folder of your module
	$dsp->AddSingleRow($dsp->FetchModTpl("sample", "my_template"));


	// Lets use the database - This will simply read all usernames from the database and display them
	$res = $db->query("SELECT username FROM {$config["tables"]["user"]}");
	while ($user = $db->fetch_array($res)){
		$user_out .= $user["username"] .", ";
	}
	$dsp->AddSingleRow($user_out);

	$user_insg = $db->num_rows($res);
	$db->free_result($res);

	$dsp->AddSingleRow($lang["sample"]["user_insg"] .": ". $user_insg);

	// This will finaly output all the $dsp-Rows
	$dsp->AddContent();
?>
