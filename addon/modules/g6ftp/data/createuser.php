<?php
//variables
$username=$_POST['username'];
$password=$_POST['password'];
$email=$_POST['email'];

include_once("config.inc");

//no username entered
		if($username == ''){
                die("<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=?mod=g6ftp&action=show3                                       \">");
		}
//no password entered
		if($password == ''){
                die("<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=?mod=g6ftp&action=show4 \">");
		}

// get contents of a file into a string
$handle = fopen ($filename, "r");
$contents = fread ($handle, filesize ($filename));
fclose ($handle);

$lines = explode("\n",$contents);

//date and time
$DateTime = date("m/d/Y h:i:s A");


for($i=0;$i<count($lines);$i++) {
	if (preg_match("(^\[)", $lines[$i], $regs)) {

		$patterns[0] = "/(^\[)/";
		$patterns[1] = "/(])/";
		$replacements[0] = "";
		$replacements[1] = "";

		$lines[$i] = preg_replace($patterns, $replacements, $lines[$i]);

//username taken
		if(strtolower($_POST['username']) == strtolower(trim($lines[$i]))){
                die("<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=usertaken.php?username=".$_POST['username']."         \">");
		}
		

	}	
}

//rename variables
$contents = str_replace ("%USERNAME%", "$username", $contents);
$contents = str_replace ("%PASSWORD%", "$password", $contents);
$contents = str_replace ("%EMAIL%", "$email", $contents);

    if (!$handle = fopen($filename_new, 'w')) {
         print "Cannot write file $filename_new is not writable (Make sure your folder has been CHMOD 777) 1/2";
         exit;
    }	
	
    // Write $somecontent to our opened file.
    if (!fwrite($handle, $contents)) {
        print "Cannot write file $filename_new is not writable (Make sure your folder has been CHMOD 777) 2/2";
        exit;
    }
    print "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; ".$_POST['username']."&password=".$_POST['password']." \">";
    print "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=?mod=g6ftp&action=show5     \">";
    
    fclose($handle);
					

?>
