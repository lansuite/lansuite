<?php

$ms_number = 0;
class MasterSearch2
{
    public $query = array('select' => '', 'from' => '', 'where' => '', 'group_by' => '', 'order_by' => '', 'limit' => '',
    'having' => '', 'default_order_by' => '', 'default_order_dir' => '', 'order_by_end' => '');
    public $result_field = array();
    public $search_fields = array();
    public $search_dropdown = array();
    public $icon_field = array();
    public $multi_select_action = array();
    public $config = array();
    public $bgcolors = array();
    public $bgcolor_attr = '';
    public $orderByFieldFound = false;
    public $post_in_get = '';
    public $NoItemsText = '';
    public $SQLFieldTypes = array();
    public $HiddenGetFields = array();
    public $ms_number = 0;
    public $TargetPageField = '';
    public $TargetPageCount = 0;
    public $quicklinks = array();
    public $isExport = 0;

  // Constructor
    public function MasterSearch2($module = '')
    {
        global $ms_number;

        $ms_number++;
        $this->ms_number = $ms_number;

        $this->query['select'] = '';
        $this->query['from'] = '';
        $this->query['where'] = '';
        $this->query['group_by'] = '';
        $this->query['order_by'] = '';
        $this->query['limit'] = '';
        $this->query['having'] = '';
        $this->query['default_order_by'] = '';
        $this->query['default_order_dir'] = '';
        $this->query['order_by_end'] = '';

        if ($_GET['design'] != 'plain' and $_GET['msExport'] != '') {
            $this->isExport = $_GET['msExport'];
        }

        // Write Get to Post, for MF expects this for default values
        if ($_GET['search_input']) {
            foreach ($_GET['search_input'] as $key => $val) {
                $_POST['search_input'][$key] = $val;
            }
        }
        if ($_GET['search_dd_input']) {
            foreach ($_GET['search_dd_input'] as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $key2 => $val2) {
                        $_POST['search_dd_input'][$key][$key2] = $val2;
                    }
                } else {
                    $_POST['search_dd_input'][$key] = $val;
                }
            }
        }

        $this->config['EntriesPerPage'] = 20;
        $this->config['dont_link_first_line'] = false;
        $this->NoItemsText = t('Es wurden keine Einträge gefunden');
    }

    public function AddSelect($sql_field)
    {
        $this->query['select'] .= $sql_field .', ';

    // cut of 'xxx as ', in front of alias name
        $first_as = strpos(strtolower($sql_field), ' as ');
        if ($first_as > 0) {
            $sql_field = substr($sql_field, $first_as + 4, strlen($sql_field));
        }
        if ($sql_field == $_GET['order_by']) {
            $this->orderByFieldFound = true;
        }
    }

    public function AddBGColor($sql_field, $color_list)
    {
        $this->AddSelect($sql_field);
        $this->bgcolors = $color_list;
        $this->bgcolor_attr = $sql_field;
    }

    public function AddTextSearchField($caption, $sql_fields)
    {
        $arr = array();
        $arr['caption'] = $caption;
        $arr['sql_fields'] = $sql_fields;
        array_push($this->search_fields, $arr);
    }

    public function AddTextSearchDropDown($caption, $sql_field, $selections, $default = '', $multiple = 0)
    {
        $arr = array();
        $arr['caption'] = $caption;
        $arr['sql_field'] = $sql_field;
        $arr['selections'] = $selections;
        $arr['multiple'] = $multiple;

        $curr_pos = count($this->search_dropdown);
        if ($default != '' and !isset($_GET["search_dd_input"][$curr_pos])) {
            $_GET["search_dd_input"][$curr_pos] = $default;
        }

        array_push($this->search_dropdown, $arr);
    }

    public function AddResultField($caption, $sql_field, $callback = '', $max_char = 0, $width = 0)
    {
        $arr = array();
        $arr['caption'] = $caption;
        $arr['sql_field'] = $sql_field;
        $arr['callback'] = $callback;
        $arr['max_char'] = $max_char;
        $arr['width'] = $width;
        array_push($this->result_field, $arr);

        $this->AddSelect($sql_field);
    }

    public function AddIconField($icon_name, $link = '', $tooltipp = '', $callback = '')
    {
        $arr = array();
        $arr['icon_name'] = $icon_name;
        $arr['link'] = $link;
        $arr['tooltipp'] = $tooltipp;
        $arr['callback'] = $callback;
        array_push($this->icon_field, $arr);
    }

    public function AddMultiSelectAction($caption, $action, $security_question = 0, $icon = '')
    {
        $arr = array();
        $arr['caption'] = $caption;
        $arr['action'] = $action;
        $arr['security_question'] = $security_question;
        $arr['icon'] = $icon;
        array_push($this->multi_select_action, $arr);
    }

    public function SetTargetPage($name, $pages)
    {
        $this->TargetPageField = $name;
        $this->TargetPageCount = $pages;
    }

    public function PrintSearch($working_link, $select_id_field, $multiaction = '')
    {
        global $smarty, $db, $config, $dsp, $templ, $func, $auth, $line, $framework;

        $UrlParas = explode('&', substr($working_link, strpos($working_link, '?') + 1, strlen($working_link)));
        foreach ($UrlParas as $UrlPara) {
            list($key, $val) = explode('=', $UrlPara);
            $this->HiddenGetFields[$key] .= $val;
        }

#    $working_link .= $this->post_in_get;
        $working_link .= '&ms_number='. $this->ms_number;
        $this->AddSelect($select_id_field);
        $min_skipped_items = 99;
   
        $this->query['from'] = str_replace('%prefix%', $config['database']['prefix'], $this->query['from']);

    ###### Generate Where
        if ($this->query['where'] == '') {
            $this->query['where'] = '1 = 1';
        }
    
    // Generate where from input fields
        $z = 0;
        if ($this->search_fields) {
            foreach ($this->search_fields as $current_field_list) {
                if ($_GET["search_input"][$z] != '') {
                    $x = 0;
                    $sql_one_search_field = '';
                    if ($current_field_list['sql_fields']) {
                        foreach ($current_field_list['sql_fields'] as $sql_field => $compare_mode) {
                            if ($x > 0) {
                                $sql_one_search_field .= ' OR ';
                            }
                            switch ($compare_mode) {
                                case 'aton':
                                                      $sql_one_search_field .= "($sql_field = INET6_ATON('". $_GET["search_input"][$z] ."'))";
                                    break;
                                case 'exact':
                                                      $sql_one_search_field .= "($sql_field = '". $_GET["search_input"][$z] ."')";
                                    break;
                                case 'fulltext':
                                                      $sql_one_search_field .= "(MATCH ($sql_field) AGAINST ('{$_GET["search_input"][$z]}' IN BOOLEAN MODE))";
                                                      $this->AddResultField(t('Score'), "ROUND(MATCH ($sql_field) AGAINST ('{$_GET["search_input"][$z]}' IN BOOLEAN MODE), 3) AS score");
                                    break;
                                case '1337':
                                                      $key_1337 = $_GET["search_input"][$z];
                                                      $key_1337 = str_replace('?', '[?]', $key_1337);
                                                      $key_1337 = str_replace('+', '[+]', $key_1337);
                                                      $key_1337 = str_replace('*', '[*]', $key_1337);
                                                      $key_1337 = str_replace('.', '[.]', $key_1337);
                                                      $key_1337 = str_replace('|', '[|]', $key_1337);
                                                      $key_1337 = str_replace('[', '[[]', $key_1337);

                                                      $key_1337 = str_replace("o", "(o|0)", $key_1337);
                                                      $key_1337 = str_replace("O", "(O|0)", $key_1337);
                                                      $key_1337 = str_replace("l", "(l|1|\\\\||!)", $key_1337);
                                                      $key_1337 = str_replace("L", "(L|1|\\\\||!)", $key_1337);
                                                      $key_1337 = str_replace("i", "(i|1|\\\\||!)", $key_1337);
                                                      $key_1337 = str_replace("I", "(I|1|\\\\||!)", $key_1337);
                                                      $key_1337 = str_replace("e", "(e|3|€)", $key_1337);
                                                      $key_1337 = str_replace("E", "(E|3|€)", $key_1337);
                                                      $key_1337 = str_replace("t", "(t|7)", $key_1337);
                                                      $key_1337 = str_replace("T", "(T|7)", $key_1337);
                                                      $key_1337 = str_replace("a", "(a|@)", $key_1337);
                                                      $key_1337 = str_replace("A", "(A|@)", $key_1337);
                                                      $key_1337 = str_replace("s", "(s|5|$)", $key_1337);
                                                      $key_1337 = str_replace("S", "(S|5|$)", $key_1337);
                                                      $key_1337 = str_replace("z", "(z|2)", $key_1337);
                                                      $key_1337 = str_replace("Z", "(Z|2)", $key_1337);

                                                      $key_1337 = str_replace(']', '[[.right-square-bracket.]]', $key_1337);
                                                      $sql_one_search_field .= "($sql_field REGEXP '$key_1337')";
                                    break;
                                case 'multiword':
                                                      // Split at ' ' and use each term as correkt one
                                                      $words = explode(' ', $_GET['search_input'][$z]);
                                                      $sql_one_search_field .= "($sql_field LIKE '%";
                                                      $sql_one_search_field .= implode("%') OR ($sql_field LIKE '%", $words);
                                                      $sql_one_search_field .= "%')";
                                    break;
                                default:
                                                      $sql_one_search_field .= "($sql_field LIKE '%". $_GET["search_input"][$z] ."%')";
                                    break;
                            }
                            $x++;
                        }
                    }
                    if ($sql_one_search_field != '') {
                        $this->query['where'] .= " AND ($sql_one_search_field)";
                    }
                }
                $z++;
            }
        }

    // Generate additional where from dropdown fields
        $z = 0;
        if ($this->search_dropdown) {
            foreach ($this->search_dropdown as $current_field_list) {
                if ($_GET["search_dd_input"][$z] != '') {
                    if ($current_field_list['sql_field'] != '') {
                        if (is_array($_GET["search_dd_input"][$z])) {
                            $values = $_GET["search_dd_input"][$z];
                        } else {
                            $values = explode(',', $_GET["search_dd_input"][$z]);
                        }

                        $x = 0;
                        $sql_one_search_field = '';
                        foreach ($values as $value) {
                            if ($x > 0) {
                                $sql_one_search_field .= ' OR ';
                            }

            // Negation, greater than, less than
                            $pre_eq = '';
                            $value = $func->AllowHTML($value); # Converts &lt; back to <
                            if (substr($value, 0, 1) == '!' or substr($value, 0, 1) == '<' or substr($value, 0, 1) == '>') {
                                $pre_eq = substr($value, 0, 1);
                                $value = substr($value, 1, strlen($value) - 1);
                            }
            
                            if ($value != '') {
                                if ($value == 'NULL') {
                                    $sql_one_search_field .= "({$current_field_list['sql_field']} IS NULL)";
                                } else {
                                    $sql_one_search_field .= "({$current_field_list['sql_field']} $pre_eq= '$value')";
                                }
                            }
                            $x++;
                        }
          // If COUNT function is used in select, write this variable in the having statement, otherwise in the where statement
                        if (strpos($current_field_list['sql_field'], 'OUNT(') == 0) {
                            $this->query['where'] .= " AND ($sql_one_search_field)";
                        } else {
                            $this->query['having'] .= "($sql_one_search_field) AND ";
                        }
                    }
                }
                $z++;
            }
        }

    ###### Modificate Having
        if ($this->query['having'] != '') {
            // Cut off trailing AND, if exists
            if (substr($this->query['having'], strlen($this->query['having']) - 5, 5) == ' AND ') {
                $this->query['having'] = substr($this->query['having'], 0, strlen($this->query['having']) - 5);
            }
              // Write HAVING in front of statement
              $this->query['having'] = 'HAVING '.$this->query['having'];
        }

    ###### Generate Select
        $this->query['select'] = substr($this->query['select'], 0, strlen($this->query['select']) - 2);

    ###### Generate Group By
        $this->query['group_by'] .= $select_id_field;

    ###### Generate Order By
        if (strpos($_GET['order_by'], "\'") > 0) {
            $_GET['order_by'] = '';
        } # Important for FIND_IN_SET ranking

    // Is $_GET['order_by'] defined in select statement? if not set to default order by value
        if ($_GET['order_by'] and !$this->orderByFieldFound) {
            $func->information(t('Sortieren nach "%1" nicht möglich. Es wird statt dessen nach "%2" sortiert', $_GET['order_by'], $this->query['default_order_by']), NO_LINK);
            $_GET['order_by'] = '';
        }

    // Order by user selection
        if ($_GET['order_by']) {
            $this->query['order_by'] = $_GET['order_by'];

              // Order direction given by user?
            if ($_GET['order_dir']) {
                if (strtolower($_GET['order_dir']) != 'desc') {
                    $_GET['order_dir'] = 'asc';
                } else {
                    $this->query['order_by'] .= ' '. $_GET['order_dir'];
                }

                // Get default order direction by sql-field type
            } else {
                if (strpos($this->query['from'], ' ')) {
                    $FirstTable = substr($this->query['from'], 0, strpos($this->query['from'], ' '));
                } else {
                    $FirstTable = $this->query['from'];
                }
        
                    $res = $db->qry("DESCRIBE %plain%", $FirstTable);
                while ($row = $db->fetch_array($res)) {
                    $this->SQLFieldTypes[$row['Field']] = $row['Type'];
                }
                    $db->free_result($res);
        
                if ($this->SQLFieldTypes[$this->query['order_by']] == 'datetime'
                    or $this->SQLFieldTypes[$this->query['order_by']] == 'date'
                    or $this->SQLFieldTypes[$this->query['order_by']] == 'time'
                    or $this->SQLFieldTypes[$this->query['order_by']] == 'timestamp') {
                    $this->query['order_by'] .= ' DESC';
                }
            }
      
            // Default order by (if non given per URL)
        } elseif ($this->query['default_order_by']) {
            $this->query['order_by'] = $this->query['default_order_by'];
            if ($this->query['default_order_dir']) {
                $this->query['order_by'] .= ' '. $this->query['default_order_dir'];
            }
        }

        if ($this->query['order_by'] == '') {
            $this->query['order_by'] = $select_id_field .' ASC';
        }
        if ($this->query['order_by_end']) {
            $this->query['order_by'] .= ', '. $this->query['order_by_end'];
        }

        if ($_GET['EntsPerPage'] != '') {
            $this->config['EntriesPerPage'] = $_GET['EntsPerPage'];
        }

    ###### Generate Limit
        if (!$this->config['EntriesPerPage'] or $this->isExport) {
            $this->query['limit'] = '';
        } else {
            if ($_GET['ms_page'] != '' and (!$_GET['ms_number'] or $_GET['ms_number'] == $this->ms_number)) {
                $page_start = (int)$_GET['ms_page'] * (int)$this->config['EntriesPerPage'];
            } else {
                $page_start = 0;
            }
            if ($page_start < 0) {
                $page_start = 0;
            }
                $this->query['limit'] = 'LIMIT '. (int)$page_start .', '. (int)$this->config['EntriesPerPage'];
        }
        
    
    ###### Execute SQL
        $res = $db->qry(
            '%plain%',
            "SELECT SQL_CALC_FOUND_ROWS {$this->query['select']}
      FROM {$this->query['from']}
      WHERE {$this->query['where']}
      GROUP BY {$this->query['group_by']}
      {$this->query['having']}
      ORDER BY {$this->query['order_by']}
      {$this->query['limit']}
      "
        );
/*
    echo "SELECT SQL_CALC_FOUND_ROWS {$this->query['select']}<br>
      FROM {$this->query['from']}<br>
      WHERE {$this->query['where']}<br>
      GROUP BY {$this->query['group_by']}<br>
      {$this->query['having']}<br>*
      ORDER BY {$this->query['order_by']}<br>
      {$this->query['limit']}
      ";
*/

        $this->HiddenGetFields['order_by'] = $_GET['order_by'];
        $this->HiddenGetFields['order_dir'] = $_GET['order_dir'];
        $this->HiddenGetFields['EntsPerPage'] = $_GET['EntsPerPage'];
        $smarty->assign('action', $working_link);

    ###### Generate Page-Links
        $count_rows = $db->qry_first('SELECT FOUND_ROWS() AS count');
        if ($this->config['EntriesPerPage'] > 0) {
            $count_pages = ceil($count_rows['count'] / $this->config['EntriesPerPage']);
        }

        if ($this->config['EntriesPerPage'] and ($count_rows['count'] > $this->config['EntriesPerPage'])) {
            $framework->AddToPageTitle(t('Seite') .' '. ((int)$_GET['ms_page'] + 1));

            $link = $_SERVER['QUERY_STRING'] .'&ms_page=';
            $link = preg_replace('#mf_step=.\\&?#si', '', $link);
            $link = preg_replace('#mf_id=.\\&?#si', '', $link);
            $link = preg_replace('#ms_page=.\\&?#si', '', $link);
            $pages = t('Seite') .': ';
            $link_start = ' <a href="index.php?';
            $link_end = '" onclick="loadPage(this.href); return false" class="menu">';
      // Previous page link
            if ((int)$_GET['ms_page'] > 0) {
                $pages .= $link_start . $link . ($_GET['ms_page'] - 1) . $link_end .'<b>&lt;</b></a>';
            }
      
      // First page link
            if ($_GET['ms_page'] > 4) {
                $pages .= $link_start . $link . '0' . $link_end .'<b>1</b></a> ... ';
                $i = $_GET['ms_page'] - 3;
            } else {
                $i = 0;
            }

      // Direct page link
            while ($i < $count_pages and $i < ($_GET['ms_page'] + 4)) {
                if ($_GET['ms_page'] == $i) {
                    $pages .= (" " . ($i + 1));
                } else {
                    $pages .= $link_start . $link . $i . $link_end .'<b>'. ($i + 1) .'</b></a>';
                }
                      $i++;
            }
      
      // Last page link
            if ($i < $count_pages) {
                if ($i < $count_pages - 1) {
                    $pages .= ' ... ';
                }
                $pages .= $link_start . $link . ($count_pages - 1) . $link_end .'<b>'. $count_pages .'</b></a>';
            }
      
      // Next page link
            if (($_GET['ms_page'] + 1) < $count_pages) {
                $pages .= $link_start . $link . ($_GET['ms_page'] + 1) . $link_end .'<b>&gt;</b></a>';
            }
        }
        $EntsPerPage = array();
        if ($count_rows['count'] > 10) {
            $EntsPerPage[10] = t('Zeige %1 von %2', 10, $count_rows['count']);
        }
        if ($count_rows['count'] > 20) {
            $EntsPerPage[20] = t('Zeige %1 von %2', 20, $count_rows['count']);
        }
        if ($count_rows['count'] > 50) {
            $EntsPerPage[50] = t('Zeige %1 von %2', 50, $count_rows['count']);
        }
        if ($count_rows['count'] > 100) {
            $EntsPerPage[100] = t('Zeige %1 von %2', 100, $count_rows['count']);
        }
        if ($count_rows['count'] > 10) {
            $EntsPerPage[0] = t('Zeige alle %1', $count_rows['count']);
        }
        if ($count_rows['count'] <= $this->config['EntriesPerPage']) {
            $EntsFound = t('%1 Einträge', $count_rows['count']);
        } else {
            $EntsFound = '';
        }
        $smarty->assign('EntsFound', $EntsFound);
        $smarty->assign('EntsPerPage', $EntsPerPage);
        $smarty->assign('EntPerPage', $this->config['EntriesPerPage']);
        $smarty->assign('pages', $pages);
    #$smarty->assign('EntPerPageAction', $working_link);

    ###### Output Search
    // Text Inputs
        $SearchInputs = array();
        $z = 0;
        $x = 0;
        $y = 0;
        if ($this->search_fields) {
            foreach ($this->search_fields as $current_field) {
                $arr = array();
                $arr['type'] = 'text';
                $arr['name'] = "search_input[$z]";
                $arr['value'] = $_GET['search_input'][$z];
                $arr['caption'] = $current_field['caption'];
                if ($current_field['sql_fields']) {
                    foreach ($current_field['sql_fields'] as $compare_mode) {
                        if ($compare_mode == 'fulltext') {
                            $arr['helpletId'] = 'fulltext';
                            $arr['helpletText'] = 'Fulltext';
                        }
                    }
                }
                $SearchInputs[$x][$y] = $arr;
                $y++;
                $z++;
                if ($y == 2) {
                    $y = 0;
                    $x++;
                }
            }
        }

    // Dropdown Inputs
        $z = 0;
        if ($this->search_dropdown) {
            foreach ($this->search_dropdown as $current_field) {
                $arr = array();
                $arr['type'] = 'select';
                $arr['name'] = "search_dd_input[$z]";
                $arr['caption'] = $current_field['caption'];
                $arr['options'] = $current_field['selections'];
                $arr['selected'] = $_GET['search_dd_input'][$z];

                $arr['multiple'] = '';
                if ($current_field['multiple']) {
                    $arr['multiple'] = ' multiple="multiple" rows="'. $current_field['multiple'] .'"';
                    $arr['name'] .= '[]';
                }

                $SearchInputs[$x][$y] = $arr;
                $y++;
                $z++;
                if ($y == 2) {
                    $y = 0;
                    $x++;
                }
            }
        }

    // If odd number of input fields, add the last one in a single row
        if ($y == 1) {
            $SearchInputs[$x][$y]['type'] = 'space';
            $SearchInputs[$x][$y]['caption'] = '&nbsp;';
        }

        if ($this->search_fields or $this->search_dropdown) {
            $smarty->assign('quicklinks', $this->quicklinks);
            $smarty->assign('SearchInputs', $SearchInputs);
            $smarty->assign('HiddenGetFields', $this->HiddenGetFields);
            if (!$this->isExport) {
                $dsp->AddContentLine($smarty->fetch('modules/mastersearch2/templates/search_case.htm'));
            }
        }

    // Hidden Fields for EntPerPage Box
        $this->HiddenGetFields = array();
        $UrlParas = explode('&', $_SERVER['QUERY_STRING']);
        foreach ($UrlParas as $UrlPara) {
            list($key, $val) = explode('=', $UrlPara);
            if ($key != 'ms_page') {
                if (!array_key_exists(urldecode($key), $this->HiddenGetFields)) {
                    $this->HiddenGetFields[urldecode($key)] .= urldecode($val);
                }
            }
        }
        $smarty->assign('HiddenGetFields', $this->HiddenGetFields);

    ###### Output Result
    // When no Items were found
        if ($db->num_rows($res) == 0) {
            if ($this->NoItemsText) {
                $func->information($this->NoItemsText, NO_LINK);
            }
        } else {
              #### Generate Result Head
              $head = array();

              // Checkbox Headline (Empty field)
            if (count($this->multi_select_action) > 0) {
                $head[0]['width'] = '16';
                $head[0]['entry'] = '&nbsp;';
                $head[0]['type'] = 'input';
            }

              // Normal headline
            foreach ($this->result_field as $current_field) {
                // Cut out AS
                $first_as = strpos(strtolower($current_field['sql_field']), ' as ');
                if ($first_as > 0) {
                    $current_field['sql_field'] = substr($current_field['sql_field'], $first_as + 4, strlen($current_field['sql_field']));
                }

                  // Order Link and Image
                    ($_GET['ms_page'] == 'all')? $add_page = '&ms_page=all' : $add_page = '';
                    $order_dir = 'asc';
                if ($_GET['order_by'] == $current_field['sql_field']) {
                    if ($this->SQLFieldTypes[$current_field['sql_field']] == 'datetime'
                    or $this->SQLFieldTypes[$current_field['sql_field']] == 'date'
                    or $this->SQLFieldTypes[$current_field['sql_field']] == 'time'
                    or $this->SQLFieldTypes[$current_field['sql_field']] == 'timestamp') {
                        ($_GET['order_dir'] != 'asc')? $order_dir = 'asc' : $order_dir = 'desc';
                    } else {
                        ($_GET['order_dir'] != 'desc')? $order_dir = 'desc' : $order_dir = 'asc';
                    }
                }

                  // Generate Headlines
                    $arr = array();
                if ($current_field['caption']) {
                    $arr['entry'] = $current_field['caption'];
                    $arr['link'] = $_SERVER['QUERY_STRING'];
                    $arr['link'] = preg_replace('#order_by=.*\\&#sUi', '', $arr['link']);
                    $arr['link'] = preg_replace('#\\&order_by=.*$#sUi', '', $arr['link']);
                    $arr['link'] = preg_replace('#order_dir=.*\\&#sUi', '', $arr['link']);
                    $arr['link'] = preg_replace('#\\&order_dir=.*$#sUi', '', $arr['link']);
                    $arr['link'] = 'index.php?'. $arr['link'] ."&order_by={$current_field['sql_field']}&order_dir=$order_dir$add_page";
                    $arr['link'] = preg_replace('#mf_step=.\\&?#si', '', $arr['link']);
                    $arr['link'] = preg_replace('#mf_id=.\\&?#si', '', $arr['link']);

                    if ($_GET['order_by'] == $current_field['sql_field']) {
                        if ($order_dir == 'desc') {
                            $arr['entry'] .= " <img src=\"design/{$auth['design']}/images/arrows_orderby_desc_active.gif\" border=\"0\" />";
                        } else {
                            $arr['entry'] .= " <img src=\"design/{$auth['design']}/images/arrows_orderby_asc_active.gif\" border=\"0\" />";
                        }
                    }
                }
                    $head[] = $arr;
            }

              #### Generate Result Body
              $body = array();
                $x = 0;
                $maxIcons = 0;
            while ($line = $db->fetch_array($res)) { // Start: Row
                $y = 0;

                if ($this->bgcolor_attr) {
                    $body[$x]['bgcolor'] = 'style="background-color:'. $this->bgcolors[$line[$this->bgcolor_attr]] .'" ';
                }

                // cut of 'table.', befor field name
                if (strpos($select_id_field, '.') > 0) {
                        $select_id_field = substr($select_id_field, strpos($select_id_field, '.') + 1, strlen($select_id_field));
                }

                // Checkbox
                if (count($this->multi_select_action) > 0) {
                    $body[$x]['line'][0]['entry'] = '<input type="checkbox" class="checkbox" name="action['. $line[$select_id_field] .']">';
                    $body[$x]['line'][0]['type'] = 'input';
                    $y++;
                }

                // Normal fields
                foreach ($this->result_field as $k => $current_field) {
                    $arr = array();

                      // cut of 'table.', in front of field name
                      $first_as = strpos(strtolower($current_field['sql_field']), ' as ');
                    $first_dot = strpos($current_field['sql_field'], '.');
                    if ($first_as > 0) {
                        $current_field['sql_field'] = substr($current_field['sql_field'], $first_as + 4, strlen($current_field['sql_field']));
                    } elseif ($first_dot > 0) {
                        $current_field['sql_field'] = substr($current_field['sql_field'], $first_dot + 1, strlen($current_field['sql_field']));
                    }

                      // Exec Callback
                    if ($current_field['callback']) {
                          $arr['entry'] = call_user_func($current_field['callback'], $line[$current_field['sql_field']], $line[$select_id_field]);
                    } else {
                          $arr['entry'] = $line[$current_field['sql_field']];
                    }

                      // Cut of oversize chars
                    if ($current_field['max_char'] and strlen($arr['entry']) > $current_field['max_char']) {
                          $arr['entry'] = substr($arr['entry'], 0, $current_field['max_char'] - 2) .'...';
                    }

                      // Link first row to same target as first icon
                    if ($k == 0 and !$this->config['dont_link_first_line'] and $this->icon_field[0]['link']) {
                        if ($this->TargetPageCount) {
                            $TargetPage = floor($line[$this->TargetPageField] / $this->TargetPageCount);
                        } else {
                            $TargetPage = 0;
                        }
                            $arr['link'] = $this->icon_field[0]['link'];
                        if (strpos($arr['link'], '%id%')) {
                            $arr['link'] = str_replace('%id%', $line[$select_id_field], $arr['link']);
                        } else {
                            $arr['link'] .= $line[$select_id_field];
                        }
                        if (strpos($arr['link'], '%page%')) {
                            $arr['link'] = str_replace('%page%', $TargetPage, $arr['link']);
                        }
                    }

                      // Width?
                    if ($current_field['width']) {
                          $arr['width'] = $current_field['width'];
                    }

                      // Output from template
                    if ($arr['entry'] == '') {
                          $arr['entry'] = '&nbsp;';
                    }

                        $body[$x]['line'][$y] = $arr;
                        $y++;
                }

                // Icon fields
                $y = 0;
                foreach ($this->icon_field as $current_field) {
                    $arr = array();

                    if (!$current_field['callback'] or call_user_func($current_field['callback'], $line[$select_id_field])) {
                        if (substr($current_field['link'], 0, 11) == 'javascript:') {
                            $arr['link'] = '#" onclick="'. $current_field['link'];
                        } else {
                            $arr['link'] = $current_field['link'];
                        }
                        if ($this->TargetPageCount) {
                            $TargetPage = floor($line[$this->TargetPageField] / $this->TargetPageCount);
                        } else {
                            $TargetPage = 0;
                        }
                        if (strpos($arr['link'], '%id%')) {
                            $arr['link'] = str_replace('%id%', $line[$select_id_field], $arr['link']);
                        } else {
                            $arr['link'] .= $line[$select_id_field];
                        }
                        if (strpos($arr['link'], '%page%')) {
                            $arr['link'] = str_replace('%page%', $TargetPage, $arr['link']);
                        }
                        $arr['name'] = $current_field['icon_name'];
                        $arr['title'] = $current_field['tooltipp'];
                        $displayed++;

                        $body[$x]['icons'][$y] = $arr;
                        $y++;
                    }
                }

                if ($y > $maxIcons) {
                    $maxIcons = $y;
                }
                $x++;
            } // End: Row
      
              $smarty->assign('maxIcons', $maxIcons);
                $smarty->assign('head', $head);
                $smarty->assign('body', $body);

              // Multi-Select Dropdown
              $MultiOptions = array();
            if (count($this->multi_select_action) > 0) {
                $smarty->assign('MultiCaption', t('Bitte auswählen'));
                $z = 0;
                foreach ($this->multi_select_action as $current_action) {
                    $arr = array();
                    if ($z == 0) {
                        $multi_select_actions = '"'. $current_action['action'] .'"';
                    } else {
                        $multi_select_actions .= ', "'. $current_action['action'] .'"';
                    }
                    if ($z == 0) {
                        $security_questions = '"'. $current_action['security_question'] .'"';
                    } else {
                        $security_questions .= ', "'. $current_action['security_question'] .'"';
                    }

                    $arr['BGIcon'] = $current_action['icon'];
                    $arr['caption'] = $current_action['caption'];
                    $arr['value'] = $z;
                    $MultiOptions[] = $arr;
                    $z++;
                }
                $smarty->assign('multi_select_actions', $multi_select_actions);
                $smarty->assign('security_questions', $security_questions);
                $smarty->assign('MultiOptions', $MultiOptions);
            }
                $db->free_result($res);

                $smarty->assign('ms_number', $this->ms_number);
            if (!$this->isExport) {
                $dsp->AddContentLine($smarty->fetch('modules/mastersearch2/templates/result_case.htm'));
            }
        }

    // Generate Exports
        if ($this->isExport) {
            switch ($this->isExport) {
                case 'csv':
                        include("modules/install/class_export.php");
                        $export = new Export();

                        $output = '';
                        $y = 0;
                    foreach ($head as $field) {
                        if ($field['type'] != 'input' and $field['type'] != 'space') {
                            $y++;
                            if ($y > 1) {
                                $output .= ';';
                            }
                            if ($field['entry'] == '&nbsp;') {
                                $field['entry'] = '';
                            }
                            $output .= '"'. str_replace('"', '""', strip_tags(utf8_decode($field['entry']))) .'"';
                        }
                    }

                    foreach ($body as $row) {
                          $y = 0;
                          $output .= "\n";
                        foreach ($row['line'] as $field) {
                            if ($field['type'] != 'input' and $field['type'] != 'space') {
                                $y++;
                                if ($y > 1) {
                                    $output .= ';';
                                }
                                if ($field['entry'] == '&nbsp;') {
                                    $field['entry'] = '';
                                }
                                $output .= '"'. str_replace('"', '""', strip_tags(utf8_decode($field['entry']))) .'"';
                            }
                        }
                    }

                          $export->SendExport($output, 'lansuite-'. $_GET['mod'] .'.csv');
                    break;
            }
        }
    } // End: PrintSearch()
} // End: Class


###### Some global Callbacks
// Callbacks which are only for local interest, should be defined in the modules search-file

function MS2GetDate($time)
{
    global $dsp, $templ;
  
    if ($time > 0) {
        return '<span class="small">'. date('d.m.y', $time) .'<br />'. date('H:i', $time) .'</span>';
    } else {
        return $dsp->FetchIcon('', 'no', '-');
    }
}

function MS2GetTime($time)
{
    global $dsp, $templ;
  
    if ($time > 0) {
        return date('H:i', $time);
    } else {
        return $dsp->FetchIcon('', 'no', '-');
    }
}

function TrueFalse($val)
{
    global $dsp, $templ;
  
    if ($val) {
        return $dsp->FetchIcon('', 'yes', t('Ja'));
    } else {
        return $dsp->FetchIcon('', 'no', t('Nein'));
    }
}

function UserNameAndIcon($username)
{
    global $line, $dsp;

    if ($username == '') {
        return '<i>System</i>';
    } elseif ($line['userid']) {
        return $dsp->FetchUserIcon($line['userid'], $username);
    } else {
        return $username;
    }
}

function Text2LSCode($text)
{
    global $func;
  
    return $func->text2html($text);
}
