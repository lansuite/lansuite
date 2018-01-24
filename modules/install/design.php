<?php

$xml = new xml();

$dsp->NewContent(t('Design Manager'), t('Editiere Design-Templates und setze das aktive Design'));

switch ($_GET['step']) {
  // List designs
    default:
        // Open design-dir
        $design_dir = opendir("design/");

        // Check all Subdirs of $design_dir for valid design-xml-files
        $t_array = array();
        while ($akt_design = readdir($design_dir)) {
            if ($akt_design != '.' and $akt_design != '..' and $akt_design != '.svn' and $akt_design != 'templates' and $akt_design != 'images') {
                $file = "design/$akt_design/design.xml";
                if (file_exists($file)) {
                    $xml_file = "";
                    $handle = fopen($file, "rb");
                    $xml_file = fread($handle, filesize($file));
                    fclose($handle);

                    $name = $xml->get_tag_content('name', $xml_file);
                    $description = $xml->get_tag_content('description', $xml_file);
                    $version = $xml->get_tag_content('version', $xml_file);
                    $author = $xml->get_tag_content('author', $xml_file);
                    $contact = $xml->get_tag_content('contact', $xml_file);
                    $website = $xml->get_tag_content('website', $xml_file);
                    $comments = $xml->get_tag_content('comments', $xml_file);

                    $dsp->AddDoubleRow("<a href=\"index.php?mod=install&action=design&step=10&des=$akt_design\">$name (Version: $version)<br />$description</a>", "$author [$contact]<br /><a href=\"http://$website\" target=\"_blank\">$website</a>");
                }
            }
        }
        break;

  // List designs templates
    case 10:
        $dsp->AddSingleRow("<a href=\"index.php?mod=install&action=design&step=11&des={$_GET['des']}&file=index\">index.php</a>");
        $dsp->AddSingleRow("<a href=\"index.php?mod=install&action=design&step=11&des={$_GET['des']}&file=index_fullscreen\">index_fullscreen.htm</a>");
        $dsp->AddSingleRow("<a href=\"index.php?mod=install&action=design&step=11&des={$_GET['des']}&file=box_case\">box_case.htm</a>");
        $dsp->AddSingleRow("<a href=\"index.php?mod=install&action=design&step=11&des={$_GET['des']}&file=box_case_closed\">box_case_closed.htm</a>");
        break;

  // Edit template
    case 11:
        if (!$_POST['content']) {
            switch ($_GET['file']) {
                case 'index':
                    $file = "design/{$_GET['des']}/templates/index.php";
                    break;
                case 'index_fullscreen':
                    $file = "design/{$_GET['des']}/templates/index_fullscreen.htm";
                    break;
                case 'box_case':
                    $file = "design/{$_GET['des']}/templates/box_case.htm";
                    break;
                case 'box_case_closed':
                    $file = "design/{$_GET['des']}/templates/box_case_closed.htm";
                    break;
            }

            $FileCont = "";
            $handle = fopen($file, "rb");
            $FileCont = fread($handle, filesize($file));
            fclose($handle);
            $_POST['content'] = $FileCont;
        }

        $dsp->AddTextAreaRow('content', 'index.php', $FileCont, '', '', 40);
        break;
}

$dsp->AddContent();
