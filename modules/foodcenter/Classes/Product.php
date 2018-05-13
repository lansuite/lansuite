<?php

namespace LanSuite\Module\Foodcenter;

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
     * @var Category
     */
    private $cat;

    /**
     * Supplier
     *
     * @var Supplier
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
     * @var ProductOption[]
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
        $this->cat        = new Category($_POST['cat_id']);
        $this->supp       = new Supplier($_POST['supp_id']);
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
            $this->cat        = new Category($row['cat_id']);
            $this->supp       = new Supplier($row['supp_id']);
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
        $gd = new \LanSuite\GD();
        if ($gd->available) {
            $dsp->AddFileSelectRow("file", t('Bild hochladen'), $this->error_food['file'], null, null, true);
            $dsp->AddPictureDropDownRow("pic", t('Bild hochladen'), "ext_inc/foodcenter", $this->error_food['file'], true, basename($this->pic));
        }

        // Select Cat
        if (!is_object($this->cat)) {
            $this->cat = new Category();
        }
        $this->cat->cat_form();

        // Select Supplier
        if (!is_object($this->supp)) {
            $this->supp = new Supplier();
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
     * @throws \Exception
     * @throws \SmartyException
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
     * @param Product $prod
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
