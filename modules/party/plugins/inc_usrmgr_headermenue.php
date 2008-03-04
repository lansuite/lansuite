<?
// This File is a Part of the LS-Pluginsystem. It will be included in
// modules/usrmgr/details.php to generate Modulspezific Headermenue 
// for Userdetails

// ADD HERE MODULSPECIFIC INCLUDES

// ADD HERE MODULPUGINCODE
if ($auth['type'] >= 1) {
   function p_date($mysql_date) {
       if ($mysql_date > 0) {
           return func::unixstamp2date(func::MysqlDateToTimestamp($mysql_date),'datetime');
       } else {
           return "-";
       }
   }
   function p_onparty($user_id) {
       if ($user_id){
           return "ja";
       } else {
           return "nein";
       }
   }
   function p_paidstatus($paid) {
       switch ($paid) {
           case 0: return t("Nicht Bezahlt"); break;
           case 1: return t("Vorkasse"); break;
           case 2: return t("Abendkasse"); break;
       }
   }
 
   function p_getactive($name) {
       global $cfg, $db;
       $row = $db->qry_first("SELECT name, party_id FROM %prefix%partys WHERE name = %string%", $name);
       if ($cfg['signon_partyid'] == $row['party_id']) return "<b>".$name." (aktiv)</b>";
       else return $name;
   }

      include_once('modules/mastersearch2/class_mastersearch2.php');
      $ms2 = new mastersearch2('usrmgr');

      $ms2->query['from'] = "{$config["tables"]["partys"]} p LEFT JOIN {$config["tables"]["party_user"]} u ON p.party_id = u.party_id AND u.user_id = ". (int)$_GET['userid'];
      $ms2->query['where'] = "u.user_id = ". (int)$_GET['userid'] . " OR u.user_id is NULL";

      $ms2->config['EntriesPerPage'] = 50;

      $ms2->AddResultField(t('Party'), 'p.name', 'p_getactive');
      $ms2->AddResultField(t('Angemeldet'), 'u.user_id', 'p_onparty');
      $ms2->AddResultField(t('Bezahlt'), 'u.paid', 'p_paidstatus');
      $ms2->AddResultField(t('Bezahltdatum'), 'u.paiddate', 'p_date');
      $ms2->AddResultField(t('Eingecheckt'), 'u.checkin', 'p_date');
      $ms2->AddResultField(t('Ausgecheckt'), 'u.checkout', 'p_date');
      if ($auth['type'] >= 2) $ms2->AddIconField('edit', 'index.php?mod=party&action=change_status&user_id='. $_GET['userid'] .'&party_id=', t('Editieren'));

      $ms2->PrintSearch('index.php?mod=usrmgr&action=details&userid='. $_GET['userid'] .'&headermenuitem=6', 'p.party_id');
}
?>