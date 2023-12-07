<?php

$md = new \LanSuite\MasterDelete();

// Delete post
$pidParameter = $_GET['pid'] ?? 0;
$tidParameter = $_GET['tid'] ?? 0;
$stepParameter = $_GET['step'] ?? 0;
if ($pidParameter) {
    $md->DeleteIfEmpty['board_threads'] = 'tid';
    $md->Delete('board_posts', 'pid', $_GET['pid']);

// Delete thread
} elseif ($tidParameter) {
    $md->Delete('board_threads', 'tid', $_GET['tid']);

// Delete board
} else {
    match ($stepParameter) {
        2 => $md->Delete('board_forums', 'fid', $_GET['fid']),
        10 => $md->MultiDelete('board_forums', 'fid'),
        default => include_once('modules/board/show.php'),
    };
}
