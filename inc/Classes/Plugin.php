<?php

namespace LanSuite;

/**
 * Class Plugin
 *
 * This class generates the needed data array to load the plugin files.
 */
class Plugin
{

    private array $modules = [];

    private array $captions = [];

    private array $icons = [];

    private int $currentIndex = 0;

    private int $count = 0;

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
     */
    public function fetch($index = -1): array|bool
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
