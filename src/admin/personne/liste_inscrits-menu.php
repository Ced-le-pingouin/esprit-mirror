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
** Fichier ................: liste_inscrits-menu.php
** Description ............:
** Date de création .......: 02/09/2004
** Dernière modification ..: 31/05/2005
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
$url_bMenu      = (empty($_GET["menu"]) ? FALSE : (bool)$_GET["menu"]);
$url_iIdForm    = (empty($_GET["idForm"]) ? 0 : $_GET["idForm"]);
$url_iIdMod     = (empty($_GET["idMod"]) ? 0 : $_GET["idMod"]);
$url_iIdStatuts = (empty($_GET["idStatuts"]) ? NULL : $_GET["idStatuts"]);

// ---------------------
// Initialiser
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
BLOCK_HTML_HEAD;

// ---------------------
// Composer le menu
// ---------------------
$iIdxMenu = 1;
$aMenus = array();

if ($url_bMenu)
{
	if ($url_iIdForm > 0)
		$aMenus[] = array("Inscription","gestion_utilisateur('{$url_iIdForm}')",$iIdxMenu++,"text-align: left;");
	
	if ($url_iIdMod > 0)
		$aMenus[] = array("Envoi courriel","choix_courriel('?idStatuts={$url_iIdStatuts}')",$iIdxMenu++,"text-align: center;");
}

$aMenus[] = array("Fermer","top.close()",$iIdxMenu);

// ---------------------
// Afficher le menu
// ---------------------
include_once(dir_template("dialogue","dialog-menu.tpl.php"));

?>

