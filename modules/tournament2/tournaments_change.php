<?php
$stepParameter = $_GET['step'] ?? 0;
switch ($stepParameter) {
    case "open":
        if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                echo $key;
                $database->query('UPDATE %prefix%tournament_tournaments SET status = ? WHERE tournamentid = ?', ["open", $key]);
            }
        }
        $func->confirmation(t('Das Turnier wurde erfolgreich geündert'), "index.php?mod=tournament2");
    
        break;
  
    case "lock":
        if ($auth['type'] >= \LS_AUTH_TYPE_SUPERADMIN) {
            foreach ($_POST['action'] as $key => $val) {
                echo $key;
                $database->query('UPDATE %prefix%tournament_tournaments SET status = ? WHERE tournamentid = ?', ["locked", $key]);
            }
        }
        $func->confirmation(t('Das Turnier wurde erfolgreich geündert'), "index.php?mod=tournament2");
        break;
}
