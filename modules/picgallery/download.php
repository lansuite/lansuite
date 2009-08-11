<?php

if (file_exists("ext_inc/picgallery/$_GET[picurl]")) {
  $_GET['picurl'] = str_replace("..","",$_GET['picurl']);
  $picinfo = GetImageSize("ext_inc/picgallery/$_GET[picurl]");
  if($picinfo['2'] == "1" OR $picinfo['2'] == "2" OR $picinfo['2'] == "3") {
    header("content-type: application/octet-stream");
    header("content-disposition: attachment; filename=$_GET[picurl]");
    
    readfile("ext_inc/picgallery/$_GET[picurl]");
  } else {
    header("content-type: application/octet-stream");
    header("content-disposition: attachment; filename=$_GET[picurl]");
    
    readfile("ext_inc/picgallery/$_GET[picurl]");
  }
}
?>