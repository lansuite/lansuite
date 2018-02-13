<?php

$md = new masterdelete();
$md->Delete('partys', 'party_id', $_GET['party_id']);
