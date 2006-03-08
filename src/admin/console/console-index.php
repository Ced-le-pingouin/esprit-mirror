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

require_once("globals.inc.php");

// ---------------------
// Initialisation
// ---------------------
$sParamUrl = NULL;
$sNomFichierIndex = "dialog-index-2.tpl";

// ---------------------
// Frame du Titre
// ---------------------
$sTitrePrincipal = "Console";
$sFrameSrcTitre = "console-titre.php";

// ---------------------
// Frame principal
// ---------------------

// Javascript
$sBlockHead = NULL;

// Frameset
$sFrameSrcPrincipal = "<frame src=\"console.php\" name=\"principal\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"yes\" noresize=\"noresize\">";

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "console-menu.php";
require_once(dir_template("dialogue","dialog-index.tpl.php"));
?>

