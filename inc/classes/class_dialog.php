<?php
define('NO_LINK', -1);

/**
 * CLASS Dialog : Create Dialogboxes
 * Based on class_func.php from Lansuite 3.3
 *
 * @package lansuite_core
 * @author bytekilla, knox
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class dialogs {

  /**#@+
   * Private und Configvariablen
   * @access private
   */
    var $internal_referer = "";   // Internal Referer for "backto" actions
  /**#@-*/

  /**
   * CONSTRUCTOR : Initialize basic Variables for Authorisation
   *
   * @return
   */
    function dialogs() {
        $url_array = parse_url($_SERVER['HTTP_REFERER']);
        $this->internal_referer = "?".$url_array['query'].$url_array['fragment'];
    }

  /**
   * Create HTML for an Errorbox
   *
   * @param mixed Massagetext
   * @param string Backlink
   * @return string Returns HTML for build Content
   */
    function error($text, $link_target = '') {
        global $templ, $lang, $dsp;
        
        if ($link_target == '') $link_target = $this->internal_referer;
        if ($link_target == NO_LINK) $link_target = '';
        if ($link_target) $templ['error']['info']['link'] = $dsp->FetchCssButton('Zurück', $link_target, 'Zurück zur vorherigen Seite');

        switch($text) {
            case "ACCESS_DENIED":
                $templ['error']['info']['errormsg'] = $lang['class_func']['error_access_denied'];
            break;
            case "NO_LOGIN":
                $templ['error']['info']['errormsg'] = $lang['class_func']['error_no_login'];
            break;
            case "NOT_FOUND":
                $templ['error']['info']['errormsg'] = $lang['class_func']['error_not_found'];
            break;
            case "DEACTIVATED":
                $templ['error']['info']['errormsg'] = $lang['class_func']['error_deactivated'];
            break;
            case "NO_REFRESH":
                $templ['error']['info']['errormsg'] = $lang['class_func']['error_no_refresh'];
            break;
            default:
                $templ['error']['info']['errormsg'] = $text;
            break;
        }
        
		return $dsp->FetchTpl('design/templates/error.htm');
    }

  /**
   * Create HTML for an Confirmationbox
   *
   * @param mixed Massagetext
   * @param string Targetlink
   * @return string Returns HTML for build Content
   */
    function confirmation($text, $link_target = '') {
        global $templ, $dsp;

        if ($link_target == '') $link_target = $this->internal_referer;
        if ($link_target == NO_LINK) $link_target = '';
        if ($link_target) $templ['confirmation']['control']['link'] = $dsp->FetchCssButton('Zurück', $link_target, 'Zurück zur vorherigen Seite');
        $templ['confirmation']['info']['confirmationmsg']   = $text;
	    
		return $dsp->FetchTpl('design/templates/confirmation.htm');
    }

  /**
   * Create HTML for an Infobox
   *
   * @param mixed Massagetext
   * @param string Linktarget
   * @param string Buttontext 
   * @return string Returns HTML for build Content
   */
    function information($text, $link_target = '', $button_text = 'back') {
        global $templ, $dsp;

        if ($link_target == '') $link_target = $this->internal_referer;
        if ($link_target == NO_LINK) $link_target = '';
        if ($link_target) $templ['confirmation']['control']['link'] = $dsp->FetchCssButton('Zurück', $link_target, 'Zurück zur vorherigen Seite');
        $templ['confirmation']['info']['confirmationmsg'] = $text;

    	return $dsp->FetchTpl('design/templates/information.htm');
    }

  /**
   * Create HTML for an Multiquestionbox
   * $questionarray = array('Yes', 'No', 'Stop')
   * $linkarray =     array('yes.php', 'no.php', 'stop.php')  
   *
   * @param mixed Single Array with Questionsstrings
   * @param mixed Single Array with Links for Questionsstrings
   * @param mixed Text to display
   * @return string Returns HTML for build Content
   */
    function multiquestion($questionarray, $linkarray, $text) {
        global $templ, $dsp;

        ($text)? $templ['multiquestion']['info']['text'] = $text : $templ['multiquestion']['info']['text'] = t('Bitte wählen Sie eine Möglichkeit aus:');
        if (is_array($questionarray)) foreach($questionarray as $ind => $question)
    	$templ['multiquestion']['control']['row'] .= '<br /><br /><a href="'. $linkarray[$ind] .'">'. $question .'</a>';
		
		return $dsp->FetchTpl("design/templates/multiquestion.htm");
    }

  /**
   * Create HTML for an Dialog
   *
   * @param mixed $dialogarray
   * @param mixed $linkarray
   * @param mixed $picarray
   * @return string Returns HTML for build Content
   */
    function dialog($dialogarray, $linkarray, $picarray) {
        global $templ, $dsp;

        if ($dialogarray[0]=="") $dialogarray[0]="question";
        if ($dialogarray[1]=="") $dialogarray[1]="Frage";
        $templ['dialog']['info']['icon']        = $dialogarray[0]; // using the pic filename w/o "icon_" & ".gif" !
        $templ['dialog']['info']['caption']     = $dialogarray[1];
        $templ['dialog']['info']['questionmsg'] = $dialogarray[2];
        if (is_array($linkarray)) foreach ($linkarray as $ind => $link)
        $templ['dialog']['control']['row'] .= $dsp->FetchButton($link, $picarray[$ind]);

        return $dsp->FetchTpl("design/templates/dialog.htm");
    }

  /**
   * Create HTML for an question
   *
   * @param mixed Questiontext
   * @param mixed Link for target "yes"
   * @param string Link target "no"
   * @return string Returns HTML for build Content
   */
    function question($text, $link_target_yes, $link_target_no = '') {
        global $templ, $dsp;

        if ($link_target_no == '') $link_target_no = $this->internal_referer;
        $templ['question']['info']['questionmsg']   = $text;
        $templ['question']['control']['link']['yes'] = $dsp->FetchIcon($link_target_yes, "yes");
        $templ['question']['control']['link']['no'] = $dsp->FetchIcon($link_target_no, "no");

	    return $dsp->FetchTpl("design/templates/question.htm");
    }
}
?>