<?php

function smarty_function_mailto($params, &$smarty)
{
    $extra = '';    

    if (empty($params['address'])) {
        $smarty->trigger_error("mailto: missing 'address' parameter");
        return;
    } else {
        $address = $params['address'];
    }

    $text = $address;

    // netscape and mozilla do not decode %40 (@) in BCC field (bug?)
    // so, don't encode it.
    $mail_parms = array();
    foreach ($params as $var=>$value) {
        switch ($var) {
            case 'cc':
            case 'bcc':
            case 'followupto':
                if (!empty($value))
                    $mail_parms[] = $var.'='.str_replace('%40','@',rawurlencode($value));
                break;
                
            case 'subject':
            case 'newsgroups':
                $mail_parms[] = $var.'='.rawurlencode($value);
                break;

            case 'extra':
            case 'text':
                $$var = $value;

            default:
        }
    }

    $mail_parm_vals = '';
    for ($i=0; $i<count($mail_parms); $i++) {
        $mail_parm_vals .= (0==$i) ? '?' : '&';
        $mail_parm_vals .= $mail_parms[$i];
    }
    $address .= $mail_parm_vals;

    $encode = (empty($params['encode'])) ? 'none' : $params['encode'];
    if (!in_array($encode,array('javascript','hex','none')) ) {
        $smarty->trigger_error("mailto: 'encode' parameter must be none, javascript or hex");
        return;
    }

    if ($encode == 'javascript' ) {
        $string = 'document.write(\'<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>\');';

        $js_encode = '';
        for ($x=0; $x < strlen($string); $x++) {
            $js_encode .= '%' . bin2hex($string[$x]);
        }

        return '<script type="text/javascript" language="javascript">eval(unescape(\''.$js_encode.'\'))</script>';

    } elseif ($encode == 'hex') {

        preg_match('!^(.*)(\?.*)$!',$address,$match);
        if(!empty($match[2])) {
            $smarty->trigger_error("mailto: hex encoding does not work with extra attributes. Try javascript.");
            return;
        }
        for ($x=0; $x < strlen($address); $x++) {
            if(preg_match('!\w!',$address[$x])) {
                $address_encode .= '%' . bin2hex($address[$x]);
            } else {
                $address_encode .= $address[$x];
            }
        }
        for ($x=0; $x < strlen($text); $x++) {
            $text_encode .= '&#x' . bin2hex($text[$x]).';';
        }

        return '<a href="mailto:'.$address_encode.'" '.$extra.'>'.$text_encode.'</a>';

    } else {
        // no encoding
        return '<a href="mailto:'.$address.'" '.$extra.'>'.$text.'</a>';

    }

}

/* vim: set expandtab: */

?>