<?php

namespace LanSuite;

class MasterForm
{
    //@TODO: Check and properly set accessiblity for all of these

    public const FIELD_OPTIONAL = 1;

    public const HTML_ALLOWED = 1;

    public const LSCODE_ALLOWED = 1;

    public const HTML_WYSIWYG = 2;

    public const LSCODE_BIG = 3;

    public const IS_PASSWORD = 1;

    public const IS_NEW_PASSWORD = 2;

    public const IS_SELECTION = 3;

    public const IS_MULTI_SELECTION = 4;

    public const IS_FILE_UPLOAD = 5;

    public const IS_PICTURE_SELECT = 6;

    public const IS_TEXT_MESSAGE = 7;

    public const IS_CAPTCHA = 8;

    public const IS_NOT_CHANGEABLE = 9;

    public const IS_CALLBACK = 10;

    public const CHECK_ERROR_PROC = 1;

    public const OUTPUT_PROC = 2;

    private array $FormFields = [];

    private array $Groups = [];

    private array $SQLFields = [];

    private array $WYSIWYGFields = [];

    private array $DependOn = [];

    private array $error = [];

    /**
     * @var string
     */
    public $ManualUpdate = '';

    /**
     * @var string
     */
    public $AdditionalDBAfterSelectFunction = '';

    /**
     * @var string
     */
    public $AdditionalDBPreUpdateFunction = '';

    /**
     * @var string
     */
    public $AdditionalDBUpdateFunction = '';

    /**
     * @var string
     */
    public $CheckBeforeInserFunction = '';

    /**
     * @var int
     */
    private $DependOnStarted = 0;

    /**
     * @var bool
     */
    public $isChange = false;

    private string $FormEncType = '';

    private int $PWSecID = 0;

    /**
     * @var string
     */
    public $AdditionalKey = '';

    /**
     * @var string
     */
    public $AddInsertControllField = '';

    /**
     * @var string
     */
    public $AddChangeCondition = '';

    private int $NumFields = 0;

    /**
     * @var int
     */
    public $insert_id = -1;

    /**
     * @var int
     */
    public $LogID = 0;

    /**
     * @var string
     */
    public $LinkBack = '';

    /**
     * @var string
     */
    public $SendButtonText = '';

    private int $OptGroupOpen = 0;

    private $MultiLineID = 0;

    private array $MultiLineIDs = [];

    private int $FCKeditorID = 0;

    private array $Pages = [];

    /**
     * Master form number
     */
    private int $number = 0;

    /**
     * Master form ID
     */
    private int $MFID;

    /**
     * Current page in pagination
     */
    private int $currentPage = 0;

    /**
     * Name of a form field
     */
    private string $DependOnField;

    /**
     * The MasterForm class deals internally with a number to handle multiple forms on one page.
     * If you are using the MasterForm class only once at the page, you can just initialize and use it.
     * If you use it multiple times, you need to increment the internal counter.
     * If you use two forms on one page, you need to increment the counter once. If you use three forms, twice. And so on.
     *
     * E.g.
     *      $formOne = new \LanSuite\MasterForm();
     *      ...
     *      $formTwo = new \LanSuite\MasterForm();
     *      $formTwo->IncrementNumber();
     *      ...
     *      $formThree = new \LanSuite\MasterForm();
     *      $formThree->IncrementNumber();
     *      $formThree->IncrementNumber();
     *
     * The reason for this: The internal number is used to assign the data to the related form.
     * If they overlap, it could lead to wrong data in the previous forms.
     *
     */
    public function __construct($MFID = 0)
    {
        $this->MFID = $MFID;
        $this->IncrementNumber();
    }

    /**
     * Returns the current masterform number
     *
     * @return int
     */
    public function GetNumber()
    {
        return $this->number;
    }

    /**
     * Increments the masterform number
     *
     * @return void
     */
    public function IncrementNumber()
    {
        $this->number++;
    }

    /**
     * Decrement the masterform  number
     *
     * @return void
     */
    public function DecrementNumber()
    {
        $this->number--;
    }

    /**
     * @param string    $name
     * @return void
     */
    private function AddToSQLFields($name)
    {
        if (!in_array($name, $this->SQLFields)) {
            $this->SQLFields[] = $name;
        }
    }

    /**
     * @param string    $name
     * @param string    $value
     * @return void
     */
    public function AddFix($name, $value)
    {
        $this->AddToSQLFields($name);
        $_POST[$name] = $value;
    }

    /**
     * @param string    $caption
     * @param string    $name
     * @param string    $type
     * @param string    $selections
     * @param int       $optional
     * @param string    $callback
     * @param int       $DependOnThis
     * @param string    $DependOnCriteria
     * @return void
     */
    public function AddField($caption, $name, $type = '', $selections = '', $optional = 0, $callback = '', $DependOnThis = 0, $DependOnCriteria = '')
    {
        if ($type == self::IS_TEXT_MESSAGE || $type == self::IS_NOT_CHANGEABLE) {
            $optional = 1;
        }

        $arr = [
            'caption' => $caption,
            'name' => $name,
            'type' => $type,
            'optional' => $optional,
            'callback' => $callback,
            'selections' => $selections,
            'DependOnCriteria' => $DependOnCriteria,
            'page' => $this->currentPage
        ];

        if ($type == self::IS_FILE_UPLOAD) {
            $this->FormEncType = 'multipart/form-data';
        }

        if ($DependOnThis) {
            $this->DependOn[$name] = $DependOnThis;
        }

        $this->FormFields[] = $arr;
        $this->AddToSQLFields($name);
        if ($selections == self::HTML_WYSIWYG) {
            $this->WYSIWYGFields[] = $name;
        }
        $this->NumFields++;
    }

    /**
     * @param string $caption
     * @return void
     */
    public function AddGroup($caption = '')
    {
        if (count($this->FormFields) > 0) {
            $arr = [
                'caption' => $caption,
                'fields' => $this->FormFields,
            ];
            $this->Groups[] = $arr;
            $this->FormFields = [];
        }
    }

    /**
     * @param string $caption
     * @return void
     */
    public function AddPage($caption = '')
    {
        if (!$caption) {
            $caption = t('Seite') . ' ' . count($this->Pages);
        }

        // Adds non-group-fields to fake group
        $this->AddGroup();

        if (count($this->Groups) > 0) {
            $arr = [
                'caption' => $caption,
                'groups' => $this->Groups
            ];
            $this->Pages[] = $arr;
            $this->Groups = [];
        }
    }

    /**
     * @param string    $caption
     * @param string    $id1
     * @param string    $id2
     * @param string    $text
     * @param string    $table
     * @param string    $defText
     * @param string    $where
     * @return void
     */
    public function AddDropDownFromTable($caption, $id1, $id2, $text, $table, $defText = '', $where = '')
    {
        global $db;

        $selections = [];
        if ($defText) {
            $selections[''] = $defText;
        }
        if ($where) {
            $where = ' WHERE ' . $where;
        }
        $res = $db->qry('SELECT %plain%, %plain% FROM %prefix%%plain%%plain% GROUP BY %plain% ORDER BY %plain%', $id2, $text, $table, $where, $id2, $text);
        while ($row = $db->fetch_array($res)) {
            if ($row[$id2]) {
                $selections[$row[$id2]] = $row[$text];
            }
        }
        $db->free_result($res);
        $this->AddField($caption, $id1, self::IS_SELECTION, $selections, self::FIELD_OPTIONAL);
    }

    /**
     * @param string $id
     * @return void
     */
    public function AddDBLineID($id)
    {
        $this->MultiLineIDs[] = $id;
    }

    /**
     * @param string    $BaseURL
     * @param string    $table
     * @param string    $idname
     * @param int       $id
     * @return bool|int|mixed|string
     * @throws \Exception
     * @throws \SmartyException
     */
    public function SendForm($BaseURL, $table, $idname = '', $id = 0)
    {
        $SQLFieldTypes = [];
        $SQLFieldUnique = [];
        $field = [];
        $addUpdSuccess = null;
        global $dsp, $db, $config, $func, $sec, $framework, $__POST, $smarty, $cfg;

        // In freeze-mode there are no changes to the database allowed
        if ($cfg['sys_freeze']) {
            $func->information(t('Diese Webseite ist Momentan im "Freeze-Mode".[br]D.h. es können keine neuen Daten in die Datenbank geschrieben werden.[br][br]Bitte versuche es zu einem Späteren Zeitpunkt nocheinmal.'));
            return false;
        }

        // Break, if in wrong form
        $masterFormStepParameter = $_GET['mf_step'] ?? 0;
        $Step_Tmp = $masterFormStepParameter;
        if ($masterFormStepParameter == 2 && $_GET['mf_id'] != $this->GetNumber()) {
            $Step_Tmp = 1;
        }

        // If more then one row in a table should be edited
        if (strpos($id, ' ') > 0) {
            $this->MultiLineID = $id;
            $id = '';
        }

        // Adds non-page-fields to fake page
        $this->AddPage();
        if ($BaseURL) {
            $StartURL = $BaseURL . '&' . $idname . '=' . $id;
        } else {
            $StartURL = $framework->get_clean_url_query('base');
            $StartURL = str_replace('&mf_step=2', '', $StartURL);
            $StartURL = preg_replace('#&mf_id=[0-9]*#si', '', $StartURL);

            if (str_starts_with($StartURL, '&' . $idname . '=' . $id)) {
                $StartURL .= '&' . $idname . '=' . $id;
            }
        }

        $this->LinkBack = $StartURL . '#MF' . $this->GetNumber();
        if ($id or $this->MultiLineID) {
            $this->isChange = true;
        }

        $AddKey = '';
        if ($this->AdditionalKey != '') {
            $AddKey = $this->AdditionalKey . ' AND ';
        }
        $InsContName = 'InsertControll' . $this->MFID;

        // If the table entry should be created, or deleted whether the control field is checked
        if ($this->AddInsertControllField != '') {
            if ($this->MultiLineID) {
                $find_entry = $db->qry('SELECT * FROM %prefix%$table WHERE ' . $this->MultiLineID);
            } else {
                $find_entry = $db->qry("SELECT * FROM %prefix%$table WHERE $AddKey $idname = %int%", $id);
            }

            if ($db->num_rows($find_entry)) {
                $this->isChange = 1;
            } else {
                $this->isChange = 0;
            }

            $db->free_result($find_entry);
        }

        // Get SQL-Field types
        $res = $db->qry('DESCRIBE %prefix%%plain%', $table);
        while ($row = $db->fetch_array($res)) {
            $SQLFieldTypes[$row['Field']] = $row['Type'];

            if ($row['Key'] == 'PRI' || $row['Key'] == 'UNI') {
                $SQLFieldUnique[$row['Field']] = true;
            } else {
                $SQLFieldUnique[$row['Field']] = false;
            }
        }
        $db->free_result($res);

        // Split fields, which consist of more than one
        if ($this->SQLFields) {
            foreach ($this->SQLFields as $key => $val) {
                if (strpos($this->SQLFields[$key], '|') > 0) {
                    $subfields = explode('|', $this->SQLFields[$key]);
                    if ($subfields) {
                        foreach ($subfields as $subfield) {
                            $this->SQLFields[] = $subfield;
                        }
                    }
                }
            }
        }

        // Delete non existing DB fields, from array
        if ($this->SQLFields) {
            foreach ($this->SQLFields as $key => $val) {
                if (!array_key_exists($val, $SQLFieldTypes)) {
                    unset($this->SQLFields[$key]);
                }
            }
        }

        // Error-Switch
        switch ($Step_Tmp) {
            default:
                $_SESSION['mf_referrer'][$this->GetNumber()] = $func->internal_referer;

                // Read current values, if change
                if ($this->isChange) {
                    $db_query = '';
                    if ($this->SQLFields) {
                        foreach ($this->SQLFields as $val) {
                            if ($SQLFieldTypes[$val] == 'varbinary(16)' and $val == 'ip') {
                                $db_query .= ', INET6_NTOA('.$val.') AS '.$val;
                            } else {
                                $db_query .= ", $val";
                            }
                        }
                    }

                    // Select current values for Multi-Line-Edit
                    if ($this->MultiLineID) {
                        $z = 0;
                        $res = $db->qry('SELECT %plain% %plain% FROM %prefix%%plain% WHERE %plain%', $idname, $db_query, $table, $this->MultiLineID);
                        while ($row = $db->fetch_array($res)) {
                            foreach ($this->SQLFields as $key => $val) {
                                $_POST[$val . '[' . $row[$idname] . ']'] = $row[$val];
                            }
                            $z++;
                        }
                        $db->free_result($res);

                    // Select current values for normal edit
                    } else {
                        $row = $db->qry_first('SELECT 1 AS found %plain% FROM %prefix%%plain% WHERE %plain% %plain% = %int%', $db_query, $table, $AddKey, $idname, $id);
                        if ($row['found']) {
                            foreach ($this->SQLFields as $key => $val) {
                                if (!in_array($key, $this->WYSIWYGFields)) {
                                    $_POST[$val] = $row[$val];
                                } else {
                                    $_POST[$val] = $row[$val];
                                }
                            }
                        } else {
                            $func->error(t('Diese ID existiert nicht.'));
                            return false;
                        }
                    }
                }

                if ($this->AdditionalDBAfterSelectFunction) {
                    $addUpdSuccess = call_user_func($this->AdditionalDBAfterSelectFunction, '');
                }
                break;

            // Check for errors and convert data, if necessary (dates, passwords, ...)
            case 2:
                $this->FCKeditorID = 0;
                if ($this->Pages) {
                    foreach ($this->Pages as $page) {
                        if ($page['groups']) {
                            foreach ($page['groups'] as $GroupKey => $group) {
                                if ($group['fields']) {
                                    foreach ($group['fields'] as $FieldKey => $field) {
                                        if ($field['name']) {
                                            $err = false;

                                            // Copy WYSIWYG editor variable
                                            if (array_key_exists($field['name'], $SQLFieldTypes) && ($SQLFieldTypes[$field['name']] == 'text' || $SQLFieldTypes[$field['name']] == 'mediumtext' || $SQLFieldTypes[$field['name']] == 'longtext') && $field['selections'] == self::HTML_WYSIWYG) {
                                                $this->FCKeditorID++;
                                                $_POST[$field['name']] = $_POST['FCKeditor'. $this->FCKeditorID];
                                            }

                                            // If not in DependOn-Group, or DependOn-Group is active
                                            if (!$this->DependOnStarted || (array_key_exists($this->DependOnField, $_POST) && $_POST[$this->DependOnField])) {
                                                // -- Convertions --
                                                // Convert Post-date to unix-timestap
                                                if (array_key_exists($field['name'], $SQLFieldTypes) && $SQLFieldTypes[$field['name']] == 'datetime') {
                                                    //1997-12-31 23:59:59
                                                    $_POST[$field['name']] = $_POST[$field['name'].'_value_year'] .'-'. $_POST[$field['name'].'_value_month'] .'-'.
                                                    $_POST[$field['name'].'_value_day'] .' '. $_POST[$field['name'].'_value_hours'] .':'. $_POST[$field['name'].'_value_minutes'] .':00';
                                                    $__POST[$field['name']] = $_POST[$field['name']];
                                                }

                                                if (array_key_exists($field['name'], $SQLFieldTypes) && $SQLFieldTypes[$field['name']] == 'date') {
                                                    $_POST[$field['name']] = $_POST[$field['name'].'_value_year'] .'-'. $_POST[$field['name'].'_value_month'] .'-'. $_POST[$field['name'].'_value_day'];
                                                    $__POST[$field['name']] = $_POST[$field['name']];
                                                }

                                                // Upload submitted file
                                                if (array_key_exists($field['name'].'_keep', $_POST) && $_POST[$field['name'].'_keep']) {
                                                    foreach ($this->SQLFields as $key => $val) {
                                                        if ($val == $field['name']) {
                                                            unset($this->SQLFields[$key]);
                                                        }
                                                    }
                                                } elseif ($field['type'] == self::IS_FILE_UPLOAD) {
                                                    if (substr($field['selections'], strlen($field['selections']) - 1, 1) == '_') {
                                                        $_POST[$field['name']] = $func->FileUpload($field['name'], substr($field['selections'], 0, strrpos($field['selections'], '/')), substr($field['selections'], strrpos($field['selections'], '/') + 1, strlen($field['selections'])));
                                                    } else {
                                                        $_POST[$field['name']] = $func->FileUpload($field['name'], $field['selections']);
                                                    }
                                                }

                                                // -- Checks --
                                                // Exec callback
                                                if ($field['type'] == self::IS_CALLBACK) {
                                                      $err = call_user_func($field['selections'], $field['name'], self::CHECK_ERROR_PROC);
                                                }
                                                if ($err) {
                                                    $this->error[$field['name']] = $err;
                                                }

                                                // Check for value
                                                $fieldValue = $_POST[$field['name']] ?? '';
                                                if (!$field['optional'] && $fieldValue == '') {
                                                      $this->error[$field['name']] = t('Bitte fülle dieses Pflichtfeld aus.');

                                                // Check Int
                                                } elseif (array_key_exists($field['name'], $SQLFieldTypes) && str_contains($SQLFieldTypes[$field['name']], 'int') && $SQLFieldTypes[$field['name']] != 'tinyint(1)' && $SQLFieldTypes[$field['name']] != "enum('0','1')" && array_key_exists($field['name'], $_POST) && $_POST[$field['name']] && (int) $_POST[$field['name']] == 0) {
                                                      $this->error[$field['name']] = t('Bitte gib eine Zahl ein.');

                                                // Check date
                                                } elseif (array_key_exists($field['name'], $SQLFieldTypes) && ($SQLFieldTypes[$field['name']] == 'datetime' || $SQLFieldTypes[$field['name']] == 'date') && (!checkdate($_POST[$field['name'].'_value_month'], $_POST[$field['name'].'_value_day'], $_POST[$field['name'].'_value_year']) && !($_POST[$field['name'].'_value_month']=="00" && $_POST[$field['name'].'_value_day']=="00" && $_POST[$field['name'].'_value_year']=="0000"))) {
                                                      $this->error[$field['name']] = t('Das eingegebene Datum ist nicht korrekt.');

                                                // Check new passwords
                                                } elseif ($field['type'] == self::IS_NEW_PASSWORD && $_POST[$field['name']] != $_POST[$field['name'].'2']) {
                                                      $this->error[$field['name'].'2'] = t('Die beiden Kennworte stimmen nicht überein.');

                                                // Check captcha
                                                } elseif ($field['type'] == self::IS_CAPTCHA && ($_POST['captcha'] == '' || $_SESSION['captcha'] != strtoupper($_POST['captcha']))) {
                                                      $this->error['captcha'] = t('Captcha falsch wiedergegeben.');

                                                // No \r \n \t \0 \x0B in Non-Multiline-Fields
                                                } elseif ($field['type'] != 'text' && $field['type'] != 'mediumtext' && $field['type'] != 'longtext' && array_key_exists($field['name'], $SQLFieldTypes) && $SQLFieldTypes[$field['name']] != 'text' && $SQLFieldTypes[$field['name']] != 'mediumtext' && $SQLFieldTypes[$field['name']] != 'longtext' && !is_array($fieldValue) && ((str_contains($fieldValue, "\r")) || (str_contains($fieldValue, "\n")) || (str_contains($fieldValue, "\t")) || (str_contains($fieldValue, "\0")) || (str_contains($fieldValue, "\x0B")))) {
                                                      $this->error[$field['name']] = t('Dieses Feld enthält nicht erlaubte Steuerungszeichen (z.B. einen Tab, oder Zeilenumbruch)');

                                                // Callbacks
                                                } elseif ($field['callback']) {
                                                    $postFieldValue = $_POST[$field['name']] ?? '';
                                                    $err = call_user_func($field['callback'], $postFieldValue);
                                                    if ($err) {
                                                        $this->error[$field['name']] = $err;
                                                    }
                                                }

                                                // Check double uniques
                                                // Neccessary in Multi Line Edit Mode? If so: Still to do
                                                if (array_key_exists($field['name'], $SQLFieldTypes) && $SQLFieldUnique[$field['name']]) {
                                                    $check_double_where = '';
                                                    if ($this->isChange) {
                                                        $check_double_where = ' AND '. $idname .' != '. (int)$id;
                                                    }

                                                    $row = $db->qry_first("SELECT 1 AS found FROM %prefix%%plain% WHERE %plain% = %string% %plain%", $table, $field['name'], $_POST[$field['name']], $check_double_where);
                                                    if ($row) {
                                                        $this->error[$field['name']] = t('Dieser Eintrag existiert bereits in unserer Datenbank.');
                                                    }
                                                }
                                            }

                                            // Manage Depend-On-Groups
                                            if ($this->DependOnStarted >= 1) {
                                                $this->DependOnStarted--;
                                            }
                                            if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) {
                                                $this->DependOnStarted = $this->DependOn[$field['name']];
                                                $this->DependOnField = $field['name'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($this->error) > 0) {
                    $_POST = $__POST;
                    $Step_Tmp--;
                }
                break;
        }

        $dsp->AddJumpToMark('MF' . $this->GetNumber());

        // Form-Switch
        switch ($Step_Tmp) {
            // Output form
            default:
                $sec->unlock($table);
                $dsp->SetForm($StartURL .'&mf_step=2&mf_id='. $this->GetNumber() .'#MF' . $this->GetNumber(), '', '', $this->FormEncType);

                // InsertControll check box - the table entry will only be created, if this check box is checked, otherwise the existing entry will be deleted
                if ($this->AddInsertControllField != '') {
                    $find_entry = $db->qry('SELECT * FROM %prefix%%plain% WHERE %plain% %plain% = %int%', $table, $AddKey, $idname, $id);
                    if ($db->num_rows($find_entry)) {
                        $_POST[$InsContName] = 1;
                    }

                    $this->DependOnStarted = $this->NumFields;
                    $additionalHTML = "onclick=\"CheckBoxBoxActivate('box_$InsContName', this.checked)\"";
                    [$text1, $text2] = explode('|', $this->AddInsertControllField);
                    $optionalField = $field['optional'] ?? '';
                    $preValue = $_POST[$InsContName] ?? '';
                    $dsp->AddCheckBoxRow($InsContName, $text1, $text2, '', $optionalField, $preValue, '', '', $additionalHTML);
                    $dsp->StartHiddenBox('box_'.$InsContName, $preValue);
                }

                // Write pages links
                if ($this->Pages and count($this->Pages) > 1) {
                    $dsp->StartTabs();
                }

                // Output fields
                $z = 0;
                $y = 0;
                $this->FCKeditorID = 0;

                // Pages loop
                if ($this->Pages) {
                    foreach ($this->Pages as $PageKey => $page) {
                        if ($page['caption'] and count($this->Pages) > 1) {
                            $dsp->StartTab($page['caption']);
                        }

                        // Groups loop
                        if ($page['groups']) {
                            foreach ($page['groups'] as $GroupKey => $group) {
                                if ($group['caption']) {
                                    $dsp->AddFieldsetStart($group['caption']);
                                }

                                // Fields loop
                                if ($group['fields']) {
                                    foreach ($group['fields'] as $FieldKey => $field) {
                                        if (!$field['type'] && array_key_exists($field['name'], $SQLFieldTypes)) {
                                            $field['type'] = $SQLFieldTypes[$field['name']];
                                        }

                                        // Rename fields to arrays, if in Multi-Line-Edit-Mode
                                        if ($this->MultiLineID) {
                                            $field['name'] = $field['name'] .'['. $this->MultiLineIDs[$y] .']';
                                        }
                                        $z++;

                                        if ($z >= count($this->SQLFields)) {
                                            $z = 0;
                                            $y++;
                                        }

                                        $additionalHTML = '';
                                        switch ($field['type']) {
                                            // Textarea
                                            case 'text':
                                                $maxchar = 65535;
                                                // No break statement here on purpose

                                            case 'mediumtext':
                                                if (!isset($maxchar)) {
                                                    $maxchar = 16_777_215;
                                                }
                                                // No break statement here on purpose

                                            case 'longtext':
                                                if (!isset($maxchar)) {
                                                    $maxchar = 4_294_967_295;
                                                }
                                                $postFieldValue = $_POST[$field['name']] ?? '';
                                                $errorText = $this->error[$field['name']] ?? '';
                                                if ($field['selections'] == self::HTML_ALLOWED or $field['selections'] == self::LSCODE_ALLOWED) {
                                                    $dsp->AddTextAreaPlusRow($field['name'], $field['caption'], $postFieldValue, $errorText, '', '', $field['optional'], $maxchar);
                                                } elseif ($field['selections'] == self::LSCODE_BIG) {
                                                    $dsp->AddTextAreaPlusRow($field['name'], $field['caption'], $postFieldValue, $errorText, 70, 20, $field['optional'], $maxchar);
                                                } elseif ($field['selections'] == self::HTML_WYSIWYG) {
                                                    $this->FCKeditorID++;
                                                    ob_start();
                                                    include_once("ext_scripts/FCKeditor/fckeditor.php");
                                                    $oFCKeditor = new \FCKeditor('FCKeditor'. $this->FCKeditorID) ;
                                                    $oFCKeditor->BasePath = 'ext_scripts/FCKeditor/';
                                                    $oFCKeditor->Config["CustomConfigurationsPath"] = "../myconfig.js"  ;
                                                    $oFCKeditor->Value = $func->AllowHTML($_POST[$field['name']]);
                                                    $oFCKeditor->Height = 460;
                                                    $oFCKeditor->Create();
                                                    $fcke_content = ob_get_contents();
                                                    ob_end_clean();
                                                    $dsp->AddSingleRow($fcke_content);

                                                    $errorMessage = $this->error[$field['name']] ?? '';
                                                    if ($errorMessage) {
                                                        $dsp->AddDoubleRow($field['caption'], $dsp->errortext_prefix . $this->error[$field['name']] . $dsp->errortext_suffix);
                                                    }
                                                } else {
                                                    $postFieldText = $_POST[$field['name']] ?? '';
                                                    $errorFieldText = $this->error[$field['name']] ?? '';
                                                    $dsp->AddTextAreaRow($field['name'], $field['caption'], $postFieldText, $errorFieldText, '', '', $field['optional']);
                                                }
                                                break;

                                            // Checkbox
                                            case "enum('0','1')":
                                            case 'tinyint(1)':
                                                if ($this->DependOnStarted == 0 and array_key_exists($field['name'], $this->DependOn)) {
                                                    $additionalHTML = "onclick=\"CheckBoxBoxActivate('box_{$field['name']}', this.checked)\"";
                                                }
                                                $captionParts = explode('|', $field['caption']);
                                                $field['caption1'] = $captionParts[0];
                                                $field['caption2'] = $captionParts[1] ?? '';
                                                if (array_key_exists($field['name'], $_POST) && !$_POST[$field['name']]) {
                                                      unset($_POST[$field['name']]);
                                                }
                                                $errorText = $this->error[$field['name']] ?? '';
                                                $fieldValue = $_POST[$field['name']] ?? '';
                                                $dsp->AddCheckBoxRow($field['name'], $field['caption1'], $field['caption2'], $errorText, $field['optional'], $fieldValue, '', '', $additionalHTML);
                                                break;

                                            // Date-Select
                                            case 'datetime':
                                                $values = array(
                                                    'year' => '',
                                                    'month' => '',
                                                    'day' => '',
                                                    'hour' => '',
                                                    'min' => '',
                                                    'sec' => '',
                                                );
                                                $fieldValue = $_POST[$field['name']] ?? '';

                                                if ($fieldValue) {
                                                    [$date, $time] = explode(' ', $fieldValue);
                                                    [$values['year'], $values['month'], $values['day']] = explode('-', $date);

                                                    $timeParts = explode(':', $time);
                                                    $values['hour'] = $timeParts[0] ?? '00';
                                                    $values['min'] = $timeParts[1] ?? '00';
                                                    $values['sec'] = $timeParts[2] ?? '00';
                                                }
                                                $startj = null;
                                                if ($values['year'] == '') {
                                                    $values['year'] = "0000";
                                                    $startj = "0000";
                                                }

                                                if ($values['month'] == '') {
                                                      $values['month'] = "00";
                                                }

                                                if ($values['day'] == '') {
                                                      $values['day'] = "00";
                                                }

                                                if ($values['hour'] == '') {
                                                      $values['hour'] = "00";
                                                }

                                                if ($values['min'] == '') {
                                                      $values['min'] = "00";
                                                }

                                                if ($values['sec'] == '') {
                                                      $values['sec'] = "00";
                                                }

                                                $errorText = $this->error[$field['name']] ?? '';
                                                $dsp->AddDateTimeRow($field['name'], $field['caption'], 0, $errorText, $values, '', $startj, '', '', $field['optional']);
                                                break;

                                            // Date-Select
                                            case 'date':
                                                $values = array(
                                                    'year' => '',
                                                    'month' => '',
                                                    'day' => '',
                                                    'hour' => '',
                                                    'min' => '',
                                                    'sec' => '',
                                                );
                                                $postFieldValue = $_POST[$field['name']] ?? '';
                                                $dateParts = explode(' ', $postFieldValue);

                                                $date = $dateParts[0];
                                                $time = '';
                                                if (array_key_exists(1, $dateParts)) {
                                                    $time = $dateParts[1];
                                                }

                                                if ($date) {
                                                    [$values['year'], $values['month'], $values['day']] = explode('-', $date);
                                                }

                                                if ($time) {
                                                    [$values['hour'], $values['min'], $values['sec']] = explode(':', $time);
                                                }

                                                if ($values['year'] == '') {
                                                    $values['year'] = "0000";
                                                }

                                                if ($values['month'] == '') {
                                                    $values['month'] = "00";
                                                }

                                                if ($values['day'] == '') {
                                                    $values['day'] = "00";
                                                }

                                                if ($field['selections']) {
                                                    $area = explode('/', $field['selections']);
                                                }
                                                $start = $area[0];
                                                $end = $area[1];
                                                $fieldErrorText = $this->error[$field['name']] ?? '';
                                                $dsp->AddDateTimeRow($field['name'], $field['caption'], 0, $fieldErrorText, $values, '', $start, $end, 1, $field['optional']);
                                                break;

                                            // Password-Row
                                            case self::IS_PASSWORD:
                                                // Dont show MD5-sum, read from DB on change
                                                if (array_key_exists($field['name'], $_POST) && strlen($_POST[$field['name']]) == 32) {
                                                    $_POST[$field['name']] = '';
                                                }

                                                $postFieldValue = $_POST[$field['name']] ?? '';
                                                $fieldErrorText = $this->error[$field['name']] ?? '';
                                                $dsp->AddPasswordRow($field['name'], $field['caption'], $postFieldValue, $fieldErrorText, '', $field['optional']);
                                                break;

                                            // New-Password-Row
                                            case self::IS_NEW_PASSWORD:
                                                $postFieldValue = $_POST[$field['name']] ?? '';
                                                $postFieldSecondValue = $_POST[$field['name'].'2'] ?? '';
                                                // Dont show MD5-sum, read from DB on change
                                                if (strlen($postFieldValue) == 32) {
                                                    $_POST[$field['name']] = '';
                                                }

                                                $this->PWSecID++;
                                                $errorText = $this->error[$field['name']] ?? '';
                                                $errorTextSecond = $this->error[$field['name'].'2'] ?? '';
                                                $dsp->AddPasswordRow($field['name'], $field['caption'], $postFieldValue, $errorText, '', $field['optional'], "onkeyup=\"CheckPasswordSecurity(this.value, document.images.seclevel)\"");
                                                $dsp->AddPasswordRow($field['name'].'2', $field['caption'].' '.t('Verfikation'), $postFieldSecondValue, $errorTextSecond, '', $field['optional']);
                                                $smarty->assign('pw_security_id', $this->PWSecID);
                                                $dsp->AddDoubleRow('', $smarty->fetch('design/templates/ls_row_pw_security.htm'));
                                                break;

                                            // Captcha-Row
                                            case self::IS_CAPTCHA:
                                                include_once('ext_scripts/ascii_captcha.class.php');
                                                $captcha = new \ASCII_Captcha();
                                                $data = $captcha->create($text);
                                                $_SESSION['captcha'] = $text;
                                                $dsp->AddDoubleRow(t('Bitte gib diesen Text unterhalb ein'), "<pre style='font-size:8px;'>$data</pre>");
                                                $captchaParameter = $_POST['captcha'] ?? '';
                                                $captchaError = $this->error['captcha'] ?? '';
                                                $dsp->AddTextFieldRow('captcha', '', $captchaParameter, $captchaError);
                                                break;

                                            // Pre-Defined Dropdown
                                            case self::IS_SELECTION:
                                                if ($field['DependOnCriteria']) {
                                                    $addCriteria = ", Array('". implode("', '", $field['DependOnCriteria']) ."')";
                                                } else {
                                                      $addCriteria = '';
                                                }

                                                if ($this->DependOnStarted == 0 && array_key_exists($field['name'], $this->DependOn)) {
                                                      $additionalHTML = "onchange=\"DropDownBoxActivate('box_{$field['name']}', this.options[this.options.selectedIndex].value{$addCriteria})\"";
                                                }

                                                if (is_array($field['selections'])) {
                                                    $selections = array();
                                                    foreach ($field['selections'] as $key => $val) {
                                                        if (str_starts_with($key, '-OptGroup-')) {
                                                            if ($this->OptGroupOpen) {
                                                                $selections[] = '</optgroup>';
                                                            }
                                                            $selections[] = '<optgroup label="'. $val .'">';
                                                            $this->OptGroupOpen = 1;
                                                        } else {
                                                            $postFieldValue = $_POST[$field['name']] ?? '';
                                                            ($postFieldValue == $key) ? $selected = " selected" : $selected = "";
                                                            $selections[] = "<option$selected value=\"$key\">$val</option>";
                                                        }
                                                    }

                                                    if ($this->OptGroupOpen) {
                                                        $selections[] = '</optgroup>';
                                                    }

                                                    $this->OptGroupOpen = 0;
                                                    $fieldErrorText = $this->error[$field['name']] ?? '';
                                                    $dsp->AddDropDownFieldRow($field['name'], $field['caption'], $selections, $fieldErrorText, $field['optional'], $additionalHTML);
                                                }
                                                break;

                                            // Pre-Defined Multiselection
                                            case self::IS_MULTI_SELECTION:
                                                if (is_array($field['selections'])) {
                                                    $selections = array();
                                                    foreach ($field['selections'] as $key => $val) {
                                                        $selected = '';
                                                        if (array_key_exists($field['name'], $_POST) && is_array($_POST[$field['name']])) {
                                                            foreach ($_POST[$field['name']] as $PostedField) {
                                                                if ($PostedField == $key) {
                                                                    $selected = ' selected';
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        $selections[] = "<option value=\"$key\"$selected>$val</option>";
                                                    }

                                                    $fieldErrorText = $this->error[$field['name']] ?? '';
                                                    $dsp->AddSelectFieldRow($field['name'], $field['caption'], $selections, $fieldErrorText, $field['optional'], 7);
                                                }
                                                break;

                                            // File Upload to path
                                            case self::IS_FILE_UPLOAD:
                                                $errorText = $this->error[$field['name']] ?? '';
                                                $dsp->AddFileSelectRow($field['name'], $field['caption'], $errorText, '', '', $field['optional']);
                                                $postFieldValue = $_POST[$field['name']] ?? '';
                                                if ($postFieldValue) {
                                                    $FileEnding = strtolower(substr($_POST[$field['name']], strrpos($_POST[$field['name']], '.'), 5));
                                                    if ($FileEnding == '.png' or $FileEnding == '.gif' or $FileEnding == '.jpg' or $FileEnding == '.jpeg') {
                                                        $img = HTML_NEWLINE.'<img src="'. $_POST[$field['name']] .'" />';
                                                    } else {
                                                        $img = '';
                                                    }

                                                    $dsp->AddCheckBoxRow($field['name'].'_keep', t('Aktuelle Datei beibehalten'), $_POST[$field['name']] . $img, '', $field['optional'], 1);
                                                }
                                                break;

                                            // Picture Dropdown from path
                                            case self::IS_PICTURE_SELECT:
                                                if (is_dir($field['selections'])) {
                                                    $errorFieldText = $this->error[$field['name']] ?? '';
                                                    $postFieldText = $_POST[$field['name']] ?? '';
                                                    $dsp->AddPictureDropDownRow($field['name'], $field['caption'], $field['selections'], $errorFieldText, $field['optional'], $postFieldText);
                                                }
                                                break;

                                            case self::IS_TEXT_MESSAGE:
                                                if (!$field['selections']) {
                                                    $field['selections'] = $_POST[$field['name']];
                                                }

                                                if (is_array($field['selections'])) {
                                                      $field['selections'] = $field['selections'][$_POST[$field['name']]];
                                                }
                                                $dsp->AddDoubleRow($field['caption'], $field['selections']);
                                                break;

                                            case self::IS_CALLBACK:
                                                $fieldErrorText = $this->error[$field['name']] ?? '';
                                                $ret = call_user_func($field['selections'], $field['name'], self::OUTPUT_PROC, $fieldErrorText);
                                                if ($ret) {
                                                    $dsp->AddDoubleRow($field['caption'], $ret);
                                                }
                                                break;

                                            // Normal Textfield
                                            default:
                                                if ($field['type'] == self::IS_NOT_CHANGEABLE) {
                                                    $not_changeable = 1;
                                                } else {
                                                    $not_changeable = 0;
                                                }

                                                $maxlength = $this->get_fieldlength($field['type']);

                                                if ($maxlength > 0 && $maxlength < 70) {
                                                    $length = $maxlength + (5 - ($maxlength % 5));
                                                } else {
                                                    $length = 70;
                                                }

                                                $fieldErrorText = $this->error[$field['name']] ?? '';
                                                $postFieldText = $_POST[$field['name']] ?? '';
                                                $dsp->AddTextFieldRow($field['name'], $field['caption'], $postFieldText, $fieldErrorText, $length, $field['optional'], $not_changeable, $maxlength);
                                                break;
                                        }

                                        // Start HiddenBox
                                        if ($this->DependOnStarted == 0 && array_key_exists($field['name'], $this->DependOn)) {
                                            $postFieldValue = $_POST[$field['name']] ?? '';
                                            $dsp->StartHiddenBox('box_' . $field['name'], $postFieldValue);
                                            $this->DependOnStarted = $this->DependOn[$field['name']] + 1;
                                            unset($this->DependOn[$field['name']]);
                                        }

                                        // Stop HiddenBox, when counter has reached the last box-field
                                        if ($this->DependOnStarted == 1) {
                                            $dsp->StopHiddenBox();
                                        }

                                        // Decrease counter
                                        if ($this->DependOnStarted > 0) {
                                            $this->DependOnStarted--;
                                        }
                                    }
                                }

                                if ($group['caption']) {
                                    $dsp->AddFieldsetEnd();
                                }
                            }
                        } // End: Groups loop

                        if ($page['caption'] and count($this->Pages) > 1) {
                            $dsp->EndTab();
                        }
                    }
                } // End: Pages loop

                if ($this->Pages and count($this->Pages) > 1) {
                    $dsp->EndTabs();
                }

                if ($this->SendButtonText) {
                    $dsp->AddFormSubmitRow($this->SendButtonText);
                } elseif ($id || $this->MultiLineID) {
                    $dsp->AddFormSubmitRow('Editieren');
                } else {
                    $dsp->AddFormSubmitRow('Erstellen');
                }
                break;

            // Update DB
            case 2:
                if (!$sec->locked($table, $this->LinkBack)) {
                    // Return for manual update, if set
                    if ($this->ManualUpdate) {
                        return true;
                    }

                    if ($this->Pages) {
                        foreach ($this->Pages as $page) {
                            if ($page['groups']) {
                                foreach ($page['groups'] as $group) {
                                    if ($group['fields']) {
                                        foreach ($group['fields'] as $field) {
                                            // Convert Passwords
                                            if ($field['type'] == self::IS_NEW_PASSWORD && $_POST[$field['name']] != '') {
                                                $_POST[$field['name'] .'_original'] = $_POST[$field['name']];
                                                $_POST[$field['name']] = md5($_POST[$field['name']]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($this->CheckBeforeInserFunction) {
                        if (!call_user_func($this->CheckBeforeInserFunction, $id)) {
                            return false;
                        }
                    }

                    if ($this->AdditionalDBPreUpdateFunction) {
                        $addUpdSuccess = call_user_func($this->AdditionalDBPreUpdateFunction, $id);
                    }

                    $ChangeError = false;
                    if ($this->AddChangeCondition) {
                        $ChangeError = call_user_func($this->AddChangeCondition, $id);
                    }

                    if ($ChangeError) {
                        $func->information($ChangeError);
                    } else {
                        $addUpdSuccess = true;

                        // Generate INSERT/UPDATE query
                        $db_query = '';
                        if ($this->SQLFields) {
                            if ($this->MultiLineID) {
                                foreach ($this->MultiLineIDs as $key2 => $value2) {
                                    $db_query = '';
                                    foreach ($this->SQLFields as $key => $val) {
                                        $databaseValue = $_POST[$val][$value2] ?? '';
                                        $databaseValue = mysqli_real_escape_string($databaseValue);
                                        $db_query .= "$val = '". $databaseValue ."', ";
                                    }
                                    $db_query = substr($db_query, 0, strlen($db_query) - 2);
                                    $db->qry("UPDATE %prefix%%plain% SET %plain% WHERE %plain% = %int%", $table, $db_query, $idname, $value2);
                                    $func->log_event(t('Eintrag #%1 in Tabelle "%2" geändert', array($value2, $config['database']['prefix'] . $table)), 1, '', $this->LogID);
                                }
                            } else {
                                foreach ($this->SQLFields as $key => $val) {
                                    $postFieldValue = $_POST[$val] ?? '';
                                    if (($SQLFieldTypes[$val] == 'datetime' or $SQLFieldTypes[$val] == 'date') and $_POST[$val] == 'NOW()') {
                                        $db_query .= "$val = NOW(), ";
                                    } elseif ($SQLFieldTypes[$val] == 'tinyint(1)') {
                                        $intValue = $_POST[$val] ?? 0;
                                        $db_query .= $val .' = '. (int) $intValue .', ';
                                    } elseif ($SQLFieldTypes[$val] == 'varbinary(16)' and $val == 'ip') {
                                        $db_query .= $val .' = INET6_ATON(\''. $_POST[$val] .'\'), ';
                                    } elseif ($postFieldValue == '++' and str_contains($SQLFieldTypes[$val], 'int')) {
                                        $db_query .= "$val = $val + 1, ";
                                    } elseif ($postFieldValue == '--' and str_contains($SQLFieldTypes[$val], 'int')) {
                                        $db_query .= "$val = $val - 1, ";
                                    } else {
                                        $db_query .= "$val = '{$postFieldValue}', ";
                                    }
                                }
                                $db_query = substr($db_query, 0, strlen($db_query) - 2);

                                // If the table entry should be created, or deleted whether the control field is checked
                                $InsContNameParameter = $_POST[$InsContName] ?? 0;
                                if ($this->AddInsertControllField != '' && !$InsContNameParameter) {
                                    $db->qry("DELETE FROM %prefix%%plain% WHERE %plain% %plain% = %int%", $table, $AddKey, $idname, $id);

                                // Send query
                                } else {
                                    if ($this->isChange) {
                                        $db->qry("UPDATE %prefix%%plain% SET %plain% WHERE %plain% %plain% = %int%", $table, $db_query, $AddKey, $idname, $id);
                                        $func->log_event(t('Eintrag #%1 in Tabelle "%2" geändert', array($id, $config['database']['prefix'] . $table)), 1, '', $this->LogID);
                                        $addUpdSuccess = $id;
                                    } else {
                                        $DBInsertQuery = $db_query;
                                        if ($this->AdditionalKey != '') {
                                            $DBInsertQuery .= ', '. $this->AdditionalKey;
                                        }

                                        if ($this->AddInsertControllField) {
                                            $DBInsertQuery .= ', '. $idname .' = '. (int)$id;
                                        }
                                        $db->qry("INSERT INTO %prefix%%plain% SET %plain%", $table, $DBInsertQuery);
                                        $id = $db->insert_id();
                                        $this->insert_id = $id;
                                        $func->log_event(t('Eintrag #%1 in Tabelle "%2" eingefügt', array($id, $config['database']['prefix'] . $table)), 1, '', $this->LogID);
                                        $addUpdSuccess = $id;
                                    }
                                }
                            }
                        }

                        if ($this->AdditionalDBUpdateFunction) {
                            $addUpdSuccess = call_user_func($this->AdditionalDBUpdateFunction, $id);
                        }

                        if ($addUpdSuccess) {
                            if ($this->isChange) {
                                $func->confirmation(t('Die Daten wurden erfolgreich geändert.'), $_SESSION['mf_referrer'][$this->GetNumber()]);
                            } else {
                                $func->confirmation(t('Die Daten wurden erfolgreich eingefügt.'), $this->LinkBack);
                            }
                        }
                    }

                    if (isset($_SESSION['mf_referrer'][$this->GetNumber()])) {
                        unset($_SESSION['mf_referrer'][$this->GetNumber()]);
                    }
                    $sec->lock($table);

                    // Will be
                    //  1) return of AdditionalDBPreUpdateFunction if AddChangeCondition returns true
                    //  2) return of AdditionalDBUpdateFunction if set
                    //  3) Insert_id // Note, this will always return 0, if id field has no AUTO_INCREMENT option!
                    return $addUpdSuccess;
                }
                break;
        }

        return false;
    }

    /**
     * Returns the length of a Varchar-Field for length of a AddTextFieldRow()
     *
     * @param string    $string     Fieldname ($field['type'] = "varchar(10))
     * @return int
     */
    private function get_fieldlength($string)
    {
        if (!(strrpos($string, "varchar") === false)) {
            preg_match("/(varchar\()(\\d{1,3})(\))/i", $string, $treffer);
            return $treffer[2];
        }

        return 0;
    }
}
