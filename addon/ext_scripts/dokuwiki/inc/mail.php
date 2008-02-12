<?php
/**
 * Mail functions
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

  if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../').'/');
  require_once(DOKU_INC.'inc/utf8.php');

  // end of line for mail lines - RFC822 says CRLF but postfix (and other MTAs?)
  // think different
  if(!defined('MAILHEADER_EOL')) define('MAILHEADER_EOL',"\n");
  #define('MAILHEADER_ASCIIONLY',1);

/**
 * UTF-8 autoencoding replacement for PHPs mail function
 *
 * Email address fields (To, From, Cc, Bcc can contain a textpart and an address
 * like this: 'Andreas Gohr <andi@splitbrain.org>' - the text part is encoded
 * automatically. You can seperate receivers by commas.
 *
 * @param string $to      Receiver of the mail (multiple seperated by commas)
 * @param string $subject Mailsubject
 * @param string $body    Messagebody
 * @param string $from    Sender address
 * @param string $cc      CarbonCopy receiver (multiple seperated by commas)
 * @param string $bcc     BlindCarbonCopy receiver (multiple seperated by commas)
 * @param string $headers Additional Headers (seperated by MAILHEADER_EOL
 * @param string $params  Additonal Sendmail params (passed to mail())
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @see    mail()
 */
function mail_send($to, $subject, $body, $from='', $cc='', $bcc='', $headers=null, $params=null){
  if(defined('MAILHEADER_ASCIIONLY')){
    $subject = utf8_deaccent($subject);
    $subject = utf8_strip($subject);
  }

  if(!utf8_isASCII($subject))
    $subject = '=?UTF-8?Q?'.mail_quotedprintable_encode($subject,0).'?=';

  $header  = '';

  // No named recipients for To: in Windows (see FS#652)
  $usenames = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? false : true;

  $to = mail_encode_address($to,'',$usenames);
  $header .= mail_encode_address($from,'From');
  $header .= mail_encode_address($cc,'Cc');
  $header .= mail_encode_address($bcc,'Bcc');
  $header .= 'MIME-Version: 1.0'.MAILHEADER_EOL;
  $header .= 'Content-Type: text/plain; charset=UTF-8'.MAILHEADER_EOL;
  $header .= 'Content-Transfer-Encoding: quoted-printable'.MAILHEADER_EOL;
  $header .= $headers;
  $header  = trim($header);

  $body = mail_quotedprintable_encode($body);

  if($params == null){
    return @mail($to,$subject,$body,$header);
  }else{
    return @mail($to,$subject,$body,$header,$params);
  }
}

/**
 * Encodes an email address header
 *
 * Unicode characters will be deaccented and encoded
 * quoted_printable for headers.
 * Addresses may not contain Non-ASCII data!
 *
 * Example:
 *   mail_encode_address("föö <foo@bar.com>, me@somewhere.com","TBcc");
 *
 * @param string  $string Multiple adresses separated by commas
 * @param string  $header Name of the header (To,Bcc,Cc,...)
 * @param boolean $names  Allow named Recipients?
 */
function mail_encode_address($string,$header='',$names=true){
  $headers = '';
  $parts = split(',',$string);
  foreach ($parts as $part){
    $part = trim($part);

    // parse address
    if(preg_match('#(.*?)<(.*?)>#',$part,$matches)){
      $text = trim($matches[1]);
      $addr = $matches[2];
    }else{
      $addr = $part;
    }

    // skip empty ones
    if(empty($addr)){
      continue;
    }

    // FIXME: is there a way to encode the localpart of a emailaddress?
    if(!utf8_isASCII($addr)){
      msg(htmlspecialchars("E-Mail address <$addr> is not ASCII"),-1);
      continue;
    }

    if(!mail_isvalid($addr)){
      msg(htmlspecialchars("E-Mail address <$addr> is not valid"),-1);
      continue;
    }

    // text was given
    if(!empty($text) && $names){
      // add address quotes
      $addr = "<$addr>";

      if(defined('MAILHEADER_ASCIIONLY')){
        $text = utf8_deaccent($text);
        $text = utf8_strip($text);
      }

      if(!utf8_isASCII($text)){
        $text = '=?UTF-8?Q?'.mail_quotedprintable_encode($text,0).'?=';
      }
    }else{
      $text = '';
    }

    // add to header comma seperated and in new line to avoid too long headers
    if($headers != '') $headers .= ','.MAILHEADER_EOL.' ';
    $headers .= $text.$addr;
  }

  if(empty($headers)) return null;

  //if headername was given add it and close correctly
  if($header) $headers = $header.': '.$headers.MAILHEADER_EOL;

  return $headers;
}

/**
 * Uses a regular expresion to check if a given mail address is valid
 *
 * May not be completly RFC conform!
 *
 * @link    http://www.webmasterworld.com/forum88/135.htm
 *
 * @param   string $email the address to check
 * @return  bool          true if address is valid
 */
function mail_isvalid($email){
  return eregi("^[0-9a-z]([+-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$", $email);
}

/**
 * Quoted printable encoding
 *
 * @author umu <umuAThrz.tu-chemnitz.de>
 * @link   http://www.php.net/manual/en/function.imap-8bit.php#61216
 */
function mail_quotedprintable_encode($sText,$maxlen=74,$bEmulate_imap_8bit=true) {
  // split text into lines
  $aLines= preg_split("/(?:\r\n|\r|\n)/", $sText);

  for ($i=0;$i<count($aLines);$i++) {
    $sLine =& $aLines[$i];
    if (strlen($sLine)===0) continue; // do nothing, if empty

    $sRegExp = '/[^\x09\x20\x21-\x3C\x3E-\x7E]/e';

    // imap_8bit encodes x09 everywhere, not only at lineends,
    // for EBCDIC safeness encode !"#$@[\]^`{|}~,
    // for complete safeness encode every character :)
    if ($bEmulate_imap_8bit)
      $sRegExp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';

    $sReplmt = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
    $sLine = preg_replace( $sRegExp, $sReplmt, $sLine );

    // encode x09,x20 at lineends
    {
      $iLength = strlen($sLine);
      $iLastChar = ord($sLine{$iLength-1});

      //              !!!!!!!!
      // imap_8_bit does not encode x20 at the very end of a text,
      // here is, where I don't agree with imap_8_bit,
      // please correct me, if I'm wrong,
      // or comment next line for RFC2045 conformance, if you like
      if (!($bEmulate_imap_8bit && ($i==count($aLines)-1)))

      if (($iLastChar==0x09)||($iLastChar==0x20)) {
        $sLine{$iLength-1}='=';
        $sLine .= ($iLastChar==0x09)?'09':'20';
      }
    }    // imap_8bit encodes x20 before chr(13), too
    // although IMHO not requested by RFC2045, why not do it safer :)
    // and why not encode any x20 around chr(10) or chr(13)
    if ($bEmulate_imap_8bit) {
      $sLine=str_replace(' =0D','=20=0D',$sLine);
      //$sLine=str_replace(' =0A','=20=0A',$sLine);
      //$sLine=str_replace('=0D ','=0D=20',$sLine);
      //$sLine=str_replace('=0A ','=0A=20',$sLine);
    }

    // finally split into softlines no longer than $maxlen chars,
    // for even more safeness one could encode x09,x20
    // at the very first character of the line
    // and after soft linebreaks, as well,
    // but this wouldn't be caught by such an easy RegExp
    if($maxlen){
      preg_match_all( '/.{1,'.($maxlen - 2).'}([^=]{0,2})?/', $sLine, $aMatch );
      $sLine = implode( '=' . MAILHEADER_EOL, $aMatch[0] ); // add soft crlf's
    }
  }

  // join lines into text
  return implode(MAILHEADER_EOL,$aLines);
}


//Setup VIM: ex: et ts=2 enc=utf-8 :
