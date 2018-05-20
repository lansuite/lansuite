<?php

$dsp->NewContent(t('Benutzeralter'), '');

$res = $db->qry('
  SELECT
    birthday,
    DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), \'%Y\') + 0 AS age,
    COUNT(*) AS anz
  FROM %prefix%user
  GROUP BY age
  ORDER BY age');

while ($row = $db->fetch_array($res)) {
    if ($row['birthday'] == '0000-00-00') {
        $dsp->AddDoubleRow(t('Keine Angabe'), $row['anz']);
    } else {
        $dsp->AddDoubleRow($row['age'], $row['anz']);
    }
}
$db->free_result($res);
