<?php
session_start();
?>
<html>
<head>
<link rel="STYLESHEET" href="design/standard/style.css">
</head>
<body>
<table width="100%" width="100%">
<tr class="tbl_5"><td>
<center><b>Foreign Order</b></center><br>
<form method="POST">
<?php
if ($_POST["action"]=="Session wieder herstellen") {
	$_SESSION["auth"]["userid"]=$_POST["oldid"];
	$_POST["action"]="";
	unset($_SESSION["olduserid"]);
} 

if ($_SESSION["olduserid"]!="") {
	print "<center>Logged in: ".$_SESSION["auth"]["userid"]."<br><br>";
	?>
		<input type="hidden" name="oldid" value="<?php=$_SESSION["olduserid"]?>">
		<input type="submit" name="action" value="Session wieder herstellen" class="form"></center>
		</form>
		</td></tr></table>
		</body>
		</html>
	<?php
	die;	
}

if ($_POST["action"]=="") {
?>
	<table width="280" align="center">
	<tr class="tbl_5">
	<td align="center">UserID</td>
	<td align="center">Username</td>
	</tr>
	<tr>
	<td align="center"><input type="text" name="fo_userid" size="10" maxlength="12" class="form"></td>
	<td align="center"><input type="text" name="fo_username" size="10" maxlength="50" class="form"></td>
	</tr>
	<tr class="tbl_5">
	<td colspan="2"><br>
	Achtung! Hiermit wird die interne BenutzerID der Sitzung ver&auml;ndert. 
	Schliessen Sie dieses Fenster daher auf keinen Fall bevor Sie nicht die urspr&uuml;ngliche
	Sitzung wieder hergestellt haben.<bR><bR>
	<center><input type="submit" name="action" value="Session wechseln" class="form"></center></td>
	</tR>
	</table>
<?php
} else if ($_POST["action"]=="Session wechseln") {
	$config	= @parse_ini_file("inc/base/config.php","TRUE");
	require_once("inc/classes/class_db_mysql.php");
	$db	= new db;
	$db->connect();
	if (is_numeric($_POST["fo_userid"])==FALSE) {
		// Benutzer suchen
		$res = $db->query("SELECT userid FROM {$config['tables']['user']} WHERE username=\"".$_POST["fo_username"]."\"");
		if ($db->num_rows($res)!=1) {
			print "Keinen oder mehr als einen Benutzer gefunden. Abbruch.<br><br>";
			?>
				<a href="index.php?mod=catering&action=foreignorder">Zur&uuml;ck</a>
				</form>
				</td></tr></table>
				</body>
				</html>
			<?php
			die;
		}
		$row = $db->fetch_array($res);
		$_POST["fo_userid"]=$row["userid"];
		print "<center>Logged in: ".$_POST["fo_userid"]."<br><br>";
		?>
		<input type="hidden" name="oldid" value="<?php=$_SESSION["auth"]["userid"]?>">
		<input type="submit" name="action" value="Session wieder herstellen" class="form"></center>
		<?php
		$_SESSION["olduserid"]=$_SESSION["auth"]["userid"];
		$_SESSION["auth"]["userid"]=$_POST["fo_userid"];			
	} else {
		print "<center>Logged in: ".$_POST["fo_userid"]."<br><br>";
		?>
		<input type="hidden" name="oldid" value="<?php=$_SESSION["auth"]["userid"]?>">
		<input type="submit" name="action" value="Session wieder herstellen" class="form"></center>
		<?php
		$_SESSION["olduserid"]=$_SESSION["auth"]["userid"];
		$_SESSION["auth"]["userid"]=$_POST["fo_userid"];	
	}
}	
?>
</form>
</td></tr></table>
</body>
</html>
<?php
die;
?>
