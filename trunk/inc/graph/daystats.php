<?php

$graph = new graph(600,280);

$graph->define_chart(12,90);
		
$graph->setentry(1, 30, 1000, 0);
$graph->setentry(2, 30, 500, 1000);
$graph->setentry(3, 30, 550, 500);

$image = $graph->generate_image();

echo $image;



?>
