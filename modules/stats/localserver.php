<?php

$dsp->NewContent(t('Server Statistik'), t('Hier sehen Sie die aktuelle Auslastung des Servers auf dem Lansuite gerade ausgeführt wird'));

($config['environment']['os'])? $os = $config['environment']['os'] : $os = t('Unbekannt');
$dsp->AddDoubleRow(t('Serverinfo'), $os);

if ($config["server_stats"]["status"] == "1"){
	// Unix
	if($config["server_stats"]["uptime"] == "1"){
		$uptime = $stats->uptime();
		$uptime_string = floor($uptime/86400) .  " " . t('Tag(e)') . " "  .  floor(($uptime%86400)/3600) .  " " . t('Stunde(n)') . " "  . floor((($uptime%86400)%3600)/60) .  " " . t('Minute(n)') . " "  . floor((($uptime%86400)%3600)%60) .  " " . t('Sekunde(n)');
		$dsp->AddDoubleRow(t('L&auml;uft seit'), $uptime_string);
	}
	if($config["server_stats"]["loadavg"] == "1"){
		$loadavg = $stats->load_avg();
		$load_avg_string = $loadavg[0] . " (1min) /" . $loadavg[1] . " (5min) /" . $loadavg[2] . " (15min)";
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=loadavg','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Load Avarage') . "</a>", $load_avg_string);
	}
	if($config["server_stats"]["cpuinfo"] == "1"){
		$cpuinfo = $stats->cpu_info();
		$dsp->AddDoubleRow(t('Prozessor(en)'), eregi_replace("model name :","",$cpuinfo['cpu_info']));
	}
	if($config["server_stats"]["meminfo"] == "1"){
		$meminfo = $stats->mem_info();
		$dsp->AddDoubleRow(t('Arbeitsspeicher gesamt'), $meminfo["mem_total"] . " MB");
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=mem_free','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Arbeitsspeicher frei') . "</a>", $meminfo["mem_free"] . " MB");
		$dsp->AddDoubleRow(t('Swapspeicher gesamt'), $meminfo["swap_total"] . " MB");
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=swap_free','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Swapspeicher frei') . "</a>", $meminfo["swap_free"] . " MB");
	}
	if($config["server_stats"]["ifconfig"] == "1"){
		$transver_byte = $stats->ifconfig();
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=eth_tx_mbytes','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Bis jetzt gesendet (TX)') . "</a>", $transver_byte["TX"]);
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=eth_rx_mbytes','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Bis jetzt empfangen (RX)') . "</a>", $transver_byte["RX"]);
	}

  // Win
	if($config["server_stats"]["ls_getinfo"] == "1"){
		system("modules/stats/ls_getinfo.exe");
		include("modules/stats/sysinfo.php");
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=loadavg','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Load Avarage') . "</a>", $sysinfo_cpuusage);
		$dsp->AddDoubleRow(t('Arbeitsspeicher gesamt'), $sysinfo_ramtotal / (1024 * 1024) . " MB");
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=mem_free','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Arbeitsspeicher frei') . "</a>", $sysinfo_ramfree / (1024 * 1024) . " MB");
		$dsp->AddDoubleRow(t('Swapspeicher gesamt'), $sysinfo_virtualtotal / (1024 * 1024) . " MB");
		$dsp->AddDoubleRow("<a href=\"javascript:var w=window.open('index.php?mod=stats&action=statistic_graph&design=base&act=swap_free','_blank','width=640,height=520,resizable=no,scrollbars=no')\">" . t('Swapspeicher frei') . "</a>", $sysinfo_virtualtotal  / (1024 * 1024) . " MB");
	}
	
} else $dsp->AddSingleRow(t('Leider ist diese Anzeige auf Ihrem System nicht möglich'));

$dsp->AddBackButton("index.php?mod=stats", "stats/server");
$dsp->AddContent();

?>