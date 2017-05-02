<?php
switch ($_GET['step']) {
    default:
        include_once('modules/rent/search.inc.php');
        break;

    case 10:
        $additional_where = 'u.type >= 1';
        $current_url = 'index.php?mod=rent&action=show&step=10&stuffid='. $_GET['stuffid'];
        $target_url = 'index.php?mod=rent&action=show&step=11&stuffid='. $_GET['stuffid'] .'&userid=';
        include_once('modules/usrmgr/search_basic_userselect.inc.php');
        break;

    case 11:
        $db->qry(
            'INSERT INTO %prefix%rentuser SET stuffid = %int%, userid = %int%, out_orgaid = %int%, back_orgaid = 0',
            $_GET['stuffid'],
            $_GET['userid'],
            $auth['userid']
        );
          $func->confirmation(t('OK, der Artikel wurde verliehen.'), 'index.php?mod=rent&action=show');
        break;
}
