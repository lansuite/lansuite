<?php

namespace LanSuite\Module\PayPal;

/**
 * Simple Class to store items to pay for via PayPal
 */
class PayPalItem
{

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $value;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var string
     */
    public $sku;
    
    public function __construct($description, $value, $sku = '', $quantity = 1)
    {
        $this->description  = $description;
        $this->value        = $value;
        $this->sku          = $sku;
        $this->quantity     = $quantity;
    }
}
