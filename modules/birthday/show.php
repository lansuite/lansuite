<?php
$dsp->NewContent(t('Geburtstage'), t('Hier kannst du die Geburtstage deiner Mitspieler einsehen'));

$display = '
	<table class=tbl_0>
		<tr>
			<th>Username</th>
			<th>Name</th>
			<th>Geburtstag</th>
		</tr>';

$sqlQuery = '
	SELECT
		`name`,
		 `firstname`,
		 `username`,
		 `birthday`
	FROM %prefix%user 
	ORDER BY `birthday` ASC';

$userWithBirthdays = $database->queryWithFullResult($sqlQuery);

foreach ($userWithBirthdays as $birthdays) {
	$name = $birthdays['name'];
	$firstname = $birthdays['firstname'];
	$username = $birthdays['username'];
	$birthday = date('d.m.Y', strtotime($birthdays['birthday']));

	$display .= '
		<tr>
			<td>' . $username . '</td>
			<td>' . $firstname . ' ' . $name .' </td>
			<td>' .  $birthday . '</td>
		</tr>';
}

$display .= '</table>';
$dsp->AddSingleRow($display);
