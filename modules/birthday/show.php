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
		`%prefix%user`.`name`,
		`%prefix%user`.`firstname`,
		`%prefix%user`.`username`,
		`%prefix%user`.`birthday`
	FROM `%prefix%user`
	WHERE
		`%prefix%user`.`show_birthday` = 1
		AND `%prefix%user`.`birthday` > 0
	ORDER BY `%prefix%user`.`birthday` ASC';

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

if (count($userWithBirthdays) > 0) {
	$dsp->AddSingleRow($display);
} else {
	$dsp->AddSingleRow('Niemand hat seinen Geburtstag eingetragen oder als sichtbar konfiguriert.');
}
