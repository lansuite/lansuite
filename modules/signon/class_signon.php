<?php

class signon
{
    public $birthday;
    public $perso;

        
    public function SplitStreet($input)
    {
        $pieces = explode(" ", $input);
        $res["nr"] = array_pop($pieces);
        $res["street"] = implode(" ", $pieces);
        return $res;
    }


    public function SplitCity($input)
    {
        $pieces = explode(" ", $input);
        $res["plz"] = array_shift($pieces);
        $res["city"] = implode(" ", $pieces);
        return $res;
    }


    public function GetLansurfer($username, $password)
    {
        if (!function_exists("socket_create")) {
            return false;
        } else {
            $username = str_replace("@", "%40", $username);
            $username = str_replace(".", "%2E", $username);

            // Session-ID holen
    #		$address = gethostbyname ('www.lansurfer.com');
            $address = "212.112.228.36";
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_nonblock($socket);
            @socket_set_timeout($socket, 1, 500);
            if ($socket < 0) {
                echo "socket_create() fehlgeschlagen: Grund: " . socket_strerror($socket) . "\n";
            }
            $result = @socket_connect($socket, $address, 80);
            if ($result < 0) {
                echo "socket_connect() fehlgeschlagen.\nGrund: ($result) " . socket_strerror($result) . "\n";
            }
            $in_post = "username=$username&password=$password&savecookie=&submit=Login";
            $content_lengt = strlen($in_post);
            #Accept: image/gif, image/jpeg, */*
            $in = "POST /user/edit.phtml HTTP/1.1
			Host: lansurfer.com
			User-Agent: Mozilla/4.0
			Content-type: application/x-www-form-urlencoded
			Content-length: $content_lengt
			Connection: close

			$in_post";

            @socket_write($socket, $in, strlen($in));
            $out = "";
            while ($line = @socket_read($socket, 2048)) {
                $out .= $line;
            }
            $LS_Session = substr($out, strpos($out, "LS_Session=") + 11, 50);
            $LS_Session = substr($LS_Session, 0, strpos($LS_Session, ";"));
            @socket_close($socket);

            // Login senden und Daten holen
            $opts['http']['method'] =    "POST";
            $opts['http']['header'] =    "Content-type: application/x-www-form-urlencoded\r\n";
            "Content-length: $content_lengt";
            $opts['http']['content'] =    $in_post;

            $lansurfer_site = "";
            $handle = fopen("http://www.lansurfer.com/user/edit.phtml?LS_Session=$LS_Session", 'r', false, stream_context_create($opts));
            while (!feof($handle)) {
                $lansurfer_site .= fgets($handle, 4096);
            }
            fclose($handle);

            // Log out
            $handle = fopen("http://lansurfer.com/user.phtml?action=logout?LS_Session=$LS_Session", 'r', false, stream_context_create($opts));

            // HTML-Daten auswerten
            $input_start = 2;
            $input_end = 2;
            $lansurfer_data = array();
            for ($z = 0; $z < 30; $z++) {
                $input_start = strpos($lansurfer_site, "<input", $input_end);
                if ($input_start == 0) {
                    break;
                }
                $input_start += 6;
                $input_end = strpos($lansurfer_site, ">", $input_start) - 1;

                $line = substr($lansurfer_site, $input_start, $input_end - $input_start);
                $name = "";
                if (strpos($line, "name=") > 0) {
                    $name = substr($line, strpos($line, "name=") + 6, strlen($line));
                    if (strpos($name, " ") > 0) {
                        $name = substr($name, 0, strpos($name, " ") - 1);
                    }

                    $value = "";
                    if (strpos($line, "value=") > 0) {
                        $value = substr($line, strpos($line, "value=") + 7, strlen($line));
                        if (strpos($value, " ") > 0) {
                            $value = substr($value, 0, strpos($value, " ") - 1);
                        }
                    }
                    $lansurfer_data[$name] = $value;
                }
            }

            return $lansurfer_data;
        }
    }
} // class
