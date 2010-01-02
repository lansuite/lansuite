<?php

/**
 * Class PLUGIN for Lansuite
 *
 * This Class generates the needed Dataarray to load the Pluginfiles 
 *
 * @package lansuite_core
 * @author bytekilla
 * @version $Id$
 * @access public
 */
class plugin {

  /**
   * Intern Variables
   * @access private
   * @var mixed
   */
    var $modules    =     array();
    var $captions    =     array();
    var $icons    =     array();
    var $currentIndex = 0;
    var $count = 0;
    var $type = '';

    function plugin($type) {
      global $db, $func;

      $res = $db->qry('SELECT caption, module, icon FROM %prefix%plugin WHERE pluginType = %string% ORDER BY pos', $type);
      while ($row = $db->fetch_array($res)) {
        if ($func->isModActive($row['module'])) {
          $this->modules[] = $row['module'];
          ($row['caption'] != '')? $this->captions[] = $row['caption'] : $this->captions[] = $row['module'];
          ($row['icon'] != '')? $this->icons[] = $row['icon'] : $this->icons[] = $row['icon'];
          $this->count++;
        }
      }
      $db->free_result($res);
      $this->type = $type;
    }

  /**
   * Get the next (or specific) element
   * @access public
   * @return list(caption, include_string, icon)
   */
    function fetch($index = -1) {
      if ($index == -1) (int)$index = $this->currentIndex;

      if ($index >= $this->count) return false;

      $arr = array();
      $this->currentIndex = $index + 1;
      $arr[] = $this->captions[$index];
      $arr[] = 'modules/'. $this->modules[$index] .'/plugins/'. $this->type .'.php';
      $arr[] = $this->icons[$index];

      return $arr;
    }
}
?>