<?php

class SwitchUser {
  var $Code = '';

  function SaveOldID($userid) {
		// Generate switch back code
		for ($x = 0; $x <= 24; $x++) $this->Code .= chr(mt_rand(65, 90));

		// Save old user ID
		setcookie("olduserid", $userid, time() + (3600*24*365));
		setcookie("sb_code", $this->Code, time() + (3600*24*365));
  }
  
  function DeleteOldID() {
		setcookie("olduserid", "", 0);
		setcookie("sb_code", "", 0);
  }
}
?>