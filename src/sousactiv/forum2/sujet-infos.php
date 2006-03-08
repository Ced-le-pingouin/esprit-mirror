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
** Fichier ................: forum-infos.php
** Description ............: 
** Date de création .......: 24/09/2004
** Dernière modification ..: 13/11/2004
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
$url_iIdSujet = (empty($HTTP_GET_VARS["idSujet"]) ? 0 : $HTTP_GET_VARS["idSujet"]);

// ---------------------
// Initialiser
// ---------------------
$oSujetForum = new CSujetForum($oProjet->oBdd,$url_iIdSujet);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sujet-infos.tpl");

$oBlocInfosSujet = new TPL_Block("BLOCK_INFOS_SUJET",$oTpl);

if ($url_iIdSujet > 0)
{
	// Sujet
	$oBlocInfosSujet->remplacer("{sujet->titre}",$oSujetForum->retTitre());
	$oBlocInfosSujet->remplacer("{sujet->date_creation}",$oSujetForum->retDate("d/m/y"));
	
	// Personne
	$oSujetForum->initAuteur();
	$oBlocInfosSujet->remplacer("{personne->nom_complet}",$oSujetForum->oAuteur->retNomComplet());
	$oBlocInfosSujet->remplacer("{personne->pseudo}",$oSujetForum->oAuteur->retPseudo());
	
	$oBlocInfosSujet->afficher();
}
else
{
	$oBlocInfosSujet->effacer();
}

$oTpl->remplacer("{sujet->id}",$url_iIdSujet);

$oTpl->afficher();

$oProjet->terminer();

?>
