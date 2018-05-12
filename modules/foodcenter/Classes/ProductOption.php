<?php

namespace LanSuite\Module\Foodcenter;

class ProductOption
{
    /**
     * Product option ID
     *
     * @var int
     */
    public $id;

    /**
     * ID of the parent product
     *
     * @var int
     */
    private $parentid;

    /**
     * Type of the parent product
     *
     * @var int
     */
    private $parenttyp;

    /**
     * Barcode
     *
     * @var string
     */
    private $barcode;

    /**
     * Name of the product option
     *
     * @var string
     */
    public $caption;

    /**
     * Unit
     *
     * @var String
     */
    public $unit;

    /**
     * Number of products in stock
     *
     * @var int
     */
    public $pice;

    /**
     * Price to sell
     *
     * @var int
     */
    public $price;

    /**
     * Purchasing price
     *
     * @var int
     */
    private $eprice;

    /**
     * Required to order
     *
     * @var int
     */
    public $fix = 0;

    /**
     * Number of ordered products
     *
     * @var int
     */
    public $ordered = 0;

    /**
     * Error container
     *
     * @var array
     */
    public $error = [];

    /**
     * product_option constructor.
     *
     * @param int $id
     * @param int $type
     */
    public function __construct($id = null, $type = null)
    {
        $this->parenttyp = $type;
        if ($id != null && $id > 0) {
            $this->id = $id;
            $this->read();
        }
    }

    /**
     * Read information about the product option from formular
     *
     * @param int $parentid
     * @param int $type
     * @param int $nr
     * @return void
     */
    public function read_post($parentid, $type, $nr)
    {
        if ($_POST['hidden'][$nr] > 0) {
            $this->id = $_POST['hidden'][$nr];
        } else {
            $this->id = null;
        }

        $this->parentid = $parentid;
        $this->parenttyp = $type;
        $this->barcode  = $_POST['barcode'][$nr];
        $this->caption  = $_POST['caption'][$nr];
        $this->unit     = $_POST['unit'][$nr];
        $this->price    = str_replace(',', '.', $_POST['price'][$nr]);
        $this->eprice   = str_replace(',', '.', $_POST['eprice'][$nr]);
        $this->pice     = $_POST['piece'][$nr];
        $this->fix      = isset($_POST['fix'][$nr]) ? 1 : 0;
    }

    /**
     * Read product option information from database
     * @return void
     */
    private function read()
    {
        global $db;

        $row = $db->qry_first("SELECT * FROM %prefix%food_option WHERE id=%int%", $this->id);

        $this->parentid = $row['parentid'];
        $this->caption  = $row['caption'];
        $this->barcode  = $row['barcode'];
        $this->unit     = $row['unit'];
        $this->price    = $row['price'];
        $this->eprice   = $row['eprice'];
        $this->pice     = $row['pice'];
        $this->fix      = $row['fix'];
    }

    /**
     * Write a new product option to database
     *
     * @param int $id
     * @return void
     */
    public function write($id = 0)
    {
        global $db;

        if ($this->parentid == null) {
            $this->parentid = $id;
        }

        if ($this->id == null) {
            $db->qry("INSERT INTO %prefix%food_option  SET 
                                    parentid    = %int%,
                                    barcode     = %string%,
                                    caption     = %string%,
                                    unit        = %string%,
                                    price       = %string%,
                                    eprice      = %string%,
                                    fix         = %string%,
                                    pice        = %string%", $this->parentid, $this->barcode, $this->caption, $this->unit, $this->price, $this->eprice, $this->fix, $this->pice);
            $this->id = $db->insert_id();
        } else {
            $db->qry("UPDATE %prefix%food_option  SET 
                                    parentid    = %int%,
                                    barcode     = %string%,
                                    caption     = %string%,
                                    unit        = %string%,
                                    price       = %string%,
                                    eprice      = %string%,
                                    pice        = %string%,
                                    fix         = %string%
                                    WHERE id = %int%", $this->parentid, $this->barcode, $this->caption, $this->unit, $this->price, $this->eprice, $this->pice, $this->fix, $this->id);
        }
    }

    /**
     * @return bool
     */
    public function check()
    {
        if ($this->caption == "" && $this->parenttyp == 2) {
            $this->error['caption'] = t('Bitte geben sie einen Artikelnamen ein');
        }

        if ($this->unit == "") {
            $this->error['price'] .= t('Bitte geben sie eine einheit an (Stk./dl/kg)');
        }

        if (!is_numeric($this->price) || $this->price == "") {
            if ($this->error['price'] != "") {
                $this->error['price'] .= HTML_NEWLINE;
            }
            $this->error['price'] .= t('Bitte geben sie einen Preis an');
        }

        if (count($this->error) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Return number of options ordered
     *
     * @return int
     */
    public function count_unit()
    {
        return $this->ordered;
    }

    /**
     * Count price
     *
     * @return int
     */
    public function count_price()
    {
        if ($this->fix) {
            return $this->fix * $this->price;
        }

        return $this->ordered * $this->price;
    }

    /**
     * Form to enter data
     *
     * @param int $nr
     * @param bool $optional
     * @param bool $big
     * @param bool $multiselect
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    public function option_form($nr, $optional = null, $big = false, $multiselect = false)
    {
        global $dsp, $smarty;

        if ($multiselect) {
            $display = "";
        } else {
            $display = "none";
        }

        if ($big) {
            // display HTML for option 3
            $smarty->assign('hidden_id', "opt_big_$nr");
            $smarty->assign('hidden_display', $display);
            $dsp->AddSmartyTpl('hiddenbox_start', 'foodcenter');
            $dsp->AddCheckBoxRow("fix[$nr]", t('Option fixieren'), t('Dies ist ein Pflichtartikel'), "", $optional, $this->fix);
            $dsp->AddSmartyTpl('hiddenbox_stop', 'foodcenter');
            $dsp->AddTextFieldRow("caption[$nr]", t('Artikelname'), $this->caption, $this->error['caption'], null, $optional);
        }

        $this->addOptionRow(t('Produktoption'), t('Einheit'), t('Preis'), t('Einkaufspreis'), t('Anzahl'), t('Barcode'), "unit[$nr]", "price[$nr]", "eprice[$nr]", "piece[$nr]", "barcode[$nr]", $this->unit, $this->price, $this->eprice, $this->pice, $this->barcode, "hidden[$nr]", $this->id, $this->error['price'], $optional);
        $dsp->AddHRuleRow();
    }

    /**
     * @param int       $listid
     * @param string    $caption
     * @param bool      $checkbox
     * @return void
     */
    public function get_basket($listid, $caption, $checkbox = false)
    {
        global $dsp,$cfg;

        if ($this->caption == "" && $checkbox == false) {
            $text = $caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
        } elseif ($caption == "" || $checkbox == true) {
            $text = $this->caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
        } else {
            $text = $caption . " " .$this->caption . " / " . $this->unit . " / " . $this->price . " " . $cfg['sys_currency'];
        }

        if ($checkbox == false) {
            $dsp->AddTextFieldRow("option_{$listid}_{$this->id}", $text, $this->ordered, $this->error['pice_error']);
            $this->error['pice_error'] = "";
        } else {
            $dsp->AddCheckBoxRow("product[{$this->parentid}][{$this->id}]", "", $text, "", null, 1, 1);
        }
    }

    /**
     * Product option template
     *
     * @param string $text
     * @param string $text_product
     * @param string $text_price
     * @param string $text_eprice
     * @param string $text_piece
     * @param string $text_barcode
     * @param string $name_product
     * @param string $name_price
     * @param string $name_eprice
     * @param string $name_piece
     * @param string $name_barcode
     * @param int $value_product
     * @param int $value_price
     * @param int $value_eprice
     * @param int $value_piece
     * @param int $value_barcode
     * @param string $hidden_name
     * @param int $hidden_id
     * @param string $errortext
     * @param bool $optional
     * @return void
     * @throws \Exception
     * @throws \SmartyException
     */
    private function addOptionRow($text, $text_product, $text_price, $text_eprice, $text_piece, $text_barcode, $name_product, $name_price, $name_eprice, $name_piece, $name_barcode, $value_product, $value_price, $value_eprice, $value_piece, $value_barcode, $hidden_name, $hidden_id, $errortext, $optional = false)
    {
        global $dsp, $smarty;

        $smarty->assign('text_row', $text);
        $smarty->assign('text_product', $text_product);
        $smarty->assign('name_product', $name_product);
        $smarty->assign('value_name', $value_product);
        $smarty->assign('text_price', $text_price);
        $smarty->assign('name_price', $name_price);
        $smarty->assign('value_price', $value_price);
        $smarty->assign('text_eprice', $text_eprice);
        $smarty->assign('name_eprice', $name_eprice);
        $smarty->assign('value_eprice', $value_eprice);
        $smarty->assign('text_piece', $text_piece);
        $smarty->assign('name_piece', $name_piece);
        $smarty->assign('value_piece', $value_piece);
        $smarty->assign('text_barcode', $text_barcode);
        $smarty->assign('name_barcode', $name_barcode);
        $smarty->assign('value_barcode', $value_barcode);
        $smarty->assign('hidden_name', $hidden_name);
        $smarty->assign('hidden_id', $hidden_id);

        if ($errortext) {
            $smarty->assign('errortext', $errortext);
        }

        if ($optional) {
            $smarty->assign('optional', '_optional');
        }

        $dsp->AddDoubleRow($text, $smarty->fetch('modules/foodcenter/templates/productcontrol_price_row.htm'));
    }
}
