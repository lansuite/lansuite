<?php

/**
 * @param int $userid
 * @return array
 */
function GetUserInfo($userid)
{
    global $db, $cfg, $func;

    $row_poster = $db->qry_first("SELECT username, type, avatar_path, signature FROM %prefix%user WHERE userid=%int%", $userid);
    $count_rows = $db->qry_first("SELECT COUNT(*) AS posts FROM %prefix%board_posts WHERE userid = %int%", $userid);

    $html_image= '<img src="%s" alt="%s" border="0">';

    $user["username"]  = $row_poster["username"];
    $user["avatar"]    = ($func->chk_img_path($row_poster["avatar_path"])) ? sprintf($html_image, $row_poster["avatar_path"], "") : "";
    $user["signature"] = $row_poster["signature"];

    if ($cfg['board_ranking'] == true) {
        $user["rank"] = GetBoardRank($count_rows["posts"]);
    }
    $user["posts"] = $count_rows["posts"];

    switch ($row_poster["type"]) {
        case 1:
            $user["type"] = t('Benutzer');
            break;
        case 2:
            $user["type"] = t('Organisator');
            break;
        case 3:
            $user["type"] = t('Superadmin');
            break;
    }

    return $user;
}
