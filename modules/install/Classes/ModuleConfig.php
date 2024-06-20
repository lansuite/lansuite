<?php

namespace LanSuite\Module\Install;

/**
 * Class to handle module specific functions currently distributed everywhere in the code.
 * Separate "Module" class may come later to deal with this on a one-object-per-module basis
 */
class ModuleConfig
{

    private \LanSuite\Database $database;

    public function __construct( \LanSuite\Database &$database = null)
    {
        if($database) {
            // Use provided DB adaptor if existing, use LS standard global obj otherwise
            $this->database = &$database;
            return;
        }
            global $database;
            $this->database = &$database;
    }

    /**
     * Enables/activates a module based on the name
     *
     * @param string $moduleName The name of the module to be enabled
     */
public function enable(string $moduleName) : bool
    {
        $this->database->query("UPDATE %prefix%modules SET active = 1 WHERE name = ?", [$moduleName]);
    }

    /**
     * Disables a module based on the name
     *
     * @param string $moduleName The name of the module to be disabled
     */
public function disable(string $moduleName) : bool
    {
        $this->database->query("UPDATE %prefix%modules SET active = 0 WHERE name = ?", [$moduleName]);
    }

    /**
     * Returns the activation state of the module given by name
     *
     * @param string $moduleName The name of the module to be disabled
     */
    public function getStatus(string $moduleName) : bool
    {

        $moduleStatus= $this->database->queryWithOnlyFirstRow('SELECT active FROM %prefix%modules WHERE name = ?', [$moduleName]);
        return $moduleStatus['active'];
    }

    /**
     * Returns list of modules defined in the database
     *
     * @param bool|null $filterState Determines if modules with a given state will be returned or all of them if left empty
     */
    public function getModules(bool|null $filterState = null) : array
    {
        $where = '';
        if ($filterState) {
            $where = ' WHERE active = ' . (int) $filterState;
        }
        return $this->database->queryWithFullResult('SELECT `name`, description, active, changeable, `version`, `state`  FROM %prefix%modules'. $where);
    }

    /**
     * Triggers DB update for the mdoule
     *
     * @param string $moduleName Name of the module to update
     */
    public function updateModuleTables(string $moduleName = null) : void
    {
        $importXml = new \LanSuite\XML();
        $installImport = new \LanSuite\Module\Install\Import($importXml);
        $install = new \LanSuite\Module\Install\Install($installImport);
        $install->WriteTableFromXMLFile($moduleName, 1);
    }

    /**
     * (Over-)Writes Menu entries for a given module
     *
     * @param string $moduleName The name of the module to be written
     *
     */
    public function writeModuleMenu($moduleName='')
    {
        global $database;
        $xml = new \LanSuite\XML();
        $file = "modules/$moduleName/mod_settings/menu.xml";
        if (file_exists($file)) {
            $handle = fopen($file, "r");
            $xml_file = fread($handle, filesize($file));
            fclose($handle);

            $menu = $xml->get_tag_content("menu", $xml_file);
            $main_pos = $xml->get_tag_content("pos", $menu);
            $entrys = $xml->get_tag_content_array("entry", $menu);

            //successfully read out file, clean up existing entries
            $database->query('DELETE FROM %prefix%menu WHERE module = ?', [$moduleName]);
            //obtain ID of box that contains the navigation menu
            $menubox = $database->queryWithOnlyFirstRow('SELECT boxid FROM %prefix%boxes WHERE source = \'menu\' AND active = 1');

            //iterate through all entries and write them
            $i = 0;
            foreach ($entrys as $entry) {
                $action = $xml->get_tag_content("action", $entry);
                $file = $xml->get_tag_content("file", $entry);
                $caption = $xml->get_tag_content("caption", $entry);
                $hint = $xml->get_tag_content("hint", $entry);
                $link = $xml->get_tag_content("link", $entry);
                $requirement = $xml->get_tag_content("requirement", $entry);
                $level = $xml->get_tag_content("level", $entry);
                $needed_config = $xml->get_tag_content("needed_config", $entry);

                if ($file == "") {
                    $file = $action;
                }
                if (!$level) {
                    $level = 0;
                }
                if (!$requirement) {
                    $requirement = 0;
                }

                ($level == 0)? $pos = $main_pos : $pos = $i;
                $i++;

                $database->query(
                    "INSERT INTO %prefix%menu SET module = ?, action = ?, file = ?, caption = ?, hint = ?, link = ?, requirement = ?, level = ?, pos = ?, needed_config = ?, boxid = ?",
                    [$moduleName,
                    $action,
                    $file,
                    $caption,
                    $hint,
                    $link,
                    $requirement,
                    $level,
                    $pos,
                    $needed_config,
                    $menubox['boxid']]
                );
            }
        } else {
            echo('module.xml für modul nicht gefunden');
        }
    }
}