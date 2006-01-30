<?php

/*
** Fichier ................: changer_statut_menu.php
** Description ............:
** Date de création .......: 09/10/2002
** Dernière modification ..: 11/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Initialiser
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus   = array();
$aMenus[] = array("Valider","top.frames['Principale'].document.forms[0].submit()",1);
$aMenus[] = array("Annuler","top.close()",1);

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

