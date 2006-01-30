<?php

/*
** Fichier ................: liste_inscrits-menu.php
** Description ............:
** Date de création .......: 02/09/2004
** Dernière modification ..: 31/05/2005
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
$url_bMenu      = (empty($HTTP_GET_VARS["menu"]) ? FALSE : (bool)$HTTP_GET_VARS["menu"]);
$url_iIdForm    = (empty($HTTP_GET_VARS["idForm"]) ? 0 : $HTTP_GET_VARS["idForm"]);
$url_iIdMod     = (empty($HTTP_GET_VARS["idMod"]) ? 0 : $HTTP_GET_VARS["idMod"]);
$url_iIdStatuts = (empty($HTTP_GET_VARS["idStatuts"]) ? NULL : $HTTP_GET_VARS["idStatuts"]);

// ---------------------
// Initialiser
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$iIdxMenu = 1;
$aMenus = array();

if ($url_bMenu)
{
	if ($url_iIdForm > 0)
		$aMenus[] = array("Inscription","gestion_utilisateur('{$url_iIdForm}')",$iIdxMenu++,"text-align: left;");
	
	if ($url_iIdMod > 0)
		$aMenus[] = array("Envoi courriel","choix_courriel('?idStatuts={$url_iIdStatuts}')",$iIdxMenu++,"text-align: center;");
}

$aMenus[] = array("Fermer","top.close()",$iIdxMenu);

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

