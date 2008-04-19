<?php
// COUNT NEWS
$get_amount = $db->qry_first('SELECT count(*) as number FROM %prefix%news');
$overall_news = $get_amount["number"];

if($overall_news == 0) {
  $func->no_items(t("Newsmeldungen"), "", "rlist");
  // DEBUG
  echo md5("Fuckoff")."<br>";
  echo t("Fuckoff");
  //echo htmlentities ($translation->xml_write_db_to_file('news'));
}
else {
  //ARCHIVE PAGES
  if($_GET['subaction'] == 'archive') {
    $dsp->NewContent(t('News Archiv'), t('Archivierte Mitteilungen'));
    // SET PAGE SPLIT
    if ($cfg["news_shorted_archiv"] == "") $cfg["news_shorted_archiv"] = 10;
    $pages = page_split_archiv($_GET["news_page"], $cfg["news_shorted_archiv"], $overall_news - ($cfg['news_shorted'] + $cfg['news_completed']), "index.php?mod=news&action=show&subaction=archive", "news_page",($cfg['news_shorted'] + $cfg['news_complete']));
  
    //GET NEWS DATA AND ORDER NEWS
    $get_newsshorted = $db->qry("SELECT FROM_UNIXTIME(n.date, '%W, %d.%m.%Y'), FROM_UNIXTIME(n.date, '%H:%i'), n.caption, n.text, u.username, n.newsid FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC %plain%". $pages["sql"]);
    while($row=$db->fetch_array($get_newsshorted)) {
      $tmpDate = $func->translate_weekdayname(substr($row[0],0,strpos($row[0],","))) . substr($row[0],strpos($row[0],","));
      $shortnews[$tmpDate][$row[1]]['caption'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=" .$row[5] ."\">" .$row[2] ."</a>";
      $shortnews[$tmpDate][$row[1]]['text'] = substr(strip_tags($row[3]),0,$cfg["news_shorted_length"]) ."...";
      $shortnews[$tmpDate][$row[1]]['username'] = $row[4];
    }
    $tmpDate = "";
    $tmpSNCode ="<table cellspacing=\"5\" width=\"100%\">";
    foreach ($shortnews as $newsdate=>$value) {
      $tmpSNCode .= "<tr><td colspan=\"2\"><strong>$newsdate</strong></td></tr><tr><td colspan=\"2\"><div class=\"hrule\"></div></td></tr>";
      foreach ($shortnews[$newsdate] as $newsitemtime=>$newsitemdata ) {
        $tmpSNCode .= "<tr><td width=\"45\" align=\"center\" valign=\"top\" rowspan=\"2\">" .$newsitemtime ."</td><td><strong>" .$newsitemdata['caption'] ."</strong> (" .$newsitemdata['username'] .")</td></tr><tr><td>" .$newsitemdata['text'] ."</td></tr>";      
      } 
      $tmpSNCode .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    }
    $tmpSNCode .= "</table>";
    $templ['news']['show']['case']['control']['rows'] .= $tmpSNCode;
    
    // SET TEMPLATE CASE VARS
    $templ['news']['case']['number'] = $overall_news;
    $templ_news_case_number_per_site = $howmany;
    $templ['news']['show']['case']['control']['pages'] = $pages["html"] ."<strong><a href=\"index.php?mod=news\">" .t('Zur&uuml;ck') ."</a></strong>";
  
    $dsp->AddSingleRow($dsp->FetchModTpl("news", "show_case"));
    $dsp->AddContent();
  }
  //NEWS PAGE
  else {
    //CHECK IF SHORTED NEWS ARE ENABLED/SPECIFIED
    if ($cfg["news_shorted"]== "0") {
      $dsp->NewContent(t('Neuigkeiten'), t('Hier sehen Sie aktuelle Neuigkeiten.'));
      //DO PAGED NEWS SHOW
        // SET PAGE SPLIT
        if ($cfg["news_count"] == "") $cfg["news_count"] = 5;
        $pages = $func->page_split($_GET["news_page"], $cfg["news_count"], $overall_news, "index.php?mod=news&amp;action=show", "news_page");
    
        //GET NEWS DATA AND ORDER NEWS
        $get_news = $db->qry('SELECT n.*, u.userid, u.username FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC %plain%', $pages["sql"]);
    
        while($row=$db->fetch_array($get_news)) {
            $priority = $row["priority"];
    
            // SELECT NEWS PRIORITY
            if($priority == 1) { 
          $type = important; 
        } 
        else { 
          $type = normal; 
        }
            $templ['news']['show']['row'][$type]['info']['caption']     = $row["caption"];
            $text                                                       = $row["text"];
            $templ['news']['show']['row'][$type]['info']['username']    = $row["username"] .' '. $dsp->FetchUserIcon($row['userid']);
            $templ['news']['show']['row'][$type]['control']['userid']   = $row["poster"];
            if ($row['icon'] and $row['icon'] != 'none') $templ['news']['show']['row']['normal']['info']['icon'] =  '<img src="ext_inc/news_icons/'.$row['icon'].'" vspace="2" align="right" />';
            else $templ['news']['show']['row']['normal']['info']['icon'] = '';
    
            $newsid                                                     = $row["newsid"];
            $date                                                       = $row["date"];
            $howmany++;
    
            $templ['news']['show']['row'][$type]['info']['date']        = $func->unixstamp2date($date,"daydatetime");
    
            if ($cfg["news_html"] == 1) $text = $func->text2html($text);
            else $text = $func->AllowHTML($text);
            $templ['news']['show']['row'][$type]['info']['text']        = $text;
    
            // GET NUMBER OF COMMENTS
            $get_comments = $db->qry_first('SELECT count(*) as number FROM %prefix%comments WHERE relatedto_id=%int% AND relatedto_item=\'news\'', $newsid);
            
            if ($get_comments["number"] >= 0) { 
          $templ['news']['show']['row'][$type]['info']['comments'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=$newsid\">" .$get_comments["number"]." ". t('Kommentar(e)') ."</a>"; 
        }
    
            // Buttons
            $templ['news']['show']['row'][$type]['control']['buttons'] = "";
            if ($auth["type"] > 1) {
                $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=change&amp;step=2&amp;newsid=$newsid", "edit") . " ";
                $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=delete&amp;step=2&amp;newsid=$newsid", "delete") . " ";
            }
            $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=comment&amp;newsid=$newsid", "quote") . " ";
    
            $templ['news']['show']['case']['control']['rows'] .= $dsp->FetchModTpl("news", "show_row_$type");
        } // CLOSE WHILE
    }
    else {
      if ($cfg["news_complete"] == "") $cfg["news_complete"] = 3;
      $dsp->NewContent(t('Neuigkeiten'), t('Hier sehen Sie aktuelle Neuigkeiten.'));
      
      //SHOW COMPLETE NEWS
      //GET NEWS DATA AND ORDER NEWS
        $get_news = $db->qry('SELECT n.*, u.userid, u.username FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC LIMIT %plain%', $cfg["news_complete"]);
        while($row=$db->fetch_array($get_news)) {
            $priority = $row["priority"];
    
            // SELECT NEWS PRIORITY
            if($priority == 1) { 
          $type = important; 
        } 
        else { 
          $type = normal; 
        }
            $templ['news']['show']['row'][$type]['info']['caption']     = $row["caption"];
            $text                                                       = $row["text"];
            $templ['news']['show']['row'][$type]['info']['username']    = $row["username"] .' '. $dsp->FetchUserIcon($row['userid']);
            $templ['news']['show']['row'][$type]['control']['userid']   = $row["poster"];
            if ($row['icon'] and $row['icon'] != 'none') $templ['news']['show']['row']['normal']['info']['icon'] =  '<img src="ext_inc/news_icons/'.$row['icon'].'" vspace="2" align="right" />';
            else $templ['news']['show']['row']['normal']['info']['icon'] = '';
    
            $newsid                                                     = $row["newsid"];
            $date                                                       = $row["date"];
            $howmany++;
    
            $templ['news']['show']['row'][$type]['info']['date']        = $func->unixstamp2date($date,"daydatetime");
    
            if ($cfg["news_html"] == 1) $text = $func->text2html($text);
            else $text = $func->AllowHTML($text);
            $templ['news']['show']['row'][$type]['info']['text']        = $text;
    
            // GET NUMBER OF COMMENTS
            $get_comments = $db->qry_first('SELECT count(*) as number FROM %prefix%comments WHERE relatedto_id=%int% AND relatedto_item=\'news\'', $newsid);
            
            if ($get_comments["number"] >= 0) { 
          $templ['news']['show']['row'][$type]['info']['comments'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=$newsid\">" .$get_comments["number"]." Kommentar(e)</a>"; 
        }
    
            // Buttons
            $templ['news']['show']['row'][$type]['control']['buttons'] = "";
            if ($auth["type"] > 1) {
                $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=change&amp;step=2&amp;newsid=$newsid", "edit") . " ";
                $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=delete&amp;step=2&amp;newsid=$newsid", "delete") . " ";
            }
            $templ['news']['show']['row'][$type]['control']['buttons'] .= $dsp->FetchIcon("index.php?mod=news&amp;action=comment&amp;newsid=$newsid", "quote") . " ";
    
            $templ['news']['show']['case']['control']['rows'] .= $dsp->FetchModTpl("news", "show_row_$type");
        } // CLOSE WHILE
        
        //SHOW SHORTED NEWS
        $get_newsshorted = $db->query("SELECT from_unixtime(n.date,'%W, %d.%m.%Y'),from_unixtime(n.date,'%H:%i'), n.caption, n.text, u.username, n.newsid FROM %prefix%news AS n LEFT JOIN %prefix%user AS u ON u.userid = n.poster ORDER BY n.top DESC, n.date DESC LIMIT %plain%", $cfg["news_complete"] ."," .$cfg["news_shorted"]);
        while($row=$db->fetch_array($get_newsshorted)) {
          $tmpDate = $func->translate_weekdayname(substr($row[0],0,strpos($row[0],","))) . substr($row[0],strpos($row[0],","));
          $shortnews[$tmpDate][$row[1]]['caption'] = "<a href=\"index.php?mod=news&amp;action=comment&amp;newsid=" .$row[5] ."\">" .$row[2] ."</a>";
          $shortnews[$tmpDate][$row[1]]['text'] = substr(strip_tags($row[3]),0,$cfg["news_shorted_length"]) ."...";
          $shortnews[$tmpDate][$row[1]]['username'] = $row[4];
      }
      $tmpDate = "";
      $tmpSNCode ="<table cellspacing=\"5\" width=\"100%\">";
      foreach ($shortnews as $newsdate=>$value) {
        $tmpSNCode .= "<tr><td colspan=\"2\"><strong>$newsdate</strong></td></tr><tr><td colspan=\"2\"><div class=\"hrule\"></div></td></tr>";
        foreach ($shortnews[$newsdate] as $newsitemtime=>$newsitemdata ) {
          $tmpSNCode .= "<tr><td width=\"45\" align=\"center\" valign=\"top\" rowspan=\"2\">" .$newsitemtime ."</td><td><strong>" .$newsitemdata['caption'] ."</strong> (" .$newsitemdata['username'] .")</td></tr><tr><td>" .$newsitemdata['text'] ."</td></tr>";      
        } 
        $tmpSNCode .= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
      }
      $tmpSNCode .= "</table>";
      $templ['news']['show']['row']['shorted']['title'] = "<strong>" .t('&Auml;ltere Mitteilungen') ."</strong>";
      $templ['news']['show']['row']['shorted']['text'] = $tmpSNCode;
      $templ['news']['show']['case']['control']['rows'] .= $dsp->FetchModTpl("news", "show_row_shorted");
      if ($cfg['news_shorted_archiv'] != 0) {
        $pages["html"] = "<strong><a href=\"index.php?mod=news&amp;action=show&amp;subaction=archive\">" .t('News Archiv') ."</a></strong>";
      }
    }
  
    // SET TEMPLATE CASE VARS
    $templ['news']['case']['number'] = $overall_news;
    $templ_news_case_number_per_site = $howmany;
    $templ['news']['show']['case']['control']['pages'] = $pages["html"];
  
    $dsp->AddSingleRow($dsp->FetchModTpl("news", "show_case"));
    $dsp->AddContent();
  }
}

function page_split_archiv($current_page, $max_entries_per_page, $overall_entries, $working_link, $var_page_name, $offset) {
        if ($max_entries_per_page > 0 and $overall_entries >= 0 and $working_link != "" and $var_page_name != "") {
            if($current_page == "") {
                $page_sql = "LIMIT ".  $offset ."," . $max_entries_per_page;
                $page_a = 0;
                $page_b = $max_entries_per_page;
            }
            if($current_page == "all") {
                $page_sql = "LIMIT " .$offset ."," .$overall_entries;
                $page_a = 0;
                $page_b = $overall_entries;
            } else  {
                $page_sql = ("LIMIT " . (($current_page * $max_entries_per_page) + $offset) . ", " . ($max_entries_per_page));
                $page_a = ($current_page * $max_entries_per_page);
                $page_b = ($max_entries_per_page);
            }
            if($overall_entries > $max_entries_per_page) {
                $page_output = ("Seiten: ");
                if( $current_page != "all" && ($current_page + 1) > 1 ) {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&amp;" . $var_page_name . "=" . ($current_page - 1) . "&amp;orderby=" . $orderby . "\">" ."<b>" . "<" . "</b>" . "</a>");
                }
                $i = 0;                 
                while($i < ($overall_entries / $max_entries_per_page)) {
                    if($current_page == $i && $current_page != "all") {
                        $page_output .= (" " . ($i + 1));
                    } else {
                        $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link . "&amp;" . $var_page_name . "=" . $i . "\">" ."<b>" . ($i + 1) . "</b>" . "</a>");
                    }
                    $i++;
                }
                if($current_page != "all" && ($current_page + 1) < ($overall_entries/$max_entries_per_page)) {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link ."&amp;" . $var_page_name . "=" . ($current_page + 1) . "\">" ."<b>" . ">" . "</b>" . "</a>");
                }
                if($current_page != "all") {
                    $page_output .= ("&nbsp; " . "<a class=\"menu\" href=\"" . $working_link ."&amp;" . $var_page_name . "=all" . "\">" ."<b>" . "Alle" . "</b>" . "</a>");                                 
                }
                if ($current_page == "all") {
                    $page_output .= "&nbsp; Alle";
                }
            }
            
            if ($page_output != "") {
        $page_output .= " | ";
      }

            $output["html"] = $page_output;
            $output["sql"] = $page_sql;
            $output["a"] = $page_a;
            $output["b"] = $page_b;
    
            return($output);
        
            // ?!?! unset($output); unset($working_link); unset($page_sql); unset($page_output);
    
        } else echo ("Error: Function page_split needs defined: current_page, max_entries_per_page,working_link, page_varname For more information please visit the lansuite programmers docu");
    }

?>
