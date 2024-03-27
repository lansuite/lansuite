<?php

if (isset($_GET['autorefresh'])) {
    $autorefresh = $_GET['autorefresh'];
} else {
    $autorefresh = 0;
}

if ($autorefresh != 0) {
    echo("<meta http-equiv=\"refresh\" content=\"". $cfg['autorefresh'] ."; URL=" . $_SERVER["PHP_SELF"] . "index.php?mod=teamspeak3&autorefresh=" . $cfg['autorefresh'] . "\">\n");
}

TS3ShowOverview();
