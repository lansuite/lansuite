<?php
namespace LanSuite\Module\PayPal;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Simple Class to store items to pay for via PayPal
 *
 * @author MaLuZ
 */
class PayPalItem {
    //put your code here
    
    public $description;
    public $value;
    public $quantity;
    public $sku;
    
        function __construct($description, $value,$sku='', $quantity=1) {
            $this->description = $description;
            $this->value = $value;
            $this->sku = $sku;
            $this->quantity = $quantity;
        }
}
