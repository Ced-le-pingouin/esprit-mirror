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
** Fichier ................: copie_courriel-equipes.php
** Description ............:
** Date de création .......: 29/11/2004
** Dernière modification ..: 13/12/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// Rechercher toutes les équipes de ce niveau
$oProjet->initEquipes();

$iMonIdPers = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum = (empty($HTTP_GET_VARS["idForum"]) ? 0 : $HTTP_GET_VARS["idForum"]);

// ---------------------
// Initialiser
// ---------------------
$oForumPrefs = new CForumPrefs($oProjet->oBdd);
$oForumPrefs->initForumPrefs($url_iIdForum,$iMonIdPers);

$iModaliteForum = $oForumPrefs->retModalite();
$bSujetsParEquipe  = ($iModaliteForum != MODALITE_POUR_TOUS);

$oForumPrefs->initEquipes();

$aiIdsEquipes = array();

foreach ($oForumPrefs->aoEquipes as $oEquipe)
	if ($oEquipe->estSelectionne)
		$aiIdsEquipes[] = $oEquipe->retId();

// ---------------------
// Template
// ---------------------
$oTpl = new Template("copie_courriel-equipes.tpl");

$oBlocEquipe = new TPL_Block("BLOCK_EQUIPE",$oTpl);
$oBlocEquipe->beginLoop();

// ---------------------
// Composer la liste des sujets
// ---------------------
foreach ($oProjet->aoEquipes as $oEquipe)
{
	$oBlocEquipe->nextLoop();
	
	$iIdEquipe    = $oEquipe->retId();
	$bSelectionne = in_array($iIdEquipe,$aiIdsEquipes);
	
	$oBlocEquipe->remplacer("{equipe->id}",$iIdEquipe);
	$oBlocEquipe->remplacer("{equipe->nom}",htmlentities($oEquipe->retNom()));
	$oBlocEquipe->remplacer("{equipe->selectionne}",($bSelectionne ? " checked=\"checked\"" : NULL));
}

$oBlocEquipe->afficher();

$oTpl->remplacer("{html_form}","<form action=\"copie_courriel.php\" method=\"post\" target=\"Principale\">");
$oTpl->remplacer("{forum->id}",$url_iIdForum);
$oTpl->remplacer("{/html_form}","</form>");

$oTpl->afficher();

$oProjet->terminer();

?>

