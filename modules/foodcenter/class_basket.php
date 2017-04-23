<?php

//include_once("modules/cashmgr/class_accounting.php");
include_once("modules/foodcenter/class_accounting.php");


class basket
{
    public $count = 0;
    public $price = 0;
    public $product;
    public $account;
    
    public function basket($backlink = null)
    {
        global $auth;
        
        $this->account = new accounting($auth->user_id);
        
        // Load Basket
        if (!isset($_SESSION['basket_item']['product'])) {
            $_SESSION['basket_item'] = array();
            $_SESSION['basket_count'] = 0;
            $this->count = 0;
            $this->product = new product_list();
        } else {
            $this->product = unserialize($_SESSION['basket_item']['product']);
            $this->count = $_SESSION['basket_count'];
        }
        if ($backlink != null) {
            $_SESSION['basket_item']['backlink'] = $backlink;
        }
    }
    
    
    public function add_to_basket_from_global()
    {
        global $func;
        // Add new Products
        if (isset($_GET['add']) && $_GET['add'] > 0) {
            if ($_GET['opt'] != 0) {
                if ($this->product->add_product($_GET['add'], $_GET['opt'])) {
                    $this->count ++;
                }
            } else {
                if (!is_array($_POST['option'])) {
                    $_POST['option'] = array();
                }
                if ($this->product->add_product($_GET['add'], $_POST['option'])) {
                    $this->count ++;
                }
            }
        }
        
        $_SESSION['basket_item']['product'] = serialize($this->product);
        $_SESSION['basket_count'] = $this->count;
    }
    
    
    
    public function show_basket()
    {
        global $dsp,$cfg, $func;
            
        $dsp->NewContent(t('Warenkorb'), t('Um einen Artikel zu löschen, setze ihn auf 0 und klicken anschließend auf "Neu berechnen".'));
        if ($this->product->count_products() > 0) {
            $dsp->SetForm("index.php?mod=foodcenter&action={$_GET['action']}&mode=change");
            $dsp->AddDoubleRow("<b>" . t('Artikel / Preis') . "</b> ", "<b>" . t('Anzahl') . "</b> ");

            $this->product->get_basket_form();
            $dsp->AddDoubleRow("<b>" . t('Gesamtpreis: ') . "</b> " . $this->product->count_products_price() . " " . $cfg['sys_currency'], "<b>" . t('Anzahl Artikel: ') . "</b> " . $this->product->count_products());
            $dsp->AddFormSubmitRow(t('Neu berechnen'), false, 'calculate');
            if ($_GET['action'] == "theke") {
                $fc_theke_delivered[0] = t('Nicht abgeholt');
                $fc_theke_delivered[1] = t('Alles direkt abgeholt');
                $fc_theke_delivered[2] = t('Abgeholt ausser Wartelisten Produkte');
                foreach ($fc_theke_delivered as $key => $value) {
                    ($key == 2) ? $selected = "selected" : $selected = "";
                    $delivered_array[] .= "<option $selected value=\"$key\">$value</option>";
                }
                $dsp->AddDropDownFieldRow("delivered", "", $delivered_array, "");
            }
        
            $dsp->AddFormSubmitRow(t('Bestellen'));
        } else {
            $func->information(t('Keine Artikel im Warenkorb'), NO_LINK);
        }
        if (isset($_SESSION['basket_item']['backlink'])) {
            $dsp->AddBackButton($_SESSION['basket_item']['backlink']);
        }
        $dsp->AddContent();
    }
    
    
    public function change_basket($userid)
    {
        global $func, $cfg, $db;
        $ok = true;
        $this->count = 0;
        foreach ($_POST as $key => $value) {
            if (stristr($key, "option")) {
                $tmp = explode("_", $key);
                if (!$this->product->chanche_ordered($tmp[1], $tmp[2], $value)) {
                    $ok = false;
                }
                $this->count += $value;
            }
        }
        $this->product->check_list();
        $_SESSION['basket_item']['product'] = serialize($this->product);
        $_SESSION['basket_count'] = $this->count;
        
        // Wird nur ausgeführt wenn Credit-System an
        if ($cfg['foodcenter_credit']) {
            //accounting($userid);
//echo("<script language='JavaScript'>alert
//('kram1 : ".$this->balance." ');</script>");
    
            $result = $db->qry_first("SELECT SUM(movement) AS total FROM %prefix%food_accounting WHERE userid = ".$userid);
        
            if ($result['total'] == "") {
                $this->balance = 0;
            } else {
                $this->balance = $result['total'];
            }

    
            if ($this->product->count_products_price() <= $this->balance) {
                return $ok;
            } else {
                $func->error(t('Nicht genügend Geld auf dem Konto.'), "index.php?mod=foodcenter&action={$_GET['action']}");
                return false;
            }
        } else {
            return $ok;
        }
    }
    
    public function order_basket($userid, $delivered = 0)
    {
        global $auth;
        $this->account->change(- $this->product->order_product($userid, $delivered), t('Bestellung Foodcenter') . "  (" . $auth['username'] . ") Artikel:".$this->product->order_productdesc($userid, $delivered), $userid);
        unset($this->product);
        $this->product = new product_list();
        unset($_SESSION['basket_item']['product']);
        $_SESSION['basket_item']['product'] = serialize($this->product);
        $_SESSION['basket_count'] = 0;
    }
}
