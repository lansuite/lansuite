<?php

/**
 * @param string $file
 * @param array $data
 * @return bool
 */
function create_csv_file($file, $data)
{
    if (is_array($data)) {
        $post_values = array_values($data);

        $csv = '';
        foreach ($post_values as $i) {
            $csv .= "\"$i\",";
        }

        $csv = substr($csv, 0, -1);
        $fp = fopen($file, "a");
        fwrite($fp, $csv . "\n");
        fclose($fp);

        return true;
    } else {
        return false;
    }
}
