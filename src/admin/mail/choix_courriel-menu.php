<?php

/*
** Fichier ................: choix_courriel-menu.php
** Description ............: 
** Date de création .......: 17/01/2005
** Dernière modification ..: 19/01/2005
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
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Initialiser
// ---------------------

// Insérer ces lignes dans l'en-tête de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

if (isset($url_sMenu) && $url_sMenu == "1")
{
	$aMenus[] = array("Valider","top.valider()");
	$aMenus[] = array("Annuler","top.close()");
}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

