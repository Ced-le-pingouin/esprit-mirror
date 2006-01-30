<?php

/*
** Fichier ................: mail-menu.php
** Description ............: 
** Date de cr�ation .......: 14/12/2004
** Derni�re modification ..: 14/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_sMenu = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Initialiser
// ---------------------

// Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();
$aMenus[] = array("Envoyer","top.oMailInfos().envoyer()");
$aMenus[] = array("Annuler","top.close()");

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

