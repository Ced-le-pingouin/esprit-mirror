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
** Fichier ................: ressource_votants-index.php
** Description ............: 
** Date de cr�ation .......: 26/11/2004
** Derni�re modification ..: 09/05/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdResSA  = (empty($HTTP_GET_VARS["idResSA"]) ? 0 : $HTTP_GET_VARS["idResSA"]);
$url_iIdEquipe = (empty($HTTP_GET_VARS["idEquipe"]) ? 0 : $HTTP_GET_VARS["idEquipe"]);

// ---------------------
// Initialisation
// ---------------------
$oEquipe = new CEquipe($oProjet->oBdd,$url_iIdEquipe);

$sParamsUrl = "?idResSA={$url_iIdResSA}&idEquipe={$url_iIdEquipe}";

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "ressource_votants-titre.php";

$sTitrePrincipal = "Liste des votants";
$sSousTitre      = ($url_iIdEquipe > 0 ? $oEquipe->retNom() : NULL);

// Javascript
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = <<<BLOC_FRAME_PRINCIPALE
<frame name="principale" src="ressource_votants.php{$sParamsUrl}" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "ressource_votants-menu.php";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

