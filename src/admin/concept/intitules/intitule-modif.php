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
** Fichier ................: intitule-modif.php
** Description ............: 
** Date de création .......: 15/04/2003
** Dernière modification ..: 16/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
** Remarques ..............: Les personnes qui ont le droit de modifier
** ......................... TOUTES LES SESSIONS ont la possibilités de
** ......................... modifier/supprimer toutes les intitulés.
**
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

$oProjet->verifPeutUtiliserOutils();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdIntitule = $url_sNomIntitule = NULL;
$url_iTypeIntitule = (isset($_GET["TYPE_INTITULE"]) ? $_GET["TYPE_INTITULE"] : TYPE_MODULE);

$sCorpFonctionJSInit = NULL;

// ---------------------
// Template
// ---------------------
$oTpl = new Template("intitule-modif.tpl");

// Formulaire
$oTpl->remplacer("{intitule->id}",$url_iIdIntitule);
$oTpl->remplacer("{intitule->type}",$url_iTypeIntitule);

// Menu
$oBloc_Menu = new TPL_Block("BLOCK_MENU",$oTpl);
$oSet_Menu_Separateur = $oTpl->defVariable("SET_MENU_SEPARATEUR");
$oSet_Menu_Ajouter = $oTpl->defVariable("SET_MENU_AJOUTER");
$oSet_Menu_Modifier = $oTpl->defVariable("SET_MENU_MODIFIER");
$oSet_Menu_Supprimer = $oTpl->defVariable("SET_MENU_SUPPRIMER");

// Afficher le menu
$sMenus = $oSet_Menu_Ajouter;

if ($oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
	$sMenus .= $oSet_Menu_Separateur
		.$oSet_Menu_Modifier
		.$oSet_Menu_Separateur
		.$oSet_Menu_Supprimer;

$oBloc_Menu->ajouter($sMenus);
$oBloc_Menu->afficher();

// Afficher le template
$oTpl->afficher();

$oProjet->terminer();
?>

