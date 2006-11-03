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
** Fichier ................: glossaire_composer.php
** Description ............:
** Date de création .......: 28/07/2004
** Dernière modification ..: 29/07/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
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
$url_iIdGlossaire = (empty($_GET["idGlossaire"]) ? 0 : $_GET["idGlossaire"]);

if (!empty($_POST["idGlossaire"]))
{
	$url_iIdGlossaire = $_POST["idGlossaire"];
	
	$oProjet->oFormationCourante->effacerElementsGlossaire($url_iIdGlossaire);
	
	if (is_array($_POST["idElementsGlossaire"]))
		$oProjet->oFormationCourante->ajouterElementsGlossaire($url_iIdGlossaire,$_POST["idElementsGlossaire"]);
}
// ---------------------
// Initialiser
// ---------------------
$oGlossaire = new CGlossaire($oProjet->oBdd,$url_iIdGlossaire);
$oProjet->oFormationCourante->initElementsGlossaire($url_iIdGlossaire);

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("glossaire/glossaire_composer.tpl",FALSE,TRUE));
$oTpl->remplacer("{glossaire->titre}",mb_convert_encoding($oGlossaire->retTitre(),"HTML-ENTITIES","UTF-8"));

$oBloc_ElementsGlossaire = new TPL_Block("BLOCK_ELEMENTS_GLOSSAIRE",$oTpl);

$oSet_ElementGlossaire = $oTpl->defVariable("SET_ELEMENT_GLOSSAIRE");

if ($url_iIdGlossaire > 0)
		foreach ($oProjet->oFormationCourante->aoElementsGlossaire as $oElementGlossaire)
		{
			$oBloc_ElementsGlossaire->ajouter($oSet_ElementGlossaire);
			
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->selectionne}",($oElementGlossaire->estSelectionne() ? " checked" : NULL));
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->id}",$oElementGlossaire->retId());
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->titre}",$oElementGlossaire->retTitre());
			$oBloc_ElementsGlossaire->remplacer("{glossaire->element->texte}",$oElementGlossaire->retTexte());
		}

$oBloc_ElementsGlossaire->afficher();

$oTpl->remplacer("{glossaire->id}",$url_iIdGlossaire);

$oTpl->afficher();

$oProjet->terminer();

?>