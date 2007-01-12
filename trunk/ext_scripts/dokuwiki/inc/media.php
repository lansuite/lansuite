<?php
/**
 * All output and handler function needed for the media management popup
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../').'/');
if(!defined('NL')) define('NL',"\n");

require_once(DOKU_INC.'inc/html.php');
require_once(DOKU_INC.'inc/search.php');
require_once(DOKU_INC.'inc/JpegMeta.php');

/**
 * Lists pages which currently use a media file selected for deletion
 *
 * References uses the same visual as search results and share
 * their CSS tags except pagenames won't be links.
 *
 * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
 */
function media_filesinuse($data,$id){
    global $lang;
    echo '<h1>'.$lang['reference'].' <code>'.hsc(noNS($id)).'</code></h1>';
    echo '<p>'.hsc($lang['ref_inuse']).'</p>';

    $hidden=0; //count of hits without read permission
    usort($data,'sort_search_fulltext');
    foreach($data as $row){
        if(auth_quickaclcheck($row['id']) >= AUTH_READ){
            echo '<div class="search_result">';
            echo '<span class="mediaref_ref">'.$row['id'].'</span>';
            echo ': <span class="search_cnt">'.$row['count'].' '.$lang['hits'].'</span><br />';
            echo '<div class="search_snippet">'.$row['snippet'].'</div>';
            echo '</div>';
        }else
        $hidden++;
    }
    if ($hidden){
      print '<div class="mediaref_hidden">'.$lang['ref_hidden'].'</div>';
    }
}

/**
 * Handles the saving of image meta data
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_metasave($id,$auth,$data){
    if($auth < AUTH_UPLOAD) return false;
    global $lang;
    $src = mediaFN($id);

    $meta = new JpegMeta($src);
    $meta->_parseAll();

    foreach($data as $key => $val){
        $val=trim($val);
        if(empty($val)){
            $meta->deleteField($key);
        }else{
            $meta->setField($key,$val);
        }
    }

    if($meta->save()){
        msg($lang['metasaveok'],1);
        return $id;
    }else{
        msg($lang['metasaveerr'],-1);
        return false;
    }
}

/**
 * Display the form to edit image meta data
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_metaform($id,$auth){
    if($auth < AUTH_UPLOAD) return false;
    global $lang;

    // load the field descriptions
    static $fields = null;
    if(is_null($fields)){
        include(DOKU_CONF.'mediameta.php');
        if(@file_exists(DOKU_CONF.'mediameta.local.php')){
            include(DOKU_CONF.'mediameta.local.php');
        }
    }

    $src = mediaFN($id);

    // output
    echo '<h1>'.hsc(noNS($id)).'</h1>'.NL;
    echo '<form action="'.DOKU_BASE.'lib/exe/mediamanager.php" accept-charset="utf-8" method="post" class="meta">'.NL;
    foreach($fields as $key => $field){
        // get current value
        $tags = array($field[0]);
        if(is_array($field[3])) $tags = array_merge($tags,$field[3]);
        $value = tpl_img_getTag($tags,'',$src);

        // prepare attributes
        $p = array();
        $p['class'] = 'edit';
        $p['id']    = 'meta__'.$key;
        $p['name']  = 'meta['.$field[0].']';

        // put label
        echo '<div class="metafield">';
        echo '<label for="meta__'.$key.'">';
        echo ($lang[$field[1]]) ? $lang[$field[1]] : $field[1];
        echo ':</label>';

        // put input field
        if($field[2] == 'text'){
            $p['value'] = $value;
            $p['type']  = 'text';
            $att = buildAttributes($p);
            echo "<input $att/>".NL;
        }else{
            $att = buildAttributes($p);
            echo "<textarea $att rows=\"6\" cols=\"50\">".formText($value).'</textarea>'.NL;
        }
        echo '</div>'.NL;
    }
    echo '<div class="buttons">'.NL;
    echo '<input type="hidden" name="img" value="'.hsc($id).'" />'.NL;
    echo '<input name="do[save]" type="submit" value="'.$lang['btn_save'].
         '" title="ALT+S" accesskey="s" class="button" />'.NL;
    echo '<input name="do[cancel]" type="submit" value="'.$lang['btn_cancel'].
         '" title="ALT+C" accesskey="c" class="button" />'.NL;
    echo '</div>'.NL;
    echo '</form>'.NL;
}

/**
 * Handles media file deletions
 *
 * If configured, checks for media references before deletion
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @return mixed false on error, true on delete or array with refs
 */
function media_delete($id,$auth){
    if($auth < AUTH_DELETE) return false;
    global $conf;
    global $lang;

    $mediareferences = array();
    if($conf['refcheck']){
        search($mediareferences,$conf['datadir'],'search_reference',array('query' => $id));
    }

    if(!count($mediareferences)){
        $file = mediaFN($id);
        if(@unlink($file)){
            msg(str_replace('%s',noNS($id),$lang['deletesucc']),1);
            io_sweepNS($id,'mediadir');
            return true;
        }
        //something went wrong
        msg(str_replace('%s',$file,$lang['deletefail']),-1);
        return false;
    }elseif(!$conf['refshow']){
        msg(str_replace('%s',noNS($id),$lang['mediainuse']),0);
        return false;
    }

    return $mediareferences;
}

/**
 * Handles media file uploads
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @return mixed false on error, id of the new file on success
 */
function media_upload($ns,$auth){
    if($auth < AUTH_UPLOAD) return false;
    require_once(DOKU_INC.'inc/confutils.php');
    global $lang;
    global $conf;

    // get file and id
    $id   = $_POST['id'];
    $file = $_FILES['upload'];
    if(empty($id)) $id = $file['name'];

    // check extensions
    list($fext) = mimetype($file['name']);
    list($iext) = mimetype($id);
    if($fext && !$iext){
        // no extension specified in id - readd original one
        $id .= '.'.$fext;
    }elseif($fext && $fext != $iext){
        // extension was changed, print warning
        msg(sprintf($lang['mediaextchange'],$fext,$iext));
    }

    // get filename
    $id   = cleanID($ns.':'.$id);
    $fn   = mediaFN($id);

    // get filetype regexp
    $types = array_keys(getMimeTypes());
    $types = array_map(create_function('$q','return preg_quote($q,"/");'),$types);
    $regex = join('|',$types);

    // because a temp file was created already
    if(preg_match('/\.('.$regex.')$/i',$fn)){
        //check for overwrite
        if(@file_exists($fn) && (!$_POST['ow'] || $auth < AUTH_DELETE)){
            msg($lang['uploadexist'],0);
            return false;
        }
        // prepare directory
        io_createNamespace($id, 'media');
        if(move_uploaded_file($file['tmp_name'], $fn)) {
            // Set the correct permission here.
            // Always chmod media because they may be saved with different permissions than expected from the php umask.
            // (Should normally chmod to $conf['fperm'] only if $conf['fperm'] is set.)
            chmod($fn, $conf['fmode']);
            msg($lang['uploadsucc'],1);
            return $id;
        }else{
            msg($lang['uploadfail'],-1);
        }
    }else{
        msg($lang['uploadwrong'],-1);
    }
    return false;
}



/**
 * List all files in a given Media namespace
 */
function media_filelist($ns,$auth=null,$jump=''){
    global $conf;
    global $lang;
    $ns = cleanID($ns);

    // check auth our self if not given (needed for ajax calls)
    if(is_null($auth)) $auth = auth_quickaclcheck("$ns:*");

    echo '<h1 id="media__ns">:'.hsc($ns).'</h1>'.NL;

    if($auth < AUTH_READ){
        // FIXME: print permission warning here instead?
        echo '<div class="nothing">'.$lang['nothingfound'].'</div>'.NL;
        return;
    }

    media_uploadform($ns, $auth);

    $dir = utf8_encodeFN(str_replace(':','/',$ns));
    $data = array();
    search($data,$conf['mediadir'],'search_media',array(),$dir);

    if(!count($data)){
        echo '<div class="nothing">'.$lang['nothingfound'].'</div>'.NL;
        return;
    }

    foreach($data as $item){
        media_printfile($item,$auth,$jump);
    }
}

/**
 * Print action links for a file depending on filetype
 * and available permissions
 *
 * @todo contains inline javascript
 */
function media_fileactions($item,$auth){
    global $lang;

    // view button
    $link = ml($item['id'],'',true);
    echo ' <a href="'.$link.'" target="_blank"><img src="'.DOKU_BASE.'lib/images/magnifier.png" '.
         'alt="'.$lang['mediaview'].'" title="'.$lang['mediaview'].'" class="btn" /></a>';


    // no further actions if not writable
    if(!$item['writable']) return;

    // delete button
    if($auth >= AUTH_DELETE){
        $ask  = addslashes($lang['del_confirm']).'\\n';
        $ask .= addslashes($item['id']);

        echo ' <a href="'.DOKU_BASE.'lib/exe/mediamanager.php?delete='.rawurlencode($item['id']).'" '.
             'onclick="return confirm(\''.$ask.'\')" onkeypress="return confirm(\''.$ask.'\')">'.
             '<img src="'.DOKU_BASE.'lib/images/trash.png" alt="'.$lang['btn_delete'].'" '.
             'title="'.$lang['btn_delete'].'" class="btn" /></a>';
    }

    // edit button
    if($auth >= AUTH_UPLOAD && $item['isimg'] && $item['meta']->getField('File.Mime') == 'image/jpeg'){
        echo ' <a href="'.DOKU_BASE.'lib/exe/mediamanager.php?edit='.rawurlencode($item['id']).'">'.
             '<img src="'.DOKU_BASE.'lib/images/pencil.png" alt="'.$lang['metaedit'].'" '.
             'title="'.$lang['metaedit'].'" class="btn" /></a>';
    }

}

/**
 * Formats and prints one file in the list
 */
function media_printfile($item,$auth,$jump){
    global $lang;

    // Prepare zebra coloring
    // I always wanted to use this variable name :-D
    static $twibble = 1;
    $twibble *= -1;
    $zebra = ($twibble == -1) ? 'odd' : 'even';

    // Automatically jump to recent action
    if($jump == $item['id']) {
        $jump = ' id="scroll__here" ';
    }else{
        $jump = '';
    }

    // Prepare fileicons
    list($ext,$mime) = mimetype($item['file']);
    $class = preg_replace('/[^_\-a-z0-9]+/i','_',$ext);
    $class = 'select mediafile mf_'.$class;

    // Prepare filename
    $file = utf8_decodeFN($item['file']);

    // Prepare info
    $info = '';
    if($item['isimg']){
        $info .= (int) $item['meta']->getField('File.Width');
        $info .= '&#215;';
        $info .= (int) $item['meta']->getField('File.Height');
        $info .= ' ';
    }
    $info .= filesize_h($item['size']);

    // ouput
    echo '<div class="'.$zebra.'"'.$jump.'>'.NL;
    echo '<a name="h_'.$item['id'].'" class="'.$class.'">'.$file.'</a> ';
    echo '<span class="info">('.$info.')</span>'.NL;
    media_fileactions($item,$auth);
    echo '<div class="example" id="ex_'.$item['id'].'">';
    echo $lang['mediausage'].' <code>{{:'.$item['id'].'}}</code>';
    echo '</div>';
    if($item['isimg']) media_printimgdetail($item);
    echo '<div class="clearer"></div>'.NL;
    echo '</div>'.NL;
}

/**
 * Prints a thumbnail and metainfos
 */
function media_printimgdetail($item){
    // prepare thumbnail
    $w = (int) $item['meta']->getField('File.Width');
    $h = (int) $item['meta']->getField('File.Height');
    if($w>120 || $h>120){
        $ratio = $item['meta']->getResizeRatio(120);
        $w = floor($w * $ratio);
        $h = floor($h * $ratio);
    }
    $src = ml($item['id'],array('w'=>$w,'h'=>$h));
    $p = array();
    $p['width']  = $w;
    $p['height'] = $h;
    $p['alt']    = $item['id'];
    $p['class']  = 'thumb';
    $att = buildAttributes($p);

    // output
    echo '<div class="detail">';
    echo '<div class="thumb">';
    echo '<a name="d_'.$item['id'].'" class="select">';
    echo '<img src="'.$src.'" '.$att.' />';
    echo '</a>';
    echo '</div>';

    // read EXIF/IPTC data
    $t = $item['meta']->getField('IPTC.Headline');
    $d = $item['meta']->getField(array('IPTC.Caption','EXIF.UserComment',
                                       'EXIF.TIFFImageDescription',
                                       'EXIF.TIFFUserComment'));
    if(utf8_strlen($d) > 250) $d = utf8_substr($d,0,250).'...';
    $k = $item['meta']->getField(array('IPTC.Keywords','IPTC.Category'));

    // print EXIF/IPTC data
    if($t || $d || $k ){
        echo '<p>';
        if($t) echo '<strong>'.htmlspecialchars($t).'</strong><br />';
        if($d) echo htmlspecialchars($d).'<br />';
        if($t) echo '<em>'.htmlspecialchars($k).'</em>';
        echo '</p>';
    }
    echo '</div>';
}

/**
 * Print the media upload form if permissions are correct
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_uploadform($ns, $auth){
    global $lang;

    if($auth < AUTH_UPLOAD) return; //fixme print info on missing permissions?

    ?>
    <div class="upload"><?php echo $lang['mediaupload']?></div>
    <form action="<?php echo DOKU_BASE?>lib/exe/mediamanager.php"
          method="post" enctype="multipart/form-data" class="upload">
      <fieldset>
        <input type="hidden" name="ns" value="<?php echo hsc($ns)?>" />

        <p>
          <label for="upload__file"><?php echo $lang['txt_upload']?>:</label>
          <input type="file" name="upload" class="edit" id="upload__file" />
        </p>

        <p>
          <label for="upload__name"><?php echo $lang['txt_filename']?>:</label>
          <span class="nowrap">
          <input type="text" name="id" class="edit" id="upload__name" /><input
                 type="submit" class="button" value="<?php echo $lang['btn_upload']?>"
                 accesskey="s" />
          </span>
        </p>

        <?php if($auth >= AUTH_DELETE){?>
            <p>
              <input type="checkbox" name="ow" value="1" id="dw__ow" class="check" />
              <label for="dw__ow" class="check"><?php echo $lang['txt_overwrt']?></label>
            </p>
        <?php }?>
      </fieldset>
    </form>
    <?php
}



/**
 * Build a tree outline of available media namespaces
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_nstree($ns){
    global $conf;
    global $lang;

    // currently selected namespace
    $ns  = cleanID($ns);
    if(empty($ns)){
        $ns = dirname(str_replace(':','/',$ID));
        if($ns == '.') $ns ='';
    }
    $ns  = utf8_encodeFN(str_replace(':','/',$ns));

    $data = array();
    search($data,$conf['mediadir'],'search_index',array('ns' => $ns));

    // wrap a list with the root level around the other namespaces
    $item = array( 'level' => 0, 'id' => '',
                   'open' =>'true', 'label' => '['.$lang['mediaroot'].']');

    echo '<ul class="idx">';
    echo media_nstree_li($item);
    echo media_nstree_item($item);
    echo html_buildlist($data,'idx','media_nstree_item','media_nstree_li');
    echo '</li>';
    echo '</ul>';
}

/**
 * Userfunction for html_buildlist
 *
 * Prints a media namespace tree item
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_nstree_item($item){
    $pos   = strrpos($item['id'], ':');
    $label = substr($item['id'], $pos > 0 ? $pos + 1 : 0);
    if(!$item['label']) $item['label'] = $label;

    $ret  = '';
    $ret .= '<a href="'.DOKU_BASE.'lib/exe/mediamanager.php?ns='.idfilter($item['id']).'" class="idx_dir">';
    $ret .= $item['label'];
    $ret .= '</a>';
    return $ret;
}

/**
 * Userfunction for html_buildlist
 *
 * Prints a media namespace tree item opener
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function media_nstree_li($item){
    $class='media level'.$item['level'];
    if($item['open']){
        $class .= ' open';
        $img   = DOKU_BASE.'lib/images/minus.gif';
        $alt   = '&minus;';
    }else{
        $class .= ' closed';
        $img   = DOKU_BASE.'lib/images/plus.gif';
        $alt   = '+';
    }
    return '<li class="'.$class.'">'.
           '<img src="'.$img.'" alt="'.$alt.'" />';
}
