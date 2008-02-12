<?php
	// Here is, where you define, which page should be loaded according to submitted action

	switch ($vars["action"]) {
	
	default:
			include ("modules/task/show_task.php");
	break;

 		case add_task:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/task/add_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case show_task:
	 	 include("modules/task/show_task.php");
	break;
	
	case del_task:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/task/del_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case join_task:
	 	if ($auth['login'] == 1) { 	include("modules/task/join_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case change_task:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/task/change_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case manage_task:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/task/manage_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case my_task:
	 	if ($auth['login'] == 1) { 	include("modules/task/my_task.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	}
?>