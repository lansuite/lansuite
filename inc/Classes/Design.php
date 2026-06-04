<?php

namespace LanSuite;

class Design
{

    public static function getDesigns() 
    {
        $selections = [];
        $selections[''] = t('System-Vorgabe');

        $xml = new \LanSuite\XML();

        $ResDesign = opendir('design/');
        while ($dir = readdir($ResDesign)) {
            if (is_dir("design/$dir") and file_exists("design/$dir/design.xml") and ($dir != 'beamer')) {
                $file = "design/$dir/design.xml";
                $ResFile = fopen($file, "rb");
                $XMLFile = fread($ResFile, filesize($file));
                fclose($ResFile);
                $DesignName = $xml->get_tag_content('name', $XMLFile);
                $selections[$dir] = $DesignName;
            }
        }
        closedir($ResDesign);
        return $selections;
    }
}