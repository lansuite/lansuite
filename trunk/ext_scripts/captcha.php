<?php

session_start();
imageAuthCode(100, 20);

function imageAuthCode($width, $height) {
  mt_srand((double)microtime()*1000000);
  $auth_code = mt_rand(1000, 99999);
  $auth_code = md5($auth_code);
  $auth_code = substr($auth_code, 0, 5);
  $auth_code = strtoupper($auth_code);
  setcookie('image_auth_code', md5($auth_code), time()+60*60 , '/');
  $image = imagecreate($width,$height) or die("Can't initialize GD image stream");

  $bg_color     = imagecolorallocate($image, 240, 248, 255);
  $line_color   = imagecolorallocate($image, 150, 150, 150);
  $elpise_color = imagecolorallocate($image, 200, 200, 200);
  $text_color   = imagecolorallocate($image, 0, 0, 0);
  $rand1_h = mt_rand(1, $height);
  $rand2_h = mt_rand(1, $height);
  $rand3_h = mt_rand(1, $height);
  $rand4_h = mt_rand(1, $height);
  $rand1_v = mt_rand(1, $width);
  $rand2_v = mt_rand(1, $width);
  $rand3_v = mt_rand(1, $width);
  $rand4_v = mt_rand(1, $width);
  imageline($image, 0, $rand1_h, 100, $rand1_h, $line_color);
  imageline($image, 0, $rand2_h, 100, $rand2_h, $line_color);
  imageline($image, 0, $rand3_h, 100, $rand3_h, $line_color);
  imageline($image, 0, $rand4_h, 100, $rand4_h, $line_color);
  imageline($image, $rand1_v, 0, $rand1_v, 50, $line_color);
  imageline($image, $rand2_v, 0, $rand2_v, 50, $line_color);
  imageline($image, $rand3_v, 0, $rand3_v, 50, $line_color);
  imageline($image, $rand4_v, 0, $rand4_v, 50, $line_color);
  imagefilledellipse($image, mt_rand(0, 100), mt_rand(0, 40), mt_rand(10, 40), mt_rand(10, 25), $elpise_color);
  imagefilledellipse($image, mt_rand(0, 100), mt_rand(0, 40), mt_rand(20, 40), mt_rand(10, 25), $elpise_color);
  ImageTTFText ($image, 22, 0, 10, 20, $text_color, realpath('../ext_inc/fonts/captcha.ttf'), $auth_code);

  header("Content-type: image/png");
  imagepng($image);
  imagedestroy($image);
}
?>