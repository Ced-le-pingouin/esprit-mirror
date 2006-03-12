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
** Fichier ................: tchatche-index.php
** Description ............:
** Date de création .......: 01/03/2001
** Dernière modification ..: 17/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
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
$url_iIdNiveau   = (empty($HTTP_GET_VARS["idNiveau"]) ? 0 : $HTTP_GET_VARS["idNiveau"]);
$url_iTypeNiveau = (empty($HTTP_GET_VARS["typeNiveau"]) ? 0 : $HTTP_GET_VARS["typeNiveau"]);

if ($url_iIdNiveau < 1 || $url_iTypeNiveau < 1)
	exit();

// ---------------------
// Initialisation
// ---------------------
$sTitrePrincipal = "Chat";

$oBdd = new CBdd();

switch ($url_iTypeNiveau)
{
	case TYPE_SOUS_ACTIVITE:
		$oSousActiv = new CSousActiv($oBdd,$url_iIdNiveau);
		$sSousTitre = $oSousActiv->retNom();
		break;
		
	case TYPE_RUBRIQUE:
		$oRubrique = new CModule_Rubrique($oBdd,$url_iIdNiveau);
		$sSousTitre = $oRubrique->retNom();
		break;
		
	default:
		$sSousTitre = NULL;
}

unset($oBdd);

$sParamsUrl = "?idNiveau={$url_iIdNiveau }&typeNiveau={$url_iTypeNiveau}";

// ---------------------
// Bloc d'en-tête de la page html
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return top.frames["Principale"]; }
function oConnectes() { return top.frames["Connectes"]; }
//-->
</script>
BLOCK_HTML_HEAD;

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "tchatche-titre.php";

// ---------------------
// Frame principale
// ---------------------
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frameset cols="1,*" border="0" frameborder="0" framespacing="0">
<frame name="Connectes" src="tchatche-connectes.php{$sParamsUrl}" frameborder="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="tchatche.php{$sParamsUrl}" frameborder="0" scrolling="auto" noresize="noresize">
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "tchatche-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

