<?php

/*
** Fichier ................: mdp_oublier-menu.php
** Description ............:
** Date de création .......: 21/12/2004
** Dernière modification ..: 21/12/2004
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
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$aMenus = array();

switch ($url_sMenu)
{
	case "valider":
		$aMenus[] = array("Valider","top.oPrincipale().valider()",1);
		$aMenus[] = array("Annuler","top.close()",1);
		break;
}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

