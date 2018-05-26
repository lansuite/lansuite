<?php

/**
 * @param string $url
 * @return bool|string
 * @todo fix this to use fopen or similar to handle HTTPS and redirects
 */
function GetSite($url)
{
    global $HTTPHeader;

    $url = @parse_url($url);
    if (!$url['port']) {
        $url['port'] = 80;
    }
    $url['host'] = trim($url['host']);
    $url['path'] = trim($url['path']);
    if (!$url['host'] or !$url['path']) {
        $HTTPHeader = t('Hostname, oder Pfad fehlt');
        return '';
    }
    try {
        $fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 1);
    } catch (Exception $e) {
        // Ignore connection errors
    }

    if (!$fp) {
        $HTTPHeader = $errno.': '.$errstr;
        return '';
    } else {
        $cont = '';

        fputs($fp, "GET {$url['path']} HTTP/1.0\r\nHost: {$url['host']}\r\n\r\n");
        while (!feof($fp)) {
            $line = fgets($fp, 128);
            if ($line == '') {
                break;
            }
            $cont .= $line;
        }
        fclose($fp);

        $HTTPHeader = substr($cont, 0, strpos($cont, "\r\n\r\n"));

        $StatusCode = substr($HTTPHeader, strpos($HTTPHeader, ' ') + 1, 3);
        if ($StatusCode != 200) {
            return '';
        }

        return substr($cont, strpos($cont, "\r\n\r\n") + 4);
    }
}
