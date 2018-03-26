<?php

class masterdelete
{
    /**
     * @var array
     */
    public $References = [];

    /**
     * @var array
     */
    public $SubReferences = [];

    /**
     * @var array
     */
    public $DeleteIfEmpty = [];

    /**
     * @var int
     */
    public $LogID = 0;

    /**
     * @param string    $table
     * @param string    $idname
     * @param int       $id
     * @return bool|int|mysqli_result
     */
    public function DoDelete($table, $idname, $id)
    {
        global $func, $db, $config;
    
        // Get key to master table
        foreach ($this->DeleteIfEmpty as $key => $val) {
            $row = $db->qry_first('SELECT %plain% FROM %prefix%%plain% WHERE %plain% = %int%', $val, $table, $idname, $id);
            $MasterKey[$key] = $row[$val];
        }

        // Check if attached tables are denied
        $res = $db->qry('SELECT pri_table, pri_key, on_delete FROM %prefix%ref WHERE foreign_table = %string% AND foreign_key = %string%', $table, $idname);
        while ($row = $db->fetch_array($res)) {
            $row2 = $db->qry_first('SELECT COUNT(*) AS cnt FROM %prefix%%plain% WHERE %plain% = %int%', $row['pri_table'], $row['pri_key'], $id);

            if ($row2['cnt'] and $row['on_delete'] == 'DENY') {
                $func->information(t('Dieser Eintrag kann momentan leider nicht gelöscht werden, da Einträge aus folgender Tabelle noch darauf referenzieren') .
                ': '. HTML_NEWLINE . HTML_NEWLINE . HTML_NEWLINE. $row['pri_table'] .'.'. $row['pri_key'] .' ('. $row2['cnt'] .'x)', $_SESSION['md_referrer']);

                return false;
            }
        }
    
        // Delete main table
        $res = $db->qry('DELETE FROM %prefix%%plain% WHERE %plain% = %string%', $table, $idname, $id);
        if ($res) {
            // Delete master tables, if content is now missing
            foreach ($this->DeleteIfEmpty as $key => $val) {
                if ($val == '') {
                    $val = $idname;
                }

                $row = $db->qry_first('SELECT 1 AS found FROM %prefix%%plain% WHERE %plain% = %int%', $table, $val, $MasterKey[$key]);
                if (!$row['found']) {
                    $db->qry('DELETE FROM %prefix%%plain% WHERE %plain% = %int%', $key, $val, $MasterKey[$key]);
                }
            }

            // Delete all attached tables
            $res = $db->qry('SELECT pri_table, pri_key, on_delete FROM %prefix%ref WHERE foreign_table = %string% AND foreign_key = %string%', $table, $idname);
            while ($row = $db->fetch_array($res)) {
                switch ($row['on_delete']) {
                    case 'ASK_DELETE':
                    case 'DELETE':
                        $this->DoDelete($row['pri_table'], $row['pri_key'], $id);
                        break;
                    case 'ASK_SET0':
                    case 'SET0':
                        $db->qry("UPDATE %prefix%%plain% SET %plain% = 0 WHERE %plain% = %int%", $row['pri_table'], $row['pri_key'], $row['pri_key'], $id);
                        break;
                }
            }
            if ($table != 'log') {
                $func->log_event(t('Eintrag #%1 aus Tabelle "%2" gelöscht', array($id, $config['database']['prefix'] . $table)), 1, '', $this->LogID);
            }

        } elseif ($table != 'log') {
            $func->log_event(t('Fehler beim Löschen von #%1 aus Tabelle "%2"', array($id, $config['database']['prefix'] . $table)), 3, '', $this->LogID);
        }
    
        return $res;
    }

    /**
     * @param string    $table
     * @param string    $idname
     * @param int       $id
     * @return bool|int|mysqli_result
     */
    public function Delete($table, $idname, $id)
    {
        global $framework, $func, $db;
        
        $CurentURLBase = $framework->get_clean_url_query('base');
        $CurentURLBase = str_replace('&md_step=2', '', $CurentURLBase);
        $CurentURLBase = preg_replace('#&'. $idname .'=[0-9]*#si', '', $CurentURLBase);
        
        // Print confirmation message
        if (!$_POST['confirmed']) {
            if ($func->internal_referer != 'index.php?'.$_SERVER['QUERY_STRING']) {
                $_SESSION['md_referrer'] = $func->internal_referer;
            }
            
            $refFieldsDelete = '';
            $refFieldsSet0 = '';
            $refFieldsDeny = '';
            $res = $db->qry('SELECT pri_table, pri_key, on_delete FROM %prefix%ref WHERE foreign_table = %string% AND foreign_key = %string%', $table, $idname);
            while ($row = $db->fetch_array($res)) {
                $row2 = $db->qry_first('SELECT COUNT(*) AS cnt FROM %prefix%%plain% WHERE %plain% = %int%', $row['pri_table'], $row['pri_key'], $id);
                
                if ($row2['cnt']) {
                    if ($row['on_delete'] == 'ASK_DELETE') {
                        $refFieldsDelete .= HTML_NEWLINE. $row['pri_table'] .'.'. $row['pri_key'] .' ('. $row2['cnt'] .'x)';

                    } elseif ($row['on_delete'] == 'ASK_SET0') {
                        $refFieldsSet0 .= HTML_NEWLINE. $row['pri_table'] .'.'. $row['pri_key'] .' ('. $row2['cnt'] .'x)';

                    } elseif ($row['on_delete'] == 'DELETE') {
                        // No additional question needed

                    } elseif ($row['on_delete'] == 'SET0') {
                        // No additional question needed

                    } else {
                        $refFieldsDeny .= HTML_NEWLINE. $row['pri_table'] .'.'. $row['pri_key'] .' ('. $row2['cnt'] .'x)';
                    }
                }
            }

            if ($refFieldsDeny) {
                $func->information(t('Dieser Eintrag kann momentan leider nicht gelöscht werden, da Einträge aus folgenden Tabellen noch darauf referenzieren') .
                ': '. HTML_NEWLINE . HTML_NEWLINE . $refFieldsDeny, $_SESSION['md_referrer']);

            } else {
                $q = t('Bist du sicher, dass du diesen Eintrag löschen möchtest?');
                if ($refFieldsDelete) {
                    $q .= HTML_NEWLINE . HTML_NEWLINE .'<b>'. t('Achtung') .'</b>: '.
                    t('Folgende Einträge referenzieren noch auf diesen Eintrag:') .
                    HTML_NEWLINE .'<b>'. t('Diese Eintrag werden mitgelöscht!') .'</b>'. HTML_NEWLINE .
                    $refFieldsDelete;
                }

                if ($refFieldsSet0) {
                    $q .= HTML_NEWLINE . HTML_NEWLINE .'<b>'. t('Achtung') .'</b>: '.
                    t('Folgende Einträge referenzieren noch auf diesen Eintrag:') .
                    HTML_NEWLINE .'<b>'. t('Bei Einträge in dieser Tabelle wird die Zuordnung aufgelöst!') .'</b>'. HTML_NEWLINE .
                    $refFieldsSet0;
                }
                $func->question($q, $CurentURLBase. '&'. $idname .'='. $id, $_SESSION['md_referrer']);
            }

            return false;
        
        // Action
        } else {
            $res = $this->DoDelete($table, $idname, $id);

            if ($res) {
                $func->confirmation(t('Der Eintrag wurde erfolgreich gelöscht'), $_SESSION['md_referrer']);

            } else {
                $func->information(t('Der Eintrag konnte nicht gelöscht werden'), $_SESSION['md_referrer']);
            }

            unset($_SESSION['md_referrer']);

            return $res;
        }
    }

    /**
     *
     * TODO Question for ASK_DELETE AND ASK_SET0
     *
     * @param string    $table
     * @param string    $idname
     * @return bool
     */
    public function MultiDelete($table, $idname)
    {
        global $func;

        $failed = '';
        if ($_POST['action']) {
            foreach ($_POST['action'] as $key => $val) {
                $res = $this->DoDelete($table, $idname, $key);
                if (!$res) {
                    $failed .= HTML_NEWLINE . '#'. $key;
                }
            }

            if ($failed != '') {
                $func->information(t('Die folgenden Einträge konnte nicht gelöscht werden').':'.$failed);

            } else {
                $func->confirmation(t('Die Einträge wurde erfolgreich gelöscht'));
            }
        } else {
            $func->information(t('Es wurden keine Einträge selektiert'));
        }
    
        return !$failed;
    }
}
