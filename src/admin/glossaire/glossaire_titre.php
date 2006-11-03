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
** Fichier ................: modifier_glossaire.php
** Description ............:
** Date de création .......: 31/07/2004
** Dernière modification ..: 31/07/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
**
*/

require_once("globals.inc.php");

if (isset($_GET["glossaire_id"]))
{
	include_once("glossaire_titre.inc.php");
	exit();
}

$url_iIdGlossaire = $_GET["idGlossaire"];

$sGlossaireTitre = NULL;

if ($url_iIdGlossaire > 0)
{
	require_once(dir_database("bdd.class.php"));
	$oBdd = new CBdd();
	$oGlossaire = new CGlossaire($oBdd,$url_iIdGlossaire);
	$sGlossaireTitre = mb_convert_encoding($oGlossaire->retTitre(),"HTML-ENTITIES","UTF-8");
	unset($oBdd);
}

$oTpl = new template("glossaire_titre.tpl");

$oBloc_GlossaireTitre = new TPL_Block("BLOCK_GLOSSAIRE_TITRE",$oTpl);

$oSet_GlossaireTitre = $oTpl->defVariable("SET_GLOSSAIRE_TITRE");

$oBloc_GlossaireTitre->ajouter($oSet_GlossaireTitre);
$oBloc_GlossaireTitre->afficher();

$oTpl->remplacer("{menu}","?menu=1");

$oTpl->remplacer("{glossaire->id}",$url_iIdGlossaire);
$oTpl->remplacer("{glossaire->titre}",$sGlossaireTitre);

$oTpl->afficher();
?>
