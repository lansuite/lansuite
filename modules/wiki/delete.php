<?php
switch($_GET['step']) {
	default:
    include_once('modules/wiki/search.php');
	break;

  // Delete whole post
	case 2:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();

    $md->Delete('wiki', 'postid', $_GET['postid']);
  break;

  // Delete one version
	case 10:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();

    $md->Delete('wiki_versions', 'versionid', $_GET['versionid']);
  break;
}
?>
