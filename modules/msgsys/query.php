<?php

if ($auth['login']) {
    $row = $db->qry_first('SELECT username FROM	%prefix%user WHERE userid = %int%', $_GET['queryid']);
    $smarty->assign('username', $row['username']);
    $index .= $smarty->fetch("design/templates/messenger_query_index.htm");
    echo $index;
} else {
    $func->information("NO_LOGIN");
    echo $templ_index_content;
}
