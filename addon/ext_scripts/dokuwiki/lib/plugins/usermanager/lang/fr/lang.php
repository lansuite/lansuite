<?php
/**
 * french language file
 *
 * @author     Guy Brand <gb@isis.u-strasbg.fr>
 */
 
// for admin plugins, the menu prompt to be displayed in the admin menu
// if set here, the plugin doesn't need to override the getMenuText() method
$lang['menu'] = 'Gestion des utilisateurs'; 
 
// custom language strings for the plugin
$lang['noauth']      = '(authentification utilisateur non disponible)';
$lang['nosupport']   = '(gestion utilisateur non supportée)';

$lang['badauth']     = 'mécanisme d\'authentification invalide';     // should never be displayed!

$lang["user_id"] = "Identifiant";
$lang["user_pass"] = "Mot de passe";
$lang["user_name"] = "Nom";
$lang["user_mail"] = "Courriel";
$lang["user_groups"] = "Groupes";

$lang["field"] = "Champ";
$lang["value"] = "Valeur";
$lang["add"] = "Ajouter";
$lang["delete"] = "Supprimer";
$lang['delete_selected'] = 'Supprimer la sélection';
$lang["edit"] = "Éditer";
$lang['edit_prompt'] = 'Éditer cet utilisateur';
$lang['modify']      = 'Enregistrer les modifications';
$lang['search']      = 'Rechercher';
$lang['search_prompt'] = 'Effectuer la recherche';
$lang['clear']       = 'Réinitialiser la recherche';
$lang['filter']      = 'Filtre';

$lang['summary']     = 'Affichage des utilisateurs %1$d-%2$d parmi %3$d trouvés. %4$d utilisateurs au total.';
$lang['nonefound']   = 'Aucun utilisateur trouvé %d utilisateurs au total.';
$lang['delete_ok']   = '%d utilisateurs effacés';
$lang['delete_fail'] = '%d effacement échoué.';
$lang['update_ok']   = 'utilisateur mis à jour avec succès';
$lang['update_fail'] = 'échec de la mise à jour utilisateur';
$lang['update_exists'] = 'échec du changement de nom d\'utilisateur,le nom spécifié (%s) existe déjà (toutes les autres modifications seront effectuées).';

$lang['start']  = 'Démarrage';
$lang['prev']   = 'Précédent';
$lang['next']   = 'Suivant';
$lang['last']   = 'Dernier';

// added after 2006-03-09 release
$lang['edit_usermissing'] = 'Utilisateur sélectionné non trouvé, cet utilisateur a peut-être été supprimé ou modifié ailleurs.';
$lang['user_notify'] = 'Notifier l\'utilisateur';
$lang['note_notify'] = 'Envoi de notification par courriel uniquement lorsqu\'un nouveau mot de passe est attribué à l\'utilisateur.';
$lang['note_group'] = 'Les nouveaux utilisateurs seront ajoutés au groupe par défaut (%s) si aucun groupé n\'est spécifié.';
$lang['add_ok'] = 'Utilisateur ajouté avec succès';
$lang['add_fail'] = 'Echec de l\'ajout de l\'utilisateur';
$lang['notify_ok'] = 'Courriel  de notification expédié';
$lang['notify_fail'] = 'Echec de l\'expédition du courriel de notification';

