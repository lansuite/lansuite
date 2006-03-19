<?php
class MasterSearch2 {
	var $query;
	var $result_field = array();
	var $search_fields = array();
	var $search_dropdown = array();
	var $icon_field = array();
	var $multi_select_action = array();
  var $config = array();
  var $sql_select_field_list = array();
  var $post_in_get = '';

  // Constructor
  function MasterSearch2($module = '') {
    global $language, $lang;
    
    $this->config['EntriesPerPage'] = 20;

    // Load language files, if included from within another module
    if ($module and $module != $_GET['mod']) {
      if (file_exists("modules/{$module}/language/{$module}_lang_de.php")) include_once("modules/{$module}/language/{$module}_lang_de.php");
      if ($language != "de" and file_exists("modules/{$module}/language/{$module}_lang_{$language}.php")) include_once("modules/{$module}/language/{$module}_lang_{$language}.php");
    }

    // Add $_POST[]-Fields to $working_link
    if ($_POST['search_input']) foreach($_POST['search_input'] as $key => $val) $this->post_in_get .= "&search_input[$key]=$val";
    elseif ($_GET['search_input']) foreach($_GET['search_input'] as $key => $val) $this->post_in_get .= "&search_input[$key]=$val";     
    if ($_POST['search_dd_input']) foreach($_POST['search_dd_input'] as $key => $val) $this->post_in_get .= "&search_dd_input[$key]=$val";
    elseif ($_GET['search_dd_input']) foreach($_GET['search_dd_input'] as $key => $val) $this->post_in_get .= "&search_dd_input[$key]=$val";

    // Write back from $_GET[] to $_POST[]
    if (!isset($_POST['search_input']) and $_GET['search_input']) foreach($_GET['search_input'] as $key => $val) $_POST['search_input'][$key] = $val;
    if (!isset($_POST['search_dd_input']) and $_GET['search_dd_input']) foreach($_GET['search_dd_input'] as $key => $val) $_POST['search_dd_input'][$key] = $val;
  }

  function AddSelect($sql_field){
    if ($sql_field and !in_array($sql_field, $this->sql_select_field_list)) array_push($this->sql_select_field_list, $sql_field);
  }
 
  function AddTextSearchField($caption, $sql_fields) {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['sql_fields'] = $sql_fields;
    array_push($this->search_fields, $arr);
  }

  function AddTextSearchDropDown($caption, $sql_field, $selections, $default = '') {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['sql_field'] = $sql_field;
    $arr['selections'] = $selections;

    $curr_pos = count($this->search_dropdown);
    if ($default != '' and !isset($_POST["search_dd_input"][$curr_pos])) $_POST["search_dd_input"][$curr_pos] = $default;

    array_push($this->search_dropdown, $arr);
  }

  function AddResultField($caption, $sql_field, $callback = '', $max_char = 0) {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['sql_field'] = $sql_field;
    $arr['callback'] = $callback;
    $arr['max_char'] = $max_char;
    array_push($this->result_field, $arr);

    $this->AddSelect($sql_field); 
    $this->AddSelect($link_id); 
  }

  function AddIconField($icon_name, $link = '', $tooltipp = '') {
    $arr = array();
    $arr['icon_name'] = $icon_name;
    $arr['link'] = $link;
    $arr['tooltipp'] = $tooltipp;
    array_push($this->icon_field, $arr);
  }

  function AddMultiSelectAction($caption, $action, $security_question = 0) {
    $arr = array();
    $arr['caption'] = $caption;
    $arr['action'] = $action;
    $arr['security_question'] = $security_question;
    array_push($this->multi_select_action, $arr);
  }

	function PrintSearch($working_link, $select_id_field, $multiaction = '') {
    global $db, $config, $dsp, $templ, $func, $auth, $line, $gd;

    $working_link .= $this->post_in_get;
    $this->AddSelect($select_id_field); 

    ###### Generate Select
    $this->query['select'] = implode(', ', $this->sql_select_field_list);
    
    
    ###### Generate Where
    if ($this->query['where'] == '') $this->query['where'] = '1 = 1';
    
    // Generate where from input fields
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
      				$key_1337 = str_replace ("e", "(e|3|�)", $key_1337);
      				$key_1337 = str_replace ("E", "(E|3|�)", $key_1337);
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

    // Generate additional where from dropdown fields 
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
    $this->query['group_by'] = $select_id_field;

    
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
				$templ['ms2']['pages'] .= (' <a class="menu" href="'. $link . ($_GET['page'] - 1) .'"><b>&lt;</b></a>');
			}
			// Direct page link
			$i = 0;					
			while($i < $count_pages) {
				if ($_GET['page'] != "all" and $_GET['page'] == $i) $templ['ms2']['pages'] .= (" " . ($i + 1));
				else $templ['ms2']['pages'] .= (' <a class="menu" href="' . $link . $i . '"><b>'. ($i + 1) .'</b></a>');
				$i++;
			}
			// Next page link
			if ($_GET['page'] != "all" and ($_GET['page'] + 1) < $count_pages) {
				$templ['ms2']['pages'] .= (' <a class="menu" href="'. $link . ($_GET['page'] + 1) .'"><b>&gt;</b></a>');
			}
			// All link
			if ($_GET['page'] == "all") $templ['ms2']['pages'] .= " Alle";
			else $templ['ms2']['pages'] .= (' <a class="menu" href="'. $link . 'all"><b>Alle</b></a>');									
    }


    ###### Output Search
    $templ['ms2']['action'] = "$working_link&order_by={$_GET['order_by']}&order_dir={$_GET['order_dir']}";
    $templ['ms2']['inputs'] = '';
    // Text Inputs
    $z = 0; $x = 0;
    if ($this->search_fields) foreach ($this->search_fields as $current_field) {
      $current = $x % 2;
      $templ['ms2']['input_field_name'] = "search_input[$z]";
      $templ['ms2']['input_field_value'] = $_POST['search_input'][$z];
      $templ['ms2']['input_field_caption'][$current] = $current_field['caption'];
      $templ['ms2']['search'][$current] = $dsp->FetchModTpl('mastersearch2', 'search_input_field');
      if ($current == 1) $templ['ms2']['inputs'] .= $dsp->FetchModTpl('mastersearch2', 'search_row');
      $z++; $x++;
    }

    // Dropdown Inputs
    $z = 0;
    if ($this->search_dropdown) foreach ($this->search_dropdown as $current_field) {
      $current = $x % 2;
      $templ['ms2']['input_field_name'] = "search_dd_input[$z]";
      $templ['ms2']['input_field_options'] = '';
      foreach ($current_field['selections'] as $key => $value) {
        ((string)$_POST['search_dd_input'][$z] == (string)$key)? $selected = ' selected' : $selected = '';
        $templ['ms2']['input_field_options'] .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
      }
      $templ['ms2']['input_field_caption'][$current] = $current_field['caption'];
      $templ['ms2']['search'][$current] = $dsp->FetchModTpl('mastersearch2', 'search_input_dropdown');
      if ($current == 1) $templ['ms2']['inputs'] .= $dsp->FetchModTpl('mastersearch2', 'search_row');
      $z++; $x++;
    }
    // If odd number of input fields, add the last one in a single row
    if ($current == 0) {
      $templ['ms2']['input_field_caption'][1] = '&nbsp;';
      $templ['ms2']['search'][1] = '&nbsp;';
      $templ['ms2']['inputs'] .= $dsp->FetchModTpl('mastersearch2', 'search_row');
    }
    if ($this->search_fields or $this->search_dropdown) {
      $gd->CreateButton('search');
      $dsp->AddModTpl('mastersearch2', 'search_case');
    }


    ###### Output Result
    #### Generate Result Head
    $templ['ms2']['table_head'] = '';

    // Checkbox Headline (Empty field)
    if (count($this->multi_select_action) > 0) {
      $templ['ms2']['table_head_width'] = '16';    
      $templ['ms2']['table_head_entry'] = '&nbsp;';      
      $templ['ms2']['table_head'] .= $dsp->FetchModTpl('mastersearch2', 'result_head');        
    }

    // Normal headline
    foreach ($this->result_field as $current_field) {    
      $templ['ms2']['table_head_width'] = '*';    
      $templ['ms2']['link_item'] = $current_field['caption'];
      
      // Order Link and Image
      if ($_GET['order_by'] == $current_field['sql_field'] and $_GET['order_dir'] != 'DESC') $order_dir = 'DESC';
      else $order_dir = 'ASC';
      $templ['ms2']['link'] = "$working_link&order_by={$current_field['sql_field']}&order_dir=$order_dir";
            
      if ($_GET['order_by'] == $current_field['sql_field']) {
        if ($_GET['order_dir'] == 'DESC') $templ['ms2']['link_item'] = " <img src=\"design/{$auth['design']}/images/arrows_orderby_desc_active.gif\" border=\"0\" />";
        else $templ['ms2']['link_item'] .= " <img src=\"design/{$auth['design']}/images/arrows_orderby_asc_active.gif\" border=\"0\" />";
      } else $templ['ms2']['link_item'] .= '';

      $templ['ms2']['table_head_entry'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
      $templ['ms2']['table_head'] .= $dsp->FetchModTpl('mastersearch2', 'result_head');        
    }

    // Icon Headline (Empty field)
    foreach ($this->icon_field as $current_field) {
      $templ['ms2']['table_head_width'] = '22';    
      $templ['ms2']['table_head_entry'] = '&nbsp;';      
      $templ['ms2']['table_head'] .= $dsp->FetchModTpl('mastersearch2', 'result_head');        
    }

    #### Generate Result Body    
    $templ['ms2']['table_entrys'] = '';
    while($line = $db->fetch_array($res)) { // Start: Row
      $templ['ms2']['table_entrys_row_field'] = '';

      // cut of 'table.', befor field name
      if (strpos($select_id_field, '.') > 0) $select_id_field = substr($select_id_field, strpos($select_id_field, '.') + 1, strlen($select_id_field));
      
      // Checkbox      
      if (count($this->multi_select_action) > 0) {
        $templ['ms2']['table_entrys_row_field_entry'] = '<input type="checkbox" name="action['. $line[$select_id_field] .']">';
        $templ['ms2']['table_entrys_row_field'] .= $dsp->FetchModTpl('mastersearch2', 'result_field');
      }
      
      // Normal fields
      foreach ($this->result_field as $current_field) {

        // cut of 'table.', in front of field name
        if (strpos($current_field['sql_field'], '.') > 0) $current_field['sql_field'] = substr($current_field['sql_field'], strpos($current_field['sql_field'], '.') + 1, strlen($current_field['sql_field']));
        if (strpos($current_field['link_id'], '.') > 0) $current_field['link_id'] = substr($current_field['link_id'], strpos($current_field['link_id'], '.') + 1, strlen($current_field['link_id']));

        // Exec Callback
        if ($current_field['callback'])
          $line[$current_field['sql_field']] = call_user_func($current_field['callback'], $line[$current_field['sql_field']]);

        $templ['ms2']['table_entrys_row_field_entry'] = $line[$current_field['sql_field']];
        
        // Cut of oversize chars
        if ($current_field['max_char'] and strlen($templ['ms2']['table_entrys_row_field_entry'] > $current_field['max_char']))
          $templ['ms2']['table_entrys_row_field_entry'] = substr($templ['ms2']['table_entrys_row_field_entry'], 0, $current_field['max_char'] - 2) .'..';

        if ($templ['ms2']['table_entrys_row_field_entry'] == '') $templ['ms2']['table_entrys_row_field_entry'] = '&nbsp;';
        $templ['ms2']['table_entrys_row_field'] .= $dsp->FetchModTpl('mastersearch2', 'result_field');        
      }
      
      // Icon fields
      foreach ($this->icon_field as $current_field) {
        $templ['ms2']['link'] = $current_field['link'] . $line[$select_id_field];
        $templ['ms2']['icon_name'] = $current_field['icon_name'];
        $templ['ms2']['icon_title'] = $current_field['tooltipp'];
        $templ['ms2']['link_item'] = $dsp->FetchModTpl('mastersearch2', 'result_icon');
        if ($templ['ms2']['link']) $templ['ms2']['table_entrys_row_field_entry'] = $dsp->FetchModTpl('mastersearch2', 'result_link');
        else $templ['ms2']['table_entrys_row_field_entry'] = $templ['ms2']['link_item'];
        $templ['ms2']['table_entrys_row_field'] .= $dsp->FetchModTpl('mastersearch2', 'result_field');        
      }      
      $templ['ms2']['table_entrys'] .= $dsp->FetchModTpl('mastersearch2', 'result_row');
    } // End: Row

    // Multi-Select Dropdown
    $templ['ms2']['multi_select_dropdown'] = '';
    if (count($this->multi_select_action) > 0) {
      $templ['ms2']['select_caption'] = 'Bitte ausw�hlen';
      $templ['ms2']['select_options'] = '';
      $z = 0;
      foreach ($this->multi_select_action as $current_action) {
        if ($z == 0) $templ['ms2']['multi_select_actions'] = '"'. $current_action['action'] .'"';
        else $templ['ms2']['multi_select_actions'] .= ', "'. $current_action['action'] .'"';
        if ($z == 0) $templ['ms2']['security_questions'] = '"'. $current_action['security_question'] .'"';
        else $templ['ms2']['security_questions'] .= ', "'. $current_action['security_question'] .'"';
        $templ['ms2']['select_options'] .= "<option value=\"$z\">{$current_action['caption']}</option>";
        $z++;
      }
      $templ['ms2']['multi_select_dropdown'] = $dsp->FetchModTpl('mastersearch2', 'result_multi_dropdown');
    }
        
    $db->free_result($res);
    $templ['ms2']['result_action'] = $multiaction;
    $dsp->AddModTpl('mastersearch2', 'result_case');
    $dsp->AddContent();
  }
} // End: Class


###### Some global Callbacks
// Callbacks which are only for local interest, should be defined in the modules search-file

function MS2GetDate($time){
  if ($time > 0) return '<span class="small">'. date('d.m.y', $time) .'<br />'. date('H:i', $time) .'</span>';
  else return '0'; 
}

function MS2GetTime($time){
  if ($time > 0) return date('H:i', $time);
  else return '0'; 
}

function UserNameAndIcon($username){
  global $line, $dsp;
  
  return $username .' '. $dsp->FetchUserIcon($line['userid']);
}
?>