<?php
/**
 * DokuWiki fulltextsearch functions using the index
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

  if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../').'/');
  require_once(DOKU_INC.'inc/indexer.php');


/**
 * The fulltext search
 *
 * Returns a list of matching documents for the given query
 *
 */
function ft_pageSearch($query,&$poswords){
    $q = ft_queryParser($query);
    // use this for higlighting later:
    $poswords = str_replace('*','',join(' ',$q['and']));

    // lookup all words found in the query
    $words  = array_merge($q['and'],$q['not']);
    if(!count($words)) return array();
    $result = idx_lookup($words);

    // merge search results with query
    foreach($q['and'] as $pos => $w){
        $q['and'][$pos] = $result[$w];
    }
    // create a list of unwanted docs
    $not = array();
    foreach($q['not'] as $pos => $w){
        $not = array_merge($not,array_keys($result[$w]));
    }

    // combine and-words
    if(count($q['and']) > 1){
        $docs = ft_resultCombine($q['and']);
    }else{
        $docs = $q['and'][0];
    }
    if(!count($docs)) return array();

    // create a list of hidden pages in the result
    $hidden = array();
    $hidden = array_filter(array_keys($docs),'isHiddenPage');
    $not = array_merge($not,$hidden);

    // filter unmatched namespaces
    if(!empty($q['ns'])) {
        $pattern = implode('|^',$q['ns']);
        foreach($docs as $key => $val) {
            if(!preg_match('/^'.$pattern.'/',$key)) {
                unset($docs[$key]);
            }
        }
    }

    // remove negative matches
    foreach($not as $n){
        unset($docs[$n]);
    }

    if(!count($docs)) return array();
    // handle phrases
    if(count($q['phrases'])){
        //build a regexp
        $q['phrases'] = array_map('utf8_strtolower',$q['phrases']);
        $q['phrases'] = array_map('preg_quote',$q['phrases']);
        $regex = '('.join('|',$q['phrases']).')';
        // check the source of all documents for the exact phrases
        foreach(array_keys($docs) as $id){
            $text  = utf8_strtolower(rawWiki($id));
            if(!preg_match('/'.$regex.'/usi',$text)){
                unset($docs[$id]); // no hit - remove
            }
        }
    }

    if(!count($docs)) return array();

    // check ACL permissions
    foreach(array_keys($docs) as $doc){
        if(auth_quickaclcheck($doc) < AUTH_READ){
            unset($docs[$doc]);
        }
    }

    if(!count($docs)) return array();

    // if there are any hits left, sort them by count
    arsort($docs);

    return $docs;
}

/**
 * Returns the backlinks for a given page
 *
 * Does a quick lookup with the fulltext index, then
 * evaluates the instructions of the found pages
 */
function ft_backlinks($id){
    global $conf;
    $result = array();

    // quick lookup of the pagename
    $page    = noNS($id);
    $sw      = array(); // we don't use stopwords here
    $matches = idx_lookup(idx_tokenizer($page,$sw));  // pagename may contain specials (_ or .)
    $docs    = array_keys(ft_resultCombine(array_values($matches)));
    $docs    = array_filter($docs,'isVisiblePage'); // discard hidden pages
    if(!count($docs)) return $result;
    require_once(DOKU_INC.'inc/parserutils.php');

    // check metadata for matching links
    foreach($docs as $match){
        // metadata relation reference links are already resolved
        $links = p_get_metadata($match,"relation references");
        if (isset($links[$id])) $result[] = $match;
    }

    if(!count($result)) return $result;

    // check ACL permissions
    foreach(array_keys($result) as $idx){
        if(auth_quickaclcheck($result[$idx]) < AUTH_READ){
            unset($result[$idx]);
        }
    }

    sort($result);
    return $result;
}

/**
 * Quicksearch for pagenames
 *
 * By default it only matches the pagename and ignores the
 * namespace. This can be changed with the second parameter
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function ft_pageLookup($id,$pageonly=true){
    global $conf;
    $id    = preg_quote($id,'/');
    $pages = file($conf['cachedir'].'/page.idx');
    $pages = array_values(preg_grep('/'.$id.'/',$pages));

    $cnt = count($pages);
    for($i=0; $i<$cnt; $i++){
        if($pageonly){
            if(!preg_match('/'.$id.'/',noNS($pages[$i]))){
                unset($pages[$i]);
                continue;
            }
        }
        if(!@file_exists(wikiFN($pages[$i]))){
            unset($pages[$i]);
            continue;
        }
    }

    $pages = array_filter($pages,'isVisiblePage'); // discard hidden pages
    if(!count($pages)) return array();

    // check ACL permissions
    foreach(array_keys($pages) as $idx){
        if(auth_quickaclcheck($pages[$idx]) < AUTH_READ){
            unset($pages[$idx]);
        }
    }

    sort($pages);
    return $pages;
}

/**
 * Creates a snippet extract
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function ft_snippet($id,$poswords){
    $poswords = preg_quote($poswords,'#');
    $re       = '('.str_replace(' ','|',$poswords).')';
    $text     = rawWiki($id);

    $match = array();
    $snippets = array();
    $utf8_offset = $offset = $end = 0;
    $len = utf8_strlen($text);

    for ($cnt=3; $cnt--;) {
      if (!preg_match('#'.$re.'#iu',$text,$match,PREG_OFFSET_CAPTURE,$offset)) break;

      list($str,$idx) = $match[0];

      // convert $idx (a byte offset) into a utf8 character offset
      $utf8_idx = utf8_strlen(substr($text,0,$idx));
      $utf8_len = utf8_strlen($str);

      // establish context, 100 bytes surrounding the match string
      // first look to see if we can go 100 either side,
      // then drop to 50 adding any excess if the other side can't go to 50,
      $pre = min($utf8_idx-$utf8_offset,100);
      $post = min($len-$utf8_idx-$utf8_len,100);

      if ($pre>50 && $post>50) {
        $pre = $post = 50;
      } else if ($pre>50) {
        $pre = min($pre,100-$post);
      } else if ($post>50) {
        $post = min($post, 100-$pre);
      } else {
        // both are less than 50, means the context is the whole string
        // make it so and break out of this loop - there is no need for the
        // complex snippet calculations
        $snippets = array($text);
        break;
      }

      // establish context start and end points, try to append to previous
      // context if possible
      $start = $utf8_idx - $pre;
      $append = ($start < $end) ? $end : false;  // still the end of the previous context snippet
      $end = $utf8_idx + $utf8_len + $post;      // now set it to the end of this context

      if ($append) {
        $snippets[count($snippets)-1] .= utf8_substr($text,$append,$end-$append);
      } else {
        $snippets[] = utf8_substr($text,$start,$end-$start);
      }

      // set $offset for next match attempt
      //   substract strlen to avoid splitting a potential search success,
      //   this is an approximation as the search pattern may match strings
      //   of varying length and it will fail if the context snippet
      //   boundary breaks a matching string longer than the current match
      $utf8_offset = $utf8_idx + $post;
      $offset = $idx + strlen(utf8_substr($text,$utf8_idx,$post));
      $offset = utf8_correctIdx($text,$offset);
    }

    $m = "\1";
    $snippets = preg_replace('#'.$re.'#iu',$m.'$1'.$m,$snippets);
    $snippet = preg_replace('#'.$m.'([^'.$m.']*?)'.$m.'#iu','<span class="search_hit">$1</span>',hsc(join('... ',$snippets)));

    return $snippet;
}

/**
 * Combine found documents and sum up their scores
 *
 * This function is used to combine searched words with a logical
 * AND. Only documents available in all arrays are returned.
 *
 * based upon PEAR's PHP_Compat function for array_intersect_key()
 *
 * @param array $args An array of page arrays
 */
function ft_resultCombine($args){
    $array_count = count($args);
    if($array_count == 1){
        return $args[0];
    }

    $result = array();
    foreach ($args[0] as $key1 => $value1) {
        for ($i = 1; $i !== $array_count; $i++) {
            foreach ($args[$i] as $key2 => $value2) {
                if ((string) $key1 === (string) $key2) {
                    if(!isset($result[$key1])) $result[$key1] = $value1;
                    $result[$key1] += $value2;
                }
            }
        }
    }
    return $result;
}

/**
 * Builds an array of search words from a query
 *
 * @todo support OR and parenthesises?
 */
function ft_queryParser($query){
    global $conf;
    $swfile   = DOKU_INC.'inc/lang/'.$conf['lang'].'/stopwords.txt';
    if(@file_exists($swfile)){
        $stopwords = file($swfile);
    }else{
        $stopwords = array();
    }

    $q = array();
    $q['query']   = $query;
    $q['ns']      = array();
    $q['phrases'] = array();
    $q['and']     = array();
    $q['not']     = array();

    // strip namespace from query
    if(preg_match('/([^@]*)@(.*)/',$query,$match))  {
        $query = $match[1];
        $q['ns'] = explode('@',preg_replace("/ /",'',$match[2]));
    }

    // handle phrase searches
    while(preg_match('/"(.*?)"/',$query,$match)){
        $q['phrases'][] = $match[1];
        $q['and'] = array_merge(idx_tokenizer($match[0],$stopwords));
        $query = preg_replace('/"(.*?)"/','',$query,1);
    }

    $words = explode(' ',$query);
    foreach($words as $w){
        if($w{0} == '-'){
            $token = idx_tokenizer($w,$stopwords,true);
            if(count($token)) $q['not'] = array_merge($q['not'],$token);
        }else{
            // asian "words" need to be searched as phrases
            if(@preg_match_all('/('.IDX_ASIAN.'+)/u',$w,$matches)){
                $q['phrases'] = array_merge($q['phrases'],$matches[1]);

            }
            $token = idx_tokenizer($w,$stopwords,true);
            if(count($token)) $q['and'] = array_merge($q['and'],$token);
        }
    }

    return $q;
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
