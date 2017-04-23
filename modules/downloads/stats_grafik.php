<?php
Header("Content-type: image/svg+xml");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:ev="http://www.w3.org/2001/xml-events"
    version="1.1" baseProfile="full"
    width="600" height="300" viewBox="0 0 700 300">

  <polyline points="0 278  660 278" fill="none" stroke="black" stroke-width="3px" />
  <polyline points="0 0  0 280" fill="none" stroke="black" stroke-width="3px" />
  <polyline points="660 0  660 280" fill="none" stroke="black" stroke-width="3px" />
<?php

switch ($_GET['time']) {
    default:
        $link = 'y';
        $back = '';
        $group_by = '%Y-00-00-00-00-00';
        $where = '0000-00-00-00-00-00';
        $where_back = '';
        $_GET['timeframe'] = '0000-00-00-00-00-00';
        $XSteps = 20;
        $multiply = 'y';
        break;
    case 'y':
        $link = 'm';
        $back = '';
        $group_by = '%Y-%m-00-00-00-00';
        $where = '%Y-00-00-00-00-00';
        $where_back = '00-00-00-00-00-00';
        $XSteps = 12;
        $multiply = 'm';
        break;
    case 'm':
        $link = 'd';
        $back = 'y';
        $group_by = '%Y-%m-%d-00-00-00';
        $where = '%Y-%m-00-00-00-00';
        $where_back = '%Y-00-00-00-00-00';
        $XSteps = 31;
        $multiply = 'd';
        break;
    case 'd':
        $link = '';
        $back = 'm';
        $group_by = '%Y-%m-%d-%H-00-00';
        $where = '%Y-%m-%d-00-00-00';
        $where_back = '%Y-%m-00-00-00-00';
        $XSteps = 24;
        $multiply = 'H';
        break;
}

// Select max
$res = $db->qry("SELECT SUM(hits) AS hits FROM %prefix%download_stats
  WHERE file = %string% AND DATE_FORMAT(time, %string%) = %string%
  GROUP BY DATE_FORMAT(time, %string%)
  ", $_GET['file'], $where, $_GET['timeframe'], $group_by);
while ($row = $db->fetch_array($res)) {
    if ($row_max['hits'] < $row['hits']) {
        $row_max['hits'] = $row['hits'];
    }
}
$db->free_result($res);

$z = 1;
for ($x = 0 + ((700 - 80) / $XSteps); $x < 660; $x += ((700 - 80) / $XSteps)) {
    echo '<polyline points="'. $x .' 278  '. $x .' 270" fill="none" stroke="black" stroke-width="3px" />';
    echo '<text y="295" x="'. ($x - 8) .'" fill="blue">'. $z .'</text>';
    if ($z % 5 == 0) {
        echo '<polyline points="'. $x .' 278  '. $x .' 0" fill="none" stroke="black" stroke-width="1px" stroke-dasharray="1%, 1%" />';
    }
    $z++;
}

#$z = $row_max['visits'];
$z2 = $row_max['hits'];
for ($y = 0; $y < 280; $y+= (280 / 14)) {
    echo '<polyline points="0 '. $y .' 8  '. $y .'" fill="none" stroke="black" stroke-width="3px" />';
    echo '<polyline points="652 '. $y .' 660  '. $y .'" fill="none" stroke="black" stroke-width="3px" />';
#  echo '<text x="0" y="'. $y .'" fill="#990000">'. round($z, 0) .'</text>';
#  $z -= $row_max['visits'] / 14;
    echo '<text x="662" y="'. $y .'" fill="#009900">'. round($z2, 0) .'</text>';
    $z2 -= $row_max['hits'] / 14;
}


// Select hits
$res = $db->qry("SELECT DATE_FORMAT(time, %string%) AS group_by_time, UNIX_TIMESTAMP(time) AS display_time, SUM(hits) AS hits FROM %prefix%download_stats
  WHERE file = %string% AND DATE_FORMAT(time, %string%) = %string%
  GROUP BY DATE_FORMAT(time, %string%)
  ORDER BY DATE_FORMAT(time, %string%)
", $group_by, $_GET['file'], $where, $_GET['timeframe'], $group_by, $group_by);
$X = 0;
$Y = 280;
$Y2 = 280;
while ($row = $db->fetch_array($res)) {
    $lastX = $X;
    $lastY = $Y;
    $lastY2 = $Y2;
    $X = (0 + ((700 - 80) / $XSteps) * date($multiply, $row['display_time']));
#  $Y = 280 - (($row["visits"] / $row_max['visits']) * 280);
    $Y2 = 280 - (($row["hits"] / $row_max['hits']) * 280);
#  echo '<polyline points="'. $lastX .' '. $lastY .'  '. $X .' '. $Y .'" stroke="#990000" stroke-width="3px"/>';
    echo '<polyline points="'. $lastX .' '. $lastY2 .'  '. $X .' '. $Y2 .'" stroke="#009900" stroke-width="3px"/>';

#  echo '<text x="400" y="20" fill="blue">Debug: '. $row_max['hits'] .'</text>';
}
$db->free_result($res);

?>
</svg>