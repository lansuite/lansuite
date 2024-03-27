<?php

namespace LanSuite;

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
class BarcodeSystem
{
    private \LanSuite\Barcode $class_barcode;

    public function __construct()
    {
        global $cfg, $database;
        
        $this->class_barcode = new Barcode($cfg['sys_barcode_typ']);

        $this->class_barcode->setHeight(50);
        $this->class_barcode->setScale(1);
        $this->class_barcode->setHexColor("#000000", "#FFFFFF");
        
        if (isset($_POST['barcodefield']) && $cfg['sys_barcode_on']) {
            $data = $database->queryWithOnlyFirstRow("SELECT userid FROM %prefix%user WHERE barcode = ?", [$_POST['barcodefield']]);
            $_POST['userid'] = $data['userid'];
            $_GET['userid'] = $data['userid'];
        }
    }

    /**
     * @param int $userid
     * @return int
     */
    private function gencode($userid)
    {
        $code = 768_300_000_000;
        $code = $code + ($userid * 10000);
        $code = $code + random_int(0, 9999);
        return $code;
    }

    /**
     * @param int $userid
     * @return int
     */
    private function getcode($userid)
    {
        global $database;
        
        $data = $database->queryWithOnlyFirstRow("SELECT barcode FROM %prefix%user WHERE userid = ?", [$userid]);
        if ($data['barcode'] == "0") {
            $data['barcode'] = $this->gencode($userid);

            $database->query("UPDATE %prefix%user SET barcode = ? WHERE userid = ?", [$data['barcode'], $userid]);
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
