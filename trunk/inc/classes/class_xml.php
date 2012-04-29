<?php

class xml {
  /**
   * Get the contents of the first occurence of an specific XML-tag
   *
   * @param string tag the tag to search for
   * @param string input the string to search in
   * @param bool save should --lt-- and --gt-- be replaced? Costs performance, but needed for text-fields
   *                  Attention: Use save only in most inner calls, containing text only
   * @return string containing tag content
   */
    function getFirstTagContent($tag, $input, $save = 0) {
        $start = strpos($input, '<'. $tag .'>') + strlen($tag) + 2;

        // If tag has attributes, remove them, for the end tag won't contain them
		if (strpos($tag, " ") > 0) $tag = substr($tag, 0, strpos($tag, " "));

        $end = strpos($input, '</'. $tag .'>') - $start;
        if ($end <= 0) return '';

        if ($save) {
            return trim(str_replace("--lt--", "<", str_replace("--gt--", ">",
                substr($input, $start, $end)
                )));
        } else return trim(substr($input, $start, $end));
    }

  /**
   * Get the contents to a specific XML-tag
   *
   * @param string tag the tag to search for
   * @param string input the string to search in
   * @param bool save should --lt-- and --gt-- be replaced? Costs performance, but needed for text-fields
   *                  Attention: Use save only in most inner calls, containing text only
   * @return array of string Array containing tag contents
   */
    function getTagContentArray($tag, $input, $save = 0) {
        $content = explode('<'. $tag .'>', $input);

        // If tag has attributes, remove them, for the end tag won't contain them
		if (strpos($tag, " ") > 0) $tag = substr($tag, 0, strpos($tag, " "));

        $i = 1;
        $output = array();
		while ($i < sizeof($content)) {

            // Copy till end-tag
            if ($save) {
                $output[] = str_replace("--lt--", "<", str_replace("--gt--", ">",
                    substr($content[$i], 0, strpos($content[$i], '</'. $tag .'>'))
                    ));
            } else $output[] = substr($content[$i], 0, strpos($content[$i], '</'. $tag .'>'));
			$i++;
		}
		return $output;
    }

	// This function gets master-tags in XML-files. It also combines closed and later re-opened tags
	function get_tag_content_combine($tag_to_grab, $input) {
		$tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0);
		$i = "1";
		while ($i < sizeof($tag_content)) {
			if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
			$block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0);
			$block[0] = str_replace("--lt--", "<", $block[0]);
			$block[0] = str_replace("--gt--", ">", $block[0]);
			$output .= $block[0];
			$i++;					
		}

		return $output;
	}

	// This function gets master-tags in XML-files. It also combines closed and later re-opened tags, and writes them into arrays
	function get_tag_content_array($tag_to_grab, $input) {
		$tag_content = preg_split("/\<$tag_to_grab\>/i",$input, 0);
		$i = "1";
		while ($i < sizeof($tag_content)) {
			if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
			$block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0);
#			$block[0] = str_replace("--lt--", "<", $block[0]);
#			$block[0] = str_replace("--gt--", ">", $block[0]);
			$output[] = $block[0];
			$i++;					
		}

		return $output;
	}

	// This function grabs to contents of a single XML-Tag. It does not support combining!
	function get_tag_content($tag_to_grab, $input, $replace_html = 1) {
		// Grab header
		$tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0);
		if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
		$tag_content = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[1], 0);

		if ($replace_html) {
			$tag_content[0] = str_replace("--lt--", "<", $tag_content[0]);
			$tag_content[0] = str_replace("--gt--", ">", $tag_content[0]);
		}
		return $tag_content[0];
	}
	
	
	function level2tab($level) {
		for($i=0; $i < $level; $i++) $tab .= "\t"; 
		 
		return $tab;
	}

	// This function writes master tags
	function write_master_tag($tag, $content, $level) {	
		$tab = $this->level2tab($level);
		$brackets = $tab."<%s>\r\n" . "%s". $tab ."</%s>\r\n";

		$first_space = strpos($tag, " ");
		if ($first_space == 0) $first_space = strlen($tag);

		return sprintf($brackets, $tag, $content, substr($tag, 0, $first_space));
	}

	// This function writes tags
	function write_tag($tag, $content, $level) {
    global $func;

		$tab = $this->level2tab($level);
		$brackets = $tab."<%s>%s</%s>\r\n";

		$first_space = strpos($tag, " ");
		if ($first_space == 0) $first_space = strlen($tag);

    // Todo: AllowHTML should be removed. Then --lt-- would also not be needed anymore
    // But then the import will need to replace HTML back.
    $content = $func->AllowHTML($content);
		$content = str_replace("<", "--lt--", $content);
		$content = str_replace(">", "--gt--", $content);

		return sprintf($brackets, $tag, $content, substr($tag, 0, $first_space));
	}

	function convertinputstr($string) {
		$string	= str_replace("\"", "", $string);
		$string	= str_replace("\\", "", $string);
		$string	= str_replace("'", "", $string);

		return $string;
	}
}
?>
