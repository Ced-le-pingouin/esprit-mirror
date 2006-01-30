<?php

/*
** Fichier ................: modifier-menu.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 30/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sModaliteFenetre = (empty($HTTP_GET_VARS["modaliteFenetre"]) ? NULL : $HTTP_GET_VARS["modaliteFenetre"]);
$url_sMenu            = (empty($HTTP_GET_VARS["menu"]) ? NULL : $HTTP_GET_VARS["menu"]);

// ---------------------
// Menus
// ---------------------
if ($url_sMenu == "forum")
	include_once("modifier_forum-menu.inc.php");
else if ($url_sMenu == "sujet")
	include_once("modifier_sujet-menu.inc.php");
else if ($url_sMenu == "message")
	include_once("modifier_message-menu.inc.php");

// ---------------------
// Template
// ---------------------
$sBlockHead = NULL;

include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

