<?php

/**
 * @param int $posts
 * @return string
 */
function GetBoardRank($posts)
{
    global $cfg;

    $lines = explode("\n", $cfg['board_rank']);
    foreach ($lines as $line) {
        list($num, $name) = explode("->", $line);
        if ($num > $posts) {
            break;
        }
        $rank = $name;
    }
    return $rank;
}
