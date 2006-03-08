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
** Fichier ................: outils-index.php
** Description ............:
** Date de création .......: 03/03/2005
** Dernière modification ..: 03/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------

// ---------------------
// Initialisation
// ---------------------

// {{{ Frame du titre
$sFrameSrcTitre  = "outils-titre.php";
$sTitrePrincipal = "Liste des votants";
$sSousTitre      = NULL;
// }}}

// {{{ Frame principale
$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="liste_votants.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

// {{{ Frame du menu
$sFrameSrcMenu = "outils-menu.php";
// }}}

// ---------------------
// Template
// ---------------------
$sBlockHead = NULL;

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

