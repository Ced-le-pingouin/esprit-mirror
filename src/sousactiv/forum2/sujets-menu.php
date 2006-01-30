<?php

/*
** Fichier ................: sujets-menu.php
** Description ............: 
** Date de création .......: 24/11/2004
** Dernière modification ..: 24/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum  = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);
$url_iIdSujet  = (empty($HTTP_GET_VARS["idSujet"]) ? 0 : $HTTP_GET_VARS["idSujet"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sujets-menu.tpl");

$oBlocSujetMenu = new TPL_Block("BLOCK_SUJET_MENU",$oTpl);
$sAjtSujetParEquipes = $oBlocSujetMenu->defVariable("VAR_NOUVEAU_SUJET_EQUIPES");

$sSeparateurMenu = $oBlocSujetMenu->defVariable("VAR_MENU_SEPARATEUR");

$sAjtSujet = $oBlocSujetMenu->defVariable("VAR_NOUVEAU_SUJET");
$sModSujet = $oBlocSujetMenu->defVariable("VAR_MODIFIER_SUJET");
$sSupSujet = $oBlocSujetMenu->defVariable("VAR_SUPPRIMER_SUJET");

$oBlocSujetMenu->afficher();

$oTpl->remplacer("{sujet->id}",$url_iIdSujet);

$oTpl->afficher();

?>

