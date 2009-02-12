<?php
/*
 * Created on 17.02.2006
 *
 * @author Pieringer Johannes
 *
 * Opens the Board the installer if the board isn't configured yet.
 */
include_once('modules/board2/class_board2.php');
include_once('modules/board2/class_board2install.php');

if ($config['board2']['configured'])
{
	$func->error(t('Das phpBB Board wurde bereits integriert.'), NO_LINK);
}
else //not configured
{
	if (!isset($_GET['todo']))
	{
		$func->information('<br><b>' . t('Datenbank Backup!') . '</b><br><br>' . t('Bitte erstellen Sie ein komplettes Backup von lansuite und phpbb (DB + Dateisystem).<br>Im Fehlerfall kann die Datenbank in einem <b>inkonsistenten</b> Zustand hinterlassen werden!'), 'index.php?mod=board2&action=integrate&todo=install', '', 0, 'FORWARD');
	}
	else if ($_GET['todo'] == 'install')
	{	//The user decides what to do, install a new or integrate an existing phpBB Board
		$question = array();
		$link = array();
		$question[] .= t('Aufsetzen eines neuen phpBB Forums');
		$link[] .= 'index.php?mod=board2&action=integrate&todo=decided&work=install';
		$question[] .= t('Integrieren eines bestehenden phpBB Forums');
		$link[] .= 'index.php?mod=board2&action=integrate&todo=decided&work=integrate';
		$func->multiquestion($question, $link, t('Wollen Sie ein phpBB Forum neu Aufsetzen und in Lansuite integrieren oder wollen Sie ein bestehendes phpBB Forum in Lansuite integrieren?'));
	}
	else if ($_GET['todo'] == 'decided')
	{
		if ($_GET['work'] == 'install')
		{	//Shows the configuration parameters for the phpBB installation
			Board2install::setDefaultBoard2Prefix();
			Board2install::setDefaultBoard2Path();
			Board2install::setDefaultBoard2Version();
			$langPhpBB = Board2install::getPhpbbTranslations();

			$smarty->assign('text', t('Beachte die folgenden Punkte bei der Installation von phpBB!:'));

			$smarty->assign('desc_DBMS', $langPhpBB['dbms']);
			$smarty->assign('desc_DB_Host', $langPhpBB['DB_Host']);
			$smarty->assign('desc_DB_Name', $langPhpBB['DB_Name']);
			$smarty->assign('desc_DB_Username', $langPhpBB['DB_Username']);
			$smarty->assign('desc_DB_Password', $langPhpBB['DB_Password']);
			$smarty->assign('desc_Table_Prefix', $langPhpBB['Table_Prefix']);
			$smarty->assign('desc_Admin_email', $langPhpBB['Admin_email']);
			$smarty->assign('desc_Admin_Username', $langPhpBB['Admin_Username']);
			$smarty->assign('desc_Admin_Password', $langPhpBB['Admin_Password']);
			$smarty->assign('desc_Admin_Password_confirm', $langPhpBB['Admin_Password_confirm']);

			$smarty->assign('DBMS', t('"MySQL" oder "MySQL with MySQLi Extension"'));
			$smarty->assign('DB_Host', $config['database']['server']);
			$smarty->assign('DB_Name', $config['database']['database']);
			$smarty->assign('DB_Username', $config['database']['user']);
			$smarty->assign('DB_Password', $config['database']['passwd']);
			$smarty->assign('Table_Prefix', $config['board2']['prefix']);
			$smarty->assign('overwrite_Admin', t('Wird mit den Daten des Admins von lansuite &uuml;berschrieben.'));

			$smarty->assign('completeFormCorrectly', t('Die restlichen Felder je nach Server korrekt ausf&uuml;llen.'));
			$smarty->assign('url', Board2install::getPhpbbInstallationUrl());
			$smarty->assign('installphpBB', t('Um phpBB zu installieren, klicken Sie bitte hier.'));
			$smarty->assign('integration', str_replace('@path', $config[board2][path], t('Wenn die Installation beendet ist m&uuml;ssen noch die Ordner <b>install/</b> und <b>contrib/</b> im Verzeichnis <b>@path</b> gel&ouml;scht werden.<br><br> Im n&auml;chsten Schritt wird phpBB in lansuite integriert.')));

			$dsp->NewContent(t('Board phpBB'), t('Installation von phpBB'));
			$dsp->SetForm('index.php?mod=board2&action=integrate&todo=integrateFresh');

			$dsp->AddSingleRow($smarty->fetch('modules/board2/templates/install.htm'));

			$versions_array = Board2::getSupportedVersions();
			foreach ($versions_array as $key => &$value) {
				if ($key == $config['board2']['version']) $value = '<option selected value="' . $key . '">'. $value .'</option>';
				else $value = '<option value="' . $key . '">'. $value .'</option>';
			}
			$dsp->AddDropDownFieldRow('phpbbversion', t('Version von phpBB'), $versions_array,'');
			$dsp->AddFormSubmitRow('next');
			$dsp->AddContent();
		}
		else
		{	//The user sets the options from the existing board.
			$dsp->NewContent(t('Board phpBB'), t('Integrationseinstellungen'));
			$dsp->SetForm('index.php?mod=board2&action=integrate&todo=integrateExisting');
			$dsp->AddSingleRow(t('Lansuite und phpBB m&uuml;ssen am <b>selben Server</b> liegen und die und die beiden Datenbanken m&uuml;ssen <b>in einer Datenbank</b> gespeichert sein. (Durch den Prefix der Tabellen, kannes nicht zu &Uuml;berschneidungen kommen.)'));
			$dsp->AddTextFieldRow('prefix', t('Prefix der Datenbanktabellen des phpBB Forum:'), $config['board2']['prefix'], null);
			$dsp->AddTextFieldRow('path', t('Pfad des phpBB Forums relativ zu lansuite:'), $config['board2']['path'], null);
			$versions_array = Board2::getSupportedVersions();
			foreach ($versions_array as $key => &$value) {
				if ($key == $config['board2']['version']) $value = '<option selected value="' . $key . '">'. $value .'</option>';
				else $value = '<option value="' . $key . '">'. $value .'</option>';
			}
			$dsp->AddDropDownFieldRow('phpbbversion', t('Version von phpBB'), $versions_array,'');
			$dsp->AddFormSubmitRow('next');
			$dsp->AddContent();
		}
	}
	else if ($_GET['todo'] == 'integrateFresh')
	{
		if ($_GET['step'] == null || $_GET['step'] == '1') {
			if (array_key_exists('phpbbversion', $_POST)) Board2install::setConfigVariable('version', $_POST['phpbbversion']);

			$user_array = board2install::checkForSpecialUser();	// Usernames like Anonymous
			if (count($user_array) > 0) {
				$text = '';
				foreach ($user_array as $user) {
					$text .= '<br>' . $user;
				}
				$func->information(t('In phpBB gibt es nicht menschliche Benuter die eine spezielle Bedeutung besitzen (z.B.: Anonymous). Es gibt lansuite Accounts die solche Usernamen besitzen. Bitte pr&uuml;fen Sie ob die lansuite Accounts von einer vorherigen phpBB Integration stammen, oder ob es sich um menschliche Benutzer handelt. Wenn es menschliche User sind &auml;ndern sie bitte deren Usernamen.<br><br>Folgende Usernamen existieren in lansuite:') . $text . '<br><br>' .  t('&Auml;ndern Sie die Lansuite Usernamen und starten Sie die Integration erneut.'), 'index.php?mod=board2&action=integrate&todo=integrateFresh');
			} else {
				checkForDoubleUser('index.php?mod=board2&action=integrate&todo=integrateFresh', 'index.php?mod=board2&action=integrate&todo=integrateFresh&step=2');
			}
		}
		else if ($_GET['step'] == '2') { //Finally integrates the NEW board.
			try {
				$anonymousAcc = Board2install::integrateFreshPhpBB();
				if ($anonymousAcc == FALSE) $func->error(t('Die neue user_id des Anonymous aus phpBB k&ouml;nnte in der phpbb\\includes\\constants.php.<br> Bitte setzten sie die ID per Hand. Die k&ouml;nnen Sie per Datenbank herausfinden.'));
				
				$dsp->NewContent(t('Board phpBB'), '');
				$dsp->SetForm('index.php?mod=board2&action=integrate&todo=adminActivation');
				$dsp->AddCheckBoxRow('activation', t('Sollen die Account Aktivierung am phpBB Board mittels Admin-Mail aktiviert werden?'), '', '');
				$dsp->AddFormSubmitRow('next');
				$dsp->AddContent();
			} catch (Exception $exc) {
				$func->error($exc->getMessage());
			}
		}
	}
	else if ($_GET['todo'] == 'integrateExisting')
	{	//Starts the integration of the EXISTING board.
		if ($_GET['step'] == null || $_GET['step'] == '1')
		{	//The user has to decide which user from lansuite is a user from the EXISTING phpBB board.
			if (array_key_exists('prefix', $_POST)) Board2install::setConfigVariable('prefix', $_POST['prefix']);
			if (array_key_exists('path', $_POST)) Board2install::setConfigVariable('path', $_POST['path']);
			if (array_key_exists('phpbbversion', $_POST)) Board2install::setConfigVariable('version', $_POST['phpbbversion']);
				
			try {
				Board2install::IsIntegrationPossible();
					
				$user_array = board2install::checkForSpecialUser();	// Usernames like Anonymous
				if (count($user_array) > 0 && ($_GET['checkUser'] == null || $_GET['checkUser'] != '0')) {
					$text = '';
					foreach ($user_array as $user) {
						$text .= '<br>' . $user;
					}
					$func->information(t('In phpBB gibt es nicht menschliche Benuter die eine spezielle Bedeutung besitzen (z.B.: Anonymous). Es gibt lansuite Accounts die solche Usernamen besitzen. Bitte pr&uuml;fen Sie ob die lansuite Accounts von einer vorherigen phpBB Integration stammen, oder ob es sich um menschliche Benutzer handelt. Wenn es menschliche User sind &auml;ndern sie bitte deren Usernamen.<br><br>Folgende Usernamen existieren in lansuite:') . $text . '<br><br>' .  t('Klicken Sie auf Weiter um die lansuite Accounts mit den phpBB Accounts zu verbinden oder &auml;ndern sie die lansuite Accounts und starten Sie die Integration neu.'), 'index.php?mod=board2&action=integrate&todo=integrateExisting&step=1&checkUser=0', '', 0, 'FORWARD');
				}
				else
				{
					checkForDoubleUser('index.php?mod=board2&action=integrate&todo=integrateExisting&step=1&refresh=1&checkUser=0', 'index.php?mod=board2&action=integrate&todo=integrateExisting&step=2');
				}
			} catch (Exception $exc) {
				$func->error($exc->getMessage());
			}
		}
		else if ($_GET['step'] == '2')
		{
			$dsp->NewContent(t('Board phpBB'), t('Integrationseinstellungen'));
			$dsp->AddSingleRow(t('Es werden automatisch alle lansuite und phpBB Accounts mit der selben Email Adresse zu einem Account vereinigt.') . '<br>' . t("Klicken Sie auf Weiter."));

			$masterSearch = Board2install::getPhpBBUserWithEqualLsAccount_Email_MasterSearch();
			$masterSearch->PrintSearch('index.php?mod=party', 'l.userid');

			$dsp->SetForm('index.php?mod=board2&action=integrate&todo=integrateExisting&step=3');
			$dsp->AddFormSubmitRow('next');
			$dsp->AddContent();
		}
		else if ($_GET['step'] == '3')
		{
			$dsp->NewContent(t('Board phpBB'), t('Integrationseinstellungen'));
			$dsp->AddSingleRow(t('Es werden automatisch alle lansuite und phpBB Accounts mit den selben Usernamen und unterschiedlichen Email Adressen zu einem Account vereinigt.') . '<br>' . t("Klicken Sie auf Weiter."));

			$masterSearch = Board2install::getPhpBBUserWithEqualLsAccount_Username_MasterSearch();
			$masterSearch->PrintSearch('index.php?mod=party', 'l.userid');

			$dsp->SetForm('index.php?mod=board2&action=integrate&todo=integrateExisting&step=4');
			$dsp->AddFormSubmitRow('next');
			$dsp->AddContent();
		}
		else if ($_GET['step'] == '4')
		{
			$lansuiteUser_array = Board2install::getLansuiteUser();
			$options_array = array();
			$options_array['new'] = t('Neuen Lansuite Account anlegen.');
			$options_array[] .= "<option $selected value='new'>" . t('Neuen Lansuite Account anlegen.') . "</option>";
			foreach ($lansuiteUser_array as $user){
				$options_array[] .= "<option $selected value='{$user->GetLansuiteUserID()}'>" . $user->GetLansuiteUsername() . ' - ' . $user->GetLansuiteEmail() . "</option>";
			}

			$dsp->NewContent(t('Board phpBB'), t('Integrationseinstellungen'));
			$dsp->SetForm('index.php?mod=board2&action=integrate&todo=integrateExisting&step=5');
			$dsp->AddSingleRow(t('Es werden alle phpBB Accounts angezeigt, f&uuml;r die kein passender lansuite Account gefunden wurde. Sie k&uuml;nnen diesen Accounts h&auml;ndisch lansuite Accounts zuordnen, sonst werden automatisch neue lansuite Accounts angelegt.') . '<br>' . t("Klicken Sie auf Weiter."));
			$dsp->AddDoubleRow('<b>' . t('phpBB') . '</b>', '<b>' . t('lansuite') . '</b>');

			$phpBBUser = Board2install::getPhpBBUserWithoutEqualLsAccount();

			foreach ($phpBBUser as $key => $user){
				$dsp->AddDropDownFieldRow('b2var_' . $user->getPhpbbUserID(), $user->getPhpbbUsername() . ' - ' . $user->getPhpbbEmail(), $options_array, '');
			}

			$dsp->AddFormSubmitRow('next');
			$dsp->AddContent();
		}
		else if ($_GET['step'] == '5')
		{	//Finally integrates the EXISTING board.

			$phpBBUser = array();
			foreach ($_POST as $key => $user) {
				if (substr($key, 0,6) == 'b2var_'){
					$key = substr($key, 6, strlen($key));
					$phpBBUser[$key] = $user;
				}
			}

			try {
				if (Board2install::integrateExistingPhpBB($phpBBUser) == 1) {
					$dsp->NewContent(t('Board phpBB'),'');
					$dsp->SetForm('index.php?mod=board2&action=integrate&todo=adminActivation');
					$dsp->AddCheckBoxRow('activation', t('Soll die Account Aktivierung am phpBB Board mittels Admin-Mail verhindert werden?'), '', '');

					$dsp->AddFormSubmitRow('next');
					$dsp->AddContent();
				} else {
					$func->error(t('Es wurde ein phpBB User einem lansuite User mehrfach zugewiesen!'), 'index.php?mod=board2&action=integrate&todo=integrateExisting&step=2&error=1');
				}
			} catch (Exception $exc) {
				$func->error(t('Bei der Integration ist folgender Fehler aufgetreten: ') . $exc->getMessage() . '<br><br><b>' .  t('Die Integration wurde unvollst&auml;ndig beendet, die DB ist u.U. in einem inkonsistenten Zustand. Bitte spielen Sie das Backup ein.') . '</b>', 'index.php');
			}
		}
		else
		{
			$func->error(t('Es ist ein unbekannter Fehler aufgetreten.'), 'index.php');
		}
	}
	else if ($_GET['todo'] == 'adminActivation')
	{
		if ($_POST['activation'] == 1) Board2install::setAccountAdminActivation();
		Board2install::finishInstallation();
		$func->information(t('Das PhpBB Forum wurde erfolgreich in lansuite integriert.'), 'index.php?mod=board2&action=index', '', 0, 'FORWARD');
	}
}


function checkForDoubleUser($refresh, $next) {
	global $dsp;
	$dsp->NewContent(t('Board phpBB'), t('Integrationseinstellungen'));
	if (Board2install::getDoubleLansuiteUserCount() != 0) {
		$dsp->AddSingleRow(t('Die folgenden Usernamen kommen in der lansuite Datenbank doppelt vor. Bitte &auml;nderen oder l&ouml;schen Sie User, bis keine weiteren doppelten Benutzernamen mehr existieren.') . '<br>' . t("Nachdem Sie alle doppelten Benutzernamen entfernt haben, klicken Sie auf Aktualisieren."));
			
		$masterSearch = Board2install::getDoubleLansuiteUser();
		$masterSearch->PrintSearch('index.php?mod=party', 'l.userid');

		$dsp->SetForm($refresh);
		$dsp->AddFormSubmitRow('refresh');
	} else { //Everything fine!
		$dsp->AddSingleRow(t('In der lansuite Datenbank kommen keine Benutzernamen doppelt vor, phpbb kann integriert werden.') . '<br>' . t("Klicken Sie auf Weiter."));
		$dsp->SetForm($next);
		$dsp->AddFormSubmitRow('next');
	}
	$dsp->AddContent();
}

//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 14.01.2009 : Created the file.
 *  6. 2.2009 : Finished the implementation of the integration.
 */
?>