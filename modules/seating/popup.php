<?php

use LanSuite\Module\Seating\Seat2;

$seat2 = new Seat2();

$id         = $_GET['id'];
$function   = $_GET['function'];
$userarray  = $_GET['userarray'];
$legend     = $_GET['l'];

$dsp->NewContent("");
$dsp->AddSingleRow($seat2->DrawPlan($id, 0, '', $userarray[0]));
