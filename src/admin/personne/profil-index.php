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
** Fichier ................: personne-index.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 26/10/2005
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
if (isset($HTTP_GET_VARS["nv"]))
	$sParamUrl = "?nv=".$HTTP_GET_VARS["nv"];
else if (isset($HTTP_GET_VARS["idPers"]))
	$sParamUrl = "?idPers=".$HTTP_GET_VARS["idPers"];
else
	$sParamUrl = NULL;

if (isset($HTTP_GET_VARS["titre"]))
	$sTitrePrincipal = $HTTP_GET_VARS["titre"];
else
	$sTitrePrincipal = "Profil";

// ---------------------
// Initialiser
// ---------------------
$sFrameSrcTitre = NULL;

$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="Principal" src="profil.php{$sParamUrl}" frameborder="0" scrolling="no" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

$sFrameSrcMenu = "profil-menu.php";

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

