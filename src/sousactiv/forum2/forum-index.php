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
** Sous-activité ..........: forum-index.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 10/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
** Exemples ...............: <a href="/sousactiv/forum/forum-index.php?idForum={idForum}">Forum de la rubrique 1</a>
**                           <a href="/sousactiv/forum/forum-index.php?idNiveau={ID_RUBRIQUE}&typeNiveau={TYPE_RUBRIQUE}">Forum de la rubrique 1</a>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdForum    = (empty($_GET["idForum"]) ? 0 : $_GET["idForum"]);
$url_iIdNiveau   = (empty($_GET["idNiveau"]) ? 0 : $_GET["idNiveau"]);
$url_iTypeNiveau = (empty($_GET["typeNiveau"]) ? 0 : $_GET["typeNiveau"]);
$url_iIdPers     = (empty($_GET["idPers"]) ? 0 : $_GET["idPers"]);
$url_iIdEquipe   = (empty($_GET["idEquipe"]) ? 0 : $_GET["idEquipe"]);

// ---------------------
// Initialiser le forum
// ---------------------
$sTitrePrincipal = "Forum";

$oForum = new CForum(new CBdd(),$url_iIdForum);

if ($url_iIdForum < 1)
{
	$oForum->initForumParType($url_iTypeNiveau,$url_iIdNiveau);
	$url_iIdForum = $oForum->retId();
}

$sParamsUrlTitre = "?tp=".rawurlencode($sTitrePrincipal)
	."&st=".rawurlencode($oForum->retTexteModalite());

$sParamsUrl = "?idForum={$url_iIdForum}"
	."&idNiveau={$url_iIdNiveau}"
	."&typeNiveau={$url_iTypeNiveau}"
	.($url_iIdPers > 0 ? "&idPers={$url_iIdPers}" : NULL)
	.($url_iIdEquipe > 0 ? "&idEquipe={$url_iIdEquipe}" : NULL);

// ---------------------
// Template
// ---------------------
$oTpl = new Template("forum-index.tpl");

$oTpl->remplacer("{html.title}",htmlentities($sTitrePrincipal,ENT_COMPAT,"UTF-8"));

$oTpl->remplacer("{forum.id}",$url_iIdForum);

$oTpl->remplacer("{g_iIdNiveau}",$url_iIdNiveau);
$oTpl->remplacer("{g_iTypeNiveau}",$url_iTypeNiveau);

$oTpl->remplacer("{frame['titre'].src}","forum-titre.php{$sParamsUrlTitre}");
$oTpl->remplacer("{frame['sujets'].src}","forum-sujets.php{$sParamsUrl}");
$oTpl->remplacer("{frame['menu'].src}","forum-menu.php?idForum={$url_iIdForum}");

$oTpl->afficher();

?>

