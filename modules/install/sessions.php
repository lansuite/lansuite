<?php

switch ($_GET["step"]) {
    default:
        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('install');

        $ms2->query['from'] = "%prefix%stats_auth AS a
      LEFT JOIN %prefix%user AS u ON a.userid = u.userid";

        $ms2->config['EntriesPerPage'] = 50;
        $ms2->query['default_order_by'] = 'a.lasthit DESC';

        $list = array('' => t('Alle'), '0' => t('System'));
        $res = $db->qry("
          SELECT
            l.userid,
            u.username
          FROM %prefix%log AS l
          LEFT JOIN %prefix%user AS u ON u.userid = l.userid
          GROUP BY l.userid");
        while ($row = $db->fetch_array($res)) {
            if ($row['userid']) {
                $list[$row['userid']] = $row['username'];
            }
        }
        $db->free_result($res);
        $ms2->AddTextSearchDropDown(t('Benutzer'), 'a.userid', $list);

        $list = array('' => t('Alle'));
        $res = $db->qry('SELECT ip FROM %prefix%stats_auth GROUP BY ip ORDER BY ip');
        while ($row = $db->fetch_array($res)) {
            if ($row['ip']) {
                $list[$row['ip']] = $row['ip'];
            }
        }
        $db->free_result($res);
        $ms2->AddTextSearchDropDown(t('IP'), 'a.ip', $list);

        $ms2->AddSelect('u.userid');
        $ms2->AddResultField(t('Session-ID'), 'a.sessid');
        $ms2->AddResultField(t('Benutzername'), 'u.username', 'UserNameAndIcon');
        $ms2->AddResultField(t('IP'), 'a.ip');
        $ms2->AddResultField(t('Hits'), 'a.hits');
        $ms2->AddResultField(t('Visits'), 'a.visits');
        $ms2->AddResultField(t('Eingeloggt'), 'a.logintime', 'MS2GetDate');
        $ms2->AddResultField(t('Letzter Aufruf'), 'a.lasthit', 'MS2GetDate');

        if ($auth['type'] >= 3) {
            $ms2->AddMultiSelectAction(t('Session beenden'), "index.php?mod=install&action=sessions&step=10", 1);
        }

        $ms2->PrintSearch('index.php?mod=install&action=sessions', 'a.sessid');
        break;

    case 10:
        $md = new \LanSuite\MasterDelete();
        $md->MultiDelete('stats_auth', 'sessid');
        break;
}
