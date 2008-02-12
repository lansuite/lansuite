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
foreach ($auth as $key => $val) if (!is_int($key) and Needed($key) and $val == '') {
  $missing_fields ++;
}

if ($missing_fields) {
  include_once("modules/usrmgr/language/usrmgr_lang_$language.php");
  include_once('modules/usrmgr/class_adduser.php');
  $AddUser = new AddUser();
  $_GET['userid'] = $auth['userid'];

  // Error-Switch
  switch ($_GET['step']) {
    default:
      $AddUser->GetDBData('change');
    break;
    
    case 2:
  		$AddUser->CheckErrors('change');
  	break;
  }

  // Form- & DB-Switch  
  switch ($_GET['step']) {
    default:       
      $dsp->NewContent($lang['missing_fields']['caption'], $lang['missing_fields']['subcaption']);
      $dsp->SetForm("index.php?mod=home&step=2");
      
      $AddUser->ShowForm('change');
    break;
    
    case 2:
    	$AddUser->WriteToDB('change');
		  $func->confirmation($lang["missing_fields"]["success"], '');
    break;
  }
}
?>