<?php

$md = new \LanSuite\MasterDelete();

$pidParameter = $request->query->getInt('pid', 0);
$tidParameter = $request->query->getInt('tid', 0);
$fidParameter = $request->query->getInt('fid', 0);
$stepParameter = $request->query->getInt('step', 0);


// Delete post

if ($pidParameter) {
    $md->DeleteIfEmpty['board_threads'] = 'tid';
    $md->Delete('board_posts', 'pid', $pidParameter);

// Delete thread
} elseif ($tidParameter) {
    $md->Delete('board_threads', 'tid', $tidParameter);

// Delete board
} else {
    match ($stepParameter) {
        2 => $md->Delete('board_forums', 'fid', $fidParameter),
        10 => $md->MultiDelete('board_forums', 'fid'),
        default => include_once 'modules/board/show.php'
    };
}
