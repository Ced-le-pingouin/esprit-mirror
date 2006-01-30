<?php

/*
** Fichier ................: dossier_formations_event-menu.php
** Description ............: 
** Date de cr�ation .......: 24/05/2005
** Derni�re modification ..: 30/05/2005
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
$url_sEvent = (empty($HTTP_GET_VARS["event"]) ? NULL : $HTTP_GET_VARS["event"]);

// ---------------------
// Initialiser
// ---------------------

// {{{ Ins�rer ces lignes dans l'en-t�te de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;
// }}}

// {{{ Composer le menu
$aMenus = array();

if ("ajout" == $url_sEvent || "modif" == $url_sEvent)
{
	$aMenus[] = array("Valider","top.oPrincipale().valider()");
	$aMenus[] = array("Annuler","top.close()");
}
else if ("supp" == $url_sEvent)
{
	$aMenus[] = array("Oui","top.oPrincipale().valider()");
	$aMenus[] = array("Non","top.close()");
}
// }}}

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

