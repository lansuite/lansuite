<?php

namespace LanSuite\Module\Foodcenter;

/**
 * Class Supplier
 *
 * Management of suppliers
 */
class Supplier
{
    /**
     * Supplier ID
     *
     * @var int
     */
    public $supp_id = null;

    /**
     * Supplier description
     *
     * @var string
     */
    private $supp_desc;

    /**
     * Name of the supplier
     *
     * @var string
     */
    private $supp_caption;

    /**
     * Error container
     *
     * @var array
     */
    private $error = [];

    /**
     * supp constructor.
     *
     * @param int $id
     */
    public function __construct($id = null)
    {
        if ($id != null && $id > 0) {
            $this->supp_id = $id;
            $this->read();
        }
    }

    /**
     * Returns a list of suppliers
     *
     * @param int       $select_id
     * @param boolean   $new
     * @return array|bool
     */
    private function get_supp_array($select_id, $new = null)
    {
        global $db;

        $row = $db->qry("SELECT * FROM %prefix%food_supp");

        if ($db->num_rows($row) > 0) {
            $tmp = array();

            if ($new != null) {
                if ($select_id == 0) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                array_push($tmp, "<option $selected value='0'>".t('Neuer Lieferant')."</option>");
            }

            while ($data = $db->fetch_array($row)) {
                if ($select_id == $data['supp_id']) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                array_push($tmp, "<option $selected value='{$data['supp_id']}'>{$data['name']}</option>");
            }
            return $tmp;
        } else {
            return false;
        }
    }

    /**
     * Reads global $_POST data
     *
     * @return void
     */
    public function read_post()
    {
        if (isset($_POST['supp_id']) && $_POST['supp_id'] > 0) {
            $this->supp_id = $_POST['supp_id'];
        } else {
            $this->supp_id = null;
        }

        if ($_POST['supp_id'] == 0) {
            $this->supp_caption = $_POST['supp_name'];
            $this->supp_desc = $_POST["supp_desc"];
        }
    }

    /**
     * Reads supplier from database
     *
     * @return bool
     */
    private function read()
    {
        global $db;

        if ($this->supp_id != null) {
            $row = $db->qry_first("SELECT * FROM %prefix%food_supp WHERE supp_id=%int%", $this->supp_id);
            if (is_array($row)) {
                $this->supp_caption = $row['name'];
                $this->supp_desc    = $row['s_desc'];
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Writes a supplier into database
     *
     * @return void
     */
    public function write()
    {
        global $db;

        if ($this->supp_id == null) {
            $db->qry("INSERT INTO %prefix%food_supp SET 
                            name = %string%,
                            s_desc = %string%", $this->supp_caption, $this->supp_desc);
            $this->supp_id = $db->insert_id();
        } else {
            $db->qry("UPDADE %prefix%food_supp SET 
                            name = %string%,
                            s_desc = %string%
                            WHERE supp_id = %int%", $this->supp_caption, $this->supp_desc, $this->supp_id);
        }
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->supp_caption == "" && $this->supp_id == null) {
            $this->error['supp_name']   = t('Bitte geben sie einen Lieferant an');
            return false;
        }

        return true;
    }

    /**
     * Creates a form to create suppliers
     *
     * @return void
     */
    public function supp_form()
    {
        global $dsp;

        $supp_array = $this->get_supp_array($this->supp_id, 1);
        if ($supp_array) {
            $dsp->AddDropDownFieldRow("supp_id", t('Lieferant'), $supp_array, "");
        }
        $dsp->AddTextFieldRow("supp_name", t('Neuer Lieferant'), $_POST['supp_name'], $this->error['supp_name']);
    }
}
