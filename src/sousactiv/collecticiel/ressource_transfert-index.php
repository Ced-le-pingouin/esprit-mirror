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
** Fichier ................: ressource_transfert-index.php
** Description ............:
** Date de création .......: 27/11/2002
** Dernière modification ..: 05/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_locale("collecticiel.lang"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValeur)
	$url_sParamsUrl .= (isset($url_sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialisation
// ---------------------

// {{{ Frame du titre
$sFrameSrcTitre = "ressource_transfert-titre.php";
$sTitrePrincipal = TXT_TRANSFERER_DES_FICHIERS_TITRE;
// }}}

// {{{ Frame principale
$sBlockHead = <<<BLOCK_HTML_HEAD
BLOCK_HTML_HEAD;

$sFrameSrcPrincipal = <<<BLOCK_FRAME_SRC_PRINCIPALE
<frame name="Principale" src="ressource_transfert.php{$url_sParamsUrl}" scrolling="auto">
BLOCK_FRAME_SRC_PRINCIPALE;
// }}}

// {{{ Frame du menu
$sFrameSrcMenu = "ressource_transfert-menu.php";
// }}}

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
