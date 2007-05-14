<?php

 if( !VALID_LS ) { die("Direct access not allowed!"); } // Direct-Call-Check
 

 // die wichtigsten GET Übergaben sammeln und prüfen
 if( $_GET['beamerid'] ) {  $beamerid = substr( $_GET['beamerid'], 0, 3 );	 }	// SQL Injection Vorbeugen   
 if( $_GET['bcid'] ) {  $bcid = substr( $_GET['bcid'], 0, 3 );	 }	// SQL Injection Vorbeugen   
 if( $_POST['ctype'] ) {  $ctype = substr( $_POST['ctype'], 0, 6 );	 }	// SQL Injection Vorbeugen   
 $action = $_GET['action'];
 
 
 // Klasse einbinden und starten
 include_once ('class/beamer.class.php');
 include_once ('class/beamer_display.class.php');
 $beamermodul = new beamer();
 $beamerdisplay = new beamer_display ();

 
 // action Auswahl
 switch ( $action ) {

 	case 'newcontent'	:	$beamerdisplay->viewAddNewContent1(); break;
	
	case 'newcontent2'	:	$beamerdisplay->viewAddNewContent2(); break;
	
	case 'newcontent3'	:	
							$newContent['new'] = TRUE;
							$newContent['type'] = $_GET['ctype'];
							$newContent['caption'] = $_POST['ccaption'];
							$newContent['maxrepeats'] = $_POST['cmaxrepeats'];
							$newContent['playnow'] = $_POST['cplaynow'];
							$newContent['text'] = $_POST['FCKeditor1'];							
							$beamermodul->saveContent( $newContent );
							$beamerdisplay->viewContent();
						break;
 

	case 'editcontent'			:	$beamerdisplay->editContent(); break;
						
	case 'askfordelete'			:   $func->question(HTML_NEWLINE."Wirklich L&ouml;schen?", "?mod=beamer&action=deletecontent&bcid=".$bcid, $link_target_no = ''); break;
						
	case 'deletecontent'		:	$beamermodul->deleteContent( $bcid ); $beamerdisplay->viewContent();		break;

	case 'toggleactive'			:	$beamermodul->toggleActive( $bcid ); $beamerdisplay->viewContent();		break;

	case 'togglebeameractive'	:	$beamermodul->toggleBeamerActive( $bcid , $beamerid );	$beamerdisplay->viewContent();		break;

	case 'content'				:   $beamerdisplay->viewContent();		break;
	
	
	case 'viewcontent'	:
	case 'start'		: 
							if ( $beamerid ) 	{ 	$beamerdisplay->viewCurrentContent();	} 
							else 				{	$beamerdisplay->viewStartSite();		}
	 					break;
						
	case '' 		:
	default			: $beamerdisplay->viewModulMainPage();
					break;
	
 } 
 
?>