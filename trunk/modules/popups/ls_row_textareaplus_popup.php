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

<div class="row_value">
<?
echo $dsp->FetchButton("javascript:code('[b][/b]')", "bold");
echo $dsp->FetchButton("javascript:code('[i][/i]')", "kursiv");
echo $dsp->FetchButton("javascript:code('[u][/u]')", "underline");
echo $dsp->FetchButton("javascript:code('[c][/c]')", "code");
echo $dsp->FetchButton("javascript:code('[img][/img]')", "picture");
?>
</div>

<div class="row_value"> Schriftfarbe:
	<select name="color" onChange="javascript:code('[color=' + this.options[this.selectedIndex].value + '][/color]')" class="form">
  	<option style="color:black;" value="black" >Standard</option>
  	<option style="color:darkred;" value="darkred" >Dunkelrot</option>
  	<option style="color:red;" value="red" >Rot</option>
  	<option style="color:orange;" value="orange" >Orange</option>
  	<option style="color:brown;" value="brown" >Braun</option>
  	<option style="color:yellow;" value="yellow" >Gelb</option>
  	<option style="color:green;" value="green" >Grün</option>
  	<option style="color:olive;" value="olive" >Oliv</option>
  	<option style="color:cyan;" value="cyan" >Cyan</option>
  	<option style="color:blue;" value="blue" >Blau</option>
  	<option style="color:darkblue;" value="darkblue" >Dunkelblau</option>
  	<option style="color:indigo;" value="indigo" >Indigo</option>
  	<option style="color:violet;" value="violet" >Violett</option>
  	<option style="color:black;" value="black" >Schwarz</option>
	</select>

	Schriftgr&ouml;&szlig;e: 
	<select name="color" onChange="javascript:code('[size=' + this.options[this.selectedIndex].value + '][/size]')" class="form">
  	<option value="12" selected>Standard</option>
  	<option value="7"  >Winzig</option>
  	<option value="9"  >Klein</option>
  	<option value="12" >Normal</option>
  	<option value="18" >Groß</option>
  	<option value="24" >Riesig</option>
	</select>
</div>