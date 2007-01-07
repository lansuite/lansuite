<?php
/*
include_once('inc/classes/class_masterdelete.php');
$md = new masterdelete();

		$res = $db->query("SELECT tid FROM {$config["tables"]["board_threads"]} WHERE fid='{$_GET['fid']}'");
		while ($row = $db->fetch_array($res)) {
			$db->query("DELETE FROM {$config["tables"]["board_posts"]} WHERE tid='{$row['tid']}'");
		}
		
$md->References['board_threads'] = '';
#$md->References['board_posts'] = 'tid';

switch($_GET['step']) {
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
*/

$LSCurFile = __FILE__;

switch ($_GET['step']) {
	default:
    include_once('modules/board/show.php');
	break;

	case 2:
		$text = t('Wollen Sie dieses Forum wirklich löschen?');

		$link_target_yes = "index.php?mod=board&action=delete&step=3&fid={$_GET['fid']}";
		$link_target_no = "index.php?mod=board&action=delete";
		$func->question($text, $link_target_yes, $link_target_no);
	break;

	case 3:
		$res = $db->query("SELECT tid FROM {$config["tables"]["board_threads"]} WHERE fid='{$_GET['fid']}'");
		while ($row = $db->fetch_array($res)) {
			$db->query("DELETE FROM {$config["tables"]["board_posts"]} WHERE tid='{$row['tid']}'");
		}
		$db->free_result($res);
		$db->query("DELETE FROM {$config["tables"]["board_threads"]} WHERE fid='{$_GET['fid']}'");
		$db->query("DELETE FROM {$config["tables"]["board_forums"]} WHERE fid='{$_GET['fid']}'");

		$func->confirmation(t('Das Forum wurde erfolgreich gelöscht'), "index.php?mod=board&action=delete");
	break;

  // Post delete
	case 10:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->Delete('board_posts', 'pid', $_GET['pid']);

    // TODO: Check if last post in thread -> Delete thread!
	break;

  // Thread delete
	case 11:
    include_once('inc/classes/class_masterdelete.php');
    $md = new masterdelete();
    $md->References['board_posts'] = '';
    $md->Delete('board_threads', 'tid', $_GET['tid']);
	break;
}
?>