<?php
$sqlQuery = '
	SELECT
		`%prefix%user`.`username`,
		`%prefix%user`.`birthday`
	FROM `%prefix%user` 
	WHERE
		`%prefix%user`.`show_birthday` = 1
		AND MOD(DAYOFYEAR(`%prefix%user`.`birthday`) - DAYOFYEAR(CURRENT_DATE()) + 366, 366) BETWEEN 0 and 31
	ORDER BY DAYOFYEAR(`%prefix%user`.`birthday`) ASC';

$userWithBirthdays = $database->queryWithFullResult($sqlQuery);
foreach ($userWithBirthdays as $birthdays) {
	$username = $birthdays['username'];
	$birthday = date('d.m.', strtotime($birthdays['birthday']));
	$age = date('Y') - date('Y', strtotime($birthdays['birthday']));
	if ($birthday == date('d.m.')) {
		$box->DotRow('<b><font color=red>' . $username . ' wird</font></b>');
		$box->EngangedRow('<b><font color=red>heute ' . $age . ' Jahre</font></b>');
	} else {
		$box->DotRow($username . ' wird am');
		$box->EngangedRow($birthday . ' ' . $age . ' Jahre');
	}
}

if (count($userWithBirthdays) == 0) {
	$box->EngangedRow('Zur Zeit hat niemand Geburtstag.');
}
