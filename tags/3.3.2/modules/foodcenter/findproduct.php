<?php
include_once("modules/foodcenter/class_product.php");
include_once('modules/mastersearch2/class_mastersearch2.php');
  
function GetTitelName($id) {
	global $db, $config, $auth, $lang;

	$data = $db->query_first("SELECT caption, p_desc, cat_id FROM {$config['tables']['food_product']} WHERE id = $id");
	
	
	$return = "";
	$return .= "<a href='index.php?mod=foodcenter&headermenuitem=".$data[cat_id]."&info=".$id."'><b>".$data[caption]."</b>";
	$return .= " <br />".$data[p_desc]."</a>";

	return $return;
}

	


$product_list = new product_list();

$dsp->NewContent(t('Produktsuche'), t('Hier findest du alles was das Herz begehrt'));

$ms2 = new mastersearch2();

$ms2->query['from'] = "{$config['tables']['food_product']} AS p
			LEFT JOIN {$config['tables']['food_option']} AS o ON o.parentid = p.id";

	$cat_list = array('' => 'Alle');
	$row = $db->query("SELECT * FROM {$config['tables']['food_cat']}");
	while($res = $db->fetch_array($row)) $cat_list[$res['cat_id']] = $res['name'];
	$db->free_result($row);
	
    $ms2->AddTextSearchDropDown('Produktkategorie', 'p.cat_id', $cat_list);
    $ms2->AddTextSearchField('Produktsuche', array('p.caption' => 'like', 'p.p_desc' => 'like'));
    
	$ms2->AddSelect('p.cat_id');
	$ms2->AddResultField('Titel', 'p.id', 'GetTitelName');

	//$ms2->AddIconField('basket', 'index.php?mod=foodcenter&headermenuitem='=', $lang['ms2']['details']);

switch ($_POST['search_dd_input'][0]){
   		case 1:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_offer'];
    	break;

    	case 2:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_stop'];
    	break;

    	case 3:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_supplied'];
    	break;

    	case 4:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_kitchen'];
    	break;
    	
     	case 5:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_wait'];
    	break;  
    	
    	default:
    		$ms2->NoItemsText = $lang['foodcenter']['ordered_no_offer'];
    	break;  

}

$ms2->PrintSearch('index.php?mod=foodcenter&action=findproduct', 'p.id');
?>