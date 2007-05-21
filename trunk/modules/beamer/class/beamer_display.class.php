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
			if ( $var == "text" ) { return '<img src="design/images/icon_text.png" alt="Text" border="0">'; }
			if ( $var == "wrapper" ) { return '<img src="design/images/icon_url.png" alt="Bild" border="0">'; }	
			if ( $var == "turnier" ) { return '<img src="design/images/icon_tree.png" alt="Bild" border="0">'; }				
		}


		function formatBStatus ( $var, $bcid , $beamerid ) {
			if ( $var == "1" ) { return '<a href="?mod=beamer&action=togglebeameractive&bcid='.$bcid.'&beamerid='.$beamerid.'"><img src="design/images/icon_active_sm.png" alt="Aktiv" border="0"></a>'; }
			if ( $var == "0" ) { return '<a href="?mod=beamer&action=togglebeameractive&bcid='.$bcid.'&beamerid='.$beamerid.'"><img src="design/images/icon_deactive_sm.png" alt="Deaktiv" border="0"></a>'; }	
		}
	
		function formatBeamer1Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "1");	}
		function formatBeamer2Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "2");	}
		function formatBeamer3Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "3");	}
		function formatBeamer4Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "4");	}
		function formatBeamer5Status ( $var , $bcid ) { return formatBStatus( $var , $bcid, "5");	}			

		function formatActiveStatus ( $var, $var2 ) {
			if ( $var == "1" ) { return '<a href="?mod=beamer&action=toggleactive&bcid='.$var2.'"><img src="design/images/icon_active.png" alt="Aktiv" border="0"></a>'; }
			if ( $var == "0" ) { return '<a href="?mod=beamer&action=toggleactive&bcid='.$var2.'"><img src="design/images/icon_deactive.png" alt="Deaktiv" border="0"></a>'; }
		}
	
		$dsp->NewContent( $lang['beamer']['listcontent'] );
		$dsp->AddSingleRow("<br/><div align=\"middle\">". $dsp->FetchCssButton( $lang['beamer']['newcontent'] ,'?mod=beamer&action=newcontent','Ein neues Inhaltselement hinzuf&uuml;gen.'."</div>"));

  
  	  	include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new mastersearch2('beamer');
		$ms2->query['from'] = "lansuite_beamer_content";	// "{$config["tables"]["beamer_content"]}";
		$ms2->AddResultField('-A-', 'active', 'formatActiveStatus','',35);
		$ms2->AddResultField('Typ', 'contentType', 'formatContentType',"",35);
		$ms2->AddResultField('Titel', 'caption');
		$ms2->AddResultField('Zuletzt angezeigt', 'lastView' , 'MS2GetTime','',80);
		$ms2->AddResultField('B.1', 'b1', 'formatBeamer1Status','',25);
		$ms2->AddResultField('B.2', 'b2', 'formatBeamer2Status','',25);
		$ms2->AddResultField('B.3', 'b3', 'formatBeamer3Status','',25);
		$ms2->AddResultField('B.4', 'b4', 'formatBeamer4Status','',25);
		$ms2->AddResultField('B.5', 'b5', 'formatBeamer5Status','',25);				
		$ms2->AddIconField('reset_timestamp','?mod=beamer&action=set2first&bcid=','An den Anfang der Spielliste setzen');
		$ms2->AddIconField('edit','?mod=beamer&action=editcontent&bcid=','Bearbeiten');
		$ms2->AddIconField('delete','?mod=beamer&action=askfordelete&bcid=','L&ouml;schen');
		$ms2->PrintSearch('index.php?mod=beamer&action=content', 'bcID');		

		$dsp->AddSingleRow("<br/><div align=\"middle\">".
						   "Das Beamermodul zeigt immer den &auml;ltesten Eintrag von \"Zuletzt angezeigt\". Durch Klick auf das Icon <img src=\"design/images/icon_reset_timestamp.png\" alt=\"Set2First\" border=\"0\"> setzt man den Zeitstempel, wann das Element zuletzt angezeigt wurde, auf Null.</div>");
		$dsp->AddSingleRow("<br/><div align=\"middle\">". $dsp->FetchCssButton( $lang['beamer']['newcontent'] ,'?mod=beamer&action=newcontent','Ein neues Inhaltselement hinzuf&uuml;gen.'."</div>"));		
		$dsp->AddContent();
  	
	}


	function viewStartSite() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid,$cfg;
		$a1 = $beamermodul->countContent("1","1");
		$a2 = $beamermodul->countContent("1","2");
		$a3 = $beamermodul->countContent("1","3");
		$a4 = $beamermodul->countContent("1","4");
		$a5 = $beamermodul->countContent("1","5");				
		$dsp->NewContent( $lang['beamer']['beamerstart'] ,"");
		$dsp->AddDoubleRow('Seiteninterval in Sekunden: ',$cfg['beamer_duration_default'] );
	    if ( $a1 > 0 ) { $btn1 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=1&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');	}
	    if ( $a2 > 0 ) { $btn2 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=2&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');	}
	    if ( $a3 > 0 ) { $btn3 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=3&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');	}									
	    if ( $a4 > 0 ) { $btn4 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=4&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');	}
	    if ( $a5 > 0 ) { $btn5 = $dsp->FetchButton("?mod=beamer&action=viewcontent&beamerid=5&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'], "open", 'Beamerfenster starten');	}
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">1.</font> ".$lang['beamer']['viewcontent'].$btn1." - ".$lang['beamer']['activecontent'].$a1."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">2.</font> ".$lang['beamer']['viewcontent'].$btn2." - ".$lang['beamer']['activecontent'].$a2."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">3.</font> ".$lang['beamer']['viewcontent'].$btn3." - ".$lang['beamer']['activecontent'].$a3."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">4.</font> ".$lang['beamer']['viewcontent'].$btn4." - ".$lang['beamer']['activecontent'].$a4."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">5.</font> ".$lang['beamer']['viewcontent'].$btn5." - ".$lang['beamer']['activecontent'].$a5."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE );
		$dsp->AddContent();
	}
	
	
	
	function viewAddNewContent1() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;	
		$dsp->NewContent( $lang['beamer']['newcontent'] );
		$dsp->AddSingleRow( HTML_NEWLINE."Bitte w&auml;hlen Sie einen Inhaltstyp aus:".HTML_NEWLINE.HTML_NEWLINE);
		$dsp->SetForm("?mod=beamer&action=newcontent2");
		$dsp->AddRadioRow("ctype", "<strong>Text</strong><br /> (FCKeditor, HTML/Bilder/Flash m&ouml;glich)" , 'text' , $errortext = NULL, $optional = NULL, $checked = TRUE, $disabled = NULL);
		$dsp->AddRadioRow("ctype", "<strong>Wrapper</strong><br /> (IFrame f&uuml;r Webseiten oder sonstigen Content)" , 'wrapper' , $errortext = NULL, $optional = NULL, $checked = FALSE, $disabled = NULL);
		$dsp->AddRadioRow("ctype", "<strong>Turnierbaum</strong><br />" , 'turnier' , $errortext = NULL, $optional = NULL, $checked = FALSE, $disabled = NULL);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	}

	function viewAddNewContent2() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $ctype;		
	
		$dsp->NewContent( $lang['beamer']['newcontent'] . " - 2" );
		$dsp->SetForm("?mod=beamer&action=savecontent&ctype=".$ctype);

		if($ctype=='text') {
			$dsp->AddTextFieldRow("ccaption", "Bezeichnung: ", "", "", '50');
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
		}
		
		if($ctype=='wrapper') {
			$dsp->AddTextFieldRow("ccaption", "Bezeichnung: ", "", "", '50');
			$dsp->AddTextFieldRow("curl", "IFrame URL: ", "", "", '80');
			$dsp->AddTextFieldRow("choehe", "IFrame H&ouml;he: ", "550", "", '4');			
			$dsp->AddTextFieldRow("cbreite", "IFrame Breite: ", "980", "", '4');			
		}
		
		if($ctype=='turnier') {

			$dsp->AddDropDownFieldRow("ctid", "Turnier: ", $beamermodul->getAllTournamentsAsOptionList() , $errortext, $optional = NULL);
		
		
		}
		
		
		$dsp->AddBackButton();
		$dsp->AddFormSubmitRow("save");							
		$dsp->AddContent();
	}

	function viewEditContent () {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $ctype;		
		$content = $beamermodul->getContent( $bcid );
		$dsp->NewContent( $lang['beamer']['editcontent'] );	
		$dsp->SetForm("?mod=beamer&action=savecontent&ctype={$content['contentType']}&bcid=".$bcid);	

		if($content['contentType']=='text') {
	        ob_start();
	        include_once("ext_scripts/FCKeditor/fckeditor.php");
	        $oFCKeditor = new FCKeditor('FCKeditor1') ;
	        $oFCKeditor->BasePath	= 'ext_scripts/FCKeditor/';
	        $oFCKeditor->Value = $content['contentData'];
	        $oFCKeditor->Height = 380;
	        $oFCKeditor->Create();
	        $fcke_content = ob_get_contents();
	        ob_end_clean();
		}
		if($content['contentType']=='wrapper') {
			$arr = explode( "*" , $content['contentData'] );
			$dsp->AddTextFieldRow("curl", "IFrame URL: ", $arr[0] , "", '80');
			$dsp->AddTextFieldRow("choehe", "IFrame H&ouml;he: ", $arr[1], "", '4');			
			$dsp->AddTextFieldRow("cbreite", "IFrame Breite: ", $arr[2], "", '4');			
		}
			
        $dsp->AddSingleRow($fcke_content);
		$dsp->AddFormSubmitRow("save");							
		$dsp->AddContent();
	}
		
}


?>