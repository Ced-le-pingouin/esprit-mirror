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
** Fichier ................: forum_export-index.php
** Description ............: 
** Date de création .......: 26/10/2005
** Dernière modification ..: 26/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
** 
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("forum.lang"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = TXT_FORUM_EXPORT_TITRE;

$sBlockHead = <<<HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["principale"]; }
//-->
</script>
HTML_HEAD;

$sFrameSrcTitre = NULL;

$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="forum_export.php{$sParamsUrl}" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

$sFrameSrcMenu = "forum_export-menu.php";

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

