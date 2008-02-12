<?php

switch ($_GET['step']) {
	default:
		$mastersearch = new MasterSearch($vars, "index.php?mod=seating&action=free_seat", "index.php?mod=seating&action=free_seat&step=2&blockid=", '');
		$mastersearch->LoadConfig('seat_blocks', $lang['seat']['ms_search'], $lang['seat']['ms_result']);  // <- wo wird das definiert ?
		$mastersearch->PrintForm();
		$mastersearch->Search();
		$mastersearch->PrintResult();
		$templ['index']['info']['content'] .= $mastersearch->GetReturn();
	break;

	case 2:
		$dsp->NewContent($lang['seating']['seat_info'],$lang['seating']['seat_info_sub']);

		$dsp->AddDoubleRow($lang['seating']['seating'], '', 'seating');
		$dsp->AddDoubleRow($lang['seating']['user'],    '', 'name');
		$dsp->AddDoubleRow($lang['seating']['clan'],    '', 'clan');
		$dsp->AddDoubleRow($lang['seating']['ip'],      '', 'ip');
		$dsp->AddSingleRow($seat2->DrawPlan($_GET['blockid'], 0, "index.php?mod=seating&action=free_seat&step=3&blockid={$_GET['blockid']}"));

		$dsp->AddBackButton('index.php?mod=seating', 'seating/show');
		$dsp->AddContent();
	break;

	case 3:
		$func->question($lang['seating']['q_rel_seat'], "index.php?mod=seating&action=free_seat&step=4&blockid={$_GET['blockid']}&row={$_GET['row']}&col={$_GET['col']}", "index.php?mod=seating&action=free_seat&step=2&blockid={$_GET['blockid']}");
	break;

	case 4:
		$db->query("UPDATE {$config["tables"]["seat_seats"]} SET userid = 0, status = 1
			WHERE blockid = {$_GET['blockid']} AND row = {$_GET['row']} AND col = {$_GET['col']}");

		$func->confirmation($lang['seating']['i_rel_seat'], 'index.php?mod=seating&action=free_seat');
	break;
}
?>