<?php

namespace LanSuite;

/**
 * Class Plugin
 *
 * This class generates the needed data array to load the plugin files.
 */
class Plugin
{

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var array
     */
    private $captions = [];

    /**
     * @var array
     */
    private $icons = [];

    /**
     * @var int
     */
    private $currentIndex = 0;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var string
     */
    private $type = '';

    public function __construct($type)
    {
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
     *
     * @param int $index
     * @return array|bool
     */
    public function fetch($index = -1)
    {
        if ($index == -1) {
            $index = (int) $this->currentIndex;
        }

        if ($index >= $this->count) {
            return false;
        }

        $arr = [];
        $this->currentIndex = $index + 1;
        $arr[] = $this->captions[$index];
        $arr[] = 'modules/' . $this->modules[$index] . '/plugins/' . $this->type . '.php';
        $arr[] = $this->icons[$index];

        return $arr;
    }
}
