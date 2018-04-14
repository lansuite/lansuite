<?php
$get_cat = $db->qry("SELECT catid, name FROM %prefix%faq_cat ORDER BY name");
$count_cat = $db->num_rows($get_cat);

if ($count_cat == 0) {
    $func->information(t('Keine Einträge vorhanden.'), "index.php?mod=home");
} else {
    $dsp->NewContent(t('FAQ'), t('Auf dieser Seite siehst du häufig gestellte Fragen und deren Antworten'));

    while ($row = $db->fetch_array($get_cat)) {
        if ($auth['type'] > 2) {
            $admin_link = $dsp->FetchIcon('delete', 'index.php?mod=faq&object=item&action=delete_cat&catid=' . $row["catid"] . '&step=2');
        }
        if ($auth['type'] > 1) {
            $admin_link .= $dsp->FetchIcon('edit', 'index.php?mod=faq&object=cat&action=change_cat&catid=' . $row["catid"] . '&step=2');
        }
        $dsp->AddFieldsetStart($admin_link . $row["name"]);

        $get_item = $db->qry("SELECT caption,itemid FROM %prefix%faq_item WHERE catid = %int% ORDER BY caption", $row['catid']);
        while ($row = $db->fetch_array($get_item)) {
            if ($auth['type'] > 2) {
                $admin_link = $dsp->FetchIcon('delete', 'index.php?mod=faq&object=item&action=delete_item&itemid=' . $row["itemid"] . '&step=2');
            }
            if ($auth['type'] > 1) {
                $admin_link .= $dsp->FetchIcon('edit', 'index.php?mod=faq&object=cat&action=change_item&itemid=' . $row["itemid"] . '&step=2');
            }
            $dsp->AddSingleRow($admin_link . $dsp->FetchLink($func->text2html($row["caption"]), 'index.php?mod=faq&action=comment&itemid='. $row["itemid"]));
        }
        $dsp->AddFieldsetEnd();
    }
    $dsp->AddSingleRow($faq_content, "class='menu'");
}
