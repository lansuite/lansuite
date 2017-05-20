<?php
class gd
{
    public $img;
    public $height;
    public $width;
    public $font;
    public $font_size;
    public $free_type = 0;
    public $available = 0;


    // Constructor
    public function __construct()
    {
        if (function_exists("gd_info")) {
            $GD = gd_info();
            $this->available = 1;
            if ($GD["Freetype Support"]) {
                $this->free_type = 1;
            }
            if ($GD["FreeType Support"]) {
                $this->free_type = 1;
            }
        }
    }


    public function NewImage($width, $height, $interlace = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->SetFont();

        if (function_exists("imagecreatetruecolor")) {
            $this->img = imagecreatetruecolor($this->width, $this->height);
        } else {
            $this->img = imagecreate($this->width, $this->height);
        }
        if (!$this->img) {
            $this->img = imagecreate($this->width, $this->height);
        }
        if (!$this->img) {
            return t('Unable to Initialize new GD image stream');
        }

        ImageInterlace($this->img, $interlace);
    }


  // Output image (as file if $file is specified, or as direct output if not)
    public function PutImage($file = null, $type = null, $destroy = true)
    {
        global $config;

        if ($file) {
            $path = substr($file, 0, strrpos($file, "/"));
            $filename = substr($file, strrpos($file, "/") + 1, strlen($file));
            $type = substr($filename, strrpos($filename, ".") + 1, 4);
        }
        if ($type == "") {
            $type = "png";
        }

        if ($file) {
            if (!is_writable($path)) {
                return t('Unable to write in directory /\'/%1/\'/', $path);
            }
        } else {
            Header("Content-type: image/$type");
        }

        if ($file) {
            switch (strtolower($type)) {
                case "jpeg":
                case "jpg":
                    if (ImageTypes() & IMG_JPG) {
                        ImageJPEG($this->img, $file);
                    }
                    break;
                case "gif":
                    if (ImageTypes() & IMG_GIF) {
                        ImageGIF($this->img, $file);
                    }
                    break;
                case "wbmp":
                    if (ImageTypes() & IMG_WBMP) {
                        ImageWBMP($this->img, $file);
                    }
                    break;
                case "bmp":
                    include_once("ext_scripts/gd/bmp.php");
                    ImageBMP($this->img, $file);
                    break;
                case "ico":
                    include_once("ext_scripts/gd/ico.php");
                    ImageICO($this->img, $file);
                    break;
                case "cur":
                    include_once("ext_scripts/gd/cur.php");
                    ImageCUR($this->img, $file);
                    break;
                default:
                    if (ImageTypes() & IMG_PNG) {
                        ImagePNG($this->img, $file);
                    }
                    break;
            }
            chmod($file, octdec($config["lansuite"]["chmod_file"]));

            // Check filesize. Delete if filesize = 0 (i.e. becaus of exceeded disk quota), so it is tried to be generated on next load
            if (filesize($file) == 0) {
                unlink($file);
            }
        } else {
            switch (strtolower($type)) {
                case "jpeg":
                case "jpg":
                    if (ImageTypes() & IMG_JPG) {
                        ImageJPEG($this->img);
                    }
                    break;
                case "gif":
                    if (ImageTypes() & IMG_GIF) {
                        ImageGIF($this->img);
                    }
                    break;
                case "wbmp":
                    if (ImageTypes() & IMG_WBMP) {
                        ImageWBMP($this->img);
                    }
                    break;
                case "bmp":
                    include_once("ext_scripts/gd/bmp.php");
                    ImageBMP($this->img);
                    break;
                case "ico":
                    include_once("ext_scripts/gd/ico.php");
                    ImageICO($this->img);
                    break;
                case "cur":
                    include_once("ext_scripts/gd/cur.php");
                    ImageCUR($this->img);
                    break;
                default:
                    if (ImageTypes() & IMG_PNG) {
                        ImagePNG($this->img);
                    }
                    break;
            }
        }

        if ($destroy) {
            ImageDestroy($this->img);
        }
    }


    public function SetFont($font = null, $font_size = null)
    {
        if ($font) {
            $this->font = $font;
        } else {
            $this->font = $cfg["t_font_path"];
        }
        if (!$this->font) {
            $this->font = "ext_inc/fonts/verdana.ttf";
        }

        if ($font_size) {
            $this->font_size = $font_size;
        } else {
            $this->font_size = $cfg["t_font_size"];
        }
        if (!$this->font_size) {
            $this->font_size = 8;
        }
    }


    #### Draws the text $text @ $xpos, $ypos, with color $color.
    ## The text will be cut to a maximum of $max_length chars.
    public function Text($xpos, $ypos, $color, $text, $max_length = null, $angle = null)
    {
        global $cfg;

        if ($max_length > 4) {
            if (strlen($text) > $max_length) {
                $text = substr($text, 0, $max_length-3) . "...";
            }
        }
        if ($angle == "") {
            $angle = 0;
        }

        if ($this->free_type) {
            ImageTtfText($this->img, $this->font_size, $angle, $xpos, $ypos + $this->font_size, $color, $this->font, $text);
        } else {
            $ypos = $ypos - 3;
            $text_parts = split("\r\n", $text);
            $i = 0;
            while (list($key, $val) = each($text_parts)) {
                ImageString($this->img, 2, $xpos, $ypos + $i, $val, $color);
                $i += ($this->font_size + 2);
            }
        }
    }

    public function OpenImage($filename)
    {
        if (!file_exists($filename)) {
            return 0;
        }

        $type = strtolower(substr($filename, strrpos($filename, ".") + 1, 4));
        switch ($type) {
            default:
                return 0;
            break;
            case "png":
                if (ImageTypes() & IMG_PNG) {
                    $img_src = ImageCreateFromPNG($filename);
                }
                break;
            case "jpeg":
            case "jpg":
                if (ImageTypes() & IMG_JPG) {
                    $img_src = ImageCreateFromJPEG($filename);
                }
                break;
            case "gif":
                if (ImageTypes() & IMG_GIF) {
                    $img_src = ImageCreateFromGIF($filename);
                }
                break;
            case "wbmp":
                if (ImageTypes() & IMG_WBMP) {
                    $img_src = ImageCreateFromWBMP($filename);
                }
                break;
            case "bmp":
                include_once("ext_scripts/gd/bmp.php");
                $img_src = ImageCreateFromBMP($filename);
                break;
            case "ico":
                include_once("ext_scripts/gd/ico.php");
                $img_src = ImageCreateFromICO($filename);
                break;
            case "cur":
                include_once("ext_scripts/gd/cur.php");
                $img_src = ImageCreateFromCUR($filename);
                break;
        }

        return $img_src;
    }


    public function CreateThumb($old_file, $new_file, $max_width, $max_height)
    {
        global $func;
        if (($old_file != $new_file) and file_exists($new_file)) {
            return;
        }

        $imgsrc_old = $this->OpenImage($old_file);
        if (!$imgsrc_old) {
            $func->error("Could not open source file '$old_file'", NO_LINK);
            return false;
        } else {
            // Calculate new size
            $old_width = imagesx($imgsrc_old);
            $old_height = imagesy($imgsrc_old);
  
            $ratio_x = $old_width / $max_width;
            $ratio_y = $old_height / $max_height;

            if ($old_width <= $max_width and $old_height <= $max_height) {
                $new_width = $old_width;
                $new_height = $old_height;
            } elseif ($ratio_x > $ratio_y and $ratio_x > 0) {
                $new_width = $max_width;
                $new_height = $old_height / $ratio_x;
            } elseif ($ratio_y > 0) {
                $new_width = $old_width / $ratio_y;
                $new_height = $max_height;
            } else {
                $func->error("Source file '$old_file' has 0x0 pixel", NO_LINK);
                return false;
            }
  
            $this->NewImage($new_width, $new_height);
            ImageCopyResized($this->img, $imgsrc_old, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
  
            imagecolortransparent($this->img, imagecolorallocate($this->img, 255, 255, 255));
  
            $this->PutImage($new_file);
            ImageDestroy($imgsrc_old);
        }
        return true;
    }


    public function Colorize($source_file, $r, $g, $b, $percentage)
    {
        if ($source_file) {
            $this->img = $this->OpenImage($source_file);
            $this->width = imagesx($this->img);
            $this->height = imagesy($this->img);
        }

        // Create Layover
        $layover = imagecreate($this->width, $this->height);
        $fill = imagefill($layover, 0, 0, imagecolorallocate($layover, $r, $g, $b));

        // Merge Layover with original image
        $merge = imagecopymerge($this->img, $layover, 0, 0, 0, 0, $this->width, $this->height, $percentage);
        imagedestroy($layover);
    }


    public function MergeImages($source_file1, $source_file2, $target_file)
    {
        $this->img = $this->OpenImage($source_file1);
        $this->width = imagesx($this->img);
        $this->height = imagesy($this->img);

        $source_img2 = $this->OpenImage($source_file2);
        $source_img2_width = imagesx($source_img2);
        $source_img2_height = imagesy($source_img2);

        $merge = imagecopymerge($this->img, $source_img2, 0, 0, 0, 0, $this->width, $this->height, 25);

        imagedestroy($source_img2);

        $this->PutImage($target_file);
    }
}
