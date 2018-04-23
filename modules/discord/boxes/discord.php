<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$discord = new \LanSuite\Module\Discord();
$data = $discord->fetchServerData();
$boxcontent = $discord->genBox($data);
$box->Row($boxcontent);