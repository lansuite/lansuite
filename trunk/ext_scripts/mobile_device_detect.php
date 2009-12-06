<?php

/*

  This code is from http://detectmobilebrowsers.mobi/ - please do not republish it without due credit and hyperlink to http://detectmobilebrowsers.mobi
  
  For help generating the function call visit http://detectmobilebrowsers.mobi/ and use the function generator.
  
  Published by Andy Moore - .mobi certified mobile web developer - http://andymoore.info/

  This code is free to download and use on non-profit websites, if your website makes a profit or you require support using this code please upgrade.

  Upgrade for use on commercial websites and support: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1064282

  To submit a support request please forward your PayPal receipt with your questions to the email address you sent the money to and I will endeavour to get back to you. It might take me a few days but I reply to all support issues with as much helpful info as I can provide.

  The function has eight parameters that can be passed to it which define the way it handles different scenarios. These paramaters are:

    * iPhone - Set to true to treat iPhones as mobiles, false to treat them like full browsers or set a URL (including http://) to redirect iPhones and iPods to.
    * Android - Set to true to treat Android handsets as mobiles, false to treat them like full browsers or set a URL (including http://) to redirect Android and Google mobile users to.
    * Opera Mini - Set to true to treat Opera Mini like a mobile, false to treat it like full browser or set a URL (including http://) to redirect Opera Mini users to.
    * Blackberry - Set to true to treat Blackberry like a mobile, false to treat it like full browser or set a URL (including http://) to redirect Blackberry users to.
    * Palm - Set to true to treat Palm OS like a mobile, false to treat it like full browser or set a URL (including http://) to redirect Palm OS users to.
    * Windows - Set to true to treat Windows Mobiles like a mobile, false to treat it like full browser or set a URL (including http://) to redirect Windows Mobile users to.
    * Mobile Redirect URL - This should be full web address (including http://) of the site (or page) you want to send mobile visitors to. Leaving this blank will make the script return true when it detects a mobile.
    * Desktop Redirect URL - This should be full web address (including http://) of the site (or page) you want to send non-mobile visitors to. Leaving this blank will make the script return false when it fails to detect a mobile.

  Change Log

    * 25.11.08 - Added Amazon's Kindle to the pipe seperated array
    * 27.11.08 - Added support for Blackberry options
    * 27.01.09 - Added usage samples & help with PHP in HTML
    * 09.03.09 - Added support for Windows Mobile options
    * 09.03.09 - Removed 'ppc;'=>'ppc;', from array to reduce false positives
    * 09.03.09 - Added support for Palm OS options
    * 09.03.09 - Added sample .htaccess html.html and help.html files to download

*/

function mobile_device_detect($iphone=true,$android=true,$opera=true,$blackberry=true,$palm=true,$windows=true,$mobileredirect=false,$desktopredirect=false){

  $mobile_browser   = false; // set mobile browser as false till we can prove otherwise
  $user_agent       = $_SERVER['HTTP_USER_AGENT']; // get the user agent value - this should be cleaned to ensure no nefarious input gets executed
  $accept           = $_SERVER['HTTP_ACCEPT']; // get the content accept value - this should be cleaned to ensure no nefarious input gets executed

  switch(true){ // using a switch against the following statements which could return true is more efficient than the previous method of using if statements

    case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent)); // we find the words iphone or ipod in the user agent
      $mobile_browser = $iphone; // mobile browser is either true or false depending on the setting of iphone when calling the function
      if(substr($iphone,0,4)=='http'){ // does the value of iphone resemble a url
        $mobileredirect = $iphone; // set the mobile redirect url to the url value stored in the iphone value
      } // ends the if for iphone being a url
    break; // break out and skip the rest if we've had a match on the iphone or ipod

    case (preg_match('/android/i',$user_agent));  // we find android in the user agent
      $mobile_browser = $android; // mobile browser is either true or false depending on the setting of android when calling the function
      if(substr($android,0,4)=='http'){ // does the value of android resemble a url
        $mobileredirect = $android; // set the mobile redirect url to the url value stored in the android value
      } // ends the if for android being a url
    break; // break out and skip the rest if we've had a match on android

    case (preg_match('/opera mini/i',$user_agent)); // we find opera mini in the user agent
      $mobile_browser = $opera; // mobile browser is either true or false depending on the setting of opera when calling the function
      if(substr($opera,0,4)=='http'){ // does the value of opera resemble a rul
        $mobileredirect = $opera; // set the mobile redirect url to the url value stored in the opera value
      } // ends the if for opera being a url 
    break; // break out and skip the rest if we've had a match on opera

    case (preg_match('/blackberry/i',$user_agent)); // we find blackberry in the user agent
      $mobile_browser = $blackberry; // mobile browser is either true or false depending on the setting of blackberry when calling the function
      if(substr($blackberry,0,4)=='http'){ // does the value of blackberry resemble a rul
        $mobileredirect = $blackberry; // set the mobile redirect url to the url value stored in the blackberry value
      } // ends the if for blackberry being a url 
    break; // break out and skip the rest if we've had a match on blackberry

    case (preg_match('/(palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent)); // we find palm os in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $palm; // mobile browser is either true or false depending on the setting of palm when calling the function
      if(substr($palm,0,4)=='http'){ // does the value of palm resemble a rul
        $mobileredirect = $palm; // set the mobile redirect url to the url value stored in the palm value
      } // ends the if for palm being a url 
    break; // break out and skip the rest if we've had a match on palm os

    case (preg_match('/(windows ce; ppc;|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent)); // we find windows mobile in the user agent - the i at the end makes it case insensitive
      $mobile_browser = $windows; // mobile browser is either true or false depending on the setting of windows when calling the function
      if(substr($windows,0,4)=='http'){ // does the value of windows resemble a rul
        $mobileredirect = $windows; // set the mobile redirect url to the url value stored in the windows value
      } // ends the if for windows being a url 
    break; // break out and skip the rest if we've had a match on windows

    case (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|pda|psp|treo)/i',$user_agent)); // check if any of the values listed create a match on the user agent - these are some of the most common terms used in agents to identify them as being mobile devices - the i at the end makes it case insensitive
      $mobile_browser = true; // set mobile browser to true
    break; // break out and skip the rest if we've preg_match on the user agent returned true 

    case ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0)); // is the device showing signs of support for text/vnd.wap.wml or application/vnd.wap.xhtml+xml
      $mobile_browser = true; // set mobile browser to true
    break; // break out and skip the rest if we've had a match on the content accept headers

    case (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE'])); // is the device giving us a HTTP_X_WAP_PROFILE or HTTP_PROFILE header - only mobile devices would do this
      $mobile_browser = true; // set mobile browser to true
    break; // break out and skip the final step if we've had a return true on the mobile specfic headers

    case (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','comp'=>'comp','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','tosh'=>'tosh','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',))); // check against a list of trimmed user agents to see if we find a match
      $mobile_browser = true; // set mobile browser to true
    break; // break even though it's the last statement in the switch so there's nothing to break away from but it seems better to include it than exclude it

  } // ends the switch 

  // tell adaptation services (transcoders and proxies) to not alter the content based on user agent as it's already being managed by this script
  header('Cache-Control: no-transform'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies
  header('Vary: User-Agent, Accept'); // http://mobiforge.com/developing/story/setting-http-headers-advise-transcoding-proxies

  // if redirect (either the value of the mobile or desktop redirect depending on the value of $mobile_browser) is true redirect else we return the status of $mobile_browser
  if($redirect = ($mobile_browser==true) ? $mobileredirect : $desktopredirect){
    header('Location: '.$redirect); // redirect to the right url for this device
    exit;
  }else{ 
    return $mobile_browser; // will return either true or false 
  }

} // ends function mobile_device_detect

?>