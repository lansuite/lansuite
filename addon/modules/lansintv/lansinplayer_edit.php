<?

	$templ['lansinplayer']['case']['info']['page_title'] = "Lansin-TV (tm) - Edit rulez";
	eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("lansinplayer_case_boxheader")."\";");


$fileid=$_GET['fileid'];
		$templ['lansinplayer']['case']['info']['fileid'] = $fileid;

		$file_name=$db->query_first("SELECT pfad FROM {$config['tables']['lansintv']} WHERE id='$fileid'");
		$templ['lansinplayer']['case']['info']['file_name'] = "$file_name[0]";
		
		eval("\$templ['index']['info']['content'] .= \"". $func->gettemplate("lansinplayer_case_edit")."\";");

?>