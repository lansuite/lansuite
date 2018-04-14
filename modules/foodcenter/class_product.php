<?php

/**
 * Class ProductList
 *
 * Used to show the list of products (e.g. for the menu card)
 */
class ProductList
{
    /**
     * List of product numbers
     *
     * @var array
     */
    private $product_list = [];

    /**
     * List of products
     *
     * @var \Product[]
     */
    private $product = [];

    /**
     * Load all products from a category
     *
     * @param string $cat
     * @return void
     */
    public function load_cat($cat)
    {
        global $db;
        $products = $db->qry("SELECT id FROM %prefix%food_product WHERE cat_id=%string%", $cat);

        $i = 0;
        while ($data = $db->fetch_array($products)) {
            $this->product_list[$i] .= $data['id'];
            $this->product[$i] = new Product($data['id']);
            $i++;
        }
    }

    /**
     * Productlist for output
     *
     * @param string $worklink
     * @return void
     */
    public function get_list($worklink)
    {
        global $dsp;

        if (count($this->product) > 0) {
            for ($i = 0; $i < count($this->product); $i++) {
                $this->product[$i]->order_form($worklink);
            }

        } else {
            $dsp->AddSingleRow(t('In dieser Kategorie sind keine Produkte vorhanden'));
        }
    }

    /**
     * Detail view of product
     *
     * @param int $id
     * @param string $worklink
     * @return void
     */
    public function get_info($id, $worklink)
    {
        $data_array = array_flip($this->product_list);
        $this->product[$data_array[$id]]->get_info($worklink);
    }

    /**
     * Add a product to the list.
     * Returns true once the product is added, false otherwise
     *
     * @param int       $id
     * @param array|int $opt
     * @return bool
     */
    public function add_product($id, $opt)
    {
        // Product already in the list?
        if (in_array($id, $this->product_list)) {

            if (is_array($opt)) {
                $temp_prod = new Product($id);
                $temp_prod->ordered++;

                foreach ($opt as $key => $value) {
                    $temp_prod->order_option($key);
                }

                // Search in the list for the same product
                foreach ($this->product_list as $key => $value) {
                    if ($value == $id) {
                        // If the product is the same, just add it once
                        if ($this->product[$key]->compare($temp_prod)) {
                            $this->product[$key]->ordered++;

                            return true;
                        }
                    }
                }

                // If it is not the same product, get the last key
                end($this->product);
                $key_array = each($this->product);
                if (count($this->product) == 0) {
                    $key = 0;
                } else {
                    $key = $key_array[0] + 1;
                }

                // and add the product
                $this->product[$key] = new Product($id);
                $this->product[$key]->ordered++;
                $this->product_list[] = $id;

                foreach ($opt as $cle => $value) {
                    $this->product[$key]->order_option($cle);
                }

                return true;

            } else {
                // If the product is not given, search for it
                foreach ($this->product_list as $key => $value) {
                    if ($value == $id) {
                        $this->product[$key]->order_option($opt, 0);
                        return true;
                    }
                }

                return false;
            }

        // Product not in there yet, add it
        } else {
            $ret = true;

            end($this->product);
            $key_array = each($this->product);
            if (count($this->product) == 0) {
                $key = 0;
            } else {
                $key = $key_array[0] + 1;
            }

            // Add the product
            $this->product[$key] = new Product($id);
            $this->product[$key]->ordered++;
            $this->product_list[] = $id;

            if (is_array($opt)) {
                foreach ($opt as $cle => $value) {
                    if (!$this->product[$key]->order_option($cle)) {
                        $ret = false;
                    }
                }
            } else {
                $ret = $this->product[$key]->order_option($opt);
            }

            return $ret;
        }
    }

    /**
     * Write new basket once something changed
     *
     * @param int       $listid
     * @param array|int $opt
     * @param int $value
     * @return mixed
     */
    public function chanche_ordered($listid, $opt, $value)
    {
        if (!is_null($opt)) {
            return $this->product[$listid]->order_option($opt, $value);
        }

        return $this->product[$listid]->set_ordered($value);
    }

    /**
     * Remove empty products from the list
     *
     * @return void
     */
    public function check_list()
    {
        foreach ($this->product_list as $key => $value) {
            if ($this->product[$key]->count_unit() == 0) {
                unset($this->product[$key]);
                unset($this->product_list[$key]);
            }
        }
    }

    /**
     * Create form for the basket
     * @return void
     */
    public function get_basket_form()
    {
        foreach ($this->product_list as $key => $value) {
            $this->product[$key]->get_basket($key);
        }
    }

    /**
     * Count products
     *
     * @return int
     */
    public function count_products()
    {
        $count = 0;
        foreach ($this->product_list as $key => $value) {
            $count += $this->product[$key]->count_unit();
        }

        return $count;
    }

    /**
     * Sum up product prices
     *
     * @return int
     */
    public function count_products_price()
    {
        $price = 0;
        foreach ($this->product_list as $key => $value) {
            $price += $this->product[$key]->count_price();
        }
        return $price;
    }

    /**
     * Order product
     *
     * @param int $userid
     * @param array $delivered
     * @return int
     */
    public function order_product($userid, $delivered)
    {
        $price = 0;
        foreach ($this->product_list as $key => $value) {
            $price += $this->product[$key]->order($userid, $delivered);
        }

        return $price;
    }

    /**
     * @param int $userid
     * @param array $delivered
     * @return string
     */
    public function order_productdesc($userid, $delivered)
    {
        $tempdesc = "";
        foreach ($this->product_list as $key => $value) {
            $tempdesc .= " ".$this->product[$key]->caption." *";
        }

        return $tempdesc;
    }
}

class Product
{
    /**
     * Product ID
     *
     * @var int
     */
    private $id = null;

    /**
     * Product name
     *
     * @var string
     */
    public $caption = '';

    /**
     * Product description
     *
     * @var string
     */
    private $desc = '';

    /**
     * Category
     *
     * @var \cat
     */
    private $cat;

    /**
     * Supplier
     *
     * @var \supp
     */
    private $supp;

    /**
     * Supplier information
     *
     * @var string
     */
    private $supp_infos;

    /**
     * Product picture
     *
     * @var string
     */
    private $pic = '';

    /**
     * Management of material
     *
     * @var int
     */
    private $mat;

    /**
     * Product type
     *
     * @var int
     */
    private $type = null;

    /**
     * Multiple choice
     *
     * @var bool
     */
    private $choise = false;

    /**
     * @var int
     */
    private $wait = 0;

    /**
     * NUmber of ordered products
     *
     * @var int
     */
    public $ordered = 0;

    /**
     * Product options
     *
     * @var \ProductOption[]
     */
    private $option = [];

    /**
     * Error container
     *
     * @var array
     */
    private $error_food = [];

    /**
     * Error status
     *
     * @var boolean
     */
    private $noerror = true;

    /**
     * product constructor.
     *
     * @param int $id
     */
    public function __construct($id = null)
    {
        if ($id != null && $id > 0) {
            $this->id = $id;
            $this->read();
        }
    }

    /**
     * Read product information from form
     *
     * @return void
     */
    public function read_post()
    {
        $this->caption    = $_POST['p_caption'];
        $this->desc       = $_POST['desc'];
        $this->cat        = new cat($_POST['cat_id']);
        $this->supp       = new supp($_POST['supp_id']);
        $this->supp_infos = $_POST['supp_infos'];
        $this->mat        = (int)$_POST['mat'];
        $this->type       = $_POST['product_type'];
        $this->choise     = $_POST['chois'];
        $this->wait       = $_POST['wait'];
        $this->pic        = $_POST['pic'];
                
        $this->cat->read_post();
        $this->supp->read_post();
        
        if ($this->type == 1) {
            for ($i=0; $i < 3; $i++) {
                if ($_POST['hidden'][$i] > 0) {
                    $this->option[$i]->read_post($this->id, $this->type, $i);

                } elseif ($_POST['price'][$i] != "") {
                    $x = count($this->option);
                    $this->option[$x] = new ProductOption();
                    $this->option[$x]->read_post($this->id, $this->type, $i);
                }
            }

        } elseif ($this->type == 2) {

            if (isset($_POST['caption'][0])) {
                $q = 0;
            } else {
                $q = 3;
            }

            for ($i=$q; $i < ($q + 8); $i++) {
                if ($_POST['hidden'][$i] > 0) {
                    $this->option[$i]->read_post($this->id, $this->type, $i);

                } elseif ($_POST['caption'][$i] != "" || $i == $q) {
                    $x = count($this->option);
                    $this->option[$x] = new ProductOption();
                    $this->option[$x]->read_post($this->id, $this->type, $i);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function check()
    {
        global $func;

        if ($this->caption == "") {
            $this->error_food['caption'] = t('Bitte geben sie einen Produknamen an.');
            $this->noerror = false;
        }
        
        if ($_FILES['file']['error'] != 0 && $_FILES['file']['name'] != "") {
            $this->error_food['file']   = t('Datei konnte nicht hochgeladen werden');
            $this->noerror = false;

        } elseif ($_FILES['file']['name'] != "") {
            $func->FileUpload("file", "ext_inc/foodcenter/", $_FILES['file']['name']);
            $_POST['pic'] = $_FILES['file']['name'];
            $this->pic = $_FILES['file']['name'];
        }

        if ($this->cat->check() == false) {
            $this->noerror = false;
        }

        if ($this->supp->check() == false) {
            $this->noerror = false;
        }

        for ($i=0; $i < count($this->option); $i++) {
            if ($this->option[$i]->check() == false) {
                $this->noerror = false;
            }
        }
    
        return $this->noerror;
    }

    /**
     * Read product data from database
     *
     * @return bool
     */
    private function read()
    {
        global $db;

        if ($this->id == null) {
            return false;

        } else {
            $row = $db->qry_first("SELECT * FROM %prefix%food_product WHERE id=%int%", $this->id);

            $this->caption    = $row['caption'];
            $this->desc       = $row['p_desc'];
            $this->cat        = new cat($row['cat_id']);
            $this->supp       = new supp($row['supp_id']);
            $this->supp_infos = $row['supp_infos'];
            $this->mat        = (int) $row['mat'];
            $this->type       = $row['p_type'];
            $this->choise     = $row['chois'];
            $this->wait       = $row['wait'];
            $this->pic        = $row['p_file'];
            
            $opt = $db->qry("SELECT id FROM %prefix%food_option WHERE parentid=%int%", $this->id);
            
            $int = 0;
            while ($option = $db->fetch_array($opt)) {
                $this->option[$int] = new ProductOption($option['id'], $this->type);
                $int++;
            }
        }

        return true;
    }

    /**
     * Write product data into database
     *
     * @return void
     */
    public function write()
    {
        global $db;

        if ($this->supp->supp_id == null) {
            $this->supp->write();
        }

        if ($this->cat->cat_id == null) {
            $this->cat->write();
        }
        
        if ($this->id == null || $this->id < 1) {
            $db->qry("INSERT INTO %prefix%food_product SET
                        caption = %string%,
                        p_desc = %string%,
                        cat_id = %int%,
                        supp_id = %int%,
                        supp_infos = %string%,
                        p_file = %string%,
                        mat = %int%,
                        p_type = %string%,
                        wait = %int%,
                        chois = %int%", $this->caption, $this->desc, $this->cat->cat_id, $this->supp->supp_id, $this->supp_infos, $this->pic, $this->mat, $this->type, $this->wait, $this->choise);
            $this->id = $db->insert_id();

        } else {
            $db->qry("UPDATE %prefix%food_product SET
                        caption = %string%,
                        p_desc = %string%,
                        cat_id = %int%,
                        supp_id = %int%,
                        supp_infos = %string%,
                        p_file = %string%,
                        mat = %int%,
                        p_type = %string%,
                        chois = %int%,
                        wait = %int%
                        WHERE id=%int%", $this->caption, $this->desc, $this->cat->cat_id, $this->supp->supp_id, $this->supp_infos, $this->pic, $this->mat, $this->type, $this->choise, $this->wait, $this->id);
        }

        foreach ($this->option as $opts) {
            $opts->write($this->id);
        }
    }

    /**
     * Sum up the price
     *
     * @return int
     */
    public function count_price()
    {
        $tot_price = 0;

        if ($this->type == 2) {
            for ($i=0; $i<count($this->option); $i++) {
                if (is_object($this->option[$i])) {
                    $tot_price += $this->option[$i]->count_price();
                }
            }
            return  $this->ordered * $tot_price;

        } else {
            for ($i=0; $i<count($this->option); $i++) {
                if (is_object($this->option[$i])) {
                    $tot_price += $this->option[$i]->count_price();
                }
            }

            return $tot_price;
        }
    }

    /**
     * Order a product option
     *
     * @param int $id
     * @param int $value
     * @return bool
     */
    public function order_option($id, $value = 1)
    {
        global $func;

        $ok = true;
        for ($i = 0; $i < count($this->option); $i++) {
            $this->option[$i]->error['pice_error'] = '';

            if ($this->option[$i]->id == $id) {
                if ($value == null) {
                    $this->option[$i]->ordered++;

                } else {
                    if ($this->mat == 0 || $this->option[$i]->pice >= $value) {
                        $this->option[$i]->ordered = $value;

                    } else {
                        $this->option[$i]->ordered = $this->option[$i]->pice;
                        $this->option[$i]->error['pice_error'] = t('Das Produkt ist nicht in dieser Menge vorhanden.');
                        $func->information(t('Dieses Produkt ist leider nicht mehr vorhanden.'));
                        $ok = false;
                    }
                }
            }
        }

        return $ok;
    }

    /**
     * Count products
     *
     * @return int
     */
    public function count_unit()
    {
        if ($this->type == 2) {
            return $this->ordered;

        } else {
            $count = 0;

            for ($i=0; $i<count($this->option); $i++) {
                if ($this->option[$i]) {
                    $count += $this->option[$i]->count_unit();
                }
            }

            return $count;
        }
    }

    /**
     * Form for adding and changing products
     *
     * @param int $step
     * @return void
     */
    public function form_add_product($step)
    {
        global $dsp, $smarty;

        $nextstep = $step + 1;

        // Change or New ?
        if ($this->id != null) {
            $dsp->NewContent(t('Produkt hinzufügen'), t('Hier können sie ein Produkt hinzufügen'));
            $dsp->SetForm("index.php?mod=foodcenter&action=addproduct&step=$nextstep&id={$this->id}", "food_add", "", "multipart/form-data");

        } else {
            $dsp->NewContent(t('Produkt ändern'), t('Produkt ändern'));
            $dsp->SetForm("index.php?mod=foodcenter&action=addproduct&step=$nextstep", "food_add", "", "multipart/form-data");
        }
        
        // Add Javascript Code
        $dsp->AddSmartyTpl('javascript', 'foodcenter');
        $dsp->AddTextFieldRow("p_caption", t('Produktname'), $this->caption, $this->error_food['caption']);
        $dsp->AddTextAreaRow("desc", t('Produktbeschreibung'), $this->desc, $this->error_food['desc'], null, null, true);

        // Not functional now
        // Pic is only active with gd-Libary
        $gd = new gd();
        if ($gd->available) {
            $dsp->AddFileSelectRow("file", t('Bild hochladen'), $this->error_food['file'], null, null, true);
            $dsp->AddPictureDropDownRow("pic", t('Bild hochladen'), "ext_inc/foodcenter", $this->error_food['file'], true, basename($this->pic));
        }

        // Select Cat
        if (!is_object($this->cat)) {
            $this->cat = new cat();
        }
        $this->cat->cat_form();

        // Select Supplier
        if (!is_object($this->supp)) {
            $this->supp = new supp();
        }
        $this->supp->supp_form();

        $dsp->AddTextFieldRow("supp_infos", t('Infos für Lieferant (zb. seine Artikelnummer)'), $this->supp_infos, "", null, true);
        $dsp->AddCheckBoxRow("mat", t('Materialverwaltung'), t('Materialverwaltung aktivieren'), "", null, $this->mat, null, null);
        $dsp->AddCheckBoxRow("wait", t('Bestelllistenartikel'), t('Muss der Artikel angefordert werden (Pizza)'), "", null, $this->wait, null, null);

        // Hidden not Selected Option an List Product Options
        $add_product_prod_opt[1] = t('Normales Produkt');
        $add_product_prod_opt[2] = t('Erweitertes Produkt');
        $opts = [];
        foreach ($add_product_prod_opt as $key => $value) {
            if ($key == $this->type) {
                $selected = "selected";
                $display[$key] = "";
            } else {
                $selected = "";
                $display[$key] = "none";
            }
            $opts[] = "<option $selected value=\"$key\">$value</option>";
        }
        if ($_POST['product_opts'] == "") {
            $display[1] = "";
        }

        if ($this->type != null) {
            $dsp->AddDropDownFieldRow("product_type\" disabled onchange=\"change_option(this.options[this.options.selectedIndex].value)\"", "<input type=\"hidden\" name=\"product_type\" value=\"{$this->type}\" />" . t('Produktart'), $opts, $this->error_food['product_opts']);

        } else {
            $dsp->AddDropDownFieldRow("product_type\" onchange=\"change_option(this.options[this.options.selectedIndex].value)\"", t('Produktart'), $opts, $this->error_food['product_opts']);
        }

        if ($this->type == null || $this->type == 1) {
            // display HTML for option 1
            $smarty->assign('hidden_id', 'food_1');
            $smarty->assign('hidden_display', $display[1]);
            $dsp->AddSmartyTpl('hiddenbox_start', 'foodcenter');

            for ($i = 0; $i < 3; $i++) {
                ($i == 0) ? $optional = null : $optional = true;

                if (!is_object($this->option[$i])) {
                    $this->option[$i] = new ProductOption();
                }
                $this->option[$i]->option_form($i, $optional);
            }
            $dsp->AddSmartyTpl('hiddenbox_stop', 'foodcenter');
        }

        if ($this->type == null || $this->type == 2) {
            // display HTML for option 2
            $smarty->assign('hidden_id', 'food_2');
            $smarty->assign('hidden_display', $display[2]);
            $dsp->AddSmartyTpl('hiddenbox_start', 'foodcenter');
            $dsp->AddCheckBoxRow("chois\" onclick=\"change_optionelem(this.checked)", t('Mehrfachauswahl möglich'), "", "", null, $this->choise);
            ($this->type == null) ? $q = 3 : $q = 0;
            for ($i = $q; $i < ($q+8); $i++) {
                ($i == $q) ? $optional = null : $optional = true;
                if (!is_object($this->option[$i])) {
                    $this->option[$i] = new ProductOption();
                }
                $this->option[$i]->option_form($i, $optional, true, $this->choise);
            }
            $dsp->AddSmartyTpl('hiddenbox_stop', 'foodcenter');
        }
        
        if ($this->id != null) {
            $dsp->AddFormSubmitRow(t('Editieren'));

        } else {
            $dsp->AddFormSubmitRow(t('Hinzufügen'));
        }
    }

    /**
     * Order form
     *
     * @param string $worklink
     * @return void
     */
    public function order_form($worklink)
    {
        global $dsp, $cfg, $smarty;
        
        switch ($this->type) {
            case 1:
                unset($price_1);
                unset($price_2);
                unset($price_3);
                
                if (is_object($this->option[0])) {
                    $price_3 = "<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a>";
                    $price_3 .= "<a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
                }

                if (is_object($this->option[1])) {
                    $price_2 = "<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a>";
                    $price_2 .= "<a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
                }

                if (is_object($this->option[2])) {
                    $price_1 = "<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a>";
                    $price_1 .= "<a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" align=\"right\" /></a>";
                }

                $smarty->assign('price_1', $price_1);
                $smarty->assign('price_2', $price_2);
                $smarty->assign('price_3', $price_3);
                $dsp->AddDoubleRow("<a href='$worklink&info={$this->id}'><b>" . $this->caption . "</b><br />" . $this->desc . "</a>", $smarty->fetch('modules/foodcenter/templates/product_price_row.htm'));
                
                break;
            case 2:
                if ($this->choise == 1) {
                    $dsp->SetForm("$worklink&add={$this->id}&opt=0");
                }

                $i = 0;
                while (is_object($this->option[$i])) {
                    if ($i==0) {
                        if ($this->choise == 0) {
                            $dsp->AddHRuleRow();
                            $dsp->AddDoubleRow("<a href='$worklink&info={$this->id}'>" . $this->caption . "</a>", $this->option[$i]->caption . " " . $this->option[$i]->unit . " <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");

                        } else {
                            $dsp->AddHRuleRow();
                            $dsp->AddCheckBoxRow("option[{$this->id}]", "<a href='$worklink&info={$this->id}'>" . $this->caption . "</a>", $this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'], "", null, $this->option[$i]->fix, $this->option[$i]->fix);
                        }

                    } else {
                        if ($this->choise == 0) {
                            $dsp->AddDoubleRow("", $this->option[$i]->caption . " " . $this->option[$i]->unit . "   <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");

                        } else {
                            $dsp->AddCheckBoxRow("option[{$this->option[$i]->id}]", "", $this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'], "", null, $this->option[$i]->fix, $this->option[$i]->fix);
                        }
                    }
                    $i++;
                }

                if ($this->choise == 1) {
                    $dsp->AddFormSubmitRow(t('Bestellen'));
                }
                break;
        }
    }

    /**
     * Show basked
     *
     * @param int $listid
     * @return void
     */
    public function get_basket($listid)
    {
        global $dsp;

        $show_caption = $this->caption;
        if ($this->type == 1 || $this->choise == false) {
            for ($i = 0; $i < count($this->option); $i++) {
                if ($this->option[$i]->ordered > 0) {
                    $this->option[$i]->get_basket($listid, $show_caption, false);
                }
            }

        } else {
            $dsp->AddTextFieldRow("option_$listid", $this->caption, $this->ordered, $this->error_food['order_error']);
            $this->error_food['order_error'] = "";
            for ($i = 0; $i < count($this->option); $i++) {
                if ($this->option[$i]->ordered > 0 || $this->option[$i]->fix > 0) {
                    $this->option[$i]->get_basket($listid, $show_caption, true);
                }
            }
        }
    }

    /**
     * Detail view of a product
     *
     * @param string $worklink
     * @return void
     */
    public function get_info($worklink)
    {
        global $dsp, $auth, $cfg;
                
        $dsp->NewContent(t('Produktebeschreibung'));
        $dsp->AddDoubleRow(t('Produktname'), "<b>" . $this->caption . "</b>");

        if ($this->desc != "") {
            $dsp->AddDoubleRow(t('Produktbeschreibung'), $this->desc);
        }

        if ($this->pic != "" && file_exists("ext_inc/foodcenter/" . $this->pic)) {
            $dsp->AddDoubleRow("", "<img src=\"ext_inc/foodcenter/{$this->pic}\" border=\"0\" alt=\"{$this->caption}\" />");
        }
        $dsp->AddSingleRow(t('Auswahlmöglichkeiten'));
            
        switch ($this->type) {
            case 1:
                if (is_object($this->option[0])) {
                    $dsp->AddDoubleRow("", "<b>" . $this->option[0]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'>" . $this->option[0]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[0]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
                }

                if (is_object($this->option[1])) {
                    $dsp->AddDoubleRow("", "<b>" . $this->option[1]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'>" . $this->option[1]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[1]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
                }

                if (is_object($this->option[2])) {
                    $dsp->AddDoubleRow("", "<b>" . $this->option[2]->unit . "</b>  <a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'>" . $this->option[2]->price . " " . $cfg['sys_currency'] . "</a><a href='$worklink&add={$this->id}&opt={$this->option[2]->id}'><img src=\"design/images/icon_basket.png\" border=\"0\" alt=\"basket\" /></a>");
                }

                break;
                
            case 2:
                if ($this->choise == 1) {
                    $dsp->SetForm("$worklink&add={$this->id}&opt=0");
                }
                $i = 0;
                while (is_object($this->option[$i])) {
                    if ($i==0) {
                        if ($this->choise == 0) {
                            $dsp->AddDoubleRow("<b>" . $this->caption . "</b>", $this->option[$i]->caption . " " . $this->option[$i]->unit . " <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");

                        } else {
                            $dsp->AddCheckBoxRow("option[{$this->id}]", "<b>" . $this->caption . "</b>", $this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'], "", null, $this->option[$i]->fix, $this->option[$i]->fix);
                        }
                    } else {
                        if ($this->choise == 0) {
                            $dsp->AddDoubleRow("", $this->option[$i]->caption . " " . $this->option[$i]->unit . "   <a href='$worklink&add={$this->id}&opt={$this->option[$i]->id}'>" . $this->option[$i]->price . " " . $cfg['sys_currency'] . "</a>");

                        } else {
                            $dsp->AddCheckBoxRow("option[{$this->option[$i]->id}]", "", $this->option[$i]->caption . " " . $this->option[$i]->unit . " "  . $this->option[$i]->price . " " . $cfg['sys_currency'], "", null, $this->option[$i]->fix, $this->option[$i]->fix);
                        }
                    }
                    $i++;
                }
                if ($this->choise == 1) {
                    $dsp->AddFormSubmitRow(t('Bestellen'));
                }
                break;
        }
        if ($auth['type'] > 1) {
            $dsp->AddDoubleRow("", $dsp->FetchSpanButton(t('Editieren'), "index.php?mod=foodcenter&amp;action=addproduct&amp;id=". $this->id));
        }
        $dsp->AddBackButton($worklink);
    }

    /**
     * Comparision with a different product
     *
     * @param \Product $prod
     * @return bool
     */
    public function compare($prod)
    {
        if ($this->type == 2) {
            for ($i = 0; $i < count($prod->option); $i++) {
                if ($this->option[$i]->ordered != $prod->option[$i]->ordered) {
                    return false;
                }
            }

        } else {
            if ($this->id != $prod->id) {
                return false;
            }
        }
    
        return true;
    }

    /**
     * Change ordered products
     *
     * @param int $val
     * @return bool
     */
    public function set_ordered($val)
    {
        $error = -1;
        foreach ($this->option as $key => $value) {

            // Check all product options if they are available
            if (($val * $this->option[$key]->ordered) <  $this->option[$key]->pice) {
                if ($error == -1 || $error > $this->option[$key]->pice) {
                    $error = $this->option[$key]->pice;
                }
            }
        }

        if ($error == -1) {
            $this->error_food['order_error'] = t('Das Produkt ist nicht in dieser Menge vorhanden.');
            $this->ordered = $error;
            return false;

        } else {
            $this->ordered = $val;
        }

        return true;
    }

    /**
     * Returns the price for the ordered product
     *
     * @param int $userid
     * @param int $delivered
     * @return int
     */
    public function order($userid, $delivered)
    {
        global $db, $party;

        $time = time();
        $price = 0;

        // Extended product
        if ($this->type == 2) {

            $opt_array = [];
            foreach ($this->option as $key => $value) {
                if ($this->option[$key]->ordered > 0 || $this->option[$key]->fix == 1) {
                    $opt_array[] = $this->option[$key]->id;
                    $price += $this->option[$key]->price;
                    if ($this->mat == 1) {
                        $tmp_rest1 = $this->option[$key]->pice - $this->option[$key]->ordered;
                        $db->qry("UPDATE %prefix%food_option SET pice = %int% WHERE id = %int%", $tmp_rest1, $this->option[$key]->id);
                    }
                }
            }

            if ($this->wait == 1) {
                $status = 2 ;
            } else {
                $status = 1;
            }

            $opt_string = implode("/", $opt_array);
            if ($db->qry("INSERT INTO %prefix%food_ordering SET 
                    userid = %int%,
                    productid = %int%,
                    partyid = %int%,
                    opts = %string%,
                    pice = %int%,
                    status = %string%,
                    ordertime = %string%,
                    lastchange = %string%,
                    supplytime = '0'", $userid, $this->id, $party->party_id, $opt_string, $this->ordered, $status, $time, $time)) {
                return $price * $this->ordered;

            } else {
                return 0;
            }

        // Simple product
        } else {

            foreach ($this->option as $key => $value) {
                if ($this->option[$key]->ordered > 0 || $this->option[$key]->fix == 1) {
                    if ($this->wait == 1) {
                        $status = 2;
                    } else {
                        $status = 1;
                    }

                    if ($db->qry("INSERT INTO %prefix%food_ordering SET 
                                    userid = %int%,
                                    productid = %int%,
                                    partyid = %int%,
                                    opts = %int%,
                                    pice = %int%,
                                    status = %string%,
                                    ordertime = %string%,
                                    lastchange = %string%,
                                    supplytime = '0'", $userid, $this->id, $party->party_id, $this->option[$key]->id, $this->option[$key]->ordered, $status, $time, $time)) {
                        $price += $this->option[$key]->price * $this->option[$key]->ordered;
                    }

                    if ($this->mat == 1) {
                        $tmp_rest2 = $this->option[$key]->pice - $this->option[$key]->ordered;
                        $db->qry("UPDATE %prefix%food_option SET pice = %int% WHERE id = %int%", $tmp_rest2, $this->option[$key]->id);
                    }
                }
            }

            return $price;
        }
    }
}

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

        $this->_Add_Option_Row(t('Produktoption'), t('Einheit'), t('Preis'), t('Einkaufspreis'), t('Anzahl'), t('Barcode'), "unit[$nr]", "price[$nr]", "eprice[$nr]", "piece[$nr]", "barcode[$nr]", $this->unit, $this->price, $this->eprice, $this->pice, $this->barcode, "hidden[$nr]", $this->id, $this->error['price'], $optional);
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
     * @param string    $text
     * @param string    $text_product
     * @param string    $text_price
     * @param string    $text_eprice
     * @param string    $text_piece
     * @param string    $text_barcode
     * @param string    $name_product
     * @param string    $name_price
     * @param string    $name_eprice
     * @param string    $name_piece
     * @param string    $name_barcode
     * @param int       $value_product
     * @param int       $value_price
     * @param int       $value_eprice
     * @param int       $value_piece
     * @param int       $value_barcode
     * @param string    $hidden_name
     * @param int       $hidden_id
     * @param string    $errortext
     * @param bool $optional
     * @return void
     */
    public function _Add_Option_Row($text, $text_product, $text_price, $text_eprice, $text_piece, $text_barcode, $name_product, $name_price, $name_eprice, $name_piece, $name_barcode, $value_product, $value_price, $value_eprice, $value_piece, $value_barcode, $hidden_name, $hidden_id, $errortext, $optional = false)
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

/**
 * Class supp
 *
 * Management of suppliers
 */
class supp
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
     * @return supp
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
            if ($db->num_rows($row) > 0) {
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


/**
 * Class cat
 *
 * Management of categories.
 * Used for menu cards.
 */
class cat
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
     * @return cat
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
            if ($db->num_rows($row) > 0) {
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

        $dsp->AddTextFieldRow("cat_name", t('Neue Produktkategorie'), $_POST['cat_name'], $this->error_food['catname']);
    }
}
