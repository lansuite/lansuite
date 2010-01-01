<?php


class beamer_display {


	function __construct(){

	}

	
	function viewModulMainPage() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;	
	
		$dsp->NewContent( t('Beamer&uuml;bersicht') ,"");
		$dsp->AddSingleRow('<br/>'.t('Mit diesem Modul k&ouml;nnen Sie Texte und anderen Daten f&uuml;r eine Beamerpr&auml;sentation aufbereiten.').'<br/><br/>'.
						   t('Aktive Inhalte: ').$beamermodul->countContent("1").'<br/>'.
						   t('Inhalte gesamt: ').$beamermodul->countContent().
						   '<p />' );
		$dsp->AddSingleRow('<br />' . t('Das Modul arbeitet derzeit nur mit dem Template /\'/simple/\'/ und /\'/beamer/\'/ zusammen. F&uuml;r eine schnelle L&ouml;sung erstellen Sie einen zus&auml;tzlichen Account der das Beamer-Template verwendet. Damit haben Sie die besten Ergebnisse im Fullscreen Mode. <p/>Damit es mit jedem anderen Template funktioniert, m&uuml;ssen Sie in ihrem Template im Bereich der Meta-Angaben folgende Codezeilen hinzuf&uuml;gen:<p/> if( $_GET[/\'/sitereload/\'/] ) { echo ... (Restlichen Anweisungsblock bitte as der Design-index.php entnehmen.)  } ')	);				
		$dsp->AddSingleRow("<br />");
		$dsp->AddContent();
	}
	

	function viewCurrentContent() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $func;		
		$dsp->NewContent( "","");
		//$dsp->AddIFrame("localhost/pma/","1024","500");
		$dsp->AddSingleRow( $func->AllowHTML($beamermodul->getCurrentContent( $beamerid )) );
		$dsp->AddSingleRow( HTML_NEWLINE."");	
		$dsp->AddContent();
	}

	
	function viewContent () {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $config;

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
	
		$dsp->NewContent( t('Auflistung der Inhalte') );
		$dsp->AddSingleRow("<br/><div align=\"middle\">". $dsp->FetchCssButton( t('Inhalte hinzuf&uuml;gen') ,'?mod=beamer&action=newcontent','Ein neues Inhaltselement hinzuf&uuml;gen.'."</div>"));

  
  	  	include_once('modules/mastersearch2/class_mastersearch2.php');
		$ms2 = new mastersearch2('beamer');
		$ms2->query['from'] = '%prefix%beamer_content';
		$ms2->AddResultField('-A-', 'active', 'formatActiveStatus','',35);
		$ms2->AddResultField(t('Typ'), 'contentType', 'formatContentType',"",35);
		$ms2->AddResultField(t('Titel'), 'caption');
		$ms2->AddResultField(t('Zuletzt angezeigt'), 'lastView' , 'MS2GetTime','',80);
		$ms2->AddResultField('B.1', 'b1', 'formatBeamer1Status','',25);
		$ms2->AddResultField('B.2', 'b2', 'formatBeamer2Status','',25);
		$ms2->AddResultField('B.3', 'b3', 'formatBeamer3Status','',25);
		$ms2->AddResultField('B.4', 'b4', 'formatBeamer4Status','',25);
		$ms2->AddResultField('B.5', 'b5', 'formatBeamer5Status','',25);				
		$ms2->AddIconField('reset_timestamp','?mod=beamer&action=set2first&bcid=',t('An den Anfang der Spielliste setzen'));
		$ms2->AddIconField('edit','?mod=beamer&action=editcontent&bcid=',t('Bearbeiten'));
		$ms2->AddIconField('delete','?mod=beamer&action=askfordelete&bcid=',t('L&ouml;schen'));
		$ms2->PrintSearch('index.php?mod=beamer&action=content', 'bcID');		

		$dsp->AddSingleRow("<br/><div align=\"middle\">".
						   "Das Beamermodul zeigt immer den &auml;ltesten Eintrag von \"Zuletzt angezeigt\". Durch Klick auf das Icon <img src=\"design/images/icon_reset_timestamp.png\" alt=\"Set2First\" border=\"0\"> setzt man den Zeitstempel, wann das Element zuletzt angezeigt wurde, auf Null.</div>");
		$dsp->AddSingleRow("<br/><div align=\"middle\">". $dsp->FetchCssButton( t('Inhalte hinzuf&uuml;gen') ,'?mod=beamer&action=newcontent','Ein neues Inhaltselement hinzuf&uuml;gen.'."</div>"));		
		$dsp->AddContent();
  	
	}


	function viewStartSite() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid,$cfg;
		$a1 = $beamermodul->countContent("1","1");
		$a2 = $beamermodul->countContent("1","2");
		$a3 = $beamermodul->countContent("1","3");
		$a4 = $beamermodul->countContent("1","4");
		$a5 = $beamermodul->countContent("1","5");				
		$dsp->NewContent( t('Beamerinhalte pr&auml;sentieren') ,"");
		$dsp->AddDoubleRow('Seiteninterval in Sekunden: ',$cfg['beamer_duration_default'] );
	    if ( $a1 > 0 ) { $btn1 = $dsp->FetchSpanButton(t('Beamerfenster starten'), "?mod=beamer&action=viewcontent&beamerid=1&design=beamer&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'].'" target="_blank');	}
	    if ( $a2 > 0 ) { $btn2 = $dsp->FetchSpanButton(t('Beamerfenster starten'), "?mod=beamer&action=viewcontent&beamerid=2&design=beamer&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'].'" target="_blank');	}
	    if ( $a3 > 0 ) { $btn3 = $dsp->FetchSpanButton(t('Beamerfenster starten'), "?mod=beamer&action=viewcontent&beamerid=3&design=beamer&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'].'" target="_blank');	}
	    if ( $a4 > 0 ) { $btn4 = $dsp->FetchSpanButton(t('Beamerfenster starten'), "?mod=beamer&action=viewcontent&beamerid=4&design=beamer&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'].'" target="_blank');	}
	    if ( $a5 > 0 ) { $btn5 = $dsp->FetchSpanButton(t('Beamerfenster starten'), "?mod=beamer&action=viewcontent&beamerid=5&design=beamer&fullscreen=yes&sitereload=".$cfg['beamer_duration_default'].'" target="_blank');	}
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">1.</font> ".t('Beamerfenster ').$btn1." - ".t('Aktive Inhalte: ').$a1."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">2.</font> ".t('Beamerfenster ').$btn2." - ".t('Aktive Inhalte: ').$a2."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">3.</font> ".t('Beamerfenster ').$btn3." - ".t('Aktive Inhalte: ').$a3."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">4.</font> ".t('Beamerfenster ').$btn4." - ".t('Aktive Inhalte: ').$a4."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE." <font size=\"4\">5.</font> ".t('Beamerfenster ').$btn5." - ".t('Aktive Inhalte: ').$a5."<p/><br/>");
		$dsp->AddSingleRow( HTML_NEWLINE );
		$dsp->AddContent();
	}
	
	
	
	function viewAddNewContent1() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid;	
		$dsp->NewContent( t('Inhalte hinzuf&uuml;gen') );
		$dsp->AddSingleRow( HTML_NEWLINE.t("Bitte w&auml;hlen Sie einen Inhaltstyp aus:").HTML_NEWLINE.HTML_NEWLINE);
		$dsp->SetForm("?mod=beamer&action=newcontent2");
		$dsp->AddRadioRow("ctype", t("<strong>Text</strong><br /> (FCKeditor, HTML/Bilder/Flash m&ouml;glich)") , 'text' , $errortext = NULL, $optional = NULL, $checked = TRUE, $disabled = NULL);
		$dsp->AddRadioRow("ctype", t("<strong>Wrapper</strong><br /> (IFrame f&uuml;r Webseiten oder sonstigen Content)") , 'wrapper' , $errortext = NULL, $optional = NULL, $checked = FALSE, $disabled = NULL);
		$dsp->AddRadioRow("ctype", t("<strong>Turnierbaum</strong><br />") , 'turnier' , $errortext = NULL, $optional = NULL, $checked = FALSE, $disabled = NULL);
		$dsp->AddFormSubmitRow("next");
		$dsp->AddContent();
	}

	function viewAddNewContent2() {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $ctype;		
	
		$dsp->NewContent( t('Inhalte hinzuf&uuml;gen') . " - 2" );
		$dsp->SetForm("?mod=beamer&action=savecontent&ctype=".$ctype);

		if($ctype=='text') {
			$dsp->AddTextFieldRow("ccaption", t("Bezeichnung: "), "", "", '50');
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
			$dsp->AddTextFieldRow("ccaption", t("Bezeichnung: "), "", "", '50');
			$dsp->AddTextFieldRow("curl", t("IFrame URL: "), "", "", '80');
			$dsp->AddTextFieldRow("choehe", t("IFrame H&ouml;he: "), "550", "", '4');			
			$dsp->AddTextFieldRow("cbreite", t("IFrame Breite: "), "980", "", '4');			
		}
		
		if($ctype=='turnier') {

			$dsp->AddDropDownFieldRow("ctid", t("Turnier: "), $beamermodul->getAllTournamentsAsOptionList() , $errortext, $optional = NULL);
		
		
		}
		
		
		$dsp->AddBackButton();
		$dsp->AddFormSubmitRow("save");							
		$dsp->AddContent();
	}

	function viewEditContent () {
	global $dsp, $lang, $beamermodul, $bcid, $beamerid, $ctype;		
		$content = $beamermodul->getContent( $bcid );
		$dsp->NewContent( t('Inhalt bearbeiten') );	
		$dsp->SetForm("?mod=beamer&action=savecontent&ctype={$content['contentType']}&bcid=".$bcid);	

		if($content['contentType']=='text') {
			$dsp->AddTextFieldRow("ccaption", "Bezeichnung: ", $content['caption'], "", '50');
	        ob_start();
	        include_once("ext_scripts/FCKeditor/fckeditor.php");
	        $oFCKeditor = new FCKeditor('FCKeditor1') ;
	        $oFCKeditor->BasePath	= 'ext_scripts/FCKeditor/';
	        $oFCKeditor->Value = $content['contentData'];
	        $oFCKeditor->Height = 380;
	        $oFCKeditor->Create();
	        $fcke_content = ob_get_contents();
	        ob_end_clean();
	        $dsp->AddSingleRow($fcke_content);
		}
		if($content['contentType']=='wrapper') {
			$arr = explode( "*" , $content['contentData'] );
			$dsp->AddTextFieldRow("ccaption", t("Bezeichnung: "), $content['caption'], "", '50');
			$dsp->AddTextFieldRow("curl", t("IFrame URL: "), $arr[0] , "", '80');
			$dsp->AddTextFieldRow("choehe", t("IFrame H&ouml;he: "), $arr[1], "", '4');			
			$dsp->AddTextFieldRow("cbreite", t("IFrame Breite: "), $arr[2], "", '4');			
		}

		if($content['contentType']=='turnier') {

			$dsp->AddDropDownFieldRow("ctid", t("Turnier: "), $beamermodul->getAllTournamentsAsOptionList() , $errortext, $optional = NULL);
		
		
		}		

		$dsp->AddFormSubmitRow("save");							
		$dsp->AddContent();
	}
		
}


?>