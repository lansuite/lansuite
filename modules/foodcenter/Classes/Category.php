<?php

namespace LanSuite\Module\Foodcenter;

/**
 * Class Category
 *
 * Management of categories.
 * Used for menu cards.
 */
class Category
{
    /**
     * Category ID
     *
     * @var int
     */
    public $cat_id = null;

    /**
     * Category name
     *
     * @var string
     */
    private $name = "";

    /**
     * @var array
     */
    public $error = [];

    /**
     * Constructor
     *
     * @param int $id
     */
    public function __construct($id = null)
    {
        if ($id != null && $id > 0) {
            $this->cat_id = $id;
            $this->read();
        }
    }

    /**
     * Read category data from database.
     *
     * @return boolean
     */
    private function read()
    {
        global $db;

        if ($this->cat_id != null) {
            $row = $db->qry_first("SELECT * FROM %prefix%food_cat WHERE cat_id=%int%", $this->cat_id);
            if (is_array($row)) {
                $this->name = $row['name'];
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Gibt ein Array mit allen Kategorieen zurück
     *
     * @param int       $select_id
     * @param boolean   $new
     * @return boolean
     */
    private function get_cat_array($select_id, $new = null)
    {
        global $db;

        $row = $db->qry("SELECT * FROM %prefix%food_cat");

        if ($db->num_rows($row) > 0) {
            $tmp = [];

            if ($new != null) {
                if ($select_id == 0) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                array_push($tmp, "<option $selected value='0'>".t('Neue Kategorie')."</option>");
            }

            while ($data = $db->fetch_array($row)) {
                if ($select_id == $data['cat_id']) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                array_push($tmp, "<option $selected value='{$data['cat_id']}'>{$data['name']}</option>");
            }
            return $tmp;
        } else {
            return false;
        }
    }

    /**
     * Reads global $_POST data for initialization
     *
     * @return void
     */
    public function read_post()
    {
        if (isset($_POST['cat_id']) && $_POST['cat_id'] > 0) {
            $this->cat_id = $_POST['cat_id'];
        } else {
            $this->cat_id = null;
        }

        if ($_POST['cat_id'] == 0) {
            $this->name = $_POST['cat_name'];
        }
    }

    /**
     * @return void
     */
    public function write()
    {
        global $db;

        if ($this->cat_id == null) {
            $db->qry("INSERT INTO %prefix%food_cat SET name = %string%", $this->name);
            $this->cat_id = $db->insert_id();
        } else {
            $db->qry("UPDATE %prefix%food_cat SET name = %string% WHERE cat_id=%int%", $this->name, $this->cat_id);
        }
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->name == "" && $this->cat_id == null) {
            $this->error['cat_name'] = t('Bitte geben sie eine Kategorie an');
            return false;
        }
        return true;
    }

    /**
     * Create a text field for a category
     * @return void
     */
    public function cat_form()
    {
        global $dsp;

        // Check for existing categories
        $cat_array = $this->get_cat_array($this->cat_id, 1);
        if ($cat_array) {
            $dsp->AddDropDownFieldRow("cat_id", t('Produktkategorie'), $cat_array, "");
        }

        $dsp->AddTextFieldRow("cat_name", t('Neue Produktkategorie'), $_POST['cat_name'], $this->error['cat_name']);
    }
}
