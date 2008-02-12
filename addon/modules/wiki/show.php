<?php
/*
ob_start();
include_once('ext_scripts/dokuwiki/doku.php');
ob_end_clean();
$auth = array();
*/
$dsp->NewContent(t('Wiki'), '');
if ($_GET['action'] == 'install') $dsp->AddSingleRow('<iframe src="ext_scripts/dokuwiki/install.php" width="99%" height="600"><a href="ext_scripts/dokuwiki/install.php">DokuWiki Install</a></iframe>');
else $dsp->AddSingleRow('<iframe src="ext_scripts/dokuwiki/doku.php" width="99%" height="600"><a href="ext_scripts/dokuwiki/doku.php">DokuWiki</a></iframe>');
$dsp->AddContent();

?>