<?php
/**
 * french language file
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Guy Brand <gb@isis.u-strasbg.fr>
 */

$lang['menu'] = 'Gérer les plugins'; 

// custom language strings for the plugin
$lang['refresh'] = "Actualiser la liste des plugins installés";
$lang['refresh_x'] = "Utiliser cette option si vous avez altéré manuellement un plugin"; 
$lang['download'] = "Télécharger et installer un nouveau plugin";
$lang['manage'] = "Plugins installés";

$lang['btn_info'] = 'info';
$lang['btn_update'] = 'update';
$lang['btn_delete'] = 'effacer';
$lang['btn_settings'] = 'paramètres';
$lang['btn_refresh'] = 'Actualiser';
$lang['btn_download'] = 'Ramener';
$lang['btn_enable'] = 'Sauver';

$lang['url'] = 'URL';
//$lang[''] = '';

$lang['installed'] = 'Installé :';
$lang['lastupdate'] = 'Dernière mise à jour:';
$lang['source'] = 'Source :';
$lang['unknown'] = 'inconnu';

// ..ing = header message
// ..ed = success message

$lang['refreshing'] = 'Actualisation ...';
$lang['refreshed'] = 'Actualisation du plugin terminée.';

$lang['updating'] = 'Mise à jour ...';
$lang['updated'] = 'Plugin %s mis à jour avec succès';
$lang['updates'] = 'Les plugins suivant ont été mis à jour avec succès';
$lang['update_none'] = 'Aucune mise à jour n\'a été trouvée.';

$lang['deleting'] = 'Suppression ...';
$lang['deleted'] = 'Plugin %s supprimé.';

$lang['downloading'] = 'Téléchargement ...';
$lang['downloaded'] = 'Plugin %s installé avec succès';
$lang['downloads'] = 'Les plugins suivant ont été installés avec succès:';
$lang['download_none'] = 'Aucun plugin trouvé, ou un problème inconnu est survenu durant le téléchargement et l\'installation.';

// info titles
$lang['plugin'] = 'Plugin :';
$lang['components'] = 'Composants';
$lang['noinfo'] = 'Ce plugin n\'a transmis aucune information, il pourrait être invalide.';
$lang['name'] = 'Nom :';
$lang['date'] = 'Date :';
$lang['type'] = 'Type :';
$lang['desc'] = 'Description :';
$lang['author'] = 'Auteur :';
$lang['www'] = 'Web :';
    
// error messages
$lang['error'] = 'Une erreur inconnue est survenue.';
$lang['error_download'] = 'Impossible de télécharger le fichier du plugin: %s';
$lang['error_badurl'] = 'URL suspecte - impossible de déterminer le nom du fichier à partir de l\'URL';
$lang['error_dircreate'] = 'Impossible de créer le répertoire temporaire pour réceptionner le téléchargement';
$lang['error_decompress'] = 'Le gestionnaire de plugin était incapable de décompresser le fichier téléchargé. '.
            'Ceci peut être le résultat d\'un mauvais téléchargement, auquel cas vous devriez réessayer ; '.
            'ou bien le format de compression est inconnu, auquel cas vous devez télécharger et installer le plugin manuellement.';
$lang['error_copy'] = 'Une erreur de copie est survenue lors de l\'installation des fichiers du plugin <em>%s</em>: '.
            'votre disque est peut-être plein ou les droits d\'accès au fichier incorrects. '.
            'Il a pu en résulter une installation partielle du plugin rendant votre installation du wiki instable.';
$lang['error_delete'] = 'Une erreur est survenue à la suppression du plugin <em>%s</em>.  '.
            'La raison la plus probable est l\'insuffisance des droits sur les fichiers ou le répertoire';    

//Setup VIM: ex: et ts=2 enc=utf-8 :
