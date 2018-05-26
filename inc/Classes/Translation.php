<?php

namespace LanSuite;

/**
 * Global translation
 */
class Translation
{
    /**
     * Global language
     *
     * @var string
     */
    public $language = 'de';

    /**
     * Basename of the translation file
     *
     * @var string
     */
    private $transfile_name = 'translation.xml';

    /**
     * @var array
     */
    public $lang_names = [
        'de' => 'Deutsch',
        'en' => 'Englisch',
        'es' => 'Spanisch',
        'fr' => 'Französich',
        'nl' => 'Holländisch',
        'it' => 'Italienisch'
    ];

    /**
     * Valid languages
     *
     * @var array
     */
    public $valid_lang = ['de', 'en', 'es', 'fr', 'nl', 'it'];

    /**
     * Temporary translations
     *
     * @var array
     */
    public $lang_cache = [];

    /**
     * Is cache for module loaded (db)
     *
     * @var int
     */
    private $cachemod_loaded_db = 0;

    /**
     * Is cache for module loaded (xml)
     *
     * @var int
     */
    private $cachemod_loaded_xml  = 0;

    public function __construct()
    {
        // Read language from GET, POST & set
        $this->get_lang();
    }

    /**
     * Load translations
     *
     * @param string    $mode       Data source mode (xml for install, db for running system)
     * @param string    $akt_modul  Active module
     */
    public function load_trans($mode, $akt_modul)
    {
        if ($mode == 'db') {
            // System is configured, Language will be loaded from DB
            $this->load_cache_bydb($akt_modul);
            $this->cachemod_loaded_db = 1;
        } elseif ($mode == 'xml') {
            // System is on Install, Language will be loaded from XML
            $this->load_cache_byfile('System');
            $this->load_cache_byfile('DB');
            $this->load_cache_byfile($akt_modul);
            $this->cachemod_loaded_xml = 1;
        }
    }

    /**
     * Select and set the global language
     *
     * Returns a valid language selected by the user
     *
     * @return string
     */
    public function get_lang()
    {
        global $cfg;

        if (isset($_POST['language']) && $_POST['language']) {
            $_SESSION['language'] = $_POST['language'];
        } elseif (isset($_GET['language']) && $_GET['language']) {
            $_SESSION['language'] = $_GET['language'];
        }

        if (isset($_SESSION['language']) && $_SESSION['language']) {
            $this->language = $_SESSION['language'];
        } elseif ($cfg['sys_language']) {
            $this->language = $cfg['sys_language'];
        } else {
            $this->language = 'de';
        }

        // Protect from bad code/injections
        if (!in_array($this->language, $this->valid_lang)) {
            $this->language = 'de';
        }

        return $this->language;
    }

    /**
     * Load translations for a module from database into memory
     *
     * @param string $module    Module name or DB / System
     * @return void
     */
    private function load_cache_bydb($module)
    {
        global $db;

        if ($db->success) {
            $res = $db->qry('
                SELECT id, org, ' . $this->language . ' 
                FROM %prefix%translation 
                WHERE 
                    file = %string% 
                    OR file = \'DB\' 
                    OR file = \'System\' 
                ORDER BY FIELD(file, \'System,DB,'. $module .'\')', $module);
            while ($row = $db->fetch_array($res, 0)) {
                if ($row[$this->language] != '') {
                    if ($this->lang_cache[$module][$row['id']] == '') {
                        $this->lang_cache[$module][$row['id']] = $row[$this->language];
                    }
                }
            }
        }
    }

    /**
     * Load translations from XML-file into memory
     *
     * @param string    $module     Module name or DB / System
     * @return void
     */
    private function load_cache_byfile($module)
    {
        $xmldata = $this->xml_read_to_array($module);
        if (is_array($xmldata)) {
            foreach ($xmldata as $data) {
                $text = '';
                if (isset($data[$this->language])) {
                    $text = $data[$this->language];
                }

                if (array_key_exists($module, $this->lang_cache) && $this->lang_cache[$module][$data['id']] == '' && $text != '') {
                    $this->lang_cache[$module][$data['id']] = $text;
                }
            }
        }
    }

    /**
     * Get the translation from database via hashcode
     *
     * @param string    $input          Text with placeholders (blabla %1 bla %2)
     * @param array     $parameters     Parameters
     * @param string    $key
     * @return string                   Text with inserted Parameters
     */
    public function ReplaceParameters($input, $parameters = null, $key = null)
    {
        global $cfg, $auth;

        $z = 1;
        if ($parameters) {
            foreach ($parameters as $parameter) {
                $input = str_replace('%' . $z, $parameter, $input);
                $z++;
            }
        }

        if ($key && $auth['type'] >= 2 && $cfg['show_translation_links']) {
            $input .= ' <a href=index.php?mod=misc&action=translation&step=40&id='. $key .'><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';
        }

        return $input;
    }

    /**
     * Get the translation from database via hashcode
     *
     * @param string    $hashkey    Hash code from original text in sourcecode
     * @param string    $module
     * @param string    $long
     * @return string
     */
    public function get_trans_db($hashkey, $module, $long)
    {
        global $db;

        if ($this->lang_cache[$module][$hashkey]) {
            $translated = $this->lang_cache[$module][$hashkey];
        } else {
            $row = $db->qry_first('
                SELECT id, org, ' . $this->language . ' 
                FROM %prefix%translation' . $long . ' 
                WHERE id = %string%', $hashkey);

            if ($row[$this->language]) {
                $translated = $row[$this->language];
            } else {
                $translated = '';
            }
        }

        return $translated;
    }

    /**
     * Read complete Module translation from database and write to back file.
     * This will be only used for maintenance translations
     *
     * TODO Error handling for xml and file access
     *
     * @param string    $module     Module name
     * @return void
     */
    public function xml_write_db_to_file($module)
    {
        global $db;

        $xml = new XML();

        $output = '<?xml version="1.0" encoding="UTF-8"?'.">\r\n\r\n";
        $header = $xml->write_tag('filetype', 'LanSuite', 2);
        $header .= $xml->write_tag('version', '2.0', 2);
        $header .= $xml->write_tag('source', 'http://www.lansuite.de', 2);
        $header .= $xml->write_tag('date', date('Y-m-d h:i'), 2);
        $header = $xml->write_master_tag("header", $header, 1);

        $table_head = $xml->write_tag('name', 'translation', 3);
        $tables = $xml->write_master_tag('table_head', $table_head, 2);

        $content = '';
        $res = $db->qry('
            SELECT * 
            FROM %prefix%translation 
            WHERE 
              file = %string% 
              AND obsolete = 0', $module);
        while ($row = $db->fetch_array($res)) {
            if ($row['id'] != '') {
                $entry = $xml->write_tag('id', $row['id'], 4);
                $entry .= $xml->write_tag('org', $row['org'], 4);

                if ($row['de'] != '') {
                    $entry .= $xml->write_tag('de', $row['de'], 4);
                }

                if ($row['en'] != '') {
                    $entry .= $xml->write_tag('en', $row['en'], 4);
                }

                if ($row['es'] != '') {
                    $entry .= $xml->write_tag('es', $row['es'], 4);
                }

                if ($row['fr'] != '') {
                    $entry .= $xml->write_tag('fr', $row['fr'], 4);
                }

                if ($row['nl'] != '') {
                    $entry .= $xml->write_tag('nl', $row['nl'], 4);
                }

                if ($row['it'] != '') {
                    $entry .= $xml->write_tag('it', $row['it'], 4);
                }

                $entry .= $xml->write_tag('file', $module, 4);
                $content .= $xml->write_master_tag('entry', $entry, 3);
            }
        }
        $db->free_result($res);

        // Read long Translation
        $res2 = $db->qry('
            SELECT * 
            FROM %prefix%translation_long 
            WHERE 
              file = %string% 
              AND obsolete = 0', $module);
        while ($row2 = $db->fetch_array($res2)) {
            $entry = $xml->write_tag('id', $row['id'], 4);
            $entry .= $xml->write_tag('org', $row['org'], 4);

            if ($row['de'] != '') {
                $entry .= $xml->write_tag('de', $row['de'], 4);
            }

            if ($row['en'] != '') {
                $entry .= $xml->write_tag('en', $row['en'], 4);
            }

            if ($row['es'] != '') {
                $entry .= $xml->write_tag('es', $row['es'], 4);
            }

            if ($row['fr'] != '') {
                $entry .= $xml->write_tag('fr', $row['fr'], 4);
            }

            if ($row['nl'] != '') {
                $entry .= $xml->write_tag('nl', $row['nl'], 4);
            }

            if ($row['it'] != '') {
                $entry .= $xml->write_tag('it', $row['it'], 4);
            }

            $entry .= $xml->write_tag('file', $module, 4);
            $content .= $xml->write_master_tag('entry', $entry, 3);
        }
        $db->free_result($res2);

        $tables .= $xml->write_master_tag('content', $content, 2);
        $lansuite = $xml->write_master_tag('table', $tables, 1);
        $output .= $xml->write_master_tag('lansuite', $header . $lansuite, 0);

        // File handling: Make backup copy
        $file = $this->get_trans_filename($module);
        $file_handle = fopen($file, 'w');
        fputs($file_handle, $output);
        fclose($file_handle);
    }

    /**
     * Parse all language sets into an array
     *
     * @param string    $module     Module name e.g. file-field
     * @return array
     */
    private function xml_read_to_array($module)
    {
        $records = [];
        $xml = new XML();

        $lang_file = $this->get_trans_filename($module);
        if (file_exists($lang_file)) {
            $xml_file = fopen($lang_file, 'r');
            $file_cont = fread($xml_file, filesize($lang_file));
            fclose($xml_file);

            $entries = $xml->getTagContentArray('entry', $file_cont);
            foreach ($entries as $entry) {
                $id = $xml->getFirstTagContent('id', $entry, 1);
                $records[$id]['id'] = $id;
                $records[$id]['org'] = $xml->getFirstTagContent('org', $entry, 1);
                $records[$id]['de'] = $xml->getFirstTagContent('de', $entry, 1);
                $records[$id]['en'] = $xml->getFirstTagContent('en', $entry, 1);
                $records[$id]['fr'] = $xml->getFirstTagContent('fr', $entry, 1);
                $records[$id]['it'] = $xml->getFirstTagContent('it', $entry, 1);
                $records[$id]['es'] = $xml->getFirstTagContent('es', $entry, 1);
                $records[$id]['nl'] = $xml->getFirstTagContent('nl', $entry, 1);
            }
        }

        return $records;
    }

    /**
     * Get the file path for a language file.
     * Path for System/DB and modules are different.
     *
     * @param string    $module     Module name (System, DB, Module ...)
     * @return string
     */
    private function get_trans_filename($module)
    {
        switch ($module) {
            case 'DB':
                $file = 'inc/language/' . $module . '_' . $this->transfile_name;
                break;

            case 'System':
                $file = 'inc/language/' . $module . '_' . $this->transfile_name;
                break;

            default:
                $file = 'modules/' . $module . '/mod_settings/' . $this->transfile_name;
        }

        return $file;
    }

    /**
     * Reads language specific strings from the user database tables and write those into the tranlation table.
     * Example:
     *      TUpdateFromDB('menu', 'caption') Reads all "captions"-strings from table "menu"
     *
     * @param string    $table      Table name (e.g. menu)
     * @param string    $field      Field name (e.g. caption)
     * @return int                  Number of insert entries
     */
    public function TUpdateFromDB($table, $field)
    {
        global $db, $FoundTransEntries;

        $i = 0;
        $res = $db->qry('SELECT '. $field .' FROM %prefix%' . $table);
        while ($row = $db->fetch_array($res)) {
            if ($row[$field] != '') {
                $key = md5($row[$field]);
                $row2 = $db->qry_first('SELECT 1 AS found, tid FROM %prefix%translation WHERE id = %string%', $key);

                if (!$row2['found']) {
                    $db->qry('REPLACE INTO %prefix%translation SET id = %string%, file = \'DB\', org = %string%', $key, $row[$field]);
                    $row2['tid'] = $db->insert_id();
                    $i++;
                }

                // Array is compared with the database later for synchronization
                $FoundTransEntries[] = $row2['tid'];
            }
        }
        $db->free_result($res);

        return $i;
    }

    /**
     * Reads all t()-Function strings from the complete sourcecode and write those into the translation table.
     *
     * @param string    $baseDir        Path to Scan
     * @param int $sub
     * @return string
     */
    public function TUpdateFromFiles($baseDir, $sub = 0)
    {
        global $db, $FoundTransEntries;

        $output = '';
        $baseDir .= '/';
        if ($sub == 0) {
            $FoundTransEntries = [];
        }

        // Generate module name from file
        $CurrentFile = str_replace('\\', '/', $baseDir);
        if (strpos($CurrentFile, 'modules/') !== false) {
            $CurrentFile = substr($CurrentFile, strpos($CurrentFile, 'modules/') + 8, strlen($CurrentFile));
            $CurrentFile = substr($CurrentFile, 0, strpos($CurrentFile, '/'));
        } else {
            $CurrentFile = 'System';
        }

        $ResDir = opendir($baseDir);
        while ($file = readdir($ResDir)) {
            $FilePath = $baseDir . $file;

            if (substr($file, strlen($file) - 4, 4) == '.php') {
                $ResFile = fopen($FilePath, 'r');
                $content = fread($ResFile, filesize($FilePath));
                fclose($ResFile);

                preg_match_all('/([^a-zA-Z0-9]+t\\(\\\')(.*?)(\\\'\\)|\\\'\\,)/', $content, $treffer1, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                preg_match_all('/([^a-zA-Z0-9]+t\\(\\")(.*?)(\\"\\)|\\"\\,)/', $content, $treffer2, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                $treffer = array_merge($treffer1, $treffer2);

                foreach ($treffer as $wert) {
                    $CurrentPos = $wert[2][1];
                    $CurrentTrans = $wert[2][0];

                    if ($CurrentTrans != '') {
                        $key = md5($CurrentTrans);
                        if (strlen($CurrentTrans) > 255) {
                            $long = '_long';
                        } else {
                            $long = '';
                        }

                        // Do only add expressions, which are not already in system lang-file
                        $row = $db->qry_first('
                          SELECT 1 AS found, tid 
                          FROM %prefix%translation%plain% 
                          WHERE 
                            id = %string% 
                            AND (
                              file = "System" 
                              OR file = %string%
                            )', $long, $key, $CurrentFile);
                        if ($row['found']) {
                            $output .= $CurrentFile . '@' . $CurrentPos . ': ' . $CurrentTrans .'<br />';
                        } else {
                            // New -> Insert to DB
                            $db->qry("
                              REPLACE INTO %prefix%translation%plain% 
                              SET 
                                id = %string%, 
                                file = %string%, 
                                org = %string%", $long, $key, $CurrentFile, $CurrentTrans);
                            $row['tid'] = $db->insert_id();
                            $output .= '<font color="#00ff00">' . $CurrentFile . '@' . $CurrentPos . ': ' . $CurrentTrans .'</font><br />';
                        }
                        if (!$long) {
                            $FoundTransEntries[] = $row['tid'];
                        }
                    }
                }
            } elseif ($file != '.' && $file != '..' && $file != '.svn' && is_dir($FilePath)) {
                $output .= $this->TUpdateFromFiles($FilePath, $sub++);
            }
        }

        // Mark entries as obsolete, which no do no longer exist
        if (($sub == 1 && $CurrentFile != 'System') || ($sub == 0 && $CurrentFile == 'System')) {
            $res = $db->qry("
              SELECT tid, file, org 
              FROM %prefix%translation 
              WHERE 
                file = %string% 
                AND obsolete = 0", $CurrentFile);
            while ($row = $db->fetch_array($res)) {
                if (!in_array($row['tid'], $FoundTransEntries)) {
                    $db->qry("UPDATE %prefix%translation SET obsolete = 1 WHERE tid = %int%", $row['tid']);
                    $output .= '<font color="#ff0000">'. $row['file'] .': '. $row['org'] .'</font><br />';
                }
            }
            $db->free_result($res);
            $FoundTransEntries = [];
        }

        closedir($ResDir);

        return $output;
    }
}
