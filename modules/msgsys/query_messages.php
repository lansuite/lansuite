<?php

$query = $db->qry("UPDATE %prefix%messages SET new = '0' WHERE senderid = %int% AND receiverid = %int%", $_GET['queryid'], $auth['userid']);
$query = $db->qry("SELECT text, timestamp, senderid FROM %prefix%messages
  WHERE (senderid = %int% AND receiverid = %int%) OR (senderid = %int%  AND receiverid = %int%)
  ORDER BY timestamp
  ", $auth['userid'], $_GET['queryid'], $_GET['queryid'], $auth['userid']);

$row2 = $db->qry_first("SELECT username FROM %prefix%user WHERE userid = %int%", $_GET[queryid]);

while ($row = $db->fetch_array($query)) {
    $senderid    = $row["senderid"];
    $timestamp    = $row["timestamp"];
    $text        = $row["text"];
            
    $text = $func->text2html($text);
    $date = $func->unixstamp2date($timestamp, "time");
    
    if ($senderid == $auth['userid']) {
        $class    = "tbl_blue";
        $msgs .= "<div class=\"$class\"><b>".$auth['username']." ($date):</b></div> $text".HTML_NEWLINE . HTML_NEWLINE;
    } else {
        $class    = "tbl_red";
        $msgs .= "<div class=\"$class\"><b>".$row2['username']." ($date):</b></div> $text".HTML_NEWLINE . HTML_NEWLINE;
    }
}

$msgs .= "<a name=\"end\"></a>";
$smarty->assign('msgs', $msgs);
$smarty->assign('refreshtime', 15);

$smarty->assign('queryid', $_GET['queryid']);
$index .= $smarty->fetch("design/templates/messenger_query_messages.htm");
echo $index;
