<?php
/*
 * Created on 17.02.2006
 *
 * @author Pieringer Johannes
 * 
 * Opens the Board or the installer if the board isn't configured yet or shows the board.
 */

if ($config['board2']['configured']) {
	$dsp->NewContent(t('Board'));
	if ($gettodo == 'show' or $gettodo == '')
	{	//Shows the board
		if (!$cfg['board2_new_window'])
		{	//IFrame
			if ($_SESSION["lansuite"]["fullscreen"])
			$dsp->AddIFrame($config[board2][path].'index.php', 960);
			else
			$dsp->AddIFrame($config[board2][path].'index.php', 795);
		}
		else //New Window
		{
			$smarty->assign('popupBlocked', t('Wenn das Popup geblockt wurde, '));
			$smarty->assign('clickhere', t('bitte hier klicken.'));
			$smarty->assign('url', $config[board2][path].'index.php');
			$dsp->AddSingleRow($smarty->fetch('modules/board2/templates/new_window.htm'));
		}
		$dsp->AddContent();
	}
}
else //not configured
{
	$func->information(t('Es wurde noch kein phpBB Forum integriert.') . '<br><br>' . t('Klicken Sie auf Weiter um die Integration zu beginnen.'), 'index.php?mod=board2&action=integrate', '', 0, 'FORWARD');
}
	
//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 17. 2. 2006 : First adaption of the file from the sample module.
 * 19. 2. 2006 : Functionality added.
 * 25. 3. 2006 : Outsourced the functionality into the class board2 and board2install
 * 14. 1. 2009 : Outsourced the integraiton functionallity to the integration.php
 */
?>