<?php

/*
** Fichier ................: zone_menu.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 18/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initModuleCourant();
include_once("zone_menu.inc.php");

// ---------------------
// Récupérer la description de la formation
// ---------------------
$iLongueurDescr = 0;

if ($iIdForm > 0 && $iIdMod == 0)
{
	$oFormation = new CFormation($oProjet->oBdd,$iIdForm);
	
	if (isset($oFormation) && is_object($oFormation))
		$iLongueurDescr = strlen($oFormation->retDescr());
}

// --------------------------
// Initialiser
// --------------------------
$sParamsUrl = "?idForm={$iIdForm}&idMod={$iIdMod}&idUnite=0&idSousActiv=0&idActiv=0";

// Afficher la description ou le cours de la formation
$sSrcFramePrincipale = ($iLongueurDescr > 0
	? dir_sousactiv(LIEN_PAGE_HTML,"description.php{$sParamsUrl}&idNiveau={$iIdForm}&typeNiveau=".TYPE_FORMATION)
	: "zone_menu.php{$sParamsUrl}");

$sSrcFrameMenu = "menu.php{$sParamsUrl}";

// --------------------------
// Template
// --------------------------
$oTpl = new Template(dir_theme("zone_menu-index.tpl",FALSE,TRUE));

// {{{ Titre de la page html
$oTpl->remplacer("{titre_page_html}",$oProjet->retNom());
// }}}

// {{{ Frames
$oTpl->remplacer("{src_frame_titre}","zone_menu-titre.php");
$oTpl->remplacer("{src_frame_gauche}","zone_menu-menu.php{$sParamsUrl}");
$oTpl->remplacer("{src_frame_principale}",$sSrcFramePrincipale);
$oTpl->remplacer("{src_frame_menu}",$sSrcFrameMenu);
// }}}

$oTpl->afficher();

$oProjet->terminer();

?>

