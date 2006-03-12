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
** Fichier ................: ressource_evaluation-tuteurs.php
** Description ............:
** Date de création .......: 04/04/2005
** Dernière modification ..: 26/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPers  = (empty($HTTP_GET_VARS["idPers"]) ? 0 : $HTTP_GET_VARS["idPers"]);
$url_iIdResSA = (empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);

// ---------------------
// Initialiser
// ---------------------
if (STATUT_PERS_ETUDIANT != $oProjet->retStatutUtilisateur())
{
	$oProjet->initModuleCourant();
	$oProjet->oModuleCourant->initTuteurs();
	$poTuteurs = &$oProjet->oModuleCourant->aoTuteurs;
}
else
{
	// Les étudiants ne verront que les tuteurs qui ont évalué ce document
	$oRessourceSousActiv = new CRessourceSousActiv($oProjet->oBdd,$url_iIdResSA);
	$oRessourceSousActiv->initTuteurs();
	$poTuteurs = &$oRessourceSousActiv->aoTuteurs;
	unset($oRessourceSousActiv);
}

// ---------------------
// Template
// ---------------------
$oTpl = new Template("ressource_evaluation-tuteurs.tpl");

$oBlocTuteur = new TPL_Block("BLOCK_TUTEUR",$oTpl);
$oBlocTuteur->beginLoop();

$iIdPremierTuteur = (count($poTuteurs) > 0 ? $poTuteurs[0]->retId() : 0);

foreach ($poTuteurs as $oTuteur)
{
	if (($iIdTuteur = $oTuteur->retId()) == $url_iIdPers)
		$iIdPremierTuteur = $iIdTuteur;
	
	$sSeparateurTuteur = ($oBlocTuteur->countLoops() > 0 ? "|" : NULL);
	
	$oBlocTuteur->nextLoop();
	
	$oBlocSeparateurTuteurs = new TPL_Block("BLOCK_SEPARATEUR_TUTEURS",$oBlocTuteur);
	
	if ($oBlocTuteur->countLoops() > 1)
		$oBlocSeparateurTuteurs->afficher();
	else
		$oBlocSeparateurTuteurs->effacer();
	
	$oBlocTuteur->remplacer("{tuteur.separateur}",$sSeparateurTuteur);
	$oBlocTuteur->remplacer("{tuteur.id}",$iIdTuteur);
	$oBlocTuteur->remplacer("{tuteur.nom}",$oTuteur->retNom());
	$oBlocTuteur->remplacer("{tuteur.prenom}",$oTuteur->retPrenom());
}

$oBlocTuteur->afficher();

// Sélectionner le tuteur par défaut
$oTpl->remplacer("{tuteur.id}",$iIdPremierTuteur);
$oTpl->remplacer("{ressource.id}",$url_iIdResSA);

$oTpl->afficher();

$oProjet->terminer();

?>

