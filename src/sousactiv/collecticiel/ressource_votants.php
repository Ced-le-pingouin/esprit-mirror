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
** Fichier ................: ressource_votants.php
** Description ............: 
** Date de création .......: 26/11/2004
** Dernière modification ..: 07/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();
$oProjet->initEquipe(TRUE);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdResSA  = (empty($_GET["idResSA"]) ? 0 : $_GET["idResSA"]);
$url_iIdEquipe = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);
$url_aiIdPers  = (empty($_GET["idPers"]) ? NULL : $_GET["idPers"]);

// ---------------------
// Initialiser
// ---------------------
$g_bPeutEvaluer = $oProjet->verifPermission("PERM_EVALUER_COLLECTICIEL");

// ---------------------
// Appliquer les changements
// ---------------------
if ($g_bPeutEvaluer && is_array($url_aiIdPers))
{
	$oProjet->oSousActivCourante->initEquipe($url_iIdEquipe,TRUE);
	
	foreach ($url_aiIdPers as $iIdPers)
		$oProjet->oSousActivCourante->voterPourRessource($url_iIdResSA,$iIdPers);
}

$oRSA = new CRessourceSousActiv($oProjet->oBdd,$url_iIdResSA);
$iNbVotants = $oRSA->initVotants();

$oRSA->initEquipe(TRUE);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ressource_votants.tpl");

$oBlocTableVotants          = new TPL_Block("BLOCK_TABLE_VOTANTS",$oTpl);
$oBlocTableVotantsManquants = new TPL_Block("BLOCK_TABLE_VOTANTS_MANQUANTS",$oTpl);
$oBlocPasVotantTrouve       = new TPL_Block("BLOCK_PAS_VOTANT",$oTpl);

$sSetSexe = array(
	$oTpl->defVariable("SET_SEXE_FEMININ")
	, $oTpl->defVariable("SET_SEXE_MASCULIN"));

$sSetCourriel = array (
	$oTpl->defVariable("SET_SANS_COURRIEL")
	, $oTpl->defVariable("SET_COURRIEL"));

if ($iNbVotants > 0)
{
	$aiIdVotants = array();
	
	$oBlocVotant = new TPL_Block("BLOCK_VOTANT",$oBlocTableVotants);
	$oBlocVotant->beginloop();
	
	foreach ($oRSA->aoVotants as $oVotant)
	{
		$sEmail        = $oVotant->retEmail();
		$sIcones       = $sSetCourriel[emailValide($sEmail)];
		$aiIdVotants[] = $oVotant->retId();
		
		$oBlocVotant->nextloop();
		
		$oBlocVotant->remplacer("{personne.sexe}",$sSetSexe[($oVotant->retSexe() == "M")]);
		$oBlocVotant->remplacer("{personne.nom}",$oVotant->retNom());
		$oBlocVotant->remplacer("{personne.prenom}",$oVotant->retPrenom());
		$oBlocVotant->remplacer("{outil.courriel}",$sIcones);
		$oBlocVotant->remplacer("{personne.pseudo}",$oVotant->retPseudo());
		$oBlocVotant->remplacer("{personne.courriel}",$sEmail);
	}
	
	if ($g_bPeutEvaluer
		&& $oRSA->oEquipe->retNbMembres() > $iNbVotants)
	{
		$oBlocMembreNonVotant = new TPL_Block("BLOCK_VOTANT_MANQUANT",$oBlocTableVotantsManquants);
		$oBlocMembreNonVotant->beginLoop();
		
		foreach ($oRSA->oEquipe->aoMembres as $oMembre)
		{
			$iIdPers = $oMembre->retId();
			
			if (in_array($iIdPers,$aiIdVotants))
				continue;
			
			$sEmail  = $oMembre->retEmail();
			$sIcones = $sSetCourriel[emailValide($sEmail)];
			
			$oBlocMembreNonVotant->nextLoop();
			
			$oBlocMembreNonVotant->remplacer("{personne.id}",$iIdPers);
			$oBlocMembreNonVotant->remplacer("{personne.sexe}",$sSetSexe[($oMembre->retSexe() == "M")]);
			$oBlocMembreNonVotant->remplacer("{personne.nom}",$oMembre->retNom());
			$oBlocMembreNonVotant->remplacer("{personne.prenom}",$oMembre->retPrenom());
			$oBlocMembreNonVotant->remplacer("{outil.courriel}",$sIcones);
			$oBlocMembreNonVotant->remplacer("{personne.pseudo}",$oMembre->retPseudo());
			$oBlocMembreNonVotant->remplacer("{personne.courriel}",$sEmail);
		}
		
		$oBlocMembreNonVotant->afficher();
		
		$oBlocTableVotantsManquants->remplacer(array("{ressource.id}","{equipe.id}"), array($url_iIdResSA,$url_iIdEquipe));
		$oBlocTableVotantsManquants->afficher();
	}
	else
		$oBlocTableVotantsManquants->effacer();
	
	$oBlocVotant->afficher();
	$oBlocTableVotants->afficher();
	
	$oBlocPasVotantTrouve->effacer();
}
else
{
	$oBlocTableVotants->effacer();
	$oBlocPasVotantTrouve->afficher();
	
	$oBlocTableVotantsManquants->effacer();
}

$oTpl->afficher();

$oProjet->terminer();

?>

