<?php
if ($_POST['action']) {
    foreach ($_POST['action'] as $key => $val) {
        switch ($_GET['mode']) {
            case 'del':
                $md = new masterdelete();
                $md->MultiDelete('board_posts', 'pid');
                break;
            case 'ban':
                echo $item. "b" . HTML_NEWLINE;
                break;
        }
    }
} else {
    include_once('modules/mastersearch2/class_mastersearch2.php');
    $ms2 = new mastersearch2();

    $ms2->query['from'] = "%prefix%board_posts AS p
      LEFT JOIN %prefix%user AS u ON p.userid = u.userid
      LEFT JOIN %prefix%board_threads AS t ON p.tid = t.tid
      LEFT JOIN %prefix%board_forums AS f ON t.fid = f.fid
      ";
    $ms2->query['where'] = 'f.need_type <= '. (int)($auth['type'] + 1);
    $ms2->query['default_order_by'] = 'LastPost DESC';

    $ms2->AddTextSearchField(t('Forum'), array('f.name' => 'like'));
    $ms2->AddTextSearchField(t('Titel'), array('t.caption' => 'like'));
    $ms2->AddTextSearchField(t('Text'), array('p.comment' => 'fulltext'));
    $ms2->AddTextSearchField(t('Autor'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

    $ms2->AddSelect('p.userid');
    $ms2->AddSelect('f.fid');
    $ms2->AddSelect('MAX(p.date) AS LastPost');
    $ms2->AddResultField(t('Text'), 'CONCAT(\'<b>\', f.name, \'</b> (\', t.caption, \')<br />\', p.comment) AS ThreadName', '', 140);
    $ms2->AddResultField(t('Autor'), 'u.username', 'UserNameAndIcon');
    $ms2->AddResultField(t('IP'), 'INET6_NTOA(p.ip) AS ip');
    $ms2->AddResultField(t('Datum'), 'p.date', 'MS2GetDate');

    if ($auth['type'] >= 3) {
        $ms2->AddIconField('delete', 'index.php?mod=board&action=delete&step=10&pid=', t('Delete'));
    }
    if ($auth['type'] >= 3) {
        $ms2->AddMultiSelectAction(t('Delete'), 'index.php?mod=board&action=admin_search&mode=del', 1);
    }
  
    $ms2->PrintSearch('index.php?mod=board&action=admin_search', 'p.pid');
}
