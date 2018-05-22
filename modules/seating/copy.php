<?php

switch ($_GET['step']) {
    default:
        $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('seating');

        $ms2->query['from'] = "%prefix%seat_block AS b LEFT JOIN %prefix%partys AS p on b.party_id = p.party_id";

        $ms2->AddResultField('Blockname', 'b.name');
        $ms2->AddResultField('Party', 'p.name AS partyname');

        $ms2->AddIconField('in', 'index.php?mod=seating&action=copy&step=2&blockid=', t('Kopieren'));
        $ms2->AddIconField('edit', 'index.php?mod=seating&action=edit&step=2&blockid=', t('Editieren'));
        $ms2->PrintSearch($current_url, 'b.blockid');
        break;
  
    case 2:
        $row = $db->qry_first('SELECT * FROM %prefix%seat_block WHERE blockid = %int%', $_GET['blockid']);
        $db->qry(
            '
          INSERT INTO %prefix%seat_block
          SET
            party_id = %int%,
            rows = %int%,
            cols = %int%,
            name = %string%,
            orientation = %int%,
            u18 = %int%,
            remark = %string%,
            text_tl = %string%,
            text_tc = %string%,
            text_tr = %string%,
            text_lt = %string%,
            text_lc = %string%,
            text_lb = %string%,
            text_rt = %string%,
            text_rc = %string%,
            text_rb = %string%,
            text_bl = %string%,
            text_bc = %string%,
            text_br = %string%',
            $row['party_id'],
            $row['rows'],
            $row['cols'],
            $row['name'] .' (Kopie)',
            $row['orientation'],
            $row['u18'],
            $row['remark'],
            $row['text_tl'],
            $row['text_tc'],
            $row['text_tr'],
            $row['text_lt'],
            $row['text_lc'],
            $row['text_lb'],
            $row['text_rt'],
            $row['text_rc'],
            $row['text_rb'],
            $row['text_bl'],
            $row['text_bc'],
            $row['text_br']
        );
        $blockid = $db->insert_id();

        $res = $db->qry('SELECT * FROM %prefix%seat_seats WHERE blockid = %int%', $_GET['blockid']);
        while ($row = $db->fetch_array($res)) {
            if ($row['status'] > 1 and $row['status'] < 5) {
                $row['status'] = 1;
            }

            // Mark all seats free
            $db->qry(
                '
              INSERT INTO %prefix%seat_seats
              SET
                blockid = %int%,
                col = %int%,
                row = %int%,
                status = %int%,
                ip = %string%,
                userid = 0',
                $blockid,
                $row['col'],
                $row['row'],
                $row['status'],
                $row['ip']
            );
        }
        $db->free_result($res);

        $res = $db->qry('SELECT * FROM %prefix%seat_sep WHERE blockid = %int%', $_GET['blockid']);
        while ($row = $db->fetch_array($res)) {
                $db->qry(
                    '
                  INSERT INTO %prefix%seat_sep
                  SET
                    blockid = %int%,
                    orientation = %int%,
                    value = %int%',
                    $blockid,
                    $row['orientation'],
                    $row['value']
                );
        }
        $db->free_result($res);
    
        $func->confirmation(t('Der Sitzplan wurde erfolgreich kopiert'), 'index.php?mod=seating');
        break;
}
