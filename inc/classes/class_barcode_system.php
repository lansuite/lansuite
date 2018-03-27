<?php

/**
 * Class barcode_system
 *
 * This class is for generating barcodes in different encoding symbols.
 * It supports EAN-13, EAN-8, UPC-A, UPC-E, ISBN, 2 of 5 Symbologies(std, ind, interleaved), postnet,
 * codabar, code128, code39, code93 symbologies.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * Requirements: PHP with GD library support.
 *
 * Reference: http://www.barcodeisland.com/symbolgy.phtml
 */
class barcode_system
{
    public $class_barcode;
    
    public function __construct()
    {
        global $cfg, $db;
        
        $this->class_barcode = new barcode($cfg['sys_barcode_typ']);

        $this->class_barcode->setHeight(50);
        $this->class_barcode->setScale(1);
        $this->class_barcode->setHexColor("#000000", "#FFFFFF");
        
        if (isset($_POST['barcodefield']) && $cfg['sys_barcode_on']) {
            $data = $db->qry_first("SELECT userid FROM %prefix%user WHERE barcode=%string%", $_POST['barcodefield']);
            $_POST['userid'] = $data['userid'];
            $_GET['userid'] = $data['userid'];
        }
    }

    /**
     * @param int $userid
     * @return int
     */
    public function gencode($userid)
    {
        $code = 768300000000;
        $code = $code + ($userid * 10000);
        $code = $code + mt_rand(0, 9999);
        return $code;
    }

    /**
     * @param int $userid
     * @return int
     */
    public function getcode($userid)
    {
        global $db;
        
        $data = $db->qry_first("SELECT barcode FROM %prefix%user WHERE userid=%int%", $userid);
        if ($data['barcode'] == "0") {
            $data['barcode'] = $this->gencode($userid);

            $db->qry_first("UPDATE %prefix%user SET barcode = %string% WHERE userid=%int%", $data['barcode'], $userid);
        }

        return $data['barcode'];
    }

    /**
     * @param int $userid
     * @param string $filename
     * @return bool
     */
    public function get_image($userid, $filename)
    {
        $code = $this->getcode($userid);
        return $this->class_barcode->genBarCode($code, "png", $filename);
    }

    /**
     * @param string $filename
     * @return void
     */
    public function kill_image($filename)
    {
        unlink($filename . ".png");
    }
}
