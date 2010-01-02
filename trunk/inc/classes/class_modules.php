<?php

/**
 * Manage Modules for Lansuite
 *
 * @package lansuite_core
 * @author bytekilla
 * @version $Id$
 * @access public
 */
class modules {

  /**#@+
   * Intern Variables
   * @access private
   * @var mixed
   */
    var $active_modules = array();      // Active Modules
  /**#@-*/
  
  /**
   * CONSTRUCTOR : Initialize basic Variables
   *
   */
    function modules() {
        global $db;

        // Read Active Modules (once, better performance)
        $res = $db->qry("SELECT name FROM %prefix%modules WHERE active = 1");
        while($row = $db->fetch_array($res)) $this->active_modules[] = $row['name'];
        $db->free_result($res);

        // Add Systemmodules (always active)
        $this->active_modules[] = 'helplet';
        $this->active_modules[] = 'popups';
        $this->active_modules[] = 'auth';
    }

  /**
   * Return aktive Modules in a Array
   *
   * @return array Returns the active Modules
   */
    function get_act_modules() {
        return $this->active_modules;
    }

  /**
   * Give Status for a specific Modul
   *
   * @param string Modul
   * @return boolean Returns the Status. True = active
   */
    function get_act_status($modul) {
        if (in_array($modul, $this->active_modules)) {
            return true;
        } else {
            return false;
        }
    }

  /**
   * Activate a specific Modul
   *
   * @param string Modul
   * @return boolean Returns the Status of Action. True=Success
   */
    function set_active($modul) {

    }

  /**
   * Activate a specific Modul
   *
   * @param string Modul
   * @return boolean Returns the Status of Action. True=Success
   */
    function set_inactive($modul) {

    }

  /**
   * Check Modul if minimum Requirements are fulfilled
   *
   * @param string Modul
   * @return boolean Returns the Status of Modulcheck. True=Modul Ok
   */
    function check_modul($modul) {
        /*
        Punkte könnten u.a. sein Verzeichniss vorhanden, Configdateien
        vorhanden, Uebersetzung vorhanden, etc. Was macht sinn... es
        muss ja nicht alles vorhanden sein.
        */
    }

  /**
   * Get and Reset Userpermission
   *
   * @param mixed Actual Mudulname
   * @param mixed Actual Userid
   * @return boolean Should Userrights to be resetet?
   */
    function get_modulpermission($modul, $userid) {
        global $db;
        // Has at least someone access to this mod?
        $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions WHERE module = %string%", $modul);
        // If so: Has the current user access to this mod?
        if ($permission['found']) {
            $permission = $db->qry_first("SELECT 1 AS found FROM %prefix%user_permissions WHERE module = %string% AND userid = %int%", $modul, $userid);
            // If not: Set his rights to user-rights
            if (!$permission['found']) return 1;
                else return 0;
        }
    }
    
  /**
   * Show Modulinfos (Stored in module.xml/DB)
   *
   * @param mixed $modul
   * @param mixed $field
   * @return string Returns the Fielddata
   */
    function get_modulinfo($modul, $field) {
        // Possible Fields (also see module.xml)
        // name : The modules internal name.
        // caption: The modulname displayed to the user
        // description: A short description of the module
        // active: Is the module activated by default? 0/1
        // changeable: May the user de-/activate the module? 0/1
        // author: The author of this module
        // email: The E-Mail-Adress of the modules author
        // version: The version of this module
        // state: The state of this module (f.e.: stable, in development, beta, ...)
    }

}
?>