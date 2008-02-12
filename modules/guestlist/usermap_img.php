<?php
Header("Content-type: image/png");

$x_start = 5.43;
$y_start = 46.95;
$x_end = 15.942;
$y_end = 55.155;

$map_img = ImageCreateFromPNG("modules/guestlist/map.png");
$img_width = ImageSX($map_img);
$img_height = ImageSY($map_img);

$xf = $img_width / (abs($x_start - $x_end));
$yf = $img_height / (abs($y_start - $y_end));

$red = ImageColorAllocate($map_img, 255, 0, 0);

$res = $db->query("SELECT user.userid, user.username, user.city, user.plz, COUNT(*) AS anz, locations.laenge, locations.breite
		FROM {$config["tables"]["user"]} AS user
		INNER JOIN {$config["tables"]["locations"]} AS locations ON user.plz = locations.plz
		INNER JOIN {$config["tables"]["party_user"]} AS party ON user.userid = party.user_id
		WHERE (user.plz > 0) AND (party.party_id = {$party->party_id}) AND user.type > 0
		GROUP BY user.plz
		");

while ($user = $db->fetch_array($res)) {
	$kx = (int) ($xf * ($user['laenge'] - $x_start));
	$ky = (int) ($img_height - $yf * ($user['breite'] - $y_start));

	$size = floor(1 + 0.25 * $user['anz']);
	if ($size > 5) $size = 5;
	imagefilledrectangle($map_img, $kx-$size, $ky-$size, $kx+$size, $ky+$size, $red);
}
$db->free_result($res);


Imagepng($map_img);
ImageDestroy($map_img);
?>
