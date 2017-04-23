<?php

include_once("modules/noc/class_noc.php");
$noc = new noc();

/*	Network Operations Centre
 *
 *	originally based on phpSMITH
 *
 *
 *	Maintainer: Joachim Garth <josch@one-network.org>
 */

// Check Filesystem
$filepath = "design/images/";

// Check Portstatus:
//
// On  = Connected, Enabled
// Off = Not Connected, Enabled
// Failed = Disabled

// LWL = Fibre Optic Ports
// RJ45 = Standard Ports

if ($_GET["type"] == "lwl") {
    $special_image = "lwl_";
}

$ImageURL = "design/{$_SESSION['auth']['design']}/images/noc_port_{$special_image}{$_GET['status']}.png";

// Take the standart Image as base
$Image = ImageCreateFromPNG($ImageURL);

// On RJ45 Ports, write the Speed and The Portnumber In the Middle of the Picutre
switch ($_GET["type"]) {
    default:
    case "rj45":
        $string = $_GET["portnr"];
        $black = ImageColorAllocate($Image, 0, 0, 0);

        $ImageSize = getImageSize($ImageURL);

        // Place it in the lower middle

        $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
        $stringycoordinate = ($ImageSize[1]) - (ImageFontHeight(1) + 2);

        ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

        if ($_GET["status"] == "failed") {
            $string = "DIS-";

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) + 2);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = "ABLED";

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) - 6);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }

        if ($_GET["status"] == "off") {
            $string = "NOT";

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) + 2);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = "CONN.";

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) - 6);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }

        if ($_GET["status"] == "on") {
            $string = $_GET["speed"];

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) + 2);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = $_GET["unit"]."/s";

            $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) - 6);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }

        break;

    case "lwl":
        // In LWL Port Pictures, write Port Number on the Left and Speed on the Right Side

        $black = ImageColorAllocate($Image, 0, 0, 0);

        $ImageSize = getImageSize($ImageURL);

        $string = $_GET["portnr"];

        // Place it in the lower middle
        $stringxcoordinate = ($ImageSize[0] / 4) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
        $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) - 4);

        ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

        if ($_GET["status"] == "failed") {
            $string = "DIS-";

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1));

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = "ABLED";

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) + (ImageFontHeight(1) - 8);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }

        if ($_GET["status"] == "off") {
            $string = "NOT";

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1));

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = "CONN.";

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) + (ImageFontHeight(1) - 8);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }

        if ($_GET["status"] == "on") {
            $string = $_GET["speed"];

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1));

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

            $string = $_GET["unit"]."/s";

            $stringxcoordinate = (($ImageSize[0] / 4) * 3) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
            $stringycoordinate = ($ImageSize[1] / 2) + (ImageFontHeight(1) - 8);

            ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);
        }
        
        break;

    case "lo":
        $black = ImageColorAllocate($Image, 0, 0, 0);

        $ImageSize = getImageSize($ImageURL);

        $string = $_GET["portnr"];

        // Place it in the lower middle
        $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2));
        $stringycoordinate = ($ImageSize[1]) - (ImageFontHeight(1) + 2);
        ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

        $string = "LOOP";

        $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
        $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) - 6);
        ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

        $string = "LOCAL";

        $stringxcoordinate = ($ImageSize[0] / 2) - (ImageFontWidth(1) * (strlen($string) / 2) - 1);
        $stringycoordinate = ($ImageSize[1] / 2) - (ImageFontHeight(1) + 2);
        ImageString($Image, 1, $stringxcoordinate, $stringycoordinate, $string, $black);

        break;
}

// Output the image
        imagepng($Image, $filepath . "port_" . $_GET['type'] . "_" . $_GET['status'] . "_" . $_GET['portnr']);
        header("Content-type: image/png");
        ImagePNG($Image);
