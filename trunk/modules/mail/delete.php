<?php
$LSCurFile = __FILE__;

$row = $db->query_first("SELECT 1 AS found FROM {$config["tables"]["mail_messages"]} WHERE FromUserID = ". $auth['userid']);
if (!$row['found']) $func->error(t('Sie sind nicht berechtigt diese Mail zu löschen'));
else {
  include_once('inc/classes/class_masterdelete.php');
  $md = new masterdelete();
  $md->MultiDelete('mail_messages', 'mailid');
}
?>