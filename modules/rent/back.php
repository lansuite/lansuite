<?php
switch ($_GET['step']) {
    default:
        include_once('modules/mastersearch2/class_mastersearch2.php');
        $ms2 = new mastersearch2('news');

        $ms2->query['from'] = "%prefix%rentuser AS r
      LEFT JOIN %prefix%user AS u ON u.userid = r.userid
      LEFT JOIN %prefix%rentstuff AS s ON s.stuffid = r.stuffid";
        $ms2->query['where'] = 'back_orgaid = 0';

        $ms2->AddTextSearchField('Titel', array('s.caption' => 'like'));

        $ms2->AddResultField('Titel', 's.caption');
        $ms2->AddResultField('Verliehen an', 'u.username', 'UserNameAndIcon');

        $ms2->AddIconField('back', 'index.php?mod=rent&action=back&step=10&rentid=', t('Zurücknehmen'));
        $ms2->PrintSearch('index.php?mod=rent&action=show', 'r.rentid');
        break;

    case 10:
        $db->qry('UPDATE %prefix%rentuser SET back_orgaid = %int% WHERE rentid = %int%', $auth['userid'], $_GET['rentid']);
        $func->confirmation(t('Artikel wurde zurückgenommen'), 'index.php?mod=rent&action=back');
        break;
}
