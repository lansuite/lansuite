<?php
/*
 * Created on 22.03.2009
 * 
 * 
 * 
 * @package package_name
 * @author Maztah
 * 
 */
include_once("modules/cashmgr/class_accounting.php");

function getColor() {
	global $line;
	
	if($line['my']) return "<font color='red'>-".number_format($line['movement'], 2, ',', '.') . " EUR</font>";
	if(!$line['my']) return "<font color='green'>+".number_format($line['movement'], 2, ',', '.') . " EUR</font>";
}

if($_GET['act'] == "him" and $auth['type'] < 3) $func->error("ACCESS_DENIED");
elseif($_GET['act'] == "him" and $auth['type'] = 3)
{
	switch ($_GET['step']){
	default:
	$current_url = 'index.php?mod=cashmgr&action=myaccounting&act=him';
	$target_url = 'index.php?mod=cashmgr&action=myaccounting&act=him&step=2&userid=';
	include_once('modules/usrmgr/search_basic_userselect.inc.php');
	break;
	case 2:
	$userid = $_GET['userid'];
	break;
	}
}
if(!$_GET['act'] or ($_GET['act'] and $_GET['step'] == 2))
{
if($userid == null) $userid = $auth['userid'];
	
$dsp->NewContent(t('Buchhaltung'), t('Übersicht aller meiner Ein- und Ausgaben'));

include_once('modules/mastersearch2/class_mastersearch2.php');
$ms2 = new mastersearch2('accounting');

$ms2->query['from'] = "%prefix%cashmgr_accounting AS a
                            LEFT JOIN %prefix%user AS fu ON a.fromUserid = fu.userid
                            LEFT JOIN %prefix%user AS tu ON a.toUserid = tu.userid";
$ms2->query['default_order_by'] = 'actiontime DESC';
$ms2->query['where'] = "a.toUserid = {$userid} OR a.fromUserid = {$userid}";
$ms2->config['EntriesPerPage'] = 20;
    
$party_list = array('' => 'Alle');
$row = $db->qry("SELECT party_id, name FROM %prefix%partys");
while($res = $db->fetch_array($row)) $party_list[$res['party_id']] = $res['name'];
$db->free_result($row);

$ms2->AddTextSearchDropDown('Party', 'a.partyid', $party_list, $party->party_id);
$ms2->AddTextSearchDropDown('Zahlungsart', 'a.cash', array('' => 'Alle', 0 => 'Nur Online','1' => 'Nur Bar'));

$ms2->AddResultField(t('Datum'), 'a.actiontime', 'MS2GetDate');
$ms2->AddResultField(t('Modul'), 'a.modul');
$ms2->AddResultField(t('Kommentar'), 'a.comment');
$ms2->AddSelect('a.fromUserid');
$ms2->AddSelect("IF(a.fromUserid = {$userid},'1','0') AS my");
$ms2->AddResultField(t('Bearbeiter'), 'fu.username', 'UserNameAndIcon');
$ms2->AddResultField(t('Betrag'), 'a.movement', 'getColor');

$ms2->PrintSearch('index.php?mod=cashmgr&action=myaccounting', 'a.id');
}
?>
