<?php
//------------------------------------------------------------------------------------------------------------+
  require "lgsl_files/lgsl_config.php";

  $auth   = md5($_SERVER['REMOTE_ADDR'].md5($lgsl_config['admin']['user'].md5($lgsl_config['admin']['pass'])));
  $cookie = $_COOKIE['lgsl_admin_auth'];

  if (!$lgsl_config['admin']['user'] || !$lgsl_config['admin']['pass'])
  {
    exit("ADMIN USERNAME OR PASSWORD MISSING FROM CONFIG");
  }
  elseif ($lgsl_config['admin']['pass'] == "changeme")
  {
    exit("ADMIN PASSWORD MUST BE CHANGED FROM THE DEFAULT");
  }
  elseif ($cookie == $auth)
  {
    setcookie("lgsl_admin_auth", $auth, (time() + (60 * 60 * 24)), "/");
    define("LGSL_ADMIN", "1");
  }
  elseif ($lgsl_config['admin']['user'] == $_POST['lgsl_user'] && $lgsl_config['admin']['pass'] == $_POST['lgsl_pass'])
  {
    setcookie("lgsl_admin_auth", $auth, (time() + (60 * 60 * 24)), "/");
    define("LGSL_ADMIN", "1");
  }
//------------------------------------------------------------------------------------------------------------+
  header("Content-Type:text/html; charset=utf-8");
//------------------------------------------------------------------------------------------------------------+
?>



<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>

<html xmlns='http://www.w3.org/1999/xhtml'>
  <head>
    <title>Live Game Server List</title>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <meta http-equiv='content-style-type' content='text/css' />
    <link rel='stylesheet' href='lgsl_style.css' type='text/css' />
  </head>

  <body>
    <div style='height:30px'><br /></div>




<?php
//------------------------------------------------------------------------------------------------------------+
  if (defined("LGSL_ADMIN"))
  {
    $output = "";
    require "lgsl_files/lgsl_admin.php";
    echo $output;
  }
  else
  {
    echo "
    <form method='post' action=''>
      <table style='margin:auto; text-align:center'>
        <tr><td> USERNAME: </td><td> <input type='text'     name='lgsl_user' value='' /> </td></tr>
        <tr><td> PASSWORD: </td><td> <input type='password' name='lgsl_pass' value='' /> </td></tr>
        <tr>
          <td colspan='2'>
            <br />
            <input type='submit' name='lgsl_admin_login' value='Login' />
          </td>
        </tr>
      </div>
    </form>";
  }
//------------------------------------------------------------------------------------------------------------+
?>



  </body>
</html>
