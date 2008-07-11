<?php
$z = 0;
foreach($ModOverviews as $ModOverview) {
  if ($z % 2 == 0) {
    $MainContent .= '<ul class="Line">';
    if ($z != (count($ModOverviews) - 1)) $MainContent .= '<li class="LineLeftHalf">';
    else $MainContent .= '<li class="LineRightHalf">';
  } else $MainContent .= '<li class="LineRightHalf">';
#  $st = round($sitetool->out_work(), 2);
  include('modules/home/'. $ModOverview .'.inc.php');
  $MainContent .= $dsp->FetchModTpl('home', 'show_item');
#  echo round($sitetool->out_work() - $st, 2);
  if ($z % 2 == 1) $MainContent .= '</ul>';
  $z++;
}
if ($z % 2 == 1) $MainContent .= '</ul>';
?>