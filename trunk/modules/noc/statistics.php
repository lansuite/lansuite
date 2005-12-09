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
 *
 */

 // SOME VARIABLES:
 //
 // PTQ = Percental Traffic Quotient - Is used but has another name at the moment
 // TIQ = Time Index Quotient - Not Used yet

if ( $config['environment']['gd'] == "0" ) $func->error( "Der Graph ben�tigt die GD um dargestellt zu werden", "" );

// Create an Image Stream
$Image = @ImageCreate( 611, 480 )
	or die( $func->error( "Konnte GD nicht initialisieren" ) );

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
$db->query( "SELECT time FROM {$config['tables']['noc_statistics']}" );
$rows = $db->num_rows( );
$rows -= 12;

$db->query( "SELECT time, transferedbytes FROM {$config['tables']['noc_statistics']} ORDER BY time ASC LIMIT $rows,-1" );

$i = 0;

// Get the statistics from the DB
while( $row = $db->fetch_array( ) ) {

	If( $i != 0 ) {

		$value[$i]['reltraffic'] = $row["transferedbytes"] - $lasttraffic;
		$value[$i]['time'] = $row["time"];

	}

	$lasttraffic = $row["transferedbytes"];

	$i++;

}

// We need more than 10 Values to continue...
If( count( $value ) < 10 ) {

	If( !is_array( $value ) ) {

		$msg = "Es sind keine Daten vorhanden.";

	} else {

		$msg = "Es sind noch zu wenig Daten vorhanden.";

	}

	ImageString( $Image, 3, 200, 200, $msg, $black );
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
$highestreltraffic = $current['reltraffic'];

if( $highestreltraffic == 0){
	$highestreltraffic = 0.1;
}
// hrtinmbpm = highestrelativetrafficINMegaBytePerMinute
$hrtinmbpm = $highestreltraffic / 20 / ( 1024 * 1024 );

If( $hrtinmbpm < 10 ) {

	$hrtinmbpm = round( $hrtinmbpm, 1 );

} else {

	$hrtinmbpm = round( $hrtinmbpm, 0 );

}

// Caption
ImageString( $Image, 1, 0, 0, $hrtinmbpm." MB/min", $red );
ImageString( $Image, 1, 0, 112, ( $hrtinmbpm * 0.75 )." MB/min", $red );
ImageString( $Image, 1, 0, 225, ( $hrtinmbpm * 0.50 )." MB/min", $red );
ImageString( $Image, 1, 0, 337, ( $hrtinmbpm * 0.25 )." MB/min", $red );
ImageString( $Image, 1, 0, 450, "0 MB/min", $red );

$firstvalue = current( $value );

// Initialise Loop with values from the first entry
$lastvalue['endx'] = 60;
$lastvalue['endy'] = 225 + ( 225 - ( $firstvalue['reltraffic'] / ( $highestreltraffic / 100 ) ) * 4.5 );

next( $value );

$blue = ImageColorAllocate( $Image, 0, 0, 240 );

while( $currentvalue = current( $value )) {

	$endx = $lastvalue['endx'] + 55;
	$endy = ( $currentvalue['reltraffic'] / ( $highestreltraffic / 100 ) ) * 4.5;

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
