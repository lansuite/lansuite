<?php //07.02.2005 00:30 - raphael@one-network.org

$action = $_GET['action'];

switch($action) {
    case "exportinfo":
        $templ['stats_data'] = $stats->getExportData();
        if($templ['stats_data']['start'] == 0 )     $templ['stats_data']['date'] = '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';
        else                        $templ['stats_data']['date'] = strftime("%d.%m.%Y",$templ['stats_data']['start']); 

        $templ['stats_data']['date'] .= ' - ';
                                                
        if($templ['stats_data']['end'] == 0)        $templ['stats_data']['date'] .= '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';
        else                        $templ['stats_data']['date'] .= strftime("%d.%m.%Y",$templ['stats_data']['end']);  
        
        if($templ['stats_data']['name'] == 'LanParty with LANsuite')        $templ['stats_data']['name'] = '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';
        if($templ['stats_data']['url'] == 'http://www.one-network.org')     $templ['stats_data']['url'] = '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';
        
        if($templ['stats_data']['plz'] == 0)    $templ['stats_data']['plz'] = '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';
        if($templ['stats_data']['mail'] == '')  $templ['stats_data']['mail'] = '<font color="red"><i>'.t('nicht eingetragen').'</i></font>';

    $gd->CreateButton('send');
        $dsp->AddSingleRow($dsp->FetchModTpl('misc', 'stats_export'));
        $dsp->AddContent();
    break;

    default:
        $stats->export();
        $func->confirmation(t('Die Daten wurden erfolgreich &uuml;bermittelt'), "");    
    break;
}       
?>