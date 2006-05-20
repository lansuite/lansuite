<?php  
  header('Content-Type: application/x-javascript'); 
  
  include 'config/spaw_control.config.php';
  include 'class/util.class.php';

  if (SPAW_Util::getBrowser()=='Gecko')
  {
    include 'class/script_gecko.js.php';
  }
  else
  {
    include 'class/script.js.php';
  }
?> 

