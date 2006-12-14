<?php

class xml {
	// This function gets master-tags in XML-files. It also combines closed and later re-opened tags
	function get_tag_content_combine($tag_to_grab, $input) {
		$tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0, PREG_SPLIT_NO_EMPTY);
		$i = "1";
		while ($i < sizeof($tag_content)) {
			if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
			$block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0, PREG_SPLIT_NO_EMPTY);
			$block[0] = str_replace("--lt--", "<", $block[0]);
			$block[0] = str_replace("--gt--", ">", $block[0]);
			$output .= $block[0];
			$i++;					
		}

		return $output;
	}

	// This function gets master-tags in XML-files. It also combines closed and later re-opened tags, and writes them into arrays
	function get_tag_content_array($tag_to_grab, $input) {
		$tag_content = preg_split("/\<$tag_to_grab\>/i",$input, 0, PREG_SPLIT_NO_EMPTY);
		$i = "1";
		while ($i < sizeof($tag_content)) {
			if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
			$block = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[$i], 0, PREG_SPLIT_NO_EMPTY);
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
		$tag_content = preg_split("/\<$tag_to_grab\>/i", $input, 0, PREG_SPLIT_NO_EMPTY);
		if (strpos($tag_to_grab, " ") > 0) $tag_to_grab = substr($tag_to_grab, 0, strpos($tag_to_grab, " "));
		$tag_content = preg_split("/\<\/$tag_to_grab\>/i", $tag_content[1], 0, PREG_SPLIT_NO_EMPTY);

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
		$tab = $this->level2tab($level);
		$brackets = $tab."<%s>%s</%s>\r\n";

		$first_space = strpos($tag, " ");
		if ($first_space == 0) $first_space = strlen($tag);
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
