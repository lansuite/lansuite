<?php

namespace LanSuite\Module\News;

class News
{
    /**
     * @return void
     */
    public function GenerateNewsfeed()
    {
        global $db, $cfg, $func;

        $xml = new \LanSuite\XML();

        $output = '<?xml version="1.0" encoding="UTF-8"?'.'>'."\r\n";
  
        $channel = $xml->write_tag("title", $cfg['sys_page_title'], 2);
        $channel .= $xml->write_tag("link", (!empty($cfg['sys_partyurl_ssl'])) ? $cfg["sys_partyurl_ssl"] : $cfg["sys_partyurl"], 2);
        $channel .= $xml->write_tag("description", $cfg["news_description"], 2);
        $channel .= $xml->write_tag("language", "de-de", 2);
        $channel .= $xml->write_tag("copyright", $cfg["news_copyright"], 2);
   
        if ($cfg["news_logourl"]) {
            $image = $xml->write_tag("url", $cfg["news_logourl"], 3);
            $image .= $xml->write_tag("title", $cfg['sys_page_title'] ." - Logo", 3);
            $image .= $xml->write_tag("link", (!empty($cfg['sys_partyurl_ssl'])) ? $cfg["sys_partyurl_ssl"] : $cfg["sys_partyurl"], 3);
            $channel .= $xml->write_master_tag("image", $image, 2);
        }

        $get_news = $db->qry("
          SELECT
            n.*,
            UNIX_TIMESTAMP(n.date) AS date,
            u.name,
            u.firstname,
            u.username
          FROM  %prefix%news n
          LEFT JOIN %prefix%user u ON u.userid = n.poster
          ORDER BY n.date DESC");
        while ($news = $db->fetch_array($get_news, 0)) {
            $item = $xml->write_tag("title", $func->Entity2Uml(strip_tags($news["caption"])), 3);
            $item .= $xml->write_tag("description", $func->Entity2Uml(strip_tags($news["text"])), 3);
            $item .= $xml->write_tag("author", $func->Entity2Uml("{$news['firstname']} {$news['name']} ({$news['username']})"), 3);
            $item .= $xml->write_tag("pubDate", date("D, j M Y H:i:s O", $news['date']), 3);
                                                    
            $path = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "index.php"));

            // This assumes that the author of a news item accesses Lansuite via the
            // same means (server name, port, protocol) as someone who later accesses
            // the news feed which might not be the case. But currently this seems like
            // the best way to do it..
            $linkprotocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) || $_SERVER['SERVER_PORT']=='443') ? 'https' : 'http';
            // This is quite sad - we really need to double-encode the ampersand chars
            // because otherwise they will be broken by AllowHTML in xml->write_tag ..
            $item .= $xml->write_tag("link", $linkprotocol . "://{$_SERVER['SERVER_NAME']}" . ((($linkprotocol=='https' && $_SERVER['SERVER_PORT']!=443) || ($linkprotocol=='http' && $_SERVER['SERVER_PORT']!=80)) ? ":{$_SERVER['SERVER_PORT']}" : "") . "{$path}index.php?mod=news&amp;amp;action=comment&amp;amp;newsid=". $news['newsid'], 3);
            $channel .= $xml->write_master_tag("item", $item, 2);
        }
        $db->free_result($get_news);

        $rss = $xml->write_master_tag("channel", $channel, 1);
        $output .= $xml->write_master_tag("rss version=\"0.91\"", $rss, 0);

        if (is_writable("ext_inc/newsfeed/")) {
            if ($fp = @fopen("ext_inc/newsfeed/news.xml", "w")) {
                if (@fwrite($fp, $output)) {
                    $func->log_event(t('Newsfeed wurde erfolgreich aktuallisiert'), 1, t('Newsfeed'));
                } else {
                    $func->log_event(t('Konnte Newsfeed nicht erstellen. Fehler beim Schreiben in der Datei ext_inc/newsfeed/news.xml'), 2, t('Newsfeed wurde erfolgreich aktuallisiert'));
                }
                @fclose($fp);
            } else {
                $func->log_event(t('Konnte Newsfeed nicht erstellen. Fehler beim &Ouml;ffnen der Datei ext_inc/newsfeed/news.xml'), 2, t('Newsfeed wurde erfolgreich aktuallisiert'));
            }
        } else {
            $func->log_event(t('Konnte Newsfeed nicht erstellen. Keine Schreibrechte im Ordner ext_inc/newsfeed/'), 2, t('Newsfeed wurde erfolgreich aktuallisiert'));
        }
    }
}
