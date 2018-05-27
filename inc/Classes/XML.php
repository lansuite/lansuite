<?php

namespace LanSuite;

class XML
{

    /**
     * Get the contents of the first occurrence of a specific XML-tag.
     *
     * @param string    $tag    The tag to search for
     * @param string    $input  The string to search in
     * @param bool      $save   Should --lt-- and --gt-- be replaced? Costs performance, but needed for text-fields. Attention: Use save only in most inner calls, containing text only
     * @return string
     */
    public function getFirstTagContent($tag, $input, $save = false)
    {
        $start = strpos($input, '<' . $tag . '>') + strlen($tag) + 2;

        // If the tag has attributes, remove them, for the end tag won't contain them
        if (strpos($tag, ' ') > 0) {
            $tag = substr($tag, 0, strpos($tag, ' '));
        }

        $end = strpos($input, '</' . $tag . '>') - $start;
        if ($end <= 0) {
            return '';
        }

        if ($save) {
            return trim(
                str_replace(
                    '--lt--',
                    '<',
                    str_replace('--gt--', '>', substr($input, $start, $end))
                )
            );
        }

        return trim(substr($input, $start, $end));
    }

    /**
     * @param string    $tag    The tag to search for
     * @param string    $input  The string to search in
     * @param bool      $save   Should --lt-- and --gt-- be replaced? Costs performance, but needed for text-fields. Attention: Use save only in most inner calls, containing text only
     * @return array
     */
    public function getTagContentArray($tag, $input, $save = false)
    {
        $content = explode('<' . $tag . '>', $input);

        // If the tag has attributes, remove them, for the end tag won't contain them
        if (strpos($tag, ' ') > 0) {
            $tag = substr($tag, 0, strpos($tag, ' '));
        }

        $i = 1;
        $output = [];
        while ($i < sizeof($content)) {
            // Copy till end-tag
            if ($save) {
                $output[] = str_replace(
                    '--lt--',
                    '<',
                    str_replace(
                        '--gt--',
                        '>',
                        substr($content[$i], 0, strpos($content[$i], '</' . $tag . '>'))
                    )
                );
            } else {
                $output[] = substr($content[$i], 0, strpos($content[$i], '</' . $tag . '>'));
            }
            $i++;
        }

        return $output;
    }

    /**
     * Gets master-tags in XML-files.
     * It also combines closed and later re-opened tags.
     *
     * @param string    $tag_to_grab
     * @param string    $input
     * @return string
     */
    public function get_tag_content_combine($tag_to_grab, $input)
    {
        $output = '';
        $tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0);
        $i = 1;

        while ($i < sizeof($tag_content)) {
            if (strpos($tag_to_grab, ' ') > 0) {
                $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, ' '));
            }
            $block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0);
            $block[0] = str_replace('--lt--', '<', $block[0]);
            $block[0] = str_replace('--gt--', '>', $block[0]);
            $output .= $block[0];
            $i++;
        }

        return $output;
    }

    /**
     * Gets master-tags in XML-files.
     * It also combines closed and later re-opened tags, and writes them into arrays.
     *
     * @param string    $tag_to_grab
     * @param string    $input
     * @return array
     */
    public function get_tag_content_array($tag_to_grab, $input)
    {
        $output = [];
        $tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0);
        $i = 1;

        while ($i < sizeof($tag_content)) {
            if (strpos($tag_to_grab, ' ') > 0) {
                $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, ' '));
            }
            $block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0);
            $output[] = $block[0];
            $i++;
        }

        return $output;
    }

    /**
     * Grabs to contents of a single XML-Tag.
     * It does not support combining!
     *
     * @param string    $tag_to_grab
     * @param string    $input
     * @param bool      $replace_html
     * @return string
     */
    public function get_tag_content($tag_to_grab, $input, $replace_html = true)
    {
        $tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0);
        if (strpos($tag_to_grab, ' ') > 0) {
            $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, ' '));
        }

        $tag_content = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[1], 0);

        if ($replace_html) {
            $tag_content[0] = str_replace('--lt--', '<', $tag_content[0]);
            $tag_content[0] = str_replace('--gt--', '>', $tag_content[0]);
        }

        return $tag_content[0];
    }

    /**
     * @param int       $level
     * @return string
     */
    private function level2tab($level)
    {
        $tab = '';
        for ($i = 0; $i < $level; $i++) {
            $tab .= "\t";
        }
         
        return $tab;
    }

    /**
     * Writes master tags.
     *
     * @param string    $tag
     * @param string    $content
     * @param int       $level
     * @return string
     */
    public function write_master_tag($tag, $content, $level)
    {
        $tab = $this->level2tab($level);
        $brackets = $tab . "<%s>\r\n" . '%s' . $tab . "</%s>\r\n";

        $first_space = strpos($tag, ' ');
        if ($first_space == 0) {
            $first_space = strlen($tag);
        }

        return sprintf($brackets, $tag, $content, substr($tag, 0, $first_space));
    }

    /**
     * Writes tags.
     *
     * @param string    $tag
     * @param string    $content
     * @param int       $level
     * @return string
     */
    public function write_tag($tag, $content, $level)
    {
        global $func;

        $tab = $this->level2tab($level);
        $brackets = $tab . "<%s>%s</%s>\r\n";

        $first_space = strpos($tag, ' ');
        if ($first_space == 0) {
            $first_space = strlen($tag);
        }

        // TODO: AllowHTML should be removed. Then --lt-- would also not be needed anymore
        // But then the import will need to replace HTML back.
        $content = $func->AllowHTML($content);
        $content = str_replace('<', '--lt--', $content);
        $content = str_replace('>', '--gt--', $content);

        return sprintf($brackets, $tag, $content, substr($tag, 0, $first_space));
    }

    /**
     * @param string    $string
     * @return string
     */
    public function convertinputstr($string)
    {
        $string = str_replace('"', '', $string);
        $string = str_replace('\\', '', $string);
        $string = str_replace('\'', '', $string);

        return $string;
    }
}
