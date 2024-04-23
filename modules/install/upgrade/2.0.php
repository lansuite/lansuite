<?php

//get users not having a hashed password

use LanSuite\PasswordHash;

$usersToModify  = $database->queryWithFullResult("SELECT user_id, password from %prefix%users where `password` NOT LIKE '$%'");

$hashAlgorithmConfig =
[
    'algo' => 'md5-sha512',
    'iterations' => '50000'
];

//Hash the MD5-String with PBKDF2-SHA512 and update PW field
foreach ($usersToModify as $user) {
    $passwordHash = PasswordHash::hash($user['password'], $hashAlgorithmConfig);
    $database->query('UPDATE %prefix%users SET `password` = ? WHERE user_id = ?', [$passwordHash, $user['user_id']]);
}