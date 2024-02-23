<?php

namespace LanSuite;

/**
 * Global translation
 *
 * TODO Implement proper caching interface
 * TODO Implement dependency injection to remove `global` keywords
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

    /**
     * @var array<string, string>
     */
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
     *
     * @var array<string>
     */
    public array $valid_lang = ['de', 'en', 'es', 'fr', 'nl', 'it'];

    /**
     * In memory translation cache.
     * Only holds one language.
     *
     * @var array<string, array<string, string>>
     */
    private array $lang_cache = [];

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
    public function load_trans(string $mode, string $akt_modul): void
    {
        if ($mode == 'db') {
            // System is configured, Language will be loaded from DB
            $this->load_cache_bydb($akt_modul);

        } elseif ($mode == 'xml') {
            // System is on Install, Language will be loaded from XML
            $this->load_cache_byfile('System');
            $this->load_cache_byfile('DB');
            $this->load_cache_byfile($akt_modul);
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
    private function setLangCacheEntry(string $module, string $id, string $text): void
    {
        if (!array_key_exists($module, $this->lang_cache)) {
            $this->lang_cache[$module] = [];
        }

        if (array_key_exists($id, $this->lang_cache[$module]) && $this->lang_cache[$module][$id] != '') {
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
     * @param string        $input          Text with placeholders (random text %1 here %2)
     * @param array<mixed>  $parameters     Parameters that will replace %1, %2, ...
     * @param string        $key
     * @return string                       Text with inserted Parameters
     */
    public function ReplaceParameters(string $input, array $parameters, string $key = ''): string
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

            foreach ($this->valid_lang as $languageShort) {
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

            foreach ($this->valid_lang as $languageShort) {
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
     * @param string                                    $module     Module name e.g. file-field
     * @return array<string, array<string, string>>
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
            $id = $xml->getFirstTagContent('id', $entry, true);
            $records[$id]['id'] = $id;
            $records[$id]['org'] = $xml->getFirstTagContent('org', $entry, true);
            $records[$id]['de'] = $xml->getFirstTagContent('de', $entry, true);
            $records[$id]['en'] = $xml->getFirstTagContent('en', $entry, true);
            $records[$id]['fr'] = $xml->getFirstTagContent('fr', $entry, true);
            $records[$id]['it'] = $xml->getFirstTagContent('it', $entry, true);
            $records[$id]['es'] = $xml->getFirstTagContent('es', $entry, true);
            $records[$id]['nl'] = $xml->getFirstTagContent('nl', $entry, true);
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
     * Reads language specific strings from the user database tables and write those into the translation table.
     *
     * Example:
     *      TUpdateFromDB('menu', 'caption') Reads all "captions"-strings from table "menu"
     *
     * TODO We may want to think about this, moving out of the Translation class into a "Maintenance" CLI or anything like this.
     *
     * @param string    $table      Table name (e.g. menu)
     * @param string    $field      Field name (e.g. caption)
     * @return int                  Number of insert entries that have been written to the database
     */
    public function TUpdateFromDB(string $table, string $field): int
    {
        global $db, $FoundTransEntries;

        $i = 0;
        $res = $db->qry('SELECT `'. $field .'` FROM %prefix%' . $table);
        while ($row = $db->fetch_array($res)) {
            if ($row[$field] == '') {
                continue;
            }

            $key = md5($row[$field]);
            $row2 = $db->qry_first('SELECT 1 AS `found`, `tid` FROM %prefix%translation WHERE `id` = %string%', $key);

            if (is_bool($row2)) {
                $db->qry('REPLACE INTO %prefix%translation SET `id` = %string%, `file` = \'DB\', `org` = %string%', $key, $row[$field]);
                $row2 = [
                    'tid' => $db->insert_id(),
                ];
                $i++;
            }

            // Array is compared with the database later for synchronization
            $FoundTransEntries[] = $row2['tid'];
        }
        $db->free_result($res);

        return $i;
    }

    /**
     * Reads all t()-Function strings from the complete sourcecode and write those into the translation table.
     *
     * TODO We may want to think about this, moving out of the Translation class into a "Maintenance" CLI or anything like this.
     *
     * @param string    $baseDir        Path to Scan
     * @param int       $sub
     * @return string
     */
    public function TUpdateFromFiles(string $baseDir, int $sub = 0): string
    {
        global $db, $FoundTransEntries;

        $output = '';
        if (!str_ends_with($baseDir, DIRECTORY_SEPARATOR)) {
            $baseDir .= DIRECTORY_SEPARATOR;
        }

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
                          SELECT
                            1 AS `found`,
                            `tid`
                          FROM %prefix%translation%plain% 
                          WHERE 
                            `id` = %string%
                            AND (
                              `file` = "System"
                              OR `file` = %string%
                            )', $long, $key, $CurrentFile);
                        if (is_array($row)) {
                            $output .= $CurrentFile . '@' . $CurrentPos . ': ' . $CurrentTrans .'<br />';

                        } else {
                            // New -> Insert to DB
                            $db->qry("
                              REPLACE INTO %prefix%translation%plain% 
                              SET 
                                `id` = %string%,
                                `file` = %string%,
                                `org` = %string%", $long, $key, $CurrentFile, $CurrentTrans);
                            $row = [
                                'tid' => $db->insert_id(),
                            ];
                            $output .= '<font color="#00ff00">' . $CurrentFile . '@' . $CurrentPos . ': ' . $CurrentTrans .'</font><br />';
                        }
                        if (!$long) {
                            $FoundTransEntries[] = $row['tid'];
                        }
                    }
                }
            } elseif ($file != '.' && $file != '..' && $file != '.svn' && $file != '.git' && $file != '.docker' && $file != '.github' && is_dir($FilePath)) {
                $output .= $this->TUpdateFromFiles($FilePath, $sub++);
            }
        }

        // Mark entries as obsolete, which no do no longer exist
        if (($sub == 1 && $CurrentFile != 'System') || ($sub == 0 && $CurrentFile == 'System')) {
            $res = $db->qry("
              SELECT
                `tid`,
                `file`,
                `org`
              FROM %prefix%translation 
              WHERE 
                `file` = %string%
                AND `obsolete` = 0", $CurrentFile);
            while ($row = $db->fetch_array($res)) {
                if (!in_array($row['tid'], $FoundTransEntries)) {
                    $db->qry("UPDATE %prefix%translation SET `obsolete` = 1 WHERE `tid` = %int%", $row['tid']);
                    $output .= '<font color="#ff0000">'. $row['file'] .': '. $row['org'] .'</font><br />';
                }
            }
            $db->free_result($res);
            $FoundTransEntries = [];
        }

        closedir($ResDir);

        return $output;
    }

    /**
     * Translates a single word/sentence into the configured language.
     * This function accepts multiple parameter:
     *  1. The key/name of the string to translate
     *  2. A list of values to replace in the key/name of the string to translate.
     *
     * If your translation contains variables, like a username, this variable is
     * represented by %1. %2 describes the second variable, and so on.
     * In this case you call the function like
     *  ->translate(<key>, $username, $var2)
     *
     * Example:
     *  ->translate('Du wurdest erfolgreich ausgeloggt.')
     *
     * @param array<mixed> $args    List of variadic arguments (see PHP doc for description)
     */
    public function translate(array $args): string
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
        $module = $_GET['mod'] ?? '';

        $trans_text = '';
        $long = '';
        if (strlen($input) > 255) {
            $long = '_long';
        }

        // TODO Unit test this function and make the logic below easier

        $translationEntry = $this->getLangCacheEntry($module, $key);
        // If we can't find the translation in the $module cache
        // we walk one hierarchy higher, to the System translations.
        if ($translationEntry == '') {
            $translationEntry = $this->getLangCacheEntry('System', $key);
        }

        if ($translationEntry != '') {
            // Already in memory cache
            $output = $this->ReplaceParameters($translationEntry, $parameters, $key);

        } else {
            // Try to read from DB
            if ($this->language == $this->defaultLanguage) {
                // All texts in source are in german at the moment
                $output = $this->ReplaceParameters($input, $parameters, $key);

            } else {
                if ($db->success && EnvIsConfigured()) {
                    $trans_text = $this->get_trans_db($key, $_GET['mod'], $long);
                }

                // If ok replace parameter
                if ($trans_text != '') {
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
