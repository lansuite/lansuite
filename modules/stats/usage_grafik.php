<?php
Header("Content-type: image/svg+xml");
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:ev="http://www.w3.org/2001/xml-events"
    version="1.1" baseProfile="full"
    width="700" height="300" viewBox="0 0 700 300">

  <polyline points="50 278  650 278" fill="none" stroke="black" stroke-width="3px" />
  <polyline points="50 0  50 280" fill="none" stroke="black" stroke-width="3px" />
  <polyline points="650 0  650 280" fill="none" stroke="black" stroke-width="3px" />
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
$res = $db->qry("SELECT SUM(hits) AS hits, SUM(visits) AS visits FROM %prefix%stats_usage
  WHERE DATE_FORMAT(time, %string%) = %string%
  GROUP BY DATE_FORMAT(time, %string%)
    ", $where, $_GET['timeframe'], $group_by);
while ($row = $db->fetch_array($res)) {
    if ($row_max['hits'] < $row['hits']) {
        $row_max['hits'] = $row['hits'];
    }
    if ($row_max['visits'] < $row['visits']) {
        $row_max['visits'] = $row['visits'];
    }
}
$db->free_result($res);

$z = 1;
for ($x = 50 + ((700 - 100) / $XSteps); $x < 650; $x += ((700 - 100) / $XSteps)) {
    echo '<polyline points="'. $x .' 278  '. $x .' 270" fill="none" stroke="black" stroke-width="3px" />';
    echo '<text y="295" x="'. ($x - 8) .'" fill="blue">'. $z .'</text>';
    if ($z % 5 == 0) {
        echo '<polyline points="'. $x .' 278  '. $x .' 0" fill="none" stroke="black" stroke-width="1px" stroke-dasharray="1%, 1%" />';
    }
    $z++;
}

$z = $row_max['visits'];
$z2 = $row_max['hits'];
for ($y = 0; $y < 280; $y+= (280 / 14)) {
    echo '<polyline points="50 '. $y .' 58  '. $y .'" fill="none" stroke="black" stroke-width="3px" />';
    echo '<polyline points="642 '. $y .' 650  '. $y .'" fill="none" stroke="black" stroke-width="3px" />';
    echo '<text x="0" y="'. $y .'" fill="#990000">'. round($z, 0) .'</text>';
    $z -= $row_max['visits'] / 14;
    echo '<text x="652" y="'. $y .'" fill="#009900">'. round($z2, 0) .'</text>';
    $z2 -= $row_max['hits'] / 14;
}


// Select hits + visits
$res = $db->qry("SELECT DATE_FORMAT(time, %string%) AS group_by_time, UNIX_TIMESTAMP(time) AS display_time, SUM(hits) AS hits, SUM(visits) AS visits FROM %prefix%stats_usage
  WHERE DATE_FORMAT(time, %string%) = %string%
  GROUP BY DATE_FORMAT(time, %string%)
  ORDER BY DATE_FORMAT(time, %string%)
", $group_by, $where, $_GET['timeframe'], $group_by, $group_by);
$X = 50;
$Y = 280;
$Y2 = 280;
while ($row = $db->fetch_array($res)) {
    $lastX = $X;
    $lastY = $Y;
    $lastY2 = $Y2;
    $X = (50 + ((700 - 100) / $XSteps) * date($multiply, $row['display_time']));
    $Y = 280 - (($row["visits"] / $row_max['visits']) * 280);
    $Y2 = 280 - (($row["hits"] / $row_max['hits']) * 280);
    echo '<polyline points="'. $lastX .' '. $lastY .'  '. $X .' '. $Y .'" stroke="#990000" stroke-width="3px"/>';
    echo '<polyline points="'. $lastX .' '. $lastY2 .'  '. $X .' '. $Y2 .'" stroke="#009900" stroke-width="3px"/>';

#  echo '<text x="400" y="20" fill="blue">Debug: '. $row_max['hits'] .'</text>';
}
$db->free_result($res);


?>
</svg>