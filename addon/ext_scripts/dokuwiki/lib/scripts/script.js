/**
 * Some of these scripts were taken from wikipedia.org and were modified for DokuWiki
 */

/**
 * Some browser detection
 */
var clientPC  = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko  = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1) &&
                (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('AppleWebKit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml  = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
if (clientPC.indexOf('opera')!=-1) {
    var is_opera = true;
    var is_opera_preseven = (window.opera && !document.childNodes);
    var is_opera_seven = (window.opera && document.childNodes);
}

/**
 * Rewrite the accesskey tooltips to be more browser and OS specific.
 *
 * Accesskey tooltips are still only a best-guess of what will work
 * on well known systems.
 *
 * @author Ben Coburn <btcoburn@silicodon.net>
 */
function updateAccessKeyTooltip() {
  // determin tooltip text (order matters)
  var tip = 'ALT+'; //default
  if (domLib_isMac) { tip = 'CTRL+'; }
  if (domLib_isOpera) { tip = 'SHIFT+ESC '; }
  // add other cases here...

  // do tooltip update
  if (tip=='ALT+') { return; }
  var exp = /\[ALT\+/i;
  var rep = '['+tip;
  var elements = domLib_getElementsByTagNames(['a', 'input', 'button']);
  for (var i=0; i<elements.length; i++) {
    if (elements[i].accessKey.length==1 && elements[i].title.length>0) {
      elements[i].title = elements[i].title.replace(exp, rep);
    }
  }
}

/**
 * Handy shortcut to document.getElementById
 *
 * This function was taken from the prototype library
 *
 * @link http://prototype.conio.net/
 */
function $() {
  var elements = new Array();

  for (var i = 0; i < arguments.length; i++) {
    var element = arguments[i];
    if (typeof element == 'string')
      element = document.getElementById(element);

    if (arguments.length == 1)
      return element;

    elements.push(element);
  }

  return elements;
}

/**
 * Simple function to check if a global var is defined
 *
 * @author Kae Verens
 * @link http://verens.com/archives/2005/07/25/isset-for-javascript/#comment-2835
 */
function isset(varname){
  return(typeof(window[varname])!='undefined');
}

/**
 * Select elements by their class name
 *
 * @author Dustin Diaz <dustin [at] dustindiaz [dot] com>
 * @link   http://www.dustindiaz.com/getelementsbyclass/
 */
function getElementsByClass(searchClass,node,tag) {
    var classElements = new Array();
    if ( node == null )
        node = document;
    if ( tag == null )
        tag = '*';
    var els = node.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
    for (i = 0, j = 0; i < elsLen; i++) {
        if ( pattern.test(els[i].className) ) {
            classElements[j] = els[i];
            j++;
        }
    }
    return classElements;
}

/**
 * Get the X offset of the top left corner of the given object
 *
 * @link http://www.quirksmode.org/index.html?/js/findpos.html
 */
function findPosX(object){
  var curleft = 0;
  var obj = $(object);
  if (obj.offsetParent){
    while (obj.offsetParent){
      curleft += obj.offsetLeft;
      obj = obj.offsetParent;
    }
  }
  else if (obj.x){
    curleft += obj.x;
  }
  return curleft;
} //end findPosX function

/**
 * Get the Y offset of the top left corner of the given object
 *
 * @link http://www.quirksmode.org/index.html?/js/findpos.html
 */
function findPosY(object){
  var curtop = 0;
  var obj = $(object);
  if (obj.offsetParent){
    while (obj.offsetParent){
      curtop += obj.offsetTop;
      obj = obj.offsetParent;
    }
  }
  else if (obj.y){
    curtop += obj.y;
  }
  return curtop;
} //end findPosY function

/**
 * Escape special chars in JavaScript
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function jsEscape(text){
    var re=new RegExp("\\\\","g");
    text=text.replace(re,"\\\\");
    re=new RegExp("'","g");
    text=text.replace(re,"\\'");
    re=new RegExp('"',"g");
    text=text.replace(re,'&quot;');
    re=new RegExp("\\\\\\\\n","g");
    text=text.replace(re,"\\n");
    return text;
}

/**
 * This function escapes some special chars
 * @deprecated by above function
 */
function escapeQuotes(text) {
  var re=new RegExp("'","g");
  text=text.replace(re,"\\'");
  re=new RegExp('"',"g");
  text=text.replace(re,'&quot;');
  re=new RegExp("\\n","g");
  text=text.replace(re,"\\n");
  return text;
}

/**
 * Adds a node as the first childenode to the given parent
 *
 * @see appendChild()
 */
function prependChild(parent,element) {
    if(!parent.firstChild){
        parent.appendChild(element);
    }else{
        parent.insertBefore(element,parent.firstChild);
    }
}

/**
 * Prints a animated gif to show the search is performed
 *
 * Because we need to modify the DOM here before the document is loaded
 * and parsed completely we have to rely on document.write()
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function showLoadBar(){

  document.write('<img src="'+DOKU_BASE+'lib/images/loading.gif" '+
                 'width="150" height="12" alt="..." />');

  /* this does not work reliable in IE
  obj = $(id);

  if(obj){
    obj.innerHTML = '<img src="'+DOKU_BASE+'lib/images/loading.gif" '+
                    'width="150" height="12" alt="..." />';
    obj.style.display="block";
  }
  */
}

/**
 * Disables the animated gif to show the search is done
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function hideLoadBar(id){
  obj = $(id);
  if(obj) obj.style.display="none";
}

/**
 * Adds the toggle switch to the TOC
 */
function addTocToggle() {
    if(!document.getElementById) return;
    var header = $('toc__header');
  if(!header) return;

  var showimg     = document.createElement('img');
    showimg.id      = 'toc__show';
  showimg.src     = DOKU_BASE+'lib/images/arrow_down.gif';
  showimg.alt     = '+';
    showimg.onclick = toggleToc;
  showimg.style.display = 'none';

    var hideimg     = document.createElement('img');
    hideimg.id      = 'toc__hide';
  hideimg.src     = DOKU_BASE+'lib/images/arrow_up.gif';
  hideimg.alt     = '-';
    hideimg.onclick = toggleToc;

  prependChild(header,showimg);
  prependChild(header,hideimg);
}

/**
 * This toggles the visibility of the Table of Contents
 */
function toggleToc() {
  var toc = $('toc__inside');
  var showimg = $('toc__show');
  var hideimg = $('toc__hide');
  if(toc.style.display == 'none') {
    toc.style.display      = '';
    hideimg.style.display = '';
    showimg.style.display = 'none';
  } else {
    toc.style.display      = 'none';
    hideimg.style.display = 'none';
    showimg.style.display = '';
  }
}

/*
 * This enables/disables checkboxes for acl-administration
 *
 * @author Frank Schubert <frank@schokilade.de>
 */
function checkAclLevel(){
  if(document.getElementById) {
    var scope = $('acl_scope').value;

    //check for namespace
    if( (scope.indexOf(":*") > 0) || (scope == "*") ){
      document.getElementsByName('acl_checkbox[4]')[0].disabled=false;
      document.getElementsByName('acl_checkbox[8]')[0].disabled=false;
    }else{
      document.getElementsByName('acl_checkbox[4]')[0].checked=false;
      document.getElementsByName('acl_checkbox[8]')[0].checked=false;

      document.getElementsByName('acl_checkbox[4]')[0].disabled=true;
      document.getElementsByName('acl_checkbox[8]')[0].disabled=true;
    }
  }
}

/**
 * insitu footnote addition
 *
 * provide a wrapper for domTT javascript library
 * this function is placed in the onmouseover event of footnote references in the main page
 *
 * @author Chris Smith <chris [at] jalakai [dot] co [dot] uk>
 */
var currentFootnote = 0;
function fnt(id, e, evt) {

    if (currentFootnote && id != currentFootnote) {
        domTT_close($('insitu__fn'+currentFootnote));
    }

    // does the footnote tooltip already exist?
    var fnote = $('insitu__fn'+id);
    var footnote;
    if (!fnote) {
        // if not create it...

        // locate the footnote anchor element
        var a = $( "fn__"+id );
        if (!a){ return; }

        // anchor parent is the footnote container, get its innerHTML
        footnote = new String (a.parentNode.innerHTML);

        // strip the leading footnote anchors and their comma separators
        footnote = footnote.replace(/<a\s.*?href=\".*\#fnt__\d+\".*?<\/a>/gi, '');
        footnote = footnote.replace(/^\s+(,\s+)+/,'');

        // prefix ids on any elements with "insitu__" to ensure they remain unique
        footnote = footnote.replace(/\bid=\"(.*?)\"/gi,'id="insitu__$1');
    } else {
        footnote = new String(fnt.innerHTML);
    }

    // activate the tooltip
    domTT_activate(e, evt, 'content', footnote, 'type', 'velcro', 'id', 'insitu__fn'+id, 'styleClass', 'insitu-footnote JSpopup dokuwiki', 'maxWidth', document.body.offsetWidth*0.4);
    currentFootnote = id;
}


/**
 * Add the edit window size controls
 */
function initSizeCtl(ctlid,edid){
    if(!document.getElementById){ return; }

    var ctl      = $(ctlid);
    var textarea = $(edid);
    if(!ctl || !textarea) return;

    var hgt = DokuCookie.getValue('sizeCtl');
    if(hgt){
      textarea.style.height = hgt;
    }else{
      textarea.style.height = '300px';
    }

    var l = document.createElement('img');
    var s = document.createElement('img');
    var w = document.createElement('img');
    l.src = DOKU_BASE+'lib/images/larger.gif';
    s.src = DOKU_BASE+'lib/images/smaller.gif';
    w.src = DOKU_BASE+'lib/images/wrap.gif';
    addEvent(l,'click',function(){sizeCtl(edid,100);});
    addEvent(s,'click',function(){sizeCtl(edid,-100);});
    addEvent(w,'click',function(){toggleWrap(edid);});
    ctl.appendChild(l);
    ctl.appendChild(s);
    ctl.appendChild(w);
}

/**
 * This sets the vertical size of the editbox
 */
function sizeCtl(edid,val){
  var textarea = $(edid);
  var height = parseInt(textarea.style.height.substr(0,textarea.style.height.length-2));
  height += val;
  textarea.style.height = height+'px';

  DokuCookie.setValue('sizeCtl',textarea.style.height);
}

/**
 * Toggle the wrapping mode of a textarea
 *
 * @author Fluffy Convict <fluffyconvict@hotmail.com>
 * @link   http://news.hping.org/comp.lang.javascript.archive/12265.html
 * @author <shutdown@flashmail.com>
 * @link   https://bugzilla.mozilla.org/show_bug.cgi?id=302710#c2
 */
function toggleWrap(edid){
    var txtarea = $(edid);
    var wrap = txtarea.getAttribute('wrap');
    if(wrap && wrap.toLowerCase() == 'off'){
        txtarea.setAttribute('wrap', 'soft');
    }else{
        txtarea.setAttribute('wrap', 'off');
    }
    // Fix display for mozilla
    var parNod = txtarea.parentNode;
    var nxtSib = txtarea.nextSibling;
    parNod.removeChild(txtarea);
    parNod.insertBefore(txtarea, nxtSib);
}

/**
 * Handler to close all open Popups
 */
function closePopups(){
  if(!document.getElementById){ return; }

  var divs = document.getElementsByTagName('div');
  for(var i=0; i < divs.length; i++){
    if(divs[i].className.indexOf('JSpopup') != -1){
            divs[i].style.display = 'none';
    }
  }
}

/**
 * Looks for an element with the ID scroll__here at scrolls to it
 */
function scrollToMarker(){
    var obj = $('scroll__here');
    if(obj) obj.scrollIntoView();
}

/**
 * Looks for an element with the ID focus__this at sets focus to it
 */
function focusMarker(){
    var obj = $('focus__this');
    if(obj) obj.focus();
}

/**
 * Remove messages
 */
function cleanMsgArea(){
    var elems = getElementsByClass('(success|info|error)',document,'div');
    if(elems){
        for(var i=0; i<elems.length; i++){
            elems[i].style.display = 'none';
        }
    }
}
