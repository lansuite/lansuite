<?php 
  header('Content-Type: application/x-javascript');
?>
// toolbar button effects
function SPAW_classic_bt_over(ctrl)
{
  if (!ctrl.disabled && ctrl.disabled != "true") // for gecko
  {
    ctrl.className = "SPAW_classic_tb_over";
  }
}
function SPAW_classic_bt_out(ctrl)
{
  if (!ctrl.disabled && ctrl.disabled != "true") // for gecko
  {
    var imgfile = SPAW_base_image_name(ctrl)+".gif";
    ctrl.src = imgfile;
    if (ctrl.getAttribute("spaw_state") == true || ctrl.getAttribute("spaw_state") == "true")
    {
      ctrl.className = "SPAW_classic_tb_down";
    }
    else
    {
      ctrl.className = "SPAW_classic_tb_out";
    }
  }
}
function SPAW_classic_bt_down(ctrl)
{
  if (!ctrl.disabled && ctrl.disabled != "true") // for gecko
  {
    ctrl.className = "SPAW_classic_tb_down";
  }
}
function SPAW_classic_bt_up(ctrl)
{
  if (!ctrl.disabled && ctrl.disabled != "true") // for gecko
  {
    ctrl.className = "SPAW_classic_tb_out";
  }
}
function SPAW_classic_bt_off(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+"_off.gif";
  ctrl.src = imgfile;
}

