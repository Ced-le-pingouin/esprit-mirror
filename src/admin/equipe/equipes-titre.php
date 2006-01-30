<?php

/*
** Fichier ................: equipes-index.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 17/08/2004
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
if (isset($HTTP_GET_VARS["TP"]))
	$url_sTitrePrincipal = $HTTP_GET_VARS["TP"];
else
	$url_sTitrePrincipal = "&nbsp;";

// ---------------------
// Initialiser
// ---------------------
$sBlockHead = "<link type=\"text/css\" rel=\"stylesheet\" href=\"theme://equipes.css\">";

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-titre.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlockHead);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_principal}",$url_sTitrePrincipal);
$oTpl->remplacer("{sous_titre}","");

$oTpl->afficher();

?>
