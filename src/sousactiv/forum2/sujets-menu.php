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
** Fichier ................: sujets-menu.php
** Description ............: 
** Date de création .......: 24/11/2004
** Dernière modification ..: 24/11/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum  = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);
$url_iIdSujet  = (empty($_GET["idSujet"]) ? 0 : $_GET["idSujet"]);
$url_iIdEquipe = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("sujets-menu.tpl");

$oBlocSujetMenu = new TPL_Block("BLOCK_SUJET_MENU",$oTpl);
$sAjtSujetParEquipes = $oBlocSujetMenu->defVariable("VAR_NOUVEAU_SUJET_EQUIPES");

$sSeparateurMenu = $oBlocSujetMenu->defVariable("VAR_MENU_SEPARATEUR");

$sAjtSujet = $oBlocSujetMenu->defVariable("VAR_NOUVEAU_SUJET");
$sModSujet = $oBlocSujetMenu->defVariable("VAR_MODIFIER_SUJET");
$sSupSujet = $oBlocSujetMenu->defVariable("VAR_SUPPRIMER_SUJET");

$oBlocSujetMenu->afficher();

$oTpl->remplacer("{sujet->id}",$url_iIdSujet);

$oTpl->afficher();

?>

