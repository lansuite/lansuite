<?php
/*
 * Created on 17.02.2006
 *
 * @author Pieringer Johannes
 * 
 * Opens the Board or the installer if the board isn't configured yet or shows the board.
 */

	include_once('modules/board2/class_board2.php');
	include_once('modules/board2/class_board2install.php');
	
	
	$board2 = new Board2();
	$board2install = new Board2install();
	$cAuth = new auth();

	if ($config['board2']['configured'])
	{
		if ($gettodo == 'show' or $gettodo == '')
		{	//Shows the board
			$dsp->NewContent('');
	      
			if (!$cfg['board2_new_window'])
			{	//IFrame
				if ($_SESSION["lansuite"]["fullscreen"])
					$dsp->AddIFrame($_SERVER['HTTP_HOST'] . '/' .  $config[board2][path].'index.php', 960);
				else
					$dsp->AddIFrame($_SERVER['HTTP_HOST'] . '/' .  $config[board2][path].'index.php', 795);
			}
			else //New Window
			{
				$dsp->ShowNewWindow($_SERVER['HTTP_HOST'] . '/' .  $config[board2][path].'index.php');
			}
			
			$dsp->AddContent();
		}
	}
	else //not configured
	{
		if (($cAuth->isCurrentUserOperator('board2') == 1 || $auth['type'] == 3) && $auth['login'])
		{
			if (!isset($_GET['todo']))
			{	
				$func->information($lang['board2']['installorinte']['attention'], 'index.php?mod=board2&action=index&todo=install', 'next');
			}
			else if ($_GET['todo'] == 'install')
			{	//The user decides what to do, install a new or integrate an existing phpBB Board
				$dsp->NewContent($lang['board2']['headline'], $lang['board2']['installorinte']['subheadline']);
				$dsp->AddSingleRow($lang['board2']['installorinte']['instOrInte']);
				$dsp->SetForm('index.php?mod=board2&action=index&todo=decided');
				$work = array();
				
				$work[] .= '<option value="install">'. $lang['board2']['installorinte']['install'] . '</option>';
				$work[] .= '<option value="integrate">'. $lang['board2']['installorinte']['integrate'] . '</option>';
				
				$dsp->AddDropDownFieldRow('work', $lang['board2']['installorinte']['subheadline'], $work,'');
				
				$dsp->AddFormSubmitRow('next');
				$dsp->AddContent();
			}
			else if ($_GET['todo'] == 'decided')
			{
				if ($_POST['work'] == 'install')
				{	//Shows the configuration parameters for the phpBB installation
					$langPhpBB = $board2install->getPhpBBLang();
					$board2install->setBoard2Prefix();
					
					$templ['index']['board2']['install']['_Default_lang'] = $langPhpBB['Default_lang'];
					$templ['index']['board2']['install']['_dbms'] = $langPhpBB['dbms'];
					$templ['index']['board2']['install']['_Install_Method'] = $langPhpBB['Install_Method'];
					$templ['index']['board2']['install']['_DB_Host'] = $langPhpBB['DB_Host'];
					$templ['index']['board2']['install']['_DB_Name'] = $langPhpBB['DB_Name'];
					$templ['index']['board2']['install']['_DB_Username'] = $langPhpBB['DB_Username'];
					$templ['index']['board2']['install']['_DB_Password'] = $langPhpBB['DB_Password'];
					$templ['index']['board2']['install']['_Table_Prefix'] = $langPhpBB['Table_Prefix'];
					$templ['index']['board2']['install']['_Admin_email'] = $langPhpBB['Admin_email'];
					$templ['index']['board2']['install']['_Admin_Username'] = $langPhpBB['Admin_Username'];
					$templ['index']['board2']['install']['_Admin_Password'] = $langPhpBB['Admin_Password'];
					$templ['index']['board2']['install']['_Admin_Password_confirm'] = $langPhpBB['Admin_Password_confirm'];
					
					$templ['index']['board2']['install']['Default_lang'] = $langPhpBB['board2']['language_name_english'];
					$templ['index']['board2']['install']['dbms'] = 'MySQL 4.x/5.x';
					$templ['index']['board2']['install']['Install_Method'] = $langPhpBB['Install'];
					
					$templ['index']['board2']['install']['DB_Host'] = $config['database']['server'];
					$templ['index']['board2']['install']['DB_Name'] = $config['database']['database'];
					$templ['index']['board2']['install']['DB_Username'] = $config['database']['user'];
					$templ['index']['board2']['install']['DB_Password'] = $config['database']['passwd'];
					$templ['index']['board2']['install']['Table_Prefix'] = $config['board2']['prefix'];
					$templ['index']['board2']['install']['overwrite_Admin'] = $lang['board2']['install']['overwriteAdmin'];
					
					$templ['index']['board2']['install']['text'] .= $lang['board2']['install']['text'];
					$templ['index']['board2']['install']['completeFormCorrectly'] .= $lang['board2']['install']['completeFormCorrectly'];
					$templ['index']['board2']['install']['integration'] .= str_replace('@path', $config[board2][path], $lang['board2']['install']['integration']);
					$templ['index']['board2']['install']['installphpBB'] .= $lang['board2']['install']['installphpBB'];
					
					$templ['index']['board2']['install']['url'] .= $config[board2][path].'install/install.php';
					
					$dsp->NewContent($lang['board2']['headline'], $lang['board2']['install']['subheadline']);
					$dsp->SetForm('index.php?mod=board2&action=index&todo=integrateFresh');
					
					$dsp->AddSingleRow($dsp->FetchModTpl('board2', 'install'));
					
					$dsp->AddDropDownFieldRow('phpbbversion', $lang['board2']['install']['version'], $board2install->getSupportedIntegVersions(),'');
					$dsp->AddFormSubmitRow('next');
					$dsp->AddContent();
				}
				else
				{	//The user sets the options from the existing board.
					$dsp->NewContent($lang['board2']['headline'], $lang['board2']['integrateonly']['subheadline']);
					$dsp->SetForm('index.php?mod=board2&action=index&todo=integrateExisting');
					$dsp->AddTextFieldRow('prefix', $lang['board2']['integrateonly']['prefix'], $config['board2']['prefix'], null);
					$dsp->AddTextFieldRow('path', $lang['board2']['integrateonly']['path'], $config['board2']['path'], null);
					$dsp->AddDropDownFieldRow('phpbbversion', $lang['board2']['install']['version'], $board2install->getSupportedIntegVersions(),'');
					$dsp->AddFormSubmitRow('next');
					$dsp->AddContent();
				}
			}
			else if ($_GET['todo'] == 'integrateFresh')
			{	//Finally integrates the NEW board.
				$board2install->integratePhpBB($_POST['phpbbversion']);
	  			$dsp->NewContent($lang['board2']['headline'], '');
				$dsp->SetForm('index.php?mod=board2&action=index&todo=adminActivation');
				$dsp->AddCheckBoxRow('activation', $lang['board2']['integrateonly']['boardRegister'], '', '');
				
				$dsp->AddFormSubmitRow('next');
				$dsp->AddContent();
			}
			else if ($_GET['todo'] == 'integrateExisting')
			{	//Starts the integration of the EXISTING board.
				if ($_GET['step'] == null || $_GET['step'] == '1')
				{	//The user has to decide which user from lansuite is a user from the EXISTING phpBB board.
					$board2install->setConfigVariable('prefix', $_POST['prefix']);
					$board2install->setConfigVariable('path', $_POST['path']);
					
					$dsp->NewContent($lang['board2']['headline'], $lang['board2']['test']['subheadline']);
					$dsp->SetForm('index.php?mod=board2&action=index&todo=integrateExisting&step=2');
					
					$phpBBUser = $board2install->getDoublePhpBBUser();
					if (count($phpBBUser) != 0)
					{
						$dsp->AddSingleRow($lang['board2']['test']['doubleUser']);
						foreach ($phpBBUser as $user)
							$dsp->AddSingleRow($user);
					}
					else //Everything fine!
					{
						$dsp->AddSingleRow($lang['board2']['test']['noProblems']);
						
					}
					$dsp->AddFormSubmitRow('next');
					$dsp->AddContent();
				}
				else if ($_GET['step'] == '2')
				{
					if ($_GET['error'] != 1)
					{
						$lansuiteUser = $board2install->getLansuiteUser();
			  		
						$dsp->NewContent($lang['board2']['headline'], $lang['board2']['integrateonly']['subheadline']);
						$dsp->SetForm('index.php?mod=board2&action=index&todo=integrateExisting&step=3');
						$dsp->AddSingleRow($lang['board2']['integrateonly']['user2user']);
						$dsp->addDoubleRow('phpBB', 'lansuite');
						
						$phpBBUser = $board2install->getPhpBBUser();
						
						foreach ($phpBBUser as $key => $user){
							$dsp->AddDropDownFieldRow('b2var_' . $key, $user->getUsername(), $lansuiteUser,$user->getErrorText());
						}
						
						$dsp->AddFormSubmitRow('next');
						$dsp->AddContent();
					}
					else //Error happened - double assigned phpBB user
					{
						$lansuiteUser = $board2install->getLansuiteUser();
			  
						$dsp->NewContent($lang['board2']['headline'], $lang['board2']['integrateonly']['subheadline']);
						$dsp->SetForm('index.php?mod=board2&action=index&todo=integrateExisting&step=2');
			
						$dsp->addDoubleRow('phpBB', 'lansuite');
						
						$phpBBUser = $board2install->getPhpBBUser();
						
						foreach ($phpBBUser as $key => $user){
							$dsp->AddDropDownFieldRow('b2var_' . $key, $user->getUsername(), $lansuiteUser,$user->getErrorText());
						}
						
						$dsp->AddFormSubmitRow('next');
						$dsp->AddContent();
					}
				}
				else if ($_GET['step'] == '3')
				{	//Finally integrates the EXISTING board.
					
					$phpBBUser = array();
					foreach ($_POST as $key => $user) {
						if (substr($key, 0,6) == 'b2var_'){
							$key = substr($key, 6 ,strlen($key));
							$phpBBUser[$key] = $user;
						}
					}
					
					if ($board2install->integratePhpBBOnly($phpBBUser) == 1)
					{
						$dsp->NewContent($lang['board2']['headline'], '');
						$dsp->SetForm('index.php?mod=board2&action=index&todo=adminActivation');
						$dsp->AddCheckBoxRow('activation', $lang['board2']['integrateonly']['boardRegister'], '', '');
						
						$dsp->AddFormSubmitRow('next');
						$dsp->AddContent();
					}
					else
					{
						$func->error($lang['board2']['integrateonly']['error'], 'index.php?mod=board2&action=index&todo=integrateExisting&step=2&error=1');
					}
				}
				else
				{
					$func->error($lang['board2']['integration']['bug'], 'index.php');
				}
			}
			else if ($_GET['todo'] == 'adminActivation')
			{
				if ($_POST['activation'] == 1)
					$board2install->setAccountAdminActivation();
				
				$board2install->finishInstallation();
				$dsp->NewContent($lang['board2']['headline'], '');
				$dsp->SetForm('index.php?mod=board2&action=index');
				
				$templ['index']['board2']['integration']['text'] .= $lang['board2']['integration']['successfully'];
		  		$dsp->AddSingleRow($dsp->FetchModTpl('board2', 'integrate'));
				
				$dsp->AddFormSubmitRow('next');
				$dsp->AddContent();
			}
			else
			{
				//TODO change errormessage
				$func->error($lang['board2']['not_configured'], 'index.php');
			}
		}
		else
		{
			$func->error($lang['board2']['not_configured'], 'index.php');
		}
	}
	
 //
 // Please make a history at the end of file of your changes !!
 //

 /* HISTORY
 * 17. 2. 2006 : First adaption of the file from the sample module.
 * 19. 2. 2006 : Functionality added.
 * 25. 3. 2006 : Outsourced the functionality into the class board2 and board2install
 */
?>