<?php
switch($_GET['step']) {
	default:
    include_once('modules/news/search.inc.php');
	break;

	case 2:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->Delete('news', 'newsid', $_GET['newsid']);

    include_once('modules/news/class_news.php');
    $news->GenerateNewsfeed();
  break;
}
?>
