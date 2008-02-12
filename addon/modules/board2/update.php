<?php
	
	include_once('class_board2.php');
	include_once('class_board2install.php');
	
	
	$board2 = new Board2();
	$board2install = new Board2install();
	$cAuth = new auth();
	
	if (($cAuth->isCurrentUserOperator('board2') == 1 || $auth['type'] == 3) && $auth['login'])
	{	
		if ($config['board2']['configured'])
		{
			$dsp->NewContent($lang['board2']['headline'], $lang['board2']['update']['subheadline']);
			$dsp->AddSingleRow($lang['board2']['update']['follow']);
			$dsp->AddSingleRow($lang['board2']['update']['step1']);
			$dsp->AddSingleRow($lang['board2']['update']['step2']);
			$dsp->AddSingleRow($lang['board2']['update']['step3']);
			
			$dsp->SetForm('index.php');
			$dsp->AddFormSubmitRow('next');
			
			$dsp->AddContent();
		}
		else
		{
			$func->error($lang['board2']['not_configured'], 'index.php?mod=board2&action=index');
		}
	}
	else
	{
		$func->error('ACCESS_DENIED', 'index.php?mod=board2&action=index');		
	}
?>