<?php
$z = 0;
foreach($ModOverviews as $ModOverview) {
  if ($z % 2 == 0) echo '<ul class="Line"><li class="LineLeftHalf">';
  else echo '<li class="LineRight">';
  include('modules/home/'. $ModOverview .'.inc.php');
  echo $dsp->FetchModTpl('home', 'show_item');
  echo '</li>';
  if ($z % 2 == 1) echo '</ul>';
  $z++;
}
if ($z % 2 == 1) echo '</ul>';
?>