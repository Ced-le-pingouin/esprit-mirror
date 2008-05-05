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
** Fichier ................: zone_cours-index.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 21/09/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdPers   = (empty($_GET["idPers"]) ? 0 : $_GET["idPers"]);
$url_iIdEquipe = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Initialiser
// ---------------------
$iIdPers      = $oProjet->retIdUtilisateur();
$iIdForm      = (is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod       = (is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);
$iIdRubrique  = (is_object($oProjet->oRubriqueCourante) ? $oProjet->oRubriqueCourante->retId() : 0);
$iIdActiv     = (is_object($oProjet->oActivCourante) ? $oProjet->oActivCourante->retId() : 0);
$iIdSousActiv = (is_object($oProjet->oSousActivCourante) ? $oProjet->oSousActivCourante->retId() : 0);

$sParamsUrlSupp = (empty($url_iIdEquipe)
	? (empty($url_iIdPers)
		? NULL
		: "?idPers={$url_iIdPers}")
	: "?idEquipe={$url_iIdEquipe}");

$sTitrePageHtml = ($iIdRubrique > 0 ? $oProjet->oRubriqueCourante->retNom() : NULL);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("zone_cours-index.tpl",FALSE,TRUE));

$oTpl->remplacer("{titre_page_html}",$sTitrePageHtml);

$oTpl->remplacer("{src_frame_haut}","zone_cours-titre.php");
$oTpl->remplacer("{src_frame_gauche}","zone_cours-menu.php{$sParamsUrlSupp}#premiere_page");
$oTpl->remplacer("{src_frame_principal}",dir_theme("blank.htm",FALSE));
$oTpl->remplacer("{src_frame_bas}","menu.php?idForm={$iIdForm}&idMod={$iIdMod}&idUnite={$iIdRubrique}&idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}");

$oTpl->afficher();

$oProjet->terminer();

?>

