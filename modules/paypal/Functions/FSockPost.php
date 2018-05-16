<?php

/**
 * Posts transaction data using fsockopen.
 *
 * @param string $url
 * @param array $data
 * @return array|string
 */
function fsockPost($url, $data)
{
    $web = parse_url($url);

    $postdata = '';
    foreach ($data as $i => $v) {
        $postdata .= $i . "=" . urlencode($v) . "&";
    }

    $postdata .= "cmd=_notify-validate";

    // Set the port number
    if ($web['scheme'] == "https") {
        $web['port'] = "443";
        $ssl = "ssl://";
    } else {
        $web['port'] = "80";
    }

    // Create paypal connection
    $fp = fsockopen($ssl . $web['host'], $web['port'], $errnum, $errstr, 30);
    if (!$fp) {
        echo "$errnum: $errstr";
    } else {
        fputs($fp, "POST $web[path] HTTP/1.1\r\n");
        fputs($fp, "Host: $web[host]\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ".strlen($postdata)."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $postdata . "\r\n\r\n");

        // Loop through the response from the server
        while (!feof($fp)) {
            $info[]=@fgets($fp, 1024);
        }

        fclose($fp);

        // break up results into a string
        $info=implode(",", $info);
    }

    return $info;
}
