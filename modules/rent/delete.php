<?php
include_once('inc/classes/class_masterdelete.php');
$md = new masterdelete();
$md->References['rentuser'] = '';
$md->Delete('rentstuff', 'stuffid', $_GET['stuffid']);
