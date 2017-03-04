<?php
$allTlds = explode("\n",file_get_contents("http://data.iana.org/TLD/tlds-alpha-by-domain.txt"));

$DbInsertQuery = "";
foreach ($allTlds as $key => $value) {
    if (strpos($value, "#") === false && strlen(trim($value)) > 0) $DbInsertQuery .= "('" . strtolower($value) . "')," ;
}
$DbInsertQuery = rtrim($DbInsertQuery, ",");
$db->qry("delete from %prefix%%plain%", "tlds");
$db->qry("INSERT INTO %prefix%%plain% (tld) VALUES %plain%", "tlds", $DbInsertQuery);
?>