<?php 
  header('Content-Type: application/x-javascript');
?>
// toolbar button effects
function SPAW_default_bt_over(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+"_over.gif";
  ctrl.src = imgfile;
}
function SPAW_default_bt_out(ctrl)
{
  var imgfile;
  if (ctrl.getAttribute("spaw_state") == true)
  {
    imgfile = SPAW_base_image_name(ctrl)+"_down.gif";
  }
  else
  {
    imgfile = SPAW_base_image_name(ctrl)+".gif";
  }
  ctrl.src = imgfile;
  ctrl.disabled = false;
}
function SPAW_default_bt_down(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+"_down.gif";
  ctrl.src = imgfile;
}
function SPAW_default_bt_up(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+".gif";
  ctrl.src = imgfile;
}
function SPAW_default_bt_off(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+"_off.gif";
  ctrl.src = imgfile;
  ctrl.disabled = true;
}

