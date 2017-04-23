<?php

/*************************************************************************
*
*	Lansuite - Webbased LAN-Party Management System
*	-----------------------------------------------
*
*	(c) 2001-2003 by One-Network.Org
*
*	Lansuite Version:	2.0
*	File Version:		2.0
*	Filename: 		class_graph.php
*	Module: 		Framework
*	Main editor:
*	Last change: 		05.01.2003
*	Description: 		Class to draw a graph for stats
*	Remarks:
*
**************************************************************************/

class graph
{
    public $im;
    public $height;
    public $width;
    public $background_color;
    public $black;
    public $blue;
    public $grey;
    public $red;
    public $max_hits;
    public $percent_norm;

    
    public function init_image($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        
        $this->max_hits = 1080;
        $this->percent_norm = ($this->height - 20) / $this->max_hits;
        
        $this->create_image();
    }
        
    public function create_image()
    {
        $this->im = imagecreate($this->width, $this->height)
        or die("Cannot Initialize new GD image stream");
    
        // color
        $this->background_color        = imagecolorallocate($this->im, 214, 216, 218);
        $this->black            = imagecolorallocate($this->im, 0, 0, 0);
        $this->blue            = imagecolorallocate($this->im, 78, 88, 108);
        $this->grey            = imagecolorallocate($this->im, 234, 234, 234);
        $this->red            = imagecolorallocate($this->im, 255, 0, 0);
    }
    
    public function define_chart($number, $steps)
    {
        for ($i = 0, $pos = 1; $i <= ($number*$steps); $pos++, $i=$i+$steps) {
            $string_pos = $this->height-20-(($this->height / ($number+2))*$pos);
            $string_content = "$i";
            imagestring($this->im, 2, 20, $string_pos, $string_content, $this->black);
        }
    
    // Layout
    imagefilledrectangle($this->im, 10, $this->height-20, $this->width-10, $this->height-10, $this->grey);
        imageline($this->im, 50, 10, 50, $this->height-20, $this->grey);
    }
    
    public function setentry($pos, $description_x, $hits, $hitline)
    {
        $hits_size = $hits *  $this->percent_norm;
        $hitline = $hitline *  $this->percent_norm;
        
        // Balken
        imagefilledrectangle($this->im, 50+17*$pos, $this->height-22-$hits_size, 60+17*$pos, $this->height-22, $this->blue);
         
        // Day
        imagestring($this->im, 2, 50+17*$pos, $this->height-22, $description_x, $this->black);
         
        // Hits
        imagestring($this->im, 1, 48+17*$pos, $this->height-35-$hits_size, $hits, $this->black);
         
        // Red line
        imageline($this->im, 55-17+17*$pos, $this->height-22-$hitline, 55+(17*$pos), $this->height-22-$hits_size, $this->red);
    }
    
    public function generate_image()
    {
        $image = "test.png";
        imagepng($this->im, $image);
        $image = "<img src=\"test.png\"/>";
        return $image;
    }
    

    // Filename is the name of the output file
    // Data consits arrays which are includeing index name for the name-string displayed and count for the
    // value. This value is NOT a percentage!
    // Text is an array with indices "top","left","bottom","right" to write some text on the sides of the diagramm
    
    // Tested with GD 2, freetype2. You need lib freetype2 and Arial.ttf installed
    
    public function generate_line_diagramm($filename, $text, $data)
    {
        $y_max = 70 + sizeof($data) * 40;

        $image = imagecreatetruecolor(600, $y_max);
        imagecolorallocate($image, 255, 255, 255);

    // Colours
    $colours["black"] = imagecolorallocate($image, 0, 0, 0);
        $colours["grey"] = imagecolorallocate($image, 193, 193, 193);
        $colours["red"] = imagecolorallocate($image, 255, 0, 0);
        $colours["blue"] = imagecolorallocate($image, 202, 218, 249);
        $colours["white"] = imagecolorallocate($image, 255, 255, 255);
    

    // Texts
    imagettftext($image, "10", "0", "150", "10", "255", "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", $text["top"]);
        imagettftext($image, "10", "90", "10", $y_max/2, "255", "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", $text["left"]);
        imagettftext($image, "10", "270", "590", $y_max/2, "255", "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", $text["right"]);
        imagettftext($image, "10", "0", "300", $y_max - 10, "255", "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", $text["buttom"]);


    // Background for middle
    $date = date("Y-m-d H:i");
    
        imagefilledrectangle($image, 22, 22, 580, $y_max-30, $colours["grey"]);
        imagerectangle($image, 22, 22, 580, $y_max-30, $colours["black"]);
        imagettftext($image, "8", "0", "400", $y_max - 35, "255", "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", "$date | lansuite 2.0 Chart");


    
        foreach ($data as $these_data) {
            $overall_count = $overall_count + $these_data["count"];
        }
    
        foreach ($data as $draw_this_line) {
            $x1 = 30;
            $y1 = $y1+40;
            $x2 = $x1 + (540*($draw_this_line["count"]/$overall_count));
            $y2 = $y1 + 20;
    
            $red_value = 255 * round(($draw_this_line["count"])/$overall_count, 1);
    
    
    
            $myred = imagecolorallocate($image, 255, $red_value, $red_value);
    
            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $myred);
            imagerectangle($image, $x1, $y1, $x2, $y2, $colours["black"]);
    
            $line_text = $draw_this_line["name"] . " (" . round(100*(($draw_this_line["count"]/$overall_count)), 2) . " %)";
    
    
    
            imagettftext($image, "10", "0", $x1 + 3, $y1 + 14, 250, "{$GLOBALS["config"]["environment"]["dir"]}/ext_inc/fonts/arial.ttf", $line_text);
        }

        imagepng($image, $filename, "100");
    }//function
}
