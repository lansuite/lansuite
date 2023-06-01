<?php

$md = new \LanSuite\MasterDelete();

// Delete post
if ($_GET['pid']) {
    $md->DeleteIfEmpty['board_threads'] = 'tid';
    $md->Delete('board_posts', 'pid', $_GET['pid']);

// Delete thread
} elseif ($_GET['tid']) {
    $md->Delete('board_threads', 'tid', $_GET['tid']);

// Delete board
} else {
    match ($_GET['step']) {
        2 => $md->Delete('board_forums', 'fid', $_GET['fid']),
        10 => $md->MultiDelete('board_forums', 'fid'),
        default => include_once('modules/board/show.php'),
    };
}
