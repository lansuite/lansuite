<?

/**
 * Class PLUGIN for Lansuite
 * This Class generates the needed Dataarray to load the
 * Pluginfiles 
 *
 * @package ls_core
 * @author bytekilla
 * @version $Id$
 * @access public
 */
class plugin {

  /**#@+
   * Intern Variables
   * @access private
   * @var mixed
   */
    var $list    =     array();          // Arrayliste mit den Plugins
  /**#@-*/

  /**
   * Constructor Pluginsystem
   * @param array   Array with active Modules
   * @param string  Selected Plugin
   */
    function plugin($activemodules, $plugin) {
        // Read Config of Plugins
        foreach ($activemodules as $modul) {
            $cfg_file = "modules/".$modul."/plugins/plugin_cfg.php";
            // 
            if (file_exists($cfg_file)) {
               $cfg_plugin = parse_ini_file($cfg_file, 1);
               if ($cfg_plugin[$plugin]['position'] > 0) $this->list[] =
               array(
                   "pos"     => $cfg_plugin[$plugin]['position'],
                   "caption" => $cfg_plugin[$plugin]['caption'],
                   "modul"   => $modul,
                   "file"    => "modules/".$modul."/plugins/inc_".$plugin.".php",
                   );
            }
        }
        $this->list = $this->sortarray($this->list);
    }

  /**
   * Sortarray sorts an 2dimesional Array by "pos" Field
   * @access private
   * @param mixed Array with Pluginlist array("pos"=>'...', "caption"=>'..., "modul"=>'..., "file"=>'...);
   * @return mixed Returns the sorted $plug_list Array
   */
    function sortarray($plug_list) {
         if (is_array($plug_list)) {
             $plug_sort = array();
             foreach($plug_list as $key => $array) {
                 $plug_sort[$key] = $array['pos'];
             }
             array_multisort($plug_sort, SORT_ASC, SORT_NUMERIC, $plug_list);
             return $plug_list;
         }
    }
    
  /**
   * get_list returns the Generated Pluginslist as 2-dimensional Array
   * @return mixed Returns the sorted $plug_list Array
   */
    function get_list() {
        return $this->list;
    }
}
?>