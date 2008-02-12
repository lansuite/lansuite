<?php 
	$config	= parse_ini_file("../../inc/base/config.php","TRUE");
	//if ($_GET["f"] == "") $_GET["f"]=$cfg["catering_nopic"];
	if ($_GET["f"] == "") $_GET["f"]="nopic.jpg";
	list($owidth, $oheight) = @getimagesize($_GET["f"]);
	$dwidth  = $_GET["w"];
	$dheight = round ($oheight / ($owidth / $dwidth));
	$im = @imagecreatefromjpeg($_GET["f"]);
	$tn = @imagecreatetruecolor($dwidth, $dheight);
	@imagecopyresized($tn, $im, 0,0,0,0, $dwidth, $dheight, $owidth, $oheight);
	header ("Content-type: image/jpeg");
	@imagejpeg($tn,"",100);
	@imagedestroy($tn);
	@imagedestroy($im);
?>
