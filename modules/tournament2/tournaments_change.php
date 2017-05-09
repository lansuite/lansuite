<?php

switch ($_GET['step']) {
    case "open":
        if ($auth['type'] >= 3) {
            foreach ($_POST['action'] as $key => $val) {
                echo $key;
                $db->qry('UPDATE %prefix%tournament_tournaments SET status = %string% WHERE tournamentid = %int%', "open", $key);
            }
        }
        $func->confirmation(t('Das Turnier wurde erfolgreich geündert'), "index.php?mod=tournament2");
    
        break;
  
    case "lock":
        if ($auth['type'] >= 3) {
            foreach ($_POST['action'] as $key => $val) {
                echo $key;
                $db->qry('UPDATE %prefix%tournament_tournaments SET status = %string% WHERE tournamentid = %int%', "locked", $key);
            }
        }
        $func->confirmation(t('Das Turnier wurde erfolgreich geündert'), "index.php?mod=tournament2");
        break;
}
