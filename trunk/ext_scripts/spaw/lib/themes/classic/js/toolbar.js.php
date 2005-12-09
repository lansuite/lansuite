<?php 
  header('Content-Type: application/x-javascript');
?>
// toolbar button effects
function SPAW_classic_bt_over(ctrl)
{
  ctrl.className = "SPAW_classic_tb_over";
}
function SPAW_classic_bt_out(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+".gif";
  ctrl.src = imgfile;
  ctrl.disabled = false;
  if (ctrl.getAttribute("spaw_state") == true)
  {
    ctrl.className = "SPAW_classic_tb_down";
  }
  else
  {
    ctrl.className = "SPAW_classic_tb_out";
  }
}
function SPAW_classic_bt_down(ctrl)
{
  ctrl.className = "SPAW_classic_tb_down";
}
function SPAW_classic_bt_up(ctrl)
{
  ctrl.className = "SPAW_classic_tb_out";
}
function SPAW_classic_bt_off(ctrl)
{
  var imgfile = SPAW_base_image_name(ctrl)+"_off.gif";
  ctrl.src = imgfile;
  ctrl.disabled = true;
}

