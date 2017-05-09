<?php
include_once("modules/foodcenter/class_product.php");
include_once('modules/mastersearch2/class_mastersearch2.php');
  
function GetTitelName($id)
{
    global $db, $auth, $lang;

    $data = $db->qry_first("SELECT caption, p_desc, cat_id FROM %prefix%food_product WHERE id = %int%", $id);
    
    
    $return = "";
    $return .= "<a href='index.php?mod=foodcenter&headermenuitem=".$data[cat_id]."&info=".$id."'><b>".$data[caption]."</b>";
    $return .= " <br />".$data[p_desc]."</a>";

    return $return;
}

    


$product_list = new product_list();

$dsp->NewContent(t('Produktsuche'), t('Hier findest du alles was das Herz begehrt'));

$ms2 = new mastersearch2();

$ms2->query['from'] = "%prefix%food_product AS p
			LEFT JOIN %prefix%food_option AS o ON o.parentid = p.id";

    $cat_list = array('' => 'Alle');
    $row = $db->qry("SELECT * FROM %prefix%food_cat");
while ($res = $db->fetch_array($row)) {
    $cat_list[$res['cat_id']] = $res['name'];
}
    $db->free_result($row);
    
    $ms2->AddTextSearchDropDown('Produktkategorie', 'p.cat_id', $cat_list);
    $ms2->AddTextSearchField('Produktsuche', array('p.caption' => 'like', 'p.p_desc' => 'like'));
    
    $ms2->AddSelect('p.cat_id');
    $ms2->AddResultField('Titel', 'p.id', 'GetTitelName');

    //$ms2->AddIconField('basket', 'index.php?mod=foodcenter&headermenuitem='=', t('Details'));

switch ($_POST['search_dd_input'][0]) {
    case 1:
        $ms2->NoItemsText = t('Keine aktuellen Bestellungen vorhanden.');
        break;

    case 2:
        $ms2->NoItemsText = t('Es müssen keine Produkte bestellt werden.');
        break;

    case 3:
        $ms2->NoItemsText = t('Es wird auf keine Lieferung gewartet.');
        break;

    case 4:
        $ms2->NoItemsText = t('Derzeit gibt es keine fertiggestellten Gerichte aus der Küche.');
        break;
        
    case 5:
        $ms2->NoItemsText = t('Du hast alle Produkte abgeholt.');
        break;
        
    default:
        $ms2->NoItemsText = t('Keine aktuellen Bestellungen vorhanden.');
        break;
}

$ms2->PrintSearch('index.php?mod=foodcenter&action=findproduct', 'p.id');
