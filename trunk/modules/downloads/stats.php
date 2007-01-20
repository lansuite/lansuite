<?php
$LSCurFile = __FILE__;

$dsp->NewContent(t('Statistiken'), $_GET['file']);

// Delete
if ($_GET['delfile'] and $auth['type'] >= 3) {
  include_once('inc/classes/class_masterdelete.php');
  $md = new masterdelete();
  $md->Delete('download_stats', 'file', $_GET['delfile']);
}


// List
if (!$_GET['file']) {
  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2('news');

  $ms2->query['from'] = "{$config["tables"]["download_stats"]} AS s";
  $ms2->query['default_order_by'] = 's.file';

  $ms2->AddResultField(t('Datei'), 's.file');
  $ms2->AddResultField(t('Downloads'), 'SUM(s.hits) AS hits');

  $ms2->AddIconField('details', 'index.php?mod=downloads&action=stats&file=', t('Details'));
  if ($auth['type'] >= 3) $ms2->AddIconField('delete', 'index.php?mod=downloads&action=stats&delfile=', t('Löschen'));

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
    Ihr Browser kann das Objekt leider nicht anzeigen!
  </object>');
#  #  <param name="src" value="index.php?mod=stats&action=usage_grafik&design=base&time='. $_GET['time'] .'&timeframe='. $_GET['timeframe'] .'>

  $dsp->AddDoubleRow("<b>Time</b>", "<b>Hits</b>");

  $res = $db->query("SELECT DATE_FORMAT(time, '$group_by') AS group_by_time, UNIX_TIMESTAMP(time) AS display_time, SUM(hits) AS hits FROM {$config["tables"]["download_stats"]}
    WHERE file = '{$_GET['file']}' AND DATE_FORMAT(time, '$where') = '{$_GET['timeframe']}'
    GROUP BY DATE_FORMAT(time, '$group_by')
    ORDER BY DATE_FORMAT(time, '$group_by')
  ");
  while ($row = $db->fetch_array($res)) {
    switch ($_GET['time']) {
      default: $out = $func->unixstamp2date($row['display_time'], 'year'); break;
      case 'y': $out = $func->unixstamp2date($row['display_time'], 'month'); break;
      case 'm': $out = $func->unixstamp2date($row['display_time'], 'daydate'); break;
      case 'd': $out = $func->unixstamp2date($row['display_time'], 'daydatetime'); break;
    }
    if ($link) $out = '<a href="index.php?mod=downloads&action=stats&file='.$_GET['file'].'&time='. $link .'&timeframe='. $row['group_by_time'] .'">'. $out .'</a>';
    $dsp->AddDoubleRow($out, $row['hits']);
  }
  $db->free_result($res);

  if ($where_back) {
    $row_back = $db->query_first("SELECT DATE_FORMAT(time, '$where_back') AS back_time FROM {$config["tables"]["download_stats"]}
      WHERE DATE_FORMAT(time, '$where') = '{$_GET['timeframe']}'");
    $dsp->AddBackButton('index.php?mod=downloads&action=stats&file='.$_GET['file'].'&time='. $back .'&timeframe='. $row_back['back_time']);
  } else $dsp->AddBackButton('index.php?mod=downloads&action=stats');
}
$dsp->AddContent();
?>