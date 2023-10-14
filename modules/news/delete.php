<?php
$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    default:
        include_once('modules/news/search.inc.php');
        break;

    case 2:
        $md = new \LanSuite\MasterDelete();
        $md->Delete('news', 'newsid', $_GET['newsid']);

        $news = new \LanSuite\Module\News\News();
        $news->GenerateNewsfeed();
        break;
}
