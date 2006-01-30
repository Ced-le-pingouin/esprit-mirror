<?php

/*
** Fichier ................: intitule-modif.php
** Description ............: 
** Date de création .......: 15/04/2003
** Dernière modification ..: 16/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Remarques ..............: Les personnes qui ont le droit de modifier
** ......................... TOUTES LES SESSIONS ont la possibilités de
** ......................... modifier/supprimer toutes les intitulés.
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

$oProjet->verifPeutUtiliserOutils();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdIntitule = $url_sNomIntitule = NULL;
$url_iTypeIntitule = (isset($HTTP_GET_VARS["TYPE_INTITULE"]) ? $HTTP_GET_VARS["TYPE_INTITULE"] : TYPE_MODULE);

$sCorpFonctionJSInit = NULL;

// ---------------------
// Template
// ---------------------
$oTpl = new Template("intitule-modif.tpl");

// Formulaire
$oTpl->remplacer("{intitule->id}",$url_iIdIntitule);
$oTpl->remplacer("{intitule->type}",$url_iTypeIntitule);

// Menu
$oBloc_Menu = new TPL_Block("BLOCK_MENU",$oTpl);
$oSet_Menu_Separateur = $oTpl->defVariable("SET_MENU_SEPARATEUR");
$oSet_Menu_Ajouter = $oTpl->defVariable("SET_MENU_AJOUTER");
$oSet_Menu_Modifier = $oTpl->defVariable("SET_MENU_MODIFIER");
$oSet_Menu_Supprimer = $oTpl->defVariable("SET_MENU_SUPPRIMER");

// Afficher le menu
$sMenus = $oSet_Menu_Ajouter;

if ($oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
	$sMenus .= $oSet_Menu_Separateur
		.$oSet_Menu_Modifier
		.$oSet_Menu_Separateur
		.$oSet_Menu_Supprimer;

$oBloc_Menu->ajouter($sMenus);
$oBloc_Menu->afficher();

// Afficher le template
$oTpl->afficher();

$oProjet->terminer();
?>

