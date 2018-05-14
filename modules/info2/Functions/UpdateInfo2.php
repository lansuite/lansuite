<?php

/**
 * @param int $id
 * @return bool
 */
function UpdateInfo2($id)
{
    global $db, $cfg;

    if ($id != '') {
        $menu_intem = $db->qry_first('SELECT active, caption, shorttext FROM %prefix%info WHERE infoID = %int%', $id);

        if ($menu_intem['active']) {
            ($cfg['info2_use_submenus'])? $level = 1 : $level = 0;

            if ($_POST['link']) {
                $link = $_POST['link'] .'" target="_blank';
            } else {
                $link = 'index.php?mod=info2&action=show_info2&id='. $id;
            }

            $db->qry(
                "UPDATE %prefix%menu
        SET module = 'info2',
                caption = %string%,
                hint = %string%,
                level = %int%,
                link = %string%
                WHERE link = %string%",
                $_POST["caption"],
                $_POST["shorttext"],
                $level,
                $link,
                $link
            );
        }
    }

    return true;
}
