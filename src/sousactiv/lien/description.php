<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: description.php
** Description ............:
** Date de création .......: 28/06/2004
** Dernière modification ..: 14/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_admin("awareness","awareness.inc.php",TRUE));

$oProjet = new CProjet();
$oProjet->initStatutsUtilisateur(TRUE);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_bEncadrer   = (empty($_GET["encadrer"]) ? FALSE : $_GET["encadrer"]);
$url_bIndirect   = (empty($_GET["indirect"]) ? FALSE : (bool)$_GET["indirect"]);

// ---------------------
// Initialiser
// ---------------------
$oIds = new CIds($oProjet->oBdd,$url_iTypeNiveau,$url_iIdNiveau);

$sStatutUtilisateur = phpString2js($oProjet->retTexteStatutUtilisateur());

if (!$url_bIndirect)
{
	$oProjet->modifierInfosSession(SESSION_FORM,$oIds->retIdForm(),FALSE);
	$oProjet->modifierInfosSession(SESSION_MOD,$oIds->retIdMod(),FALSE);
	$oProjet->modifierInfosSession(SESSION_UNITE,$oIds->retIdRubrique(),FALSE);
	$oProjet->modifierInfosSession(SESSION_ACTIV,$oIds->retIdActiv(),FALSE);
	$oProjet->modifierInfosSession(SESSION_SOUSACTIV,$oIds->retIdSousActiv(),FALSE);
	$oProjet->enregistrerInfosSession();
}

$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript">
<!--
function init() { top.changerStatutUtilisateur('{$sStatutUtilisateur}'); }
window.onload = init;
//-->
</script>
BLOC_HTML_HEAD;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("description.tpl",FALSE,TRUE));

// Bloc d'en-tête de la page html
$oBlocHtmlHead = new TPL_Block("BLOCK_HTML_HEAD",$oTpl);

if (TYPE_FORMATION == $url_iTypeNiveau)
	$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function init() { top.changerStatutUtilisateur('{$sStatutUtilisateur}'); }
window.onload = init;
//-->
</script>
BLOC_HTML_HEAD;
else
	$sBlocHtmlHead = <<<BLOC_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
BLOC_HTML_HEAD;

$oBlocHtmlHead->ajouter($sBlocHtmlHead);
$oBlocHtmlHead->afficher();

// Description
$oBlocDescription = new TPL_Block("BLOCK_DESCRIPTION",$oTpl);

// Awareness + description
$oAwareness = new TPL_Block("BLOCK_APPLET_AWARENESS",$oTpl);

$oSet_StyleDescrFormation = $oTpl->defVariable("SET_ENCADRER_DESCR_FORMATION");

$sStyleEncadrement = NULL;

switch ($url_iTypeNiveau)
{
	case TYPE_FORMATION:
		$oFormation = new CFormation($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oFormation->retNom(),ENT_COMPAT,"UTF-8");
		$sDescription = $oFormation->retDescr();
		$oAwareness->remplacer("{applet_awareness}",retAwarenessSpy($oFormation->retNom(),TRUE));
		$oAwareness->afficher();
		$sStyleEncadrement = " ".$oSet_StyleDescrFormation;
		unset($oFormation);
		break;
		
	case TYPE_RUBRIQUE:
		$oRubrique = new CModule_Rubrique($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oRubrique->retNom(),ENT_COMPAT,"UTF-8");
		$sDescription = $oRubrique->retDescr();
		$oAwareness->effacer();
		unset($oRubrique);
		break;
		
	case TYPE_SOUS_ACTIVITE:
		$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdNiveau);
		$sTitrePageHtml = htmlentities($oSousActiv->retNom(),ENT_COMPAT,"UTF-8");
		$sDescription = $oSousActiv->retDescr();
		$oAwareness->effacer();
		unset($oSousActiv);
		break;
		
	default:
		$sTitrePageHtml = NULL;
		$sDescription = NULL;
		$oAwareness->effacer();
}

if (isset($sDescription))
{
	$oBlocDescription->remplacer("{description.style.class}",$sStyleEncadrement);
	$oBlocDescription->remplacer("{description.texte}",convertBaliseMetaVersHtml($sDescription));
	$oBlocDescription->afficher();
}
else
	$oBlocDescription->effacer();

// {{{ Paramètres du tableau de bord
$asRechTpl = array("{tableaudebord.niveau.id}","{tableaudebord.niveau.type}");
$amReplTpl = array($oIds->retIdRubrique(),TYPE_RUBRIQUE);
$oTpl->remplacer($asRechTpl,$amReplTpl);
// }}}

$oTpl->remplacer("{html.title}",$sTitrePageHtml);

$oTpl->afficher();

$oProjet->terminer();

?>

