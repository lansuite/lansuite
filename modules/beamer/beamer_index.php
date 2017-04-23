<?php

if (!VALID_LS) {
    die("Direct access not allowed!");
} // Direct-Call-Check
 

 // die wichtigsten GET Übergaben sammeln und prüfen
if ($_GET['beamerid']) {
    $beamerid = substr($_GET['beamerid'], 0, 3);
}    // SQL Injection Vorbeugen
if ($_GET['bcid']) {
    $bcid = substr($_GET['bcid'], 0, 3);
}    // SQL Injection Vorbeugen
if ($_REQUEST['ctype']) {
    $ctype = substr($_REQUEST['ctype'], 0, 7);
}    // SQL Injection Vorbeugen
 $action = $_GET['action'];
 
 // debug
if (isset($debug)) {
    echo "<br/>Ctype: ".$ctype;
    echo "<br/>bcID: ".$bcid;
    echo "<br/>FCKeditor1-Data: ".$_POST['FCKeditor1'];
}
 
 
 // Klasse einbinden und starten
 include_once('class/beamer.class.php');
 include_once('class/beamer_display.class.php');
 $beamermodul = new beamer();
 $beamerdisplay = new beamer_display();

 
 // action Auswahl
switch ($action) {
    case 'newcontent':
         $beamerdisplay->viewAddNewContent1();
        break;
    
    case 'newcontent2':
         $beamerdisplay->viewAddNewContent2();
        break;
    
    case 'savecontent':
        if ($bcid) {
            $newContent['bcid'] = $bcid;
        }
                            $newContent['type'] = $ctype;
                            $newContent['caption'] = $_POST['ccaption'];
                            $newContent['maxrepeats'] = $_POST['cmaxrepeats'];

        switch ($ctype) {
            case 'text':
                                    $newContent['text'] = $_POST['FCKeditor1'];
                break;
            case 'wrapper':
                                    $newContent['text'] = $_POST['curl'] ."*".    $_POST['choehe'] ."*".    $_POST['cbreite'];
                break;
            case 'turnier':
                                    $newContent['text'] = $_POST['ctid'];
                $newContent['caption'] = "Turnierbaum: ".$beamermodul->getTournamentNamebyID($_POST['ctid']);
                break;
        }
                            $beamermodul->saveContent($newContent);
                            $beamerdisplay->viewContent();
        break;
 

    case 'set2first':
         $beamermodul->set2first($bcid);
        $beamerdisplay->viewContent();
        break;
                        
    case 'editcontent':
         $beamerdisplay->viewEditContent();
        break;
                        
    case 'askfordelete':
         $func->question(HTML_NEWLINE.t("Wirklich L&ouml;schen?"), "index.php?mod=beamer&action=deletecontent&bcid=".$bcid, $link_target_no = '');
        break;
                        
    case 'deletecontent':
         $beamermodul->deleteContent($bcid);
        $beamerdisplay->viewContent();
        break;

    case 'toggleactive':
         $beamermodul->toggleActive($bcid);
        $beamerdisplay->viewContent();
        break;

    case 'togglebeameractive':
         $beamermodul->toggleBeamerActive($bcid, $beamerid);
        $beamerdisplay->viewContent();
        break;

    case 'content':
         $beamerdisplay->viewContent();
        break;
    
    
    case 'viewcontent':
    case 'start':
        if ($beamerid) {
            $beamerdisplay->viewCurrentContent();
        } else {
            $beamerdisplay->viewStartSite();
        }
        break;
                        
    case '':
    default:
         $beamerdisplay->viewModulMainPage();
        break;
}
