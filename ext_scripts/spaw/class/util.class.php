<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Utility class
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2004 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2004-11-11
// ================================================

class SPAW_Util
{
  // checks browser compatibility with the control
  function checkBrowser()
  {
    $browser = $_SERVER['HTTP_USER_AGENT'];
    // check if msie
    if (eregi("MSIE[^;]*",$browser,$msie))
    {
      // get version 
      if (eregi("[0-9]+\.[0-9]+",$msie[0],$version))
      {
        // check version
        if ((float)$version[0]>=5.5)
        {
          // finally check if it's not opera impersonating ie
          if (!eregi("opera",$browser))
          {
            return true;
          }
        }
      }
    }
    elseif (ereg("Gecko/([0-9]*)",$browser,$build))
    {
      // build date of version 1.3 is 20030312
      if ($build[1] > "20030312")
        return true;
      else
        return false;
    }
    return false;
  }
  
  // returns browser type
  function getBrowser()
  {
    $browser = $_SERVER['HTTP_USER_AGENT'];
    if (eregi('opera',$browser))
    {
      return 'Opera';
    }
    elseif (eregi('MSIE',$browser))
    {
      return 'IE';
    }
    elseif (eregi('Gecko',$browser))
    {
      return 'Gecko';
    }
    else
    {
      return 'Unknown';
    }
  }
  
  // returns GET variable
  function getGETVar($var_name, $empty_value='')
  {
  
    global $HTTP_GET_VARS;
    if (!empty($_GET[$var_name]))
      return $_GET[$var_name];
    elseif (!empty($HTTP_GET_VARS[$var_name]))
      return $HTTP_GET_VARS[$var_name];
    else
      return $empty_value;
  }

  // returns POST variable
  function getPOSTVar($var_name, $empty_value='')
  {
    global $HTTP_POST_VARS;
    if (!empty($_POST[$var_name]))
      return $_POST[$var_name];
    else if (!empty($HTTP_POST_VARS[$var_name]))
      return $HTTP_POST_VARS[$var_name];
    else
      return $empty_value;
  }
  
   // returns FILES variable
  function getFILESVar($var_name, $empty_value='')
  {
    global $HTTP_POST_FILES;
    if (!empty($_FILES[$var_name]))
      return $_FILES[$var_name];
    else if (!empty($HTTP_POST_FILES[$var_name]))
      return $HTTP_POST_FILES[$var_name];
    else
      return $empty_value;
  }
  
   // returns SERVER variable
  function getSERVERVar($var_name, $empty_value='')
  {
    global $HTTP_SERVER_VARS;
    if (!empty($_SERVER[$var_name]))
      return $_SERVER[$var_name];
    else if (!empty($HTTP_SERVER_VARS[$var_name]))
      return $HTTP_SERVER_VARS[$var_name];
    else
      return $empty_value;
  }
 
 

}
?>
