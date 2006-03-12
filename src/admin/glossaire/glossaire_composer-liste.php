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
** Fichier ................: glossaire_composer-liste.php
** Description ............:
** Date de création .......: 28/07/2004
** Dernière modification ..: 12/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$iNbGlossaires = $oProjet->oFormationCourante->initGlossaires();

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("glossaire/glossaire_composer-liste.tpl",FALSE,TRUE));

// Menu simple
$oTplMenuSimple = new Template(dir_theme("menu/menu-simple.itpl",FALSE,TRUE));
$oBloc_MenuSimpleElement = new TPL_Block("BLOCK_ELEMENT",$oTplMenuSimple);
$oBloc_MenuSimpleMenu = new TPL_Block("BLOCK_MENU",$oTplMenuSimple);

// Définir
$oSet_MenuLienActif = $oTpl->defVariable("SET_MENU_LIEN_ACTIF");
$oSet_MenuSeparateur = $oTplMenuSimple->defVariable("SET_MENU_SEPARATEUR");

// Titre du menu simple
$oTplMenuSimple->remplacer("{titre}",htmlentities("Liste des glossaires"));

// Liste des glossaires
if ($iNbGlossaires > 0)
{
	$oBloc_MenuSimpleElement->beginLoop();
	
	foreach ($oProjet->oFormationCourante->aoGlossaires as $oGlossaire)
	{
		$oBloc_MenuSimpleElement->nextLoop();
		
		$oBloc_MenuSimpleElement->remplacer("{element}",$oSet_MenuLienActif);
		$oBloc_MenuSimpleElement->remplacer("{glossaire->id}",$oGlossaire->retId());
		$oBloc_MenuSimpleElement->remplacer("{glossaire->titre}",htmlentities($oGlossaire->retTitre()));
	}
	
	$oBloc_MenuSimpleElement->afficher();
}
else
{
	$oBloc_MenuSimpleElement->effacer();
}

// Menu
$sMenu = "<a"
	." href=\"javascript: void(0);\""
	." onclick=\"ajouter_glossaire(); return false;\""
	." onfocus=\"blur()\""
	." title=\"Ajouter un nouveau glossaire\""
	.">Ajouter</a>";

if ($iNbGlossaires > 0)
{
	$sMenu .= $oSet_MenuSeparateur
		."<a"
		." href=\"javascript: void(0);\""
		." onclick=\"modifier_glossaire(); return false;\""
		." onfocus=\"blur()\""
		." title=\"Modifier le nom du glossaire\""
		.">Modifier</a>"
		.$oSet_MenuSeparateur
		."<a"
		." href=\"javascript: void(0);\""
		." onclick=\"supprimer_glossaire(); return false;\""
		." onfocus=\"blur()\""
		." title=\"Supprimer le glossaire\""
		.">Supprimer</a>";
}
else
{
	$oBloc_MenuSimpleElement->effacer();
}

$oBloc_MenuSimpleMenu->remplacer("{menu}",$sMenu);
$oBloc_MenuSimpleMenu->afficher();

// ---------------------
// Insérer le menu simple dans le template principal
// ---------------------
$oBloc_ListeGlossaires = new TPL_Block("BLOCK_LISTE_GLOSSAIRES",$oTpl);
$oBloc_ListeGlossaires->ajouter($oTplMenuSimple->defVariable("SET_MENU_SIMPLE"));
$oBloc_ListeGlossaires->afficher();

$oTpl->afficher();

$oProjet->terminer();
?>
