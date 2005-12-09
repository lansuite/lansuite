<?php

$error = 0;
if(isset($_GET["search_module"])) $_POST["search_module"] = $_GET["search_module"];
switch($_POST["search_module"]) {
	case "tournament":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=tournament&action=details&tournamentid=", "");
		$mastersearch->LoadConfig("tournament", $lang["search"]["s_t"], $lang["search"]["r_t"]);
	break;

	case "news":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=news&action=comment&newsid=", "");
		$mastersearch->LoadConfig("news", $lang["search"]["s_n"], $lang["search"]["r_n"]);
	break;

	case "poll":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=poll&action=show&step=2&pollid=", "");
		$mastersearch->LoadConfig("polls", $lang["search"]["s_p"], $lang["search"]["r_p"]);
	break;

	case "faq":
		echo $lang["search"]["err_no_ms"];
		$error = 1;
	break;

	case "server":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=server&action=show_details&serverid=", "");
		$mastersearch->LoadConfig("server", $lang["search"]["s_s"], $lang["search"]["r_s"]);
	break;

	case "thread":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=board&level=thread&tid=", "");
		$mastersearch->LoadConfig("thread", $lang["search"]["s_b"], $lang["search"]["r_b"]);
	break;

	case "noc":
		if($auth["type"] > 1) {
			echo $lang["search"]["err_no_ms"];
			$error = 1;
		} else $func->error("ACCESS_DENIED", "");
	break;

	case "user":
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=usrmgr&action=details&userid=", "GROUP BY u.email");
		$mastersearch->LoadConfig("users", $lang["search"]["s_u"], $lang["search"]["r_u"]);
	break;

	case "troubleticket":
		switch ($auth["type"]) {
			default:
				$sql = " AND (status > '0' AND orgaonly = '0')";
			break;
			case 2:
				$sql = " AND (status > '0')";
			break;
			case 3:
				 $sql = " AND (status > '0')";
			break;		
		}
		$mastersearch = new MasterSearch($vars, "index.php?mod=search&action=searchbox&search_module={$_POST["search_module"]}", "index.php?mod=troubleticket&action=show&step=2&ttid=", $sql);
		$mastersearch->LoadConfig("troubleticket", $lang["search"]["s_tt"], $lang["search"]["r_tt"]);
	break;

	case "rent":
		if($auth["type"] > 1) {
			echo $lang["search"]["err_no_ms"];
			$error = 1;
		} else $func->error("ACCESS_DENIED", "");
	break;

	default:
		$error = 1;
	break;
}

if (!$error) {
	$mastersearch->PrintForm();
	$mastersearch->Search();
	$mastersearch->PrintResult();
	$templ['index']['info']['content'] .= $mastersearch->GetReturn();	
}
?>
