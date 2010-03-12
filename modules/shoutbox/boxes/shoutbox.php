<?php
/**
 * Ajax Shoutbox
 *
 * @package lansuite_core
 * @author maztah
 * @version $Id$
 * @todo Show picture without Comments
 */
 $framework->add_js_path("ext_scripts/jquery-plugins/jquery.form.js");
$box->ItemRow('data',$smarty->fetch('modules/shoutbox/templates/box-template.htm'));
?>