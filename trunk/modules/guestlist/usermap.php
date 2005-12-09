<?php
/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-------------------------------------------------------------------
*	Lansuite Version:	2.0
*	File Version:		0.3
*	Filename: 			add.php
*	Module: 			signon
*	Main editor: 		knox@orgapage.net
*	Last change: 		10.09.2003 13:31
*	Description: 		Map which shows the home of the guests
*	Remarks:
*
**************************************************************************/

$dsp->NewContent($lang["guestlist"]["map_caption"], $lang["guestlist"]["map_subcaption"]);

$res = $db->query("SELECT plz FROM {$config["tables"]["user"]} LEFT JOIN {$config["tables"]["party_user"]} ON userid = user_id WHERE (plz > 0) AND (party_id = {$party->party_id})");

if ($db->num_rows($res) == 0) $dsp->AddSingleRow($lang["guestlist"]["map_err_noplz"]);
else {

	$map_out = "<map name=\"deutschland\">";

	$x_start = 5.43;
	$y_start = 46.95;
	$x_end = 15.942;
	$y_end = 55.155;

	$size = GetImageSize ("modules/guestlist/map.png");
	$img_width = $size[0];
	$img_height = $size[1];

	$xf = $img_width / (abs($x_start - $x_end));
	$yf = $img_height / (abs($y_start - $y_end));

#		LEFT JOIN {$config["tables"]["locations"]} AS locations ON LOCATE(user.plz, locations.plz)
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

		$map_out .= "<area shape=\"rect\" coords=\"". ($kx-$size) .", ". ($ky-$size) .", ". ($kx+$size) .", ". ($ky+$size) ."\" href=\"index.php?mod=usrmgr&action=details&userid={$user["userid"]}\" title=\"{$lang["guestlist"]["map_city"]}: {$user["plz"]} {$user["city"]}
{$lang["guestlist"]["map_name"]}: {$user["username"]}
{$lang["guestlist"]["map_user"]}: {$user["anz"]}\">";
	}
	$db->free_result($res);
	$map_out .= "</map>";

	$dsp->AddSingleRow($map_out ."<img src=\"base.php?mod=usermap_img\" usemap=\"#deutschland\" border=\"0\">");
}

$db->free_result($res);

$dsp->AddContent();
?>
