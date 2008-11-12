<?php

if ($auth['login']) {

	$row = $db->qry_first('SELECT username FROM	%prefix%user WHERE userid = %int%', $_GET['queryid']);
	$templ['messenger']['query']['info']['username'] = $row['username'];

	$index .= $dsp->FetchTpl("design/templates/messenger_query_index.htm");
	echo $index;

} else {
	$func->error("NO_LOGIN","");
	echo $templ_index_content;
}
?>