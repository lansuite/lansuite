<?php
/**
 * Italian language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 * @author     Silvia Sargentoni <polinnia@tin.it>
 */

$lang['menu'] = 'Gestione Plugin'; 

// custom language strings for the plugin
$lang['download'] = "Scarica e installa un nuovo plugin";
$lang['manage'] = "Plugin installati";

$lang['btn_info'] = 'info';
$lang['btn_update'] = 'aggiorna';
$lang['btn_delete'] = 'cancella';
$lang['btn_settings'] = 'configurazione';
$lang['btn_download'] = 'Scarica';
$lang['btn_enable'] = 'Salva';

$lang['url']              = 'URL';

$lang['installed']        = 'Installato:';
$lang['lastupdate']       = 'Ultimo aggiornamento:';
$lang['source']           = 'Origine:';
$lang['unknown']          = 'sconosciuto';

// ..ing = header message
// ..ed = success message

$lang['updating']         = 'Aggiornamento in corso ...';
$lang['updated']          = 'Aggiornamento plugin %s riuscito';
$lang['updates']          = 'Aggiornamento dei seguenti plugin riuscito:';
$lang['update_none']      = 'Nessun aggiornamento trovato.';

$lang['deleting']         = 'Cancellazione in corso ...';
$lang['deleted']          = 'Plugin %s cancellato.';

$lang['downloading']      = 'Download in corso ...';
$lang['downloaded']       = 'Installazione plugin %s riuscita';
$lang['downloads']        = 'Installazione dei seguenti plugin riuscita:';
$lang['download_none']    = 'Nessun plugin trovato, oppure si è verificato un problema sconosciuto durante il download e l\'installazione.';

// info titles
$lang['plugin']           = 'Plugin:';
$lang['components']       = 'Componenti';
$lang['noinfo']           = 'Questo plugin non ha fornito alcuna informazione, potrebbe non essere valido.';
$lang['name']             = 'Nome:';
$lang['date']             = 'Data:';
$lang['type']             = 'Tipo:';
$lang['desc']             = 'Descrizione:';
$lang['author']           = 'Autore:';
$lang['www']              = 'Web:';
    
// error messages
$lang['error']            = 'Si è verificato un errore sconosciuto.';
$lang['error_download']   = 'Impossibile scaricare il plugin: %s';
$lang['error_badurl']     = 'Possibile URL non corretta - impossibile determinare il nome del file dalla URL fornita';
$lang['error_dircreate']  = 'Impossibile creare la directory temporanea dove scaricare il file';
$lang['error_decompress'] = 'Impossibile decomprimere il file scaricato. '.
                            'Questo potrebbe essere il risultato di un download incompleto, in tal caso dovresti provare di nuovo; '.
                            'oppure il formato di compressione potrebbe essere sconosciuto, in questo caso è necessario'.
                            'scaricare e installare il plugin manualmente.';
$lang['error_copy']       = 'Si è verificato un errore nella copia di un file durante l\'installazione del plugin '.
                            '<em>%s</em>: il disco potrebbe essere pieno oppure i permessi di accesso al file potrebbero non essere corretti. '.
                            'Il plugin potrebbe essere stato installato solo parzialmente, questo potrebbe causare instabilità al sistema.';
$lang['error_delete']     = 'Si è verificato un errore durante la cancellazione del plugin <em>%s</em>.  '.
                            'Molto probabilmente i permessi di acesso ai file o alla directory non sono sufficienti';

//Setup VIM: ex: et ts=4 enc=utf-8 :
