<?php
$LSCurFile = __FILE__;

if ($_POST['action']) {
	foreach ($_POST['action'] as $key => $val) {
	  switch ($_GET['mode']) {
	    case 'del':
	      $db->query("DELETE FROM {$config["tables"]["board_posts"]} WHERE pid = ". (int)$key);
				$func->confirmation($lang['board']['adms_del_success'], 'index.php?mod=board&action=admin_search');
	    break;
	    case 'ban':
				echo $item. "b" . HTML_NEWLINE;
	    break;
	  }
  }

} else {
  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2();
  
  $ms2->query['from'] = "{$config['tables']['board_posts']} AS p
      LEFT JOIN {$config["tables"]["user"]} AS u ON p.userid = u.userid
      LEFT JOIN {$config['tables']['board_threads']} AS t ON p.tid = t.tid
      LEFT JOIN {$config['tables']['board_forums']} AS f ON t.fid = f.fid
      ";
  $ms2->query['where'] = 'f.need_type <= '. (int)($auth['type'] + 1);
  $ms2->query['default_order_by'] = 'LastPost DESC';
  
  $ms2->AddTextSearchField($lang['board']['board'], array('f.name' => 'like'));
  $ms2->AddTextSearchField($lang['board']['subject'], array('t.caption' => 'like'));
  $ms2->AddTextSearchField($lang['board']['thread_text'], array('p.comment' => 'fulltext'));
  $ms2->AddTextSearchField($lang['board']['author'], array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));
  
  $ms2->AddSelect('p.userid');
  $ms2->AddSelect('f.fid');
  $ms2->AddSelect('MAX(p.date) AS LastPost');
  $ms2->AddResultField($lang['board']['thread_text'], 'CONCAT(\'<b>\', f.name, \'</b> (\', t.caption, \')<br />\', p.comment) AS ThreadName', '', 140);
  $ms2->AddResultField($lang['board']['author'], 'u.username', 'UserNameAndIcon');
  $ms2->AddResultField('IP', 'p.ip');
  $ms2->AddResultField($lang['board']['date'], 'p.date', 'MS2GetDate');
  
  if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=board&action=edit&mode=pdelete&pid=', $lang['ms2']['delete']);
  if ($auth['type'] >= 3) $ms2->AddMultiSelectAction($lang['ms2']['delete'], 'index.php?mod=board&action=admin_search&mode=del', 1);
  
  $ms2->PrintSearch('index.php?mod=board&action=admin_search', 'p.pid');
}
?>