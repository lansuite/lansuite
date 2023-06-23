<?php

namespace LanSuite;

/**
 * Global translation
 */
class Translation
{
    /**
     * Global language.
     */
    private string $language = 'de';

    /**
     * Default language.
     * Will be used in cases where an invalid language was selected.
     */
    private string $defaultLanguage = 'de';

    /**
     * Basename of the translation file
     */
    private string $transfile_name = 'translation.xml';

    public array $lang_names = [
        'de' => 'Deutsch',
        'en' => 'Englisch',
        'es' => 'Spanisch',
        'fr' => 'Französich',
        'nl' => 'Holländisch',
        'it' => 'Italienisch'
    ];

    /**
     * Valid languages.
     */
    public array $valid_lang = ['de', 'en', 'es', 'fr', 'nl', 'it'];

    /**
     * In memory translation cache.
     * Only holds one language.
     */
    private array $lang_cache = [];

    /**
     * Is cache for module loaded (database)
     */
    private int $cachemod_loaded_db = 0;

    /**
     * Is cache for module loaded (xml)
     */
    private int $cachemod_loaded_xml  = 0;

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
     * Selects and set the global language.
     * The selection process happens based on a priority list:
     *  1. POST
     *  2. GET
     *  3. SESSION
     *  4. Database selection
     *  5. Default language
     *
     * Returns a valid language selected by the user.
     *
     * @return string
     */
    public function get_lang(): string
    {
        global $cfg;

        if (isset($_POST['language']) && $_POST['language']) {
            $_SESSION['language'] = $_POST['language'];

        } elseif (isset($_GET['language']) && $_GET['language']) {
            $_SESSION['language'] = $_GET['language'];
        }

        if (isset($_SESSION['language']) && $_SESSION['language']) {
            $this->language = $_SESSION['language'];

        } elseif (isset($cfg['sys_language']) && $cfg['sys_language']) {
            $this->language = $cfg['sys_language'];

        } else {
            $this->language = $this->defaultLanguage;
        }

        // Protect from bad code/injections
        if (!in_array($this->language, $this->valid_lang)) {
            $this->language = $this->defaultLanguage;
        }

        return $this->language;
    }

    /**
     * Load translations for a module from database into memory
     *
     * @param string $module    Module name or "DB" / "System"
     * @return void
     */
    private function load_cache_bydb(string $module): void
    {
        global $db;

        $res = $db->qry('
            SELECT
                `id`,
                `org`,
                `' . $this->language . '`
            FROM %prefix%translation
            WHERE
                file = %string%
                OR file = \'DB\'
                OR file = \'System\'
            ORDER BY FIELD(`file`, \'System,DB,'. $module .'\')', $module);

        while ($row = $db->fetch_array($res, 0)) {
            $this->setLangCacheEntry($module, $row['id'], $row[$this->language]);
        }
    }

    /**
     * Load translations for a module from XML-file into memory
     *
     * @param string    $module     Module name or "DB" / "System"
     * @return void
     */
    private function load_cache_byfile(string $module): void
    {
        $xmldata = $this->xml_read_to_array($module);
        if (is_array($xmldata)) {
            foreach ($xmldata as $data) {
                $text = $data[$this->language] ?? '';
                $this->setLangCacheEntry($module, $data['id'], $text);
            }
        }
    }

    /**
     * Sets a single language cache entry.
     *
     * Does not overwrite existing language cache entries.
     */
    private function setLangCacheEntry(string $module, string $id, string $text)
    {
        if (!array_key_exists($module, $this->lang_cache)) {
            $this->lang_cache[$module] = [];
        }

        if (array_key_exists($id, $this->lang_cache[$module]) && this->lang_cache[$module][$id] != '') {
            return;
        }

        if ($text == '') {
            return;
        }

        $this->lang_cache[$module][$id] = $text;
    }

    /**
     * Returns a single language cache entry.
     */
    private function getLangCacheEntry(string $module, string $key): string
    {
        if (!array_key_exists($module, $this->lang_cache)) {
            return '';
        }

        if (!array_key_exists($key, $this->lang_cache[$module])) {
            return '';
        }

        return $this->lang_cache[$module][$key];
    }

    /**
     * Replaces parameters from $input (%1, %2, %3, ...) with the content from $parameters at the same spot.
     *
     * @param string    $input          Text with placeholders (random text %1 here %2)
     * @param array     $parameters     Parameters that will replace %1, %2, ...
     * @param string    $key
     * @return string                   Text with inserted Parameters
     */
    public function ReplaceParameters(string $input, array $parameters = null, string $key = null): string
    {
        global $cfg, $auth;

        $i = 1;
        foreach ($parameters as $parameter) {
            $input = str_replace('%' . $i, $parameter, $input);
            $i++;
        }

        if ($key && (is_array($auth) && $auth['type'] >= \LS_AUTH_TYPE_ADMIN) && $cfg['show_translation_links']) {
            // TODO Check if this link works at all. We don't have a module with the name "misc"
            $input .= ' <a href=index.php?mod=misc&action=translation&step=40&id='. $key .'><img src=design/images/icon_translate.png height=10 width=10 border=0></a>';
        }

        return $input;
    }

    /**
     * Get a single translation value from database via hashcode.
     *
     * @param string    $hashkey    Hash code from original text in sourcecode
     * @param string    $module
     * @param string    $long
     * @return string
     */
    private function get_trans_db(string $hashkey, string $module, string $long): string
    {
        global $db;

        $entry = $this->getLangCacheEntry($module, $hashkey);
        if ($entry) {
            return $entry;
        }

        $row = $db->qry_first('
            SELECT
                `id`,
                `org`,
                `' . $this->language . '`
            FROM
                %prefix%translation' . $long . '
            WHERE
                id = %string%', $hashkey);

        if (is_array($row) && $row[$this->language]) {
            $entry = $row[$this->language];
        }

        return $entry;
    }

    /**
     * Read complete module translation from database and write to back file.
     * This will be only used for maintenance translations
     *
     * TODO Error handling for xml and file access
     * TODO This function overwrites files in the source tree.
     *      A LanSuite upgrade will overwrite the written file with a newer
     *      version from the source code repository.
     *      We should check if we want to support this functionality or modify it to
     *      write a file outside of the source tree, to make it safe for code upgrades.
     *
     * @param string    $module     Module name
     * @return void
     */
    public function xml_write_db_to_file(string $module): void
    {
        global $db;

        $xml = new XML();

        $output = '<?xml version="1.0" encoding="UTF-8"?'.">\r\n\r\n";
        $header = $xml->write_tag('filetype', 'LanSuite', 2);
        $header .= $xml->write_tag('version', '2.0', 2);
        $header .= $xml->write_tag('source', 'https://github.com/lansuite/lansuite', 2);
        $header .= $xml->write_tag('date', date('Y-m-d h:i'), 2);
        $header = $xml->write_master_tag("header", $header, 1);

        $table_head = $xml->write_tag('name', 'translation', 3);
        $tables = $xml->write_master_tag('table_head', $table_head, 2);

        $content = '';
        $res = $db->qry('
            SELECT
                `id`,
                `org`,
                `de`,
                `en`,
                `es`,
                `fr`,
                `nl`,
                `it`
            FROM %prefix%translation
            WHERE
              `file` = %string%
              AND `obsolete` = 0
              AND `id` != \'\'', $module);
        while ($row = $db->fetch_array($res)) {
            $entry = $xml->write_tag('id', $row['id'], 4);
            $entry .= $xml->write_tag('org', $row['org'], 4);

            $languagesToAdd = ['de', 'en', 'es', 'fr', 'nl', 'it'];
            foreach ($languagesToAdd as $languageShort) {
                if ($row[$languageShort] != '') {
                    $entry .= $xml->write_tag($languageShort, $row[$languageShort], 4);
                }
            }

            $entry .= $xml->write_tag('file', $module, 4);
            $content .= $xml->write_master_tag('entry', $entry, 3);
        }
        $db->free_result($res);

        // Read long Translations
        $res2 = $db->qry('
            SELECT
                `id`,
                `org`,
                `de`,
                `en`,
                `es`,
                `fr`,
                `nl`,
                `it`
            FROM %prefix%translation_long 
            WHERE 
              `file` = %string%
              AND `obsolete` = 0', $module);
        while ($row = $db->fetch_array($res2)) {
            $entry = $xml->write_tag('id', $row['id'], 4);
            $entry .= $xml->write_tag('org', $row['org'], 4);

            $languagesToAdd = ['de', 'en', 'es', 'fr', 'nl', 'it'];
            foreach ($languagesToAdd as $languageShort) {
                if ($row[$languageShort] != '') {
                    $entry .= $xml->write_tag($languageShort, $row[$languageShort], 4);
                }

                if ($row[$languageShort] != '') {
                    $entry .= $xml->write_tag($languageShort, $row[$languageShort], 4);
                }
            }

            $entry .= $xml->write_tag('file', $module, 4);
            $content .= $xml->write_master_tag('entry', $entry, 3);
        }
        $db->free_result($res2);

        $tables .= $xml->write_master_tag('content', $content, 2);
        $lansuite = $xml->write_master_tag('table', $tables, 1);
        $output .= $xml->write_master_tag('lansuite', $header . $lansuite, 0);

        // We overwrite the original file
        $file = $this->get_trans_filename($module);
        $file_handle = fopen($file, 'w');
        fwrite($file_handle, $output);
        fclose($file_handle);
    }

    /**
     * Reads all translation from the $module XML file
     * and returns the data.
     *
     * @param string    $module     Module name e.g. file-field
     * @return array
     */
    private function xml_read_to_array(string $module): array
    {
        $records = [];
        $xml = new XML();

        $lang_file = $this->get_trans_filename($module);
        if (!file_exists($lang_file)) {
            return $records;
        }

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

        return $records;
    }

    /**
     * Get the file path for a language file.
     * Path for "System"/"DB" and modules are different.
     *
     * @param string    $module     Module name ("System", "DB", Module of choice)
     * @return string
     */
    private function get_trans_filename(string $module): string
    {
        $file = match ($module) {
            'System', 'DB' => 'inc/language/' . $module . '_' . $this->transfile_name,
            default => 'modules/' . $module . '/mod_settings/' . $this->transfile_name,
        };

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
        if (str_contains($CurrentFile, 'modules/')) {
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
                $treffer = [...$treffer1, ...$treffer2];

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

    public function translate(array $args)
    {
        global $db, $config, $func, $translation_no_html_replace;

        $parameters = [];

        // Prepare function parameters
        // First argument is the input string, the following are parameters
        $input = (string) array_shift($args);
        foreach ($args as $CurrentArg) {
            // If second Parameter is Array (old Style)
            if (!is_array($CurrentArg)) {
                $parameters[] = $CurrentArg;
            } else {
                $parameters = $CurrentArg;
            }
        }

        if ($input == '') {
            return '';
        }

        $key = md5($input);
        $module = '';
        if (isset($_GET['mod']) && $_GET['mod']) {
            $module = $_GET['mod'];
        }

        $trans_text = '';
        if (strlen($input) > 255) {
            $long = '_long';
        } else {
            $long = '';
        }

        $translationEntry = $this->getLangCacheEntry($module, $key);
        // If we can't find the translation in the $module cache
        // we walk one hierarchy higher, to the System translations.
        if ($translationEntry == '') {
            $translationEntry = $this->getLangCacheEntry('System', $key);
        }

        if ($translationEntry != '') {
            // Already in memory cache ($this->lang_cache[key])
            $output = $this->ReplaceParameters($translationEntry, $parameters, $key);

        } else {
            // Try to read from DB
            if ($this->language == 'de') {
                // All texts in source are in german at the moment
                $output = $this->ReplaceParameters($input, $parameters, $key);

            } else {
                if ($db->success && EnvIsConfigured()) {
                    $trans_text = $this->get_trans_db($key, $_GET['mod'], $long);
                }

                // If ok replace parameter
                if ($trans_text != '' && $trans_text != null) {
                    $output = $this->ReplaceParameters($trans_text, $parameters);

                // If any problem on get translations just return $input
                } else {
                    $output = $this->ReplaceParameters($input, $parameters, $key);
                }
            }
        }

        if ($translation_no_html_replace) {
            $translation_no_html_replace = false;

            // Deprecated. Should be replaced in t() by '<', '>' and '[br]'
            $output = str_replace('--lt--', '<', $output);
            $output = str_replace('--gt--', '>', $output);
            $output = str_replace('HTML_NEWLINE', '<br />', $output);

            return $func->text2html($output, 4);
        }

        return $output;
    }
}
