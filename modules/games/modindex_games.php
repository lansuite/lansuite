<?php

switch($_GET["action"]) {
	default:
		include ("modules/games/overview.php");
	break; 

	case minesweeper:
		include("modules/games/minesweeper.php");
	break;

	case number:
		include("modules/games/number.php");
	break;

	case hangman:
		include("modules/games/hangman.php");
	break;
}
?>