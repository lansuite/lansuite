<?php
/*
* Copyright (c) 2004-2006, woah-projekt.de
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
*   notice, this list of conditions and the following disclaimer.
* * Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer
*   in the documentation and/or other materials provided with the
*   distribution.
* * Neither the name of the phgstats project (woah-projekt.de)
*   nor the names of its contributors may be used to endorse or
*   promote products derived from this software without specific
*   prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*/

#error_reporting(E_NOTICE);
define('PHGDIR', 'ext_scripts/phgstats/');

#$use_file = basename(__FILE__);
$use_file = 'index.php?mod=gameserver';
$use_bind = '&';

require_once (PHGDIR . 'settings/style.inc.php');
/*
echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>' . $btitle . '</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">

<!--
body {
	background: ' . $bg_color . '; 
	font-family: verdana, arial, sans-serif;
	font-size: 11pt;
}

A:link, A:visited, A:active {
	text-decoration: underline;
	color: ' .$color . ';
}

A:hover {
	text-decoration: underline;
	color: ' . $h_color . ';
}

table {
	font-family: verdana, arial, sans-serif;
	font-size: 10pt;
	color: ' . $t_color . ';
	background-color: ' . $bg_color . ';
}

td {
        color: ' . $td_color . ';
	background-color: ' . $tdb_color . ';
}

td.auth {
	color: ' . $td_color . ';
	background-color: ' . $bg_color . ';
}

th {
	color: ' . $th_color . ';
	background-color: ' . $thb_color . ';
}
-->
</style>
</head>

<body>
';*/

require_once (PHGDIR . 'main/phgstats.inc.php');

/*echo '
</body>
</html>';
*/
?>
