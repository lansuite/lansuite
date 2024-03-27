<?php
$fileParameter = $_GET['file'] ?? '';
$dsp->NewContent(t('Statistiken'), $fileParameter);

// Delete
$deleteFileParameter = $_GET['delfile'] ?? false;
if ($deleteFileParameter && $auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
    $md = new \LanSuite\MasterDelete();
    $md->Delete('download_stats', 'file', $_GET['delfile']);
}

// List
if (!$fileParameter) {
    $ms2 = new \LanSuite\Module\MasterSearch2\MasterSearch2('news');

    $ms2->query['from'] = "%prefix%download_stats AS s";
    $ms2->query['default_order_by'] = 's.file';

    $ms2->AddResultField(t('Datei'), 's.file');
    $ms2->AddResultField(t('Downloads'), 'SUM(s.hits) AS hits');

    $ms2->AddIconField('details', 'index.php?mod=downloads&action=stats&file=', t('Details'));
    if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
        $ms2->AddIconField('delete', 'index.php?mod=downloads&action=stats&delfile=', t('Löschen'));
    }

    $ms2->PrintSearch('index.php?mod=downloads&action=stats', 's.file');

// Details
} else {
    switch ($_GET['time']) {
        default:
              $link = 'y';
              $back = '';
              $group_by = '%Y-00-00-00-00-00';
              $where = '0000-00-00-00-00-00';
              $where_back = '';
              $_GET['timeframe'] = '0000-00-00-00-00-00';
            break;
        case 'y':
              $link = 'm';
              $back = '';
              $group_by = '%Y-%m-00-00-00-00';
              $where = '%Y-00-00-00-00-00';
              $where_back = '00-00-00-00-00-00';
            break;
        case 'm':
              $link = 'd';
              $back = 'y';
              $group_by = '%Y-%m-%d-00-00-00';
              $where = '%Y-%m-00-00-00-00';
              $where_back = '%Y-00-00-00-00-00';
            break;
        case 'd':
              $link = '';
              $back = 'm';
              $group_by = '%Y-%m-%d-%H-00-00';
              $where = '%Y-%m-%d-00-00-00';
              $where_back = '%Y-%m-00-00-00-00';
            break;
    }

    $dsp->AddSingleRow('<object data="index.php?mod=downloads&action=stats_grafik&design=base&file='. $_GET['file'] .'&time='. $_GET['time'] .'&timeframe='. $_GET['timeframe'] .'" type="image/svg+xml" width="700" height="300">
    Dein Browser kann das Objekt leider nicht anzeigen!
  </object>');

    $dsp->AddDoubleRow("<b>Time</b>", "<b>Hits</b>");

    $res = $db->qry("
      SELECT 
        DATE_FORMAT(time, %string%) AS group_by_time,
        UNIX_TIMESTAMP(time) AS display_time,
        SUM(hits) AS hits
      FROM %prefix%download_stats
      WHERE
        file = %string%
        AND DATE_FORMAT(time, %string%) = %string%
      GROUP BY DATE_FORMAT(time, %string%)
        ORDER BY DATE_FORMAT(time, %string%)", $group_by, $_GET['file'], $where, $_GET['timeframe'], $group_by, $group_by);

    while ($row = $db->fetch_array($res)) {
        $out = match ($_GET['time']) {
            'y' => $func->unixstamp2date($row['display_time'], 'month'),
            'm' => $func->unixstamp2date($row['display_time'], 'daydate'),
            'd' => $func->unixstamp2date($row['display_time'], 'daydatetime'),
            default => $func->unixstamp2date($row['display_time'], 'year'),
        };
        if ($link) {
            $out = '<a href="index.php?mod=downloads&action=stats&file='.$_GET['file'].'&time='. $link .'&timeframe='. $row['group_by_time'] .'">'. $out .'</a>';
        }
        $dsp->AddDoubleRow($out, $row['hits']);
    }
    $db->free_result($res);

    if ($where_back) {
        $row_back = $database->queryWithOnlyFirstRow("SELECT DATE_FORMAT(time, ?) AS back_time FROM %prefix%download_stats
      WHERE DATE_FORMAT(time, ?) = ?", [$where_back, $where, $_GET['timeframe']]);
        $dsp->AddBackButton('index.php?mod=downloads&action=stats&file='.$_GET['file'].'&time='. $back .'&timeframe='. $row_back['back_time']);
    } else {
        $dsp->AddBackButton('index.php?mod=downloads&action=stats');
    }
}
