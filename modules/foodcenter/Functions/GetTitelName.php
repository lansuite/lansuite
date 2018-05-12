<?php

/**
 * Used as callback function for mastersearch
 *
 * @param int $id
 * @return string
 */
function GetTitelName($id)
{
    global $db;

    $data = $db->qry_first("SELECT caption, p_desc, cat_id FROM %prefix%food_product WHERE id = %int%", $id);

    $return = "";
    $return .= "<a href='index.php?mod=foodcenter&headermenuitem=".$data['cat_id']."&info=".$id."'><b>".$data['caption']."</b>";
    $return .= " <br />".$data['p_desc']."</a>";

    return $return;
}
