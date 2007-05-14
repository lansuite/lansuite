<?php


class beamer_display {


	function __construct(){

	}

	
	function viewModulMainPage() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;	
	
		$dsp->NewContent( $lang['beamer']['beamer'] ,"");
		$dsp->AddSingleRow('<br/>'.$lang['beamer']['introtext'].'<br/><br/>'.
						   $lang['beamer']['activecontent'].$beamermodul->countContent("1").'<br/>'.
						   $lang['beamer']['totalcontent'].$beamermodul->countContent().
						   '<br/><br/>'
							);
		$dsp->AddSingleRow("<br/>");
		$dsp->AddContent();
	}
	

	function viewCurrentContent() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;		
		$dsp->NewContent( "","");
		//$dsp->AddIFrame("localhost/pma/","1024","500");
		$dsp->AddSingleRow( $beamermodul->getCurrentContent( $beamerid ) );
		$dsp->AddSingleRow( HTML_NEWLINE."");	
		$dsp->AddContent();
	}

	
	function viewContent () {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;

		// private ms2 funktionen
		function formatContentType ( $var ) {
			if ( $var == "text" ) { return '<img src="modules/beamer/images/text.jpg" alt="Text" border="0">'; }
			if ( $var == "image" ) { return '<img src="modules/beamer/images/image.jpg" alt="Bild" border="0">'; }	
		}


		function formatBStatus ( $var, $bcid , $beamerid ) {
			if ( $var == "1" ) { return '<a href="?mod=beamer&action=togglebeameractive&bcid='.$bcid.'&beamerid='.$beamerid.'"><img src="modules/beamer/images/active_sm.gif" alt="Aktiv" border="0"></a>'; }
			if ( $var == "0" ) { return '<a href="?mod=beamer&action=togglebeameractive&bcid='.$bcid.'&beamerid='.$beamerid.'"><img src="modules/beamer/images/deactive_sm.gif" alt="Deaktiv" border="0"></a>'; }	
		}
	
		function formatBeamer1Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "1");	}
		function formatBeamer2Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "2");	}
		function formatBeamer3Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "3");	}
		function formatBeamer4Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "4");	}
		function formatBeamer5Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "5");	}			

		function formatActiveStatus ( $var, $var2 ) {
			if ( $var == "1" ) { return '<a href="?mod=beamer&action=toggleactive&bcid='.$var2.'"><img src="modules/beamer/images/active.gif" alt="Aktiv" border="0"></a>'; }
			if ( $var == "0" ) { return '<a href="?mod=beamer&action=toggleactive&bcid='.$var2.'"><img src="modules/beamer/images/deactive.gif" alt="Deaktiv" border="0"></a>'; }
		}
	
		$dsp->NewContent( $lang['beamer']['listcontent'] );
		$dsp->AddSingleRow("<br/><div align=\"middle\">". $dsp->FetchCssButton( $lang['beamer']['newcontent'] ,'?mod=beamer&action=newcontent','Ein neues Inhaltselement hinzuf&uuml;gen.'."</div>"));
		$dsp->AddContent();
  
  	  	include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new mastersearch2('beamer');
		$ms2->query['from'] = "lansuite_beamer_content";	// "{$config["tables"]["beamer_content"]}";
		$ms2->AddResultField('-A-', 'active', 'formatActiveStatus','',35);
		$ms2->AddResultField('Typ', 'contentType', 'formatContentType',"",35);
		$ms2->AddResultField('Titel', 'caption');
		$ms2->AddResultField('B.1', 'b1', 'formatBeamer1Status','',25);
		$ms2->AddResultField('B.2', 'b2', 'formatBeamer2Status','',25);
		$ms2->AddResultField('B.3', 'b3', 'formatBeamer3Status','',25);
		$ms2->AddResultField('B.4', 'b4', 'formatBeamer4Status','',25);
		$ms2->AddResultField('B.5', 'b5', 'formatBeamer5Status','',25);				
		// $ms2->AddIconField('edit','?mod=beamer&action=content&bcid=','Bearbeiten');
		$ms2->AddIconField('delete','?mod=beamer&action=askfordelete&bcid=','L&ouml;schen');
		$ms2->PrintSearch('index.php?mod=beamer&action=content', 'bcID');		
	
	}


	function viewStartSite() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid,$cfg;
		$dsp->NewContent( $lang['beamer']['beamerstart'] ,"");
	    $btn1 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=1&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');
	    $btn2 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=2&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');
	    $btn3 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=3&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');											
	    $btn4 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=4&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');
	    $btn5 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=5&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');							
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">1.</font> ".$lang['beamer']['viewcontent'].$btn1." - ".$lang['beamer']['activecontent'].$beamermodul->countContent("1","1")."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">2.</font> ".$lang['beamer']['viewcontent'].$btn2." - ".$lang['beamer']['activecontent'].$beamermodul->countContent("1","2")."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">3.</font> ".$lang['beamer']['viewcontent'].$btn3." - ".$lang['beamer']['activecontent'].$beamermodul->countContent("1","3")."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">4.</font> ".$lang['beamer']['viewcontent'].$btn4." - ".$lang['beamer']['activecontent'].$beamermodul->countContent("1","4")."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">5.</font> ".$lang['beamer']['viewcontent'].$btn5." - ".$lang['beamer']['activecontent'].$beamermodul->countContent("1","5")."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE );
		$dsp->AddContent();
	}
	
	
	
	function viewAddNewContent1() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;	
		$dsp->NewContent( $lang['beamer']['newcontent'] );
		$dsp->AddSingleRow( HTML_NEWLINE."Bitte w&auml;hlen Sie einen Inhaltstyp aus:".HTML_NEWLINE.HTML_NEWLINE);
		$dsp->SetForm("?mod=beamer&action=newcontent2");
		$dsp->AddRadioRow("ctype", "Text (FCKeditor)" , 'text' , $errortext = NULL, $optional = NULL, $checked = TRUE, $disabled = NULL);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	}

	function viewAddNewContent2() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $ctype;		

		$dsp->NewContent( $lang['beamer']['newcontent'] . " - 2" );
		$dsp->SetForm("?mod=beamer&action=newcontent3&ctype=".$ctype);
		$dsp->AddTextFieldRow("ccaption", "Bezeichnung: ", "", "", '50');
		$dsp->AddTextFieldRow("cmaxrepeats","Wiederholungen: <br/>(0 = Unlimitiert) ","0","","22");
		$dsp->AddCheckBoxRow("cplaynow","Sofort Anzeigen: <br /><br />An den Anfang der Spielliste. Der Eintrag muss noch aktivieren werden!","","", TRUE);
//		$dsp->AddTextAreaPlusRow("ctext", '','','' );

        ob_start();
        include_once("ext_scripts/FCKeditor/fckeditor.php");
        $oFCKeditor = new FCKeditor('FCKeditor1') ;
        $oFCKeditor->BasePath	= 'ext_scripts/FCKeditor/';
        $oFCKeditor->Value = "";
        $oFCKeditor->Height = 380;
        $oFCKeditor->Create();
        $fcke_content = ob_get_contents();
        ob_end_clean();
        $dsp->AddSingleRow($fcke_content);


//		$dsp->AddBackButton();
		$dsp->AddFormSubmitRow("save");							
		$dsp->AddContent();


		
/*		
		include_once('inc/classes/class_masterform.php');
		$mf = new masterform();		
		$mf->AddField("Bezeichnung" , 'caption');
		$mf->AddField("Wiederholungen: <br/>(0 = Unlimitiert) " , 'maxRepeats','','',FIELD_OPTIONAL );		
		switch ($ctype) {
		
			case 'text' : 		$mf->AddField("Text" , 'contentData','',HTML_WYSIWYG); break;
		
		}

		if ($mf->SendForm('?mod=beamer&action=newcontent2', 'beamer_content', 'bcID')){
			$dsp->NewContent("");
			$dsp->AddBackButton("?mod=beamer&action=content","back");
			$dsp->AddContent;
		}
		*/
	}


}


?>