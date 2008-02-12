<?php





$dsp->NewContent($lang["install"]["menu_cap"],"");

switch ($_GET['step']){
	default:	
				$dsp->SetForm("?mod=install&action=dbmenu&step=2");
				$dsp->AddCheckBoxRow("rewrite",$lang["install"]["menu_cap"],"","");
				$dsp->AddFormSubmitRow("next");
				$dsp->AddBackButton("?mod=install");
				break;
	case 2:
				$install->InsertMenus($_POST["rewrite"]);
				$func->information($lang["install"]["menu_write"],"?mod=install");
				break;
}

$dsp->AddContent();
?>
