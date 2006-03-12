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
** Fichier ................: ass_multiple-inscrits.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 30/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_INSCRIPTION");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForm   = (empty($HTTP_GET_VARS["ID_FORM"]) ? 0 : $HTTP_GET_VARS["ID_FORM"]);
$url_iIdStatut = (empty($HTTP_GET_VARS["STATUT_PERS"]) ? 0 : $HTTP_GET_VARS["STATUT_PERS"]);

// ---------------------
// Initialiser la formation
// ---------------------
$oFormation = new CFormation($oProjet->oBdd,$url_iIdForm);

// Rechercher tous les modules
$oFormation->initModules();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ass_multiple-inscrits.tpl");

$oBloc_Module = new TPL_Block("BLOCK_MODULE",$oTpl);
$oBloc_Module->beginLoop();


foreach ($oFormation->aoModules as $oModule)
{
	if ($url_iIdStatut == STATUT_PERS_CONCEPTEUR)
	{
		$oModule->initConcepteurs();
		$aoPersonnes = &$oModule->aoConcepteurs;
	}
	else if ($url_iIdStatut == STATUT_PERS_TUTEUR)
	{
		$oModule->initTuteurs();
		$aoPersonnes = &$oModule->aoTuteurs;
	}
	else if ($url_iIdStatut == STATUT_PERS_ETUDIANT)
	{
		$oModule->initInscrits();
		$aoPersonnes = &$oModule->aoInscrits;
	}
	
	$sStyleColonne = NULL;
	
	$oBloc_Module->nextLoop();
	
	$oBloc_Module->remplacer("{module->id}",$oModule->retId());
	$oBloc_Module->remplacer("{module->intitule}",$oModule->retNomComplet(TRUE));
	
	$oBloc_Personne_Inscrit = new TPL_Block("BLOCK_PERSONNE_INSCRITE",$oBloc_Module);
	
	if (is_array($aoPersonnes))
	{
		$iPosPersonne = 1;
		
		$oBloc_Personne_Inscrit->beginLoop();
		
		foreach ($aoPersonnes as $oPersonne)
		{
			$sStyleColonne = ($sStyleColonne == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
			
			$oBloc_Personne_Inscrit->nextLoop();
			
			$oBloc_Personne_Inscrit->remplacer("{colonne->style}",$sStyleColonne);
			
			$oBloc_Personne_Inscrit->remplacer("{personne->pos}",$iPosPersonne++);
			$oBloc_Personne_Inscrit->remplacer("{personne->id}",$oPersonne->retId());
			$oBloc_Personne_Inscrit->remplacer("{personne->nom}",$oPersonne->retNomComplet(TRUE));
		}
		
		$oBloc_Personne_Inscrit->afficher();
	}
	else
	{
		$oBloc_Personne_Inscrit->effacer();
	}
}

$oBloc_Module->afficher();

$oTpl->afficher();

$oProjet->terminer();
?>

