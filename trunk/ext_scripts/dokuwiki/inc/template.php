<?php
/**
 * DokuWiki template functions
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

  if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../').'/');
  require_once(DOKU_CONF.'dokuwiki.php');

/**
 * Returns the path to the given template, uses
 * default one if the custom version doesn't exist.
 * Also enables gzip compression if configured.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function template($tpl){
  global $conf;

  if(@is_readable(DOKU_INC.'lib/tpl/'.$conf['template'].'/'.$tpl))
    return DOKU_INC.'lib/tpl/'.$conf['template'].'/'.$tpl;

  return DOKU_INC.'lib/tpl/default/'.$tpl;
}

/**
 * Print the content
 *
 * This function is used for printing all the usual content
 * (defined by the global $ACT var) by calling the appropriate
 * outputfunction(s) from html.php
 *
 * Everything that doesn't use the main template file isn't
 * handled by this function. ACL stuff is not done here either.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_content() {
  global $ACT;

  ob_start();

  trigger_event('TPL_ACT_RENDER',$ACT,'tpl_content_core');

  $html_output = ob_get_clean();

  trigger_event('TPL_CONTENT_DISPLAY',$html_output,'ptln');
}

function tpl_content_core(){
  global $ACT;
  global $TEXT;
  global $PRE;
  global $SUF;
  global $SUM;
  global $IDX;

  switch($ACT){
    case 'show':
      html_show();
      break;
    case 'preview':
      html_edit($TEXT);
      html_show($TEXT);
      break;
    case 'recover':
      html_edit($TEXT);
      break;
    case 'edit':
      html_edit();
      break;
    case 'draft':
      html_draft();
      break;
    case 'wordblock':
      html_edit($TEXT,'wordblock');
      break;
    case 'search':
      html_search();
      break;
    case 'revisions':
      $first = is_numeric($_REQUEST['first']) ? intval($_REQUEST['first']) : 0;
      html_revisions($first);
      break;
    case 'diff':
      html_diff();
      break;
    case 'recent':
      $first = is_numeric($_REQUEST['first']) ? intval($_REQUEST['first']) : 0;
      html_recent($first);
      break;
    case 'index':
      html_index($IDX); #FIXME can this be pulled from globals? is it sanitized correctly?
      break;
    case 'backlink':
      html_backlinks();
      break;
    case 'conflict':
      html_conflict(con($PRE,$TEXT,$SUF),$SUM);
      html_diff(con($PRE,$TEXT,$SUF),false);
      break;
    case 'locked':
      html_locked();
      break;
    case 'login':
      html_login();
      break;
    case 'register':
      html_register();
      break;
    case 'resendpwd':
      html_resendpwd();
      break;
    case 'denied':
      print p_locale_xhtml('denied');
      break;
    case 'profile' :
      html_updateprofile();
      break;
    case 'admin':
      tpl_admin();
      break;
    default:
      $evt = new Doku_Event('TPL_ACT_UNKNOWN',$ACT);
      if ($evt->advise_before())
        msg("Failed to handle command: ".hsc($ACT),-1);
      $evt->advise_after();
      unset($evt);
  }
}

/**
 * Handle the admin page contents
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_admin(){

    $plugin = NULL;
    if (!empty($_REQUEST['page'])) {
        $pluginlist = plugin_list('admin');

        if (in_array($_REQUEST['page'], $pluginlist)) {

          // attempt to load the plugin
          $plugin =& plugin_load('admin',$_REQUEST['page']);
        }
    }

    if ($plugin !== NULL)
        $plugin->html();
    else
        html_admin();
}

/**
 * Print the correct HTML meta headers
 *
 * This has to go into the head section of your template.
 *
 * @triggers TPL_METAHEADER_OUTPUT
 * @param  boolean $alt Should feeds and alternative format links be added?
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_metaheaders($alt=true){
  global $ID;
  global $REV;
  global $INFO;
  global $ACT;
  global $lang;
  global $conf;
  $it=2;

  // prepare the head array
  $head = array();


  // the usual stuff
  $head['meta'][] = array( 'name'=>'generator', 'content'=>'DokuWiki '.getVersion() );
  $head['link'][] = array( 'rel'=>'start', 'href'=>DOKU_BASE );
  $head['link'][] = array( 'rel'=>'contents', 'href'=> wl($ID,'do=index',false,'&'),
                           'title'=>$lang['btn_index'] );

  if($alt){
    $head['link'][] = array( 'rel'=>'alternate', 'type'=>'application/rss+xml',
                             'title'=>'Recent Changes', 'href'=>DOKU_BASE.'feed.php');
    $head['link'][] = array( 'rel'=>'alternate', 'type'=>'application/rss+xml',
                             'title'=>'Current Namespace',
                             'href'=>DOKU_BASE.'feed.php?mode=list&ns='.$INFO['namespace']);
    $head['link'][] = array( 'rel'=>'alternate', 'type'=>'text/html', 'title'=>'Plain HTML',
                             'href'=>exportlink($ID, 'xhtml', '', false, '&'));
    $head['link'][] = array( 'rel'=>'alternate', 'type'=>'text/plain', 'title'=>'Wiki Markup',
                             'href'=>exportlink($ID, 'raw', '', false, '&'));
  }

  // setup robot tags apropriate for different modes
  if( ($ACT=='show' || $ACT=='export_xhtml') && !$REV){
    if($INFO['exists']){
      //delay indexing:
      if((time() - $INFO['lastmod']) >= $conf['indexdelay']){
        $head['meta'][] = array( 'name'=>'robots', 'content'=>'index,follow');
      }else{
        $head['meta'][] = array( 'name'=>'robots', 'content'=>'noindex,nofollow');
      }
    }else{
      $head['meta'][] = array( 'name'=>'robots', 'content'=>'noindex,follow');
    }
  }elseif(defined('DOKU_MEDIADETAIL')){
    $head['meta'][] = array( 'name'=>'robots', 'content'=>'index,follow');
  }else{
    $head['meta'][] = array( 'name'=>'robots', 'content'=>'noindex,nofollow');
  }

  // set metadata
  if($ACT == 'show' || $ACT=='export_xhtml'){
    // date of modification
    if($REV){
      $head['meta'][] = array( 'name'=>'date', 'content'=>date('Y-m-d\TH:i:sO',$REV));
    }else{
      $head['meta'][] = array( 'name'=>'date', 'content'=>date('Y-m-d\TH:i:sO',$INFO['lastmod']));
    }

    // keywords (explicit or implicit)
    if(!empty($INFO['meta']['subject'])){
      $head['meta'][] = array( 'name'=>'keywords', 'content'=>join(',',$INFO['meta']['subject']));
    }else{
      $head['meta'][] = array( 'name'=>'keywords', 'content'=>str_replace(':',',',$ID));
    }
  }

  // load stylesheets
  $head['link'][] = array('rel'=>'stylesheet', 'media'=>'screen', 'type'=>'text/css',
                          'href'=>DOKU_BASE.'lib/exe/css.php');
  $head['link'][] = array('rel'=>'stylesheet', 'media'=>'print', 'type'=>'text/css',
                          'href'=>DOKU_BASE.'lib/exe/css.php?print=1');

  // load javascript
  $js_edit  = ($ACT=='edit' || $ACT=='preview' || $ACT=='recover' || $ACT=='wordblock' ) ? 1 : 0;
  $js_write = ($INFO['writable']) ? 1 : 0;
  if(defined('DOKU_MEDIAMANAGER')){
    $js_edit  = 1;
    $js_write = 0;
  }
  if(($js_edit && $js_write) || defined('DOKU_MEDIAMANAGER')){
    $script = "NS='".$INFO['namespace']."';";
    if($conf['useacl'] && $_SERVER['REMOTE_USER']){
      require_once(DOKU_INC.'inc/toolbar.php');
      $script .= "SIG='".toolbar_signature()."';";
    }
    $head['script'][] = array( 'type'=>'text/javascript', 'charset'=>'utf-8',
                               '_data'=> $script);
  }
  $head['script'][] = array( 'type'=>'text/javascript', 'charset'=>'utf-8', '_data'=>'',
                             'src'=>DOKU_BASE.'lib/exe/js.php?edit='.$js_edit.'&write='.$js_write);

  // trigger event here
  trigger_event('TPL_METAHEADER_OUTPUT',$head,'_tpl_metaheaders_action',true);
}

/**
 * prints the array build by tpl_metaheaders
 *
 * $data is an array of different header tags. Each tag can have multiple
 * instances. Attributes are given as key value pairs. Values will be HTML
 * encoded automatically so they should be provided as is in the $data array.
 *
 * For tags having a body attribute specify the the body data in the special
 * attribute '_data'
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function _tpl_metaheaders_action($data){
  foreach($data as $tag => $inst){
    foreach($inst as $attr){
      echo '<',$tag,' ',buildAttributes($attr);
      if(isset($attr['_data'])){
        echo '>',htmlspecialchars($attr['_data']),'</',$tag,'>';
      }else{
        echo '/>';
      }
      echo "\n";
    }
  }
}

/**
 * Print a link
 *
 * Just builds a link.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_link($url,$name,$more=''){
  print '<a href="'.$url.'" ';
  if ($more) print ' '.$more;
  print ">$name</a>";
}

/**
 * Prints a link to a WikiPage
 *
 * Wrapper around html_wikilink
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_pagelink($id,$name=NULL){
  print html_wikilink($id,$name);
}

/**
 * get the parent page
 *
 * Tries to find out which page is parent.
 * returns false if none is available
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_getparent($id){
  global $conf;
  $parent = getNS($id).':';
  resolve_pageid('',$parent,$exists);
  if($parent == $id) {
    $pos = strrpos (getNS($id),':');
    $parent = substr($parent,0,$pos).':';
    resolve_pageid('',$parent,$exists);
    if($parent == $id) return false;
  }
  return $parent;
}

/**
 * Print one of the buttons
 *
 * Available Buttons are
 *
 *  edit        - edit/create/show/draft button
 *  history     - old revisions
 *  recent      - recent changes
 *  login       - login/logout button - if ACL enabled
 *  profile     - user profile button (if logged in)
 *  index       - The index
 *  admin       - admin page - if enough rights
 *  top         - a back to top button
 *  back        - a back to parent button - if available
 *  backtomedia - returns to the mediafile upload dialog
 *                after references have been displayed
 *  backlink    - links to the list of backlinks
 *  subscription- subscribe/unsubscribe button
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
 */
function tpl_button($type){
  global $ACT;
  global $ID;
  global $REV;
  global $NS;
  global $INFO;
  global $conf;
  global $auth;

  // check disabled actions and fix the badly named ones
  $ctype = $type;
  if($type == 'history') $ctype='revisions';
  if(!actionOK($ctype)) return;

  switch($type){
    case 'edit':
      #most complicated type - we need to decide on current action
      if($ACT == 'show' || $ACT == 'search'){
        if($INFO['writable']){
          if(!empty($INFO['draft'])){
            echo html_btn('draft',$ID,'e',array('do' => 'draft'),'post');
          }else{
            if($INFO['exists']){
              echo html_btn('edit',$ID,'e',array('do' => 'edit','rev' => $REV),'post');
            }else{
              echo html_btn('create',$ID,'e',array('do' => 'edit','rev' => $REV),'post');
            }
          }
        }else{
          if(!actionOK('source')) return false; //pseudo action
          echo html_btn('source',$ID,'v',array('do' => 'edit','rev' => $REV),'post');
        }
      }else{
          echo html_btn('show',$ID,'v',array('do' => 'show'));
      }
      break;
    case 'history':
      print html_btn('revs',$ID,'o',array('do' => 'revisions'));
      break;
    case 'recent':
      print html_btn('recent','','r',array('do' => 'recent'));
      break;
    case 'index':
      print html_btn('index',$ID,'x',array('do' => 'index'));
      break;
    case 'back':
      if ($parent = tpl_getparent($ID)) {
        print html_btn('back',$parent,'b',array('do' => 'show'));
      }
      break;
    case 'top':
      print html_topbtn();
      break;
    case 'login':
      if($conf['useacl']){
        if($_SERVER['REMOTE_USER']){
          print html_btn('logout',$ID,'',array('do' => 'logout',));
        }else{
          print html_btn('login',$ID,'',array('do' => 'login'));
        }
      }
      break;
    case 'admin':
      if($INFO['perm'] == AUTH_ADMIN)
        print html_btn('admin',$ID,'',array('do' => 'admin'));
      break;
    case 'backtomedia':
      print html_backtomedia_button(array('ns' => $NS),'b');
      break;
    case 'subscription':
      if($conf['useacl'] && $ACT == 'show' && $conf['subscribers'] == 1){
        if($_SERVER['REMOTE_USER']){
          if($INFO['subscribed']){
            print html_btn('unsubscribe',$ID,'',array('do' => 'unsubscribe',));
          } else {
            print html_btn('subscribe',$ID,'',array('do' => 'subscribe',));
          }
        }
      }
      break;
    case 'backlink':
      print html_btn('backlink',$ID,'',array('do' => 'backlink'));
      break;
    case 'profile':
      if($conf['useacl'] && $_SERVER['REMOTE_USER'] &&
         $auth->canDo('Profile') && ($ACT!='profile')){
        print html_btn('profile',$ID,'',array('do' => 'profile'));
      }
      break;
    default:
      print '[unknown button type]';
  }
}

/**
 * Like the action buttons but links
 *
 * Available links are
 *
 *  edit    - edit/create/show link
 *  history - old revisions
 *  recent  - recent changes
 *  login   - login/logout link - if ACL enabled
 *  profile - user profile link (if logged in)
 *  index   - The index
 *  admin   - admin page - if enough rights
 *  top     - a back to top link
 *  back    - a back to parent link - if available
 *  backlink - links to the list of backlinks
 *  subscribe/subscription - subscribe/unsubscribe link
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
 * @see    tpl_button
 */
function tpl_actionlink($type,$pre='',$suf=''){
  global $ID;
  global $INFO;
  global $REV;
  global $ACT;
  global $conf;
  global $lang;
  global $auth;

  // check disabled actions and fix the badly named ones
  $ctype = $type;
  if($type == 'history') $ctype='revisions';
  if(!actionOK($ctype)) return;

  switch($type){
    case 'edit':
      #most complicated type - we need to decide on current action
      if($ACT == 'show' || $ACT == 'search'){
        if($INFO['writable']){
          if(!empty($INFO['draft'])) {
            tpl_link(wl($ID,'do=draft'),
                       $pre.$lang['btn_draft'].$suf,
                       'class="action edit" acceskey="e" rel="nofollow"');
          } else {
            if($INFO['exists']){
              tpl_link(wl($ID,'do=edit&amp;rev='.$REV),
                       $pre.$lang['btn_edit'].$suf,
                       'class="action edit" accesskey="e" rel="nofollow"');
            }else{
              tpl_link(wl($ID,'do=edit&amp;rev='.$REV),
                       $pre.$lang['btn_create'].$suf,
                       'class="action create" accesskey="e" rel="nofollow"');
            }
          }
        }else{
          if(!actionOK('source')) return false; //pseudo action
          tpl_link(wl($ID,'do=edit&amp;rev='.$REV),
                   $pre.$lang['btn_source'].$suf,
                   'class="action source" accesskey="v" rel="nofollow"');
        }
      }else{
          tpl_link(wl($ID,'do=show'),
                   $pre.$lang['btn_show'].$suf,
                   'class="action show" accesskey="v" rel="nofollow"');
      }
      return true;
    case 'history':
      tpl_link(wl($ID,'do=revisions'),$pre.$lang['btn_revs'].$suf,'class="action revisions" accesskey="o"');
      return true;
    case 'recent':
      tpl_link(wl($ID,'do=recent'),$pre.$lang['btn_recent'].$suf,'class="action recent" accesskey="r"');
      return true;
    case 'index':
      tpl_link(wl($ID,'do=index'),$pre.$lang['btn_index'].$suf,'class="action index" accesskey="x"');
      return true;
    case 'top':
      print '<a href="#dokuwiki__top" class="action top" accesskey="x">'.$pre.$lang['btn_top'].$suf.'</a>';
      return true;
    case 'back':
      if ($parent = tpl_getparent($ID)) {
        tpl_link(wl($parent,'do=show'),$pre.$lang['btn_back'].$suf,'class="action back" accesskey="b"');
        return true;
      }
      return false;
    case 'login':
      if($conf['useacl']){
        if($_SERVER['REMOTE_USER']){
          tpl_link(wl($ID,'do=logout'),$pre.$lang['btn_logout'].$suf,'class="action logout"');
        }else{
          tpl_link(wl($ID,'do=login'),$pre.$lang['btn_login'].$suf,'class="action logout"');
        }
        return true;
      }
      return false;
    case 'admin':
      if($INFO['perm'] == AUTH_ADMIN){
        tpl_link(wl($ID,'do=admin'),$pre.$lang['btn_admin'].$suf,'class="action admin"');
        return true;
      }
      return false;
   case 'subscribe':
   case 'subscription':
      if($conf['useacl'] && $ACT == 'show' && $conf['subscribers'] == 1){
        if($_SERVER['REMOTE_USER']){
          if($INFO['subscribed']) {
            tpl_link(wl($ID,'do=unsubscribe'),$pre.$lang['btn_unsubscribe'].$suf,'class="action unsubscribe"');
          } else {
            tpl_link(wl($ID,'do=subscribe'),$pre.$lang['btn_subscribe'].$suf,'class="action subscribe"');
          }
          return true;
        }
      }
      return false;
    case 'backlink':
      tpl_link(wl($ID,'do=backlink'),$pre.$lang['btn_backlink'].$suf, 'class="action backlink"');
      return true;
    case 'profile':
      if($conf['useacl'] && $_SERVER['REMOTE_USER'] &&
         $auth->canDo('Profile') && ($ACT!='profile')){
        tpl_link(wl($ID,'do=profile'),$pre.$lang['btn_profile'].$suf, 'class="action profile"');
        return true;
      }
      return false;
    default:
      print '[unknown link type]';
      return true;
  }
}

/**
 * Print the search form
 *
 * If the first parameter is given a div with the ID 'qsearch_out' will
 * be added which instructs the ajax pagequicksearch to kick in and place
 * its output into this div. The second parameter controls the propritary
 * attribute autocomplete. If set to false this attribute will be set with an
 * value of "off" to instruct the browser to disable it's own built in
 * autocompletion feature (MSIE and Firefox)
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_searchform($ajax=true,$autocomplete=true){
  global $lang;
  global $ACT;

  print '<form action="'.wl().'" accept-charset="utf-8" class="search" id="dw__search"><div class="no">';
  print '<input type="hidden" name="do" value="search" />';
  print '<input type="text" ';
  if($ACT == 'search') print 'value="'.htmlspecialchars($_REQUEST['id']).'" ';
  if(!$autocomplete) print 'autocomplete="off" ';
  print 'id="qsearch__in" accesskey="f" name="id" class="edit" title="[ALT+F]" />';
  print '<input type="submit" value="'.$lang['btn_search'].'" class="button" title="'.$lang['btn_search'].'" />';
  if($ajax) print '<div id="qsearch__out" class="ajax_qsearch JSpopup"></div>';
  print '</div></form>';
}

/**
 * Print the breadcrumbs trace
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_breadcrumbs(){
  global $lang;
  global $conf;

  //check if enabled
  if(!$conf['breadcrumbs']) return;

  $crumbs = breadcrumbs(); //setup crumb trace

  //reverse crumborder in right-to-left mode
  if($lang['direction'] == 'rtl') $crumbs = array_reverse($crumbs,true);

  //render crumbs, highlight the last one
  print $lang['breadcrumb'].':';
  $last = count($crumbs);
  $i = 0;
  foreach ($crumbs as $id => $name){
    $i++;
    print ' <span class="bcsep">&raquo;</span> ';
    if ($i == $last) print '<span class="curid">';
    tpl_link(wl($id),$name,'class="breadcrumbs" title="'.$id.'"');
    if ($i == $last) print '</span>';
  }
}

/**
 * Hierarchical breadcrumbs
 *
 * This code was suggested as replacement for the usual breadcrumbs.
 * It only makes sense with a deep site structure.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Nigel McNie <oracle.shinoda@gmail.com>
 * @author Sean Coates <sean@caedmon.net>
 * @link   http://wiki.splitbrain.org/wiki:tipsandtricks:hierarchicalbreadcrumbs
 * @todo   May behave strangely in RTL languages
 */
function tpl_youarehere($sep=' &raquo; '){
  global $conf;
  global $ID;
  global $lang;

  // check if enabled
  if(!$conf['youarehere']) return;

  $parts = explode(':', $ID);
  $count = count($parts);

  echo $lang['youarehere'].': ';

  // always print the startpage
  $title = p_get_first_heading($conf['start']);
  if(!$title) $title = $conf['start'];
  tpl_link(wl($conf['start']),$title,'title="'.$conf['start'].'"');

  // print intermediate namespace links
  $part = '';
  for($i=0; $i<$count - 1; $i++){
    $part .= $parts[$i].':';
    $page = $part;
    resolve_pageid('',$page,$exists);
    if ($page == $conf['start']) continue; // Skip startpage 

    // output
    echo $sep;
    if($exists){
      $title = p_get_first_heading($page);
      if(!$title) $title = $parts[$i];
      tpl_link(wl($page),$title,'title="'.$page.'"');
    }else{
      tpl_link(wl($page),$parts[$i],'title="'.$page.'" class="wikilink2"');
    }
  }

  // print current page, skipping start page, skipping for namespace index
  if(isset($page) && $page==$part.$parts[$i]) return;
  $page = $part.$parts[$i];
  if($page == $conf['start']) return;
  echo $sep;
  if(@file_exists(wikiFN($page))){
    $title = p_get_first_heading($page);
    if(!$title) $title = $parts[$i];
    tpl_link(wl($page),$title,'title="'.$page.'"');
  }else{
    tpl_link(wl($page),$parts[$i],'title="'.$page.'" class="wikilink2"');
  }
}

/**
 * Print info if the user is logged in
 * and show full name in that case
 *
 * Could be enhanced with a profile link in future?
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_userinfo(){
  global $lang;
  global $INFO;
  if($_SERVER['REMOTE_USER'])
    print $lang['loggedinas'].': '.$INFO['userinfo']['name'];
}

/**
 * Print some info about the current page
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_pageinfo(){
  global $conf;
  global $lang;
  global $INFO;
  global $REV;

  // prepare date and path
  $fn = $INFO['filepath'];
  if(!$conf['fullpath']){
    if($REV){
      $fn = str_replace(realpath($conf['olddir']).DIRECTORY_SEPARATOR,'',$fn);
    }else{
      $fn = str_replace(realpath($conf['datadir']).DIRECTORY_SEPARATOR,'',$fn);
    }
  }
  $fn = utf8_decodeFN($fn);
  $date = date($conf['dformat'],$INFO['lastmod']);

  // print it
  if($INFO['exists']){
    print $fn;
    print ' &middot; ';
    print $lang['lastmod'];
    print ': ';
    print $date;
    if($INFO['editor']){
      print ' '.$lang['by'].' ';
      print $INFO['editor'];
    }
    if($INFO['locked']){
      print ' &middot; ';
      print $lang['lockedby'];
      print ': ';
      print $INFO['locked'];
    }
  }
}

/**
 * Prints or returns the name of the given page (current one if none given).
 *
 * If useheading is enabled this will use the first headline else
 * the given ID is used.
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_pagetitle($id=null, $ret=false){
  global $conf;
  if(is_null($id)){
    global $ID;
    $id = $ID;
  }

  $name = $id;
  if ($conf['useheading']) {
    $title = p_get_first_heading($id);
    if ($title) $name = $title;
  }

  if ($ret) {
      return hsc($name);
  } else {
      print hsc($name);
  }
}

/**
 * Returns the requested EXIF/IPTC tag from the current image
 *
 * If $tags is an array all given tags are tried until a
 * value is found. If no value is found $alt is returned.
 *
 * Which texts are known is defined in the functions _exifTagNames
 * and _iptcTagNames() in inc/jpeg.php (You need to prepend IPTC
 * to the names of the latter one)
 *
 * Only allowed in: detail.php
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_img_getTag($tags,$alt='',$src=null){
  // Init Exif Reader
  global $SRC;

  if(is_null($src)) $src = $SRC;

  static $meta = null;
  if(is_null($meta)) $meta = new JpegMeta($src);
  if($meta === false) return $alt;
  $info = $meta->getField($tags);
  if($info == false) return $alt;
  return $info;
}

/**
 * Prints the image with a link to the full sized version
 *
 * Only allowed in: detail.php
 */
function tpl_img($maxwidth=0,$maxheight=0){
  global $IMG;
  $w = tpl_img_getTag('File.Width');
  $h = tpl_img_getTag('File.Height');

  //resize to given max values
  $ratio = 1;
  if($w >= $h){
    if($maxwidth && $w >= $maxwidth){
      $ratio = $maxwidth/$w;
    }elseif($maxheight && $h > $maxheight){
      $ratio = $maxheight/$h;
    }
  }else{
    if($maxheight && $h >= $maxheight){
      $ratio = $maxheight/$h;
    }elseif($maxwidth && $w > $maxwidth){
      $ratio = $maxwidth/$w;
    }
  }
  if($ratio){
    $w = floor($ratio*$w);
    $h = floor($ratio*$h);
  }

  //prepare URLs
  $url=ml($IMG,array('cache'=>$_REQUEST['cache']));
  $src=ml($IMG,array('cache'=>$_REQUEST['cache'],'w'=>$w,'h'=>$h));

  //prepare attributes
  $alt=tpl_img_getTag('Simple.Title');
  $p = array();
  if($w) $p['width']  = $w;
  if($h) $p['height'] = $h;
         $p['class']  = 'img_detail';
  if($alt){
    $p['alt']   = $alt;
    $p['title'] = $alt;
  }else{
    $p['alt'] = '';
  }
  $p = buildAttributes($p);

  print '<a href="'.$url.'">';
  print '<img src="'.$src.'" '.$p.'/>';
  print '</a>';
}

/**
 * This function inserts a 1x1 pixel gif which in reality
 * is the inexer function.
 *
 * Should be called somewhere at the very end of the main.php
 * template
 */
function tpl_indexerWebBug(){
  global $ID;
  global $INFO;
  if(!$INFO['exists']) return;

  if(isHiddenPage($ID)) return; //no need to index hidden pages

  $p = array();
  $p['src']    = DOKU_BASE.'lib/exe/indexer.php?id='.rawurlencode($ID).
                 '&'.time();
  $p['width']  = 1;
  $p['height'] = 1;
  $p['alt']    = '';
  $att = buildAttributes($p);
  print "<img $att />";
}

// configuration methods
/**
 * tpl_getConf($id)
 *
 * use this function to access template configuration variables
 */
function tpl_getConf($id){
  global $conf;
  global $tpl_configloaded;

  $tpl = $conf['template'];

  if (!$tpl_configloaded){
    $tconf = tpl_loadConfig();
    if ($tconf !== false){
      foreach ($tconf as $key => $value){
        if (isset($conf['tpl'][$tpl][$key])) continue;
        $conf['tpl'][$tpl][$key] = $value;
      }
      $tpl_configloaded = true;
    }
  }

  return $conf['tpl'][$tpl][$id];
}

/**
 * tpl_loadConfig()
 * reads all template configuration variables
 * this function is automatically called by tpl_getConf()
 */
function tpl_loadConfig(){

  $file = DOKU_TPLINC.'/conf/default.php';
  $conf = array();

  if (!@file_exists($file)) return false;

  // load default config file
  include($file);

  return $conf;
}

/**
 * prints the "main content" in the mediamanger popup
 *
 * Depending on the user's actions this may be a list of
 * files in a namespace, the meta editing dialog or
 * a message of referencing pages
 *
 * Only allowed in mediamanager.php
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_mediaContent(){
  global $IMG;
  global $AUTH;
  global $INUSE;
  global $NS;
  global $JUMPTO;

  ptln('<div id="media__content">');
  if($_REQUEST['edit']){
    media_metaform($IMG,$AUTH);
  }elseif(is_array($INUSE)){
    media_filesinuse($INUSE,$IMG);
  }else{
    media_filelist($NS,$AUTH,$JUMPTO);
  }
  ptln('</div>');
}

/**
 * prints the namespace tree in the mediamanger popup
 *
 * Only allowed in mediamanager.php
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function tpl_mediaTree(){
  global $NS;

  ptln('<div id="media__tree">');
  media_nstree($NS);
  ptln('</div>');
}

//Setup VIM: ex: et ts=2 enc=utf-8 :
