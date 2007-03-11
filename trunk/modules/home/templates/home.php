<?php
$z = 0;
foreach($ModOverviews as $ModOverview) {
  if ($z % 2 == 0) {
    echo '<ul class="Line">';
    if ($z != (count($ModOverviews) - 1)) echo '<li class="LineLeftHalf">';
    else echo '<li class="LineRight">';
  } else echo '<li class="LineRight">';
  $st = round($sitetool->out_work(), 2);
  include('modules/home/'. $ModOverview .'.inc.php');
  echo $dsp->FetchModTpl('home', 'show_item');
  echo round($sitetool->out_work() - $st, 2).'</li>';
  if ($z % 2 == 1) echo '</ul>';
  $z++;
}
if ($z % 2 == 1) echo '</ul>';
?>