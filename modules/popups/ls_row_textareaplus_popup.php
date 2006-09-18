<script language="JavaScript" type="text/javascript">
<!--
function code(sign) {
<? echo "opener.document.{$_GET['form']}.{$_GET['textarea']}.value += sign;" ?>
}
//-->
</script>

<div class="row_value">
  <b>Zeichen anklicken, um es ins Textfeld einzufügen</b>
</div>
<div class="row_value">
<?
$templ['ls']['row']['textarea']['smilies'] = "";
$smilie = $db->query("SELECT shortcut, image FROM {$config["tables"]["smilies"]}");

$z = 0;
while($smilies = $db->fetch_array($smilie)) if (file_exists('ext_inc/smilies/'. $smilies['image'])) {
  echo '<a href="#" onclick="javascript:code(\''. $smilies['shortcut'] .'\'); return false">
    <img src="ext_inc/smilies/'. $smilies['image'] .'" border="0" alt="'. $smilies['image'] .'" />
  </a>';
  $z++;
  if ($z % 12 == 0) echo "<br />";
}
?>
</div>