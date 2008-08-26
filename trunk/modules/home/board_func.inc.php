<?php

function CheckPostNew($last_post, $last_read) {
  global $db, $config, $auth, $line;

  // Delete old entries
  $db->query("DELETE FROM {$config["tables"]["board_read_state"]} WHERE last_read < ". (time() - 60 * 60 * 24 * 7));

  // Older, than one week
  if ($last_post < (time() - 60 * 60 * 24 * 7)) return 0;

  // No entry -> Thread completely new
  elseif (!$last_read) return 1;

  // Entry exists
  else {

    // The posts date is newer than the mark -> New
    if ($last_read < $last_post) return 1;

    // The posts date is older than the mark -> Old
    else return 0;
  }
}
?>