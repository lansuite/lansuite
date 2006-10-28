<?
$res = $db->query("SELECT * FROM {$config['tables']['party_prices']} WHERE party_id = ". (int)$party->party_id);
while ($row = $db->fetch_array($res)) {
  echo $row['price_text'] .' / '. $row['price'] .' '. $cfg['sys_currency'] .'%'. $row['price_id'] ."\r";
}
$db->free_result($res);
?>
