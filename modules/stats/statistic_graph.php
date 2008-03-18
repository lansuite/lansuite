<?php

/* LANsuite v2
 *
 * Network Operations Centre
 *
 * Module: 		Da Network Traffic Statistics Graph
 *
 * Amount of Work: 	DAMN this was 72 Hours of programming at least.
 *
 * Comment: 		Don't panic if you don't get the sense of this piece of crap... uh I mean code.
 *			It was written with a big lack of sleep...
 *
 * Main Author: 	Joachim Garth <josch@one-network.org>
 * Sub Author :		Marco Müller <marco@chuchi.tv> for Statistic Modul
 */

 // SOME VARIABLES:
 //
 // PTQ = Percental Traffic Quotient - Is used but has another name at the moment
 // TIQ = Time Index Quotient - Not Used yet

 // Check GD Libary
 	if ( $config['environment']['gd'] == "0" ) $func->error( t('Die Statistikanzeige ben&ouml;tigt ddie GD Bibliothek'), "" );

	// Create an Image Stream
	$Image = @ImageCreate( 611, 480 )
	or die( $func->error( t('Konnte kein Bild erzeugen') ) );

	// Declare "White"
	$white = ImageColorAllocate ( $Image, 255, 255, 255 );

	// Background
	ImageFill( $Image, 0, 0, $white );

	// Declare "Black"
	$black = ImageColorAllocate( $Image, 0, 0, 0 );

	// X and Y Axis ( Border )
	ImageLine( $Image, 60, 0, 60, 460, $black );
	ImageLine( $Image, 60, 460, 610, 460, $black );

	$red = ImageColorAllocate( $Image, 240, 0, 0 );

	// Create the Lines
	$db->query( "SELECT time FROM {$config['tables']['stats_localserver']}" );
	$rows = $db->num_rows( );
	
  (($rows - 11) < 0)? $st = 0 : $st = $rows - 11;
	$db->query( "SELECT time, {$_GET['act']} FROM {$config['tables']['stats_localserver']} ORDER BY time ASC LIMIT ". $st ."," . ($rows + 1) );

	$i = 0;

	// Get the statistics from the DB
	while( $row = $db->fetch_array( ) ) {

		$value[$i]['time'] = $row["time"];
		$value[$i][$_GET['act']] = $row[$_GET['act']];
		$i++;

	}

	// We need more than 10 Values to continue...
	If( count( $value ) < 10 ) {

		If( !is_array( $value ) ) {

			$msg = t('Keine Daten vorhanden. Sie m&uuml;ssen das Modulsuitebeat aktivieren und den Cron-Job einrichten');

		} else {

			$msg = t('Zu wenige Daten f&uuml;r ein Skript / Stellen sie die Laufzeit des Cron-Jobs auf 1min und warten sie 15min');

		}

		ImageString( $Image, 3, 50, 200, $msg, $black );
		Header( "Content-type: image/png" );
		ImagePNG( $Image );
		die( );

	} // END If( !is_array( $value ) )

	// Create a temporary clone of $value to sort and find the entry with the highest relative traffic
	$tmp = $value;

	arsort( $tmp );
	reset( $tmp );

	// Calculate the Highest Traffic Difference
	$current = current( $tmp );

	$highestpoint = $current[$_GET['act']];
	
	if($highestpoint == 0) $highestpoint =0.1;
	switch ($_GET['act']){
		case 'eth_tx_mbytes' || 'eth_rx_mbytes':
			// hrtinmbpm = highestrelativetrafficINMegaBytePerMinute
			$hrtinmbpm = $highestpoint;
		break;
		case 'mem_free' || 'swap_free':
			$hrtinmbpm = $highestpoint;
		break;
		case 'loadavg':
			$hrtinmbpm = $highestpoint;
		break;
	}
	
	
	If( $hrtinmbpm < 10 ) {

		$hrtinmbpm = round( $hrtinmbpm, 1 );

	} else {

		$hrtinmbpm = round( $hrtinmbpm, 0 );

	}

	// Caption
	switch ($_GET['act']){
		case 'eth_tx_mbytes':
		case 'eth_rx_mbytes':
			ImageString( $Image, 1, 0, 0, $hrtinmbpm." MB/min", $red );
			ImageString( $Image, 1, 0, 112, ( $hrtinmbpm * 0.75 )." MB/min", $red );
			ImageString( $Image, 1, 0, 225, ( $hrtinmbpm * 0.50 )." MB/min", $red );
			ImageString( $Image, 1, 0, 337, ( $hrtinmbpm * 0.25 )." MB/min", $red );
			ImageString( $Image, 1, 0, 450, "0 MB/min", $red );
		break;
		case  'mem_free':
		case  'swap_free':
			ImageString( $Image, 1, 0, 0, $hrtinmbpm." MB", $red );
			ImageString( $Image, 1, 0, 112, ( $hrtinmbpm * 0.75 )." MB", $red );
			ImageString( $Image, 1, 0, 225, ( $hrtinmbpm * 0.50 )." MB", $red );
			ImageString( $Image, 1, 0, 337, ( $hrtinmbpm * 0.25 )." MB", $red );
			ImageString( $Image, 1, 0, 450, "0 MB", $red );
		break;
		case ('loadavg'):
			ImageString( $Image, 1, 0, 0, $hrtinmbpm." %", $red );
			ImageString( $Image, 1, 0, 112, ( $hrtinmbpm * 0.75 )." %", $red );
			ImageString( $Image, 1, 0, 225, ( $hrtinmbpm * 0.50 )." %", $red );
			ImageString( $Image, 1, 0, 337, ( $hrtinmbpm * 0.25 )." %", $red );
			ImageString( $Image, 1, 0, 450, "0 %", $red );
		break;
	}		
			
	
	$firstvalue = current( $value );

	// Initialise Loop with values from the first entry
	$lastvalue['endx'] = 60;
	$lastvalue['endy'] = 225 + ( 225 - ( $firstvalue[$_GET['act']] / ( $highestpoint / 100 ) ) * 4.5 );

	next( $value );

	$blue = ImageColorAllocate( $Image, 0, 0, 240 );

	while( $currentvalue = current( $value )) {

		$endx = $lastvalue['endx'] + 55;
		$endy = ( $currentvalue[$_GET['act']] / ( $highestpoint / 100 ) ) * 4.5;

		$endy = 225 + ( 225 - $endy );

		ImageLine( $Image, $endx, $endy - 25, $endx, 0, $blue );
		ImageLine( $Image, $endx, $endy + 25, $endx, 460, $blue );

		$time = $func->unixstamp2date( $currentvalue['time'], "shorttime" );
		$date = $func->unixstamp2date( $currentvalue['time'], "date" );

		ImageString( $Image, 1, $lastvalue['endx'] + 16, 462, $time, $red );
		ImageString( $Image, 1, $lastvalue['endx'] + 2, 472, $date, $black );

		ImageLine( $Image, $lastvalue['endx'], $lastvalue['endy'], $endx, $endy, $red );

		$lastvalue['endx'] = $endx;
		$lastvalue['endy'] = $endy;

		next( $value );

	}

	// Mark this PHP as PNG Image
	Header( "Content-type: image/png" );
	ImagePNG ($Image);


?>