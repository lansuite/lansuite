<?php
class MasterSearch2 {
	var $query;
	var $result_field = array();
	var $result_fields = 0;
	var $search_fields = array();
	var $search_dropdown = array();
	var $icon_field = array();
	var $icon_fields = 0;
  var $config = array();
  var $sql_select_field_list = array();

  // Constructor
  function MasterSearch2() {
    $this->config['EntriesPerPage'] = 20;
  }
  
  function AddTextSearchField($caption, $sql_fields) {
    $arr['caption'] = $caption;
    $arr['sql_fields'] = $sql_fields;
    array_push($this->search_fields, $arr);
  }

  function AddTextSearchDropDown($caption, $sql_field, $selections) {
    $arr['caption'] = $caption;
    $arr['sql_field'] = $sql_field;
    $arr['selections'] = $selections;
    array_push($this->search_dropdown, $arr);
  }

  function AddResultField($caption, $sql_field, $link = '', $link_id = '', $callback = '', $max_char = 0) {
    $this->result_field[$this->result_fields]['caption'] = $caption;
    $this->result_field[$this->result_fields]['sql_field'] = $sql_field;
    if ($sql_field and !in_array($sql_field, $this->sql_select_field_list)) array_push($this->sql_select_field_list, $sql_field); 
    $this->result_field[$this->result_fields]['callback'] = $callback;
    $this->result_field[$this->result_fields]['max_char'] = $max_char;
    $this->result_field[$this->result_fields]['link'] = $link;
    $this->result_field[$this->result_fields]['link_id'] = $link_id;
    if ($link_id and !in_array($link_id, $this->sql_select_field_list)) array_push($this->sql_select_field_list, $link_id); 
    $this->result_fields++;
  }

  function AddIconField($icon_name, $sql_field, $link = '') {
    $this->icon_field[$this->icon_fields]['icon_name'] = $icon_name;
    $this->icon_field[$this->icon_fields]['sql_field'] = $sql_field;
    if ($sql_field and !in_array($sql_field, $this->sql_select_field_list)) array_push($this->sql_select_field_list, $sql_field); 
    $this->icon_field[$this->icon_fields]['link'] = $link;
    $this->icon_fields++;
  }

	function PrintSearch($working_link, $target_link, $target_id) {
    global $db, $config, $dsp, $templ, $func, $auth;

    // Add $_POST[]-Fields to $working_link
    if ($_POST) foreach($_POST['search_input'] as $key => $val) $working_link .= "&search_input[$key]=$val";
    elseif ($_GET['search_input']) foreach($_GET['search_input'] as $key => $val) $working_link .= "&search_input[$key]=$val";     
    if ($_POST) foreach($_POST['search_dd_input'] as $key => $val) $working_link .= "&search_dd_input[$key]=$val";
    elseif ($_GET['search_dd_input']) foreach($_GET['search_dd_input'] as $key => $val) $working_link .= "&search_dd_input[$key]=$val";

    // Write back from $_GET[] to $_POST[]
    if ($_POST['sending_x'] == '' and $_GET['search_input']) foreach($_GET['search_input'] as $key => $val) $_POST['search_input'][$key] = $val;
    if ($_POST['sending_x'] == '' and $_GET['search_dd_input']) foreach($_GET['search_dd_input'] as $key => $val) $_POST['search_dd_input'][$key] = $val;

    ###### Generate Select
    if ($target_id and !in_array($target_id, $this->sql_select_field_list)) array_push($this->sql_select_field_list, $target_id);
    $this->query['select'] = implode(', ', $this->sql_select_field_list);
    
    
    ###### Generate Where
    $this->query['where'] = '1 = 1';
    $z = 0;
    if ($this->search_fields) foreach ($this->search_fields as $current_field_list) {
      if ($_POST["search_input"][$z] != '') {
        $x = 0;
        $sql_one_search_field = '';
        if ($current_field_list['sql_fields']) foreach ($current_field_list['sql_fields'] as $sql_field => $compare_mode) {
          if ($x > 0) $sql_one_search_field .= ' OR ';
          switch ($compare_mode) {
            case 'exact':
              $sql_one_search_field .= "($sql_field = '". $_POST["search_input"][$z] ."')";
            break;
            case '1337':
      				$key_1337 = $_POST["search_input"][$z];
      				$key_1337 = str_replace ("o", "(o|0)", $key_1337);
      				$key_1337 = str_replace ("O", "(O|0)", $key_1337);
      				$key_1337 = str_replace ("l", "(l|1|\\\\||!)", $key_1337);
      				$key_1337 = str_replace ("L", "(L|1|\\\\||!)", $key_1337);
      				$key_1337 = str_replace ("i", "(i|1|\\\\||!)", $key_1337);
      				$key_1337 = str_replace ("I", "(I|1|\\\\||!)", $key_1337);
      				$key_1337 = str_replace ("e", "(e|3|€)", $key_1337);
      				$key_1337 = str_replace ("E", "(E|3|€)", $key_1337);
      				$key_1337 = str_replace ("t", "(t|7)", $key_1337);
      				$key_1337 = str_replace ("T", "(T|7)", $key_1337);
      				$key_1337 = str_replace ("a", "(a|@)", $key_1337);
      				$key_1337 = str_replace ("A", "(A|@)", $key_1337);
      				$key_1337 = str_replace ("s", "(s|5|$)", $key_1337);
      				$key_1337 = str_replace ("S", "(S|5|$)", $key_1337);
      				$key_1337 = str_replace ("z", "(z|2)", $key_1337);
      				$key_1337 = str_replace ("Z", "(Z|2)", $key_1337);
              $sql_one_search_field .= "($sql_field REGEXP '$key_1337')";
            break;
            default:
              $sql_one_search_field .= "($sql_field LIKE '%". $_POST["search_input"][$z] ."%')";
            break;
          }
          $x++;
        }
        if ($sql_one_search_field != '') $this->query['where'] .= " AND ($sql_one_search_field)";
      }
      $z++;
    }

    $z = 0;
    if ($this->search_dropdown) foreach ($this->search_dropdown as $current_field_list) {
      if ($_POST["search_dd_input"][$z] != '') {
        if ($current_field_list['sql_field'] != '') { 
          $values = explode(',', $_POST["search_dd_input"][$z]);
          $x = 0;
          $sql_one_search_field = '';
          foreach ($values as $value) {
            if ($x > 0) $sql_one_search_field .= ' OR ';

            // Negation, greater than, less than
            $pre_eq = '';
            if (substr($value, 0, 1) == '!' or substr($value, 0, 1) == '<' or substr($value, 0, 1) == '>') {
              $pre_eq = substr($value, 0, 1);
              $value = substr($value, 1, strlen($value) - 1);
            }
            
            if ($value != '') $sql_one_search_field .= "({$current_field_list['sql_field']} $pre_eq= '$value')";
            $x++;
          }
          $this->query['where'] .= " AND ($sql_one_search_field)";
        }
      }
      $z++;
    }


    ###### Generate Group By
    $this->query['group_by'] = $target_id;

    
    ###### Generate Order By
    $this->query['order_by'] = '';
    if ($_GET['order_by']) {
      $this->query['order_by'] = 'ORDER BY '. $_GET['order_by'];
      if ($_GET['order_dir']) $this->query['order_by'] .= ' '. $_GET['order_dir'];
    }

    
    ###### Generate Limit
    if ($_GET['page'] == 'all') $this->query['limit'] = '';
    else {
      if ($_GET['page'] != '') $page_start = (int)$_GET['page'] * (int)$this->config['EntriesPerPage'];
      else $page_start = 0;
      $this->query['limit'] = "LIMIT $page_start, ". $this->config['EntriesPerPage'];
    }
        
    // Get number of results
    $res_count = $db->query_first("SELECT count(*) AS count
      FROM {$this->query['from']}
      WHERE {$this->query['where']}
      ");

    
    ###### Execute SQL
    $res = $db->query("SELECT SQL_CALC_FOUND_ROWS {$this->query['select']}
      FROM {$this->query['from']}
      WHERE {$this->query['where']}
      GROUP BY {$this->query['group_by']}
      {$this->query['order_by']}
      {$this->query['limit']}
      ");
/*      
    echo "SELECT SQL_CALC_FOUND_ROWS {$this->query['select']}<br>
      FROM {$this->query['from']}<br>
      WHERE {$this->query['where']}<br>
      GROUP BY {$this->query['group_by']}<br>
      {$this->query['order_by']}<br>
      {$this->query['limit']}
      ";
*/

    ###### Generate Page-Links
    $count_rows = $db->query_first('SELECT FOUND_ROWS() AS count');
    $count_pages = floor($count_rows['count'] / $this->config['EntriesPerPage']);
    
		if ($count_rows['count'] > $this->config['EntriesPerPage']) {
		  $link = "$working_link&order_by={$_GET['order_by']}&order_dir={$_GET['order_dir']}&page=";
			$templ['ms2']['pages'] = ("Seiten: ");

			// Previous page link
			if ($_GET['page'] != "all" and (int)$_GET['page'] > 0) {
				$templ['ms2']['pages'] .= (' <a class="menue" href="'. $link . ($_GET['page'] - 1) .'"><b>&lt;</b></a>');
			}

			// Direct page link
			$i = 0;					
			while($i < $count_pages) {
				if ($_GET['page'] != "all" and $_GET['page'] == $i) $templ['ms2']['pages'] .= (" " . ($i + 1));
				else $templ['ms2']['pages'] .= (' <a class="menue" href="' . $link . $i . '"><b>'. ($i + 1) .'</b></a>');
				$i++;
			}

			// Next page link
			if ($_GET['page'] != "all" and ($_GET['page'] + 1) < $count_pages) {
				$templ['ms2']['pages'] .= (' <a class="menue" href="'. $link . ($current_page + 1) .'"><b>&gt;</b></a>');
			}

			// All link
			if ($_GET['page'] == "all") $templ['ms2']['pages'] .= " Alle";
			else $templ['ms2']['pages'] .= (' <a class="menue" href="'. $link . '"><b>Alle</b></a>');									
    }


    ###### Output Search
    $templ['ms2']['action'] = "$working_link&order_by={$_GET['order_by']}&order_dir={$_GET['order_dir']}";
    $templ['ms2']['inputs'] = '';
    // Text Inputs
    $z = 0;
    foreach ($this->search_fields as $current_field) {
      $templ['ms2']['input_field_caption'] = $current_field['caption'];
      $templ['ms2']['input_field_name'] = "search_input[$z]";
      $templ['ms2']['input_field_value'] = $_POST['search_input'][$z];
      $templ['ms2']['inputs'] .= $dsp->FetchModTpl('mastersearch2', 'search_input_field');
      $z++;
    }
    // Dropdown Inputs
    $z = 0;
    foreach ($this->search_dropdown as $current_field) {
      $templ['ms2']['input_field_caption'] = $current_field['caption'];
      $templ['ms2']['input_field_name'] = "search_dd_input[$z]";
      $templ['ms2']['input_field_options'] = '';
      foreach ($current_field['selections'] as $key => $value) {
        ((string)$_POST['search_dd_input'][$z] == (string)$key)? $selected = ' selected' : $selected = '';
        $templ['ms2']['input_field_options'] .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
      }
      $templ['ms2']['input_field_value'] = 
      $templ['ms2']['inputs'] .= $dsp->FetchModTpl('mastersearch2', 'search_input_dropdown');
      $z++;
    }
    $dsp->AddModTpl('mastersearch2', 'search_case');

    ###### Output Result
    // Generate Result Head
    $templ['ms2']['table_head'] = '';
    foreach ($this->result_field as $current_field) {    
      $templ['ms2']['table_head_width'] = '*';    
      $templ['ms2']['table_head_entry'] = $current_field['caption'];
      
      // Order Link and Image
      if ($_GET['order_by'] == $current_field['sql_field'] and $_GET['order_dir'] != 'DESC') $order_dir = 'DESC';
      else $order_dir = 'ASC';
      $templ['ms2']['order_link'] = "$working_link&order_by={$current_field['sql_field']}&order_dir=$order_dir";
            
      if ($_GET['order_by'] == $current_field['sql_field']) {
        if ($_GET['order_dir'] == 'DESC') $templ['ms2']['order_image'] = " <img src=\"design/{$auth['design']}/images/arrows_orderby_desc_active.gif\" border=\"0\" />";
        else $templ['ms2']['order_image'] = " <img src=\"design/{$auth['design']}/images/arrows_orderby_asc_active.gif\" border=\"0\" />";
      } else $templ['ms2']['order_image'] = '';

      $templ['ms2']['table_head'] .= $dsp->FetchModTpl('mastersearch2', 'result_head');        
    }

    // Icon Headline
    foreach ($this->icon_field as $current_field) {
      $templ['ms2']['table_head_width'] = '1%';    
      $templ['ms2']['table_head_entry'] = '&nbsp;';
      $templ['ms2']['arrow_order_asc'] = '';
      $templ['ms2']['arrow_order_desc'] = '';
      $templ['ms2']['table_head'] .= $dsp->FetchModTpl('mastersearch2', 'result_head');        
    }

    // Generate Result Body    
    $templ['ms2']['table_entrys'] = '';
    while($line = $db->fetch_array($res)) {
      $templ['ms2']['table_entrys_row_field'] = '';
      
      // Normal rows
      foreach ($this->result_field as $current_field) {
        if (strpos($current_field['sql_field'], '.') > 0) $current_field['sql_field'] = substr($current_field['sql_field'], strpos($current_field['sql_field'], '.') + 1, strlen($current_field['sql_field']));
        if (strpos($current_field['link_id'], '.') > 0) $current_field['link_id'] = substr($current_field['link_id'], strpos($current_field['link_id'], '.') + 1, strlen($current_field['link_id']));

        // Exec Callback
        if ($current_field['callback']) $line[$current_field['sql_field']] = $this->$current_field['callback']($line[$current_field['sql_field']]);
        $templ['ms2']['table_entrys_row_field_entry'] = $line[$current_field['sql_field']];
        if ($templ['ms2']['table_entrys_row_field_entry'] == '') $templ['ms2']['table_entrys_row_field_entry'] = '&nbsp;';
        
        // Link it?
        ($current_field['link_id'] != '')? $link_id = $line[$current_field['link_id']] : $link_id = ''; 
        if ($current_field['link'] != '' or $link_id != '') {
          $templ['ms2']['table_entrys_row_field_entry'] = '<a href="'. $current_field['link'] . $link_id .'">'. $templ['ms2']['table_entrys_row_field_entry'] .'</a>'; 
        }
        
        $templ['ms2']['table_entrys_row_field'] .= $dsp->FetchModTpl('mastersearch2', 'result_field');        
      }
      
      // Icon rows
      foreach ($this->icon_field as $current_field) {
        if (strpos($current_field['sql_field'], '.') > 0) $current_field['sql_field'] = substr($current_field['sql_field'], strpos($current_field['sql_field'], '.') + 1, strlen($current_field['sql_field']));

        $templ['ms2']['link'] = $current_field['link'] . $line[$current_field['sql_field']];
        $templ['ms2']['icon_name'] = $current_field['icon_name'];
        $templ['ms2']['icon_title'] = $current_field['icon_name'];
        $templ['ms2']['table_entrys_row_field_entry'] = $dsp->FetchModTpl('mastersearch2', 'result_icon_link');
        $templ['ms2']['table_entrys_row_field'] .= $dsp->FetchModTpl('mastersearch2', 'result_field');        
      }      
      $templ['ms2']['table_entrys'] .= $dsp->FetchModTpl('mastersearch2', 'result_row');
    }
        
    $db->free_result($res);
    $dsp->AddModTpl('mastersearch2', 'result_case');
  }
  

  ###### Callbacks
  function GetDate($time){
    if ($time > 0) return date('d.m.y H:i', $time);
    else return '0'; 
  }      
  
  function GetTime($time){
    if ($time > 0) return date('H:i', $time);
    else return '0'; 
  }      
}
?>