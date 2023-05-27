<?php

/**
 * @return void
 */
function WriteMenuEntries()
{
    global $smarty, $res, $db, $dsp, $MenuCallbacks;

    if ($db->num_rows($res) == 0) {
        $dsp->AddDoubleRow("", "<i>- keine -</i>");
    } else {
        while ($row = $db->fetch_array($res)) {
            $smarty->assign('action', $row["action"]);
            $smarty->assign('file', $row["file"]);
            $smarty->assign('id', $row["id"]);
            $smarty->assign('caption', $row["caption"]);
            $smarty->assign('hint', $row["hint"]);
            $smarty->assign('link', $row["link"]);
            $smarty->assign('pos', $row["pos"]);
            $smarty->assign('module', $_GET['module']);

            $boxid = '';
            if ($row['level'] == 0) {
                $boxid = 'Boxid: <input type="text" name="boxid['.$row['id'].']" value="'. $row['boxid'] .'" size="2" />';
            }
            $smarty->assign('boxid', $boxid);

            $needed_config = "<option value=\"\">-".t('keine')."-</option>";
            $res2 = $db->qry("SELECT cfg_key FROM %prefix%config WHERE cfg_type = 'boolean' OR cfg_type = 'int' ORDER BY cfg_key");
            if ($MenuCallbacks) {
                foreach ($MenuCallbacks as $MenuCallback) {
                    ($MenuCallback == $row["needed_config"])? $selected = " selected" : $selected = "";
                    $needed_config .= "<option value=\"{$MenuCallback}\"$selected>{$MenuCallback}</option>";
                }
            }
            $db->free_result($res2);
            $smarty->assign('needed_config', $needed_config);

            $requirement = "";
            for ($i = 0; $i <= 5; $i++) {
                ($i == $row["requirement"])? $selected = " selected" : $selected = "";
                $out = match ($i) {
                    1 => t('Nur Eingeloggte'),
                    2 => t('Nur Admins'),
                    3 => t('Nur Superadminen'),
                    4 => t('Keine Admins'),
                    5 => t('Nur Ausgeloggte'),
                    default => t('Jeder'),
                };
                $requirement .= "<option value=\"$i\"$selected>$out</option>";
            }
            $smarty->assign('requirement', $requirement);

            $dsp->AddSmartyTpl('menuitem', 'install');
            $dsp->AddHRuleRow();
        }
    }
    $db->free_result($res);
}
