<?php // 18.03.2003 21:24 - raphael@one-network.org

$action = $_GET['action'];

$user_agent_str 		= getenv("HTTP_USER_AGENT");

if(ereg("Opera", $user_agent_str))	 	{ $user_agent_type = 1; }
elseif(ereg("Netscape", $user_agent_str))	{ $user_agent_type = 2; }
elseif(ereg("MSIE", $user_agent_str)) 		{ $user_agent_type = 1; }
elseif(ereg("Gecko", $user_agent_str)) 		{ $user_agent_type = 2; }
elseif(ereg("Konqueror", $user_agent_str)) 	{ $user_agent_type = 2; }
else 						{ $user_agent_type = 2; }

//-- ACTION SWITCH --//
switch($action) {
	
	default: 		include("modules/misc/default.php");		break;
		
	
	case ip:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/ip.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case ip_block:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/ip_block.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case ipgen:
	 	if ($auth['login'] == 1 AND $auth['type'] >= 3) { 	include("modules/misc/ipgen.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 		}
	break;
	
	case ipprint:
		if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/ipprint.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;
	
	case sticker:
		if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/sticker.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;
	
	case whatsup:
		if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/whatsup.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;

	case import:
		if ($auth['login'] == 1 AND $auth['type'] >= 3) { 	include("modules/misc/import.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;

	case import_xml:
		if ($auth['login'] == 1 AND $auth['type'] >= 3) { 	include("modules/misc/import_xml.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;

	case import_csv:
		if ($auth['login'] == 1 AND $auth['type'] >= 3) { 	include("modules/misc/import_csv.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;
	
	case log:
		if ($auth['login'] == 1 AND $auth['type'] >= 2) 	include("modules/misc/log.php");
		else 					$func->error("ACCESS_DENIED","");
	break;
	
	
	
	case export:
		if ($auth['login'] == 1 AND $auth['type'] >= 2) { 	include("modules/misc/export.php"); 	}
		else { 			$func->error("ACCESS_DENIED",""); 	}		
	break;
	
	
	
} // switch - action
?>
