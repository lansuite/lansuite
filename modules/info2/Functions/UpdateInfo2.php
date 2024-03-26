<?php

/**
 * @param int $id
 * @return bool
 */
function UpdateInfo2($id)
{
    global $db, $database, $cfg;

    if ($id != '') {
        $menu_intem = $database->queryWithOnlyFirstRow('SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = ?', [$id]);

        if ($menu_intem['active']) {
            ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

            $linkParameter = $_POST['link'] ?? '';
            if ($linkParameter) {
                $link = $linkParameter .'" target="_blank';
            } else {
                $link = 'index.php?mod=info2&action=show_info2&id='. $id;
            }

            $database->query("
                UPDATE %prefix%menu
                SET
                    module = 'info2',
                    caption = ?,
                    hint = ?,
                    level = ?,
                    link = ?
                WHERE
                    link = ?",
                [$_POST["caption"], $_POST["shorttext"], $level, $link, $link]
            );
        }
    }

    return true;
}
