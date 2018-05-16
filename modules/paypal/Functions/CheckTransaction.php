<?php

/**
 * @param string $verify_file
 * @param string $checked_file
 * @param string $verify_id
 * @param string $item_id
 * @return bool
 */
function check_transaction($verify_file, $checked_file, $verify_id, $item_id)
{
    $data = @file($verify_file);
    $check = @file($checked_file);

    $data = array_reverse($data);

    // Check Files
    if (isset($data) && isset($check)) {
        // Check for Transfer
        foreach ($data as $line) {
            if (strstr($line, $verify_id) && strstr($line, $item_id)) {
                $found = true;
                break;
            }
        }

        // Check for double Transfer
        if ($found) {
            $fp = @fopen($checked_file, "a");
            $found_double = false;
            foreach ($check as $line) {
                if (strstr($line, $verify_id) && strstr($line, $item_id)) {
                    $found_double = true;
                }
            }
        }

        // Write for double check and get true
        if ($found == true && $found_double == false) {
            @fwrite($fp, $verify_id . " " . $item_id . "\n");
            @fclose($fp);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
