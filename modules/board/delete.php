<?php

$md = new masterdelete();

// Delete post
if ($_GET['pid']) {
    $md->DeleteIfEmpty['board_threads'] = 'tid';
    $md->Delete('board_posts', 'pid', $_GET['pid']);

// Delete thread
} elseif ($_GET['tid']) {
    #$md->References['board_posts'] = '';
  #$md->References['board_bookmark'] = '';
    $md->Delete('board_threads', 'tid', $_GET['tid']);

// Delete board
} else {
    #$md->References['board_threads'] = '';
  #$md->SubReferences['board_posts'] = 'tid';
  #$md->SubReferences['board_bookmark'] = 'tid';

    switch ($_GET['step']) {
        default:
            include_once('modules/board/show.php');
            break;

        case 2:
            $md->Delete('board_forums', 'fid', $_GET['fid']);
            break;

        case 10:
            $md->MultiDelete('board_forums', 'fid');
            break;
    }
}
