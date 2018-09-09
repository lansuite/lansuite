<?php

if ($auth['login'] == 1) {
    // Checkout infos
    $halfanhour = date("U") - 30*60;

    $query = $db->qry("
      SELECT
        userid,
        text,
        priority,
        date
      FROM %prefix%infobox
      WHERE
        userid = %int%
        AND date > %int%
      ORDER BY
        priority DESC,
        date DESC
      LIMIT 0,3", $_SESSION["auth"]["userid"], $halfanhour);

    while ($row=$db->fetch_array()) {
        if ($row["priority"] == "1") {
            $class = "row_value";
        } elseif ($row["priority"] == "2") {
            $class = "row_value_highlighted";
        } elseif ($row["priority"] == "3") {
            $class = "row_value_important";
        }

        $box->EngangedRow("<i>" . $func->unixstamp2date($row["date"], "datetime") . "</i>", "", "", $class);
        $box->EngangedRow("<i>" . $row["text"], "", "", $class);
    }

    if ($db->num_rows() < "1") {
        $box->EngangedRow("<i>". t('Keine Einträge in den '). HTML_NEWLINE .t('letzten 30 Minuten'). "</i>", "", "");
    }
}
