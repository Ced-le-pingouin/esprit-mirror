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
** Fichier ................: dossier_formations_event.php
** Description ............:
** Date de création .......: 24/05/2005
** Dernière modification ..: 29/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

error_reporting(E_ALL);

require_once("globals.inc.php");
require_once(dir_database("dossier_formations.tbl.php"));

$oProjet = new CProjet();

$g_iIdUtilisateur = $oProjet->retIdUtilisateur();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sEvent          = (empty($_GET["event"]) ? NULL : $_GET["event"]);
$url_iIdDossierForms = (empty($_GET["idDossierForms"]) ? NULL : $_GET["idDossierForms"]);

if (empty($url_sEvent) || $g_iIdUtilisateur < 1)
	exit();

// ---------------------
// Appliquer les changements
// ---------------------
list(,$sEvent) = explode(":","{$url_sEvent}:");

if ("ajout" == $sEvent || "modif" == $sEvent)
{
	$sNomDossier = (empty($_GET["nomDossierForms"]) ? "Dossier sans nom" : $_GET["nomDossierForms"]);
	
	$oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);
	
	if ($url_iIdDossierForms < 1)
		$oDossierForms->initAjouter($g_iIdUtilisateur);
	
	$oDossierForms->defNom($sNomDossier);
	$oDossierForms->defNumOrdre($_GET["ordreDossierForms"]);
	$oDossierForms->defPremierDossier((isset($_GET["premierDossierForms"]) && "on" == $_GET["premierDossierForms"]));
	$oDossierForms->defVisible((isset($_GET["visibleDossierForms"]) &&"on" == $_GET["visibleDossierForms"]));
	
	if ($url_iIdDossierForms > 0)
		$oDossierForms->enregistrer();
	else
		$url_iIdDossierForms = $oDossierForms->ajouter();
}
else if ("supp" == $sEvent)
{
	$oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);
	$oDossierForms->effacer();
	$url_iIdDossierForms = 0;
}

// Mettre à jour le cookie
if (!empty($sEvent))
{
	if ($oProjet->retInfosSession(SESSION_DOSSIER_FORMS) != $url_iIdDossierForms)
		$oProjet->modifierInfosSession(SESSION_DOSSIER_FORMS,$url_iIdDossierForms,TRUE);
	
	exit(str_replace("{dossier_forms.id}",$url_iIdDossierForms,file_get_contents("dossier_formations_event-recharger.htm")));
}

// ---------------------
// Initialiser
// ---------------------
if ($url_iIdDossierForms > 0)
{
	$oDossierForms = new CDossierForms($oProjet->oBdd,$url_iIdDossierForms);
	$iNbNumOrdre = $oDossierForms->retNbDossierForms();
}
else
{
	$oDossierForms = new CDossierForms($oProjet->oBdd);
	$oDossierForms->initAjouter($g_iIdUtilisateur);
	$iNbNumOrdre = $oDossierForms->retNbDossierForms()+1;
}

$g_asRechTpl = array("{dossier.nom}","{dossier.premier.checked}");

// ---------------------
// Template
// ---------------------
$oTpl = new Template("dossier_formations_event.tpl");

// {{{ Ajouter/modifier un dossier
$oBlocDossier = new TPL_Block("BLOCK_MODIFIER_DOSSIER",$oTpl);

if ("ajout" == $url_sEvent || "modif" == $url_sEvent)
{
	$bPremier = $oDossierForms->retPremierDossier();
	$bVisible = $oDossierForms->retVisible();
	
	$amRemplTpl = array(
		$oDossierForms->retNom()
		, ($bPremier ? " checked=\"checked\"" : NULL)
	);
	
	$oBlocDossier->remplacer($g_asRechTpl,$amRemplTpl);
	
	$oBlocNumeroOrdre = new TPL_Block("BLOCK_NUMERO_ORDRE",$oBlocDossier);
	$oBlocNumeroOrdre->beginLoop();
	
	$g_asRechTpl = array("{dossier.numero_ordre}","{dossier.numero_ordre.selected}");
	
	$iNumOrdre = $oDossierForms->retNumOrdre();
	
	for ($i=1; $i<=$iNbNumOrdre; $i++)
	{
		$oBlocNumeroOrdre->nextLoop();
		$oBlocNumeroOrdre->remplacer($g_asRechTpl,array($i,($i == $iNumOrdre ? " selected=\"selected\"" : NULL)));
	}
	
	$oBlocNumeroOrdre->afficher();
	
	$oBlocDossier->afficher();
}
else
	$oBlocDossier->effacer();
// }}}

// {{{ Supprimer un dossier
$oBlocDossier = new TPL_Block("BLOCK_SUPPRIMER_DOSSIER",$oTpl);

if ("supp" == $url_sEvent)
{
	$oBlocDossier->remplacer("{dossier.nom}",emb_htmlentities($oDossierForms->retNom()));
	$oBlocDossier->afficher();
}
else
	$oBlocDossier->effacer();
// }}}

$oTpl->remplacer("{dossier_formations.id}",$url_iIdDossierForms);
$oTpl->remplacer("{inputs.event.value}",$url_sEvent);

$oTpl->afficher();

$oProjet->terminer();

?>

