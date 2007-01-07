<?php 

function Needed($key){
	global $cfg;

	if ($cfg['signon_show_'. $key] == 2) return true;
	else return false;
}

// Check, if all required user data fields, are known and force user to add them, if not.
$auth['lastname'] = $auth['name'];
$auth['gender'] = $auth['sex'];
$auth['wwcl_id'] = $auth['wwclid'];
$auth['ngl_id'] = $auth['nglid'];
foreach ($auth as $key => $val) if (!is_int($key) and Needed($key) and $val == '') $missing_fields ++;

if ($missing_fields) {
  $_GET['userid'] = $auth['userid'];

  include_once("modules/usrmgr/language/usrmgr_lang_$language.php");
  include_once('modules/usrmgr/add.php');
}
?>