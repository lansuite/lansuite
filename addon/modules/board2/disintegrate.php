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
			if (!isset($_GET['todo']))
			{
				$func->question($lang['board2']['disintegrate']['question'], 'index.php?mod=board2&action=disintegrate&todo=disintegrate', 'index.php?mod=board2&action=index');
			}
			else
			{
				if ($_GET['todo'] == 'disintegrate')
				{
					$board2install->deIntegrate();
					
					$dsp->NewContent($lang['board2']['headline'], $lang['board2']['installorinte']['subheadline']);
					$dsp->AddSingleRow($lang['board2']['disintegrate']['successfully']);
					$dsp->AddContent();
				} 
				//else
				//{
			}
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