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
** Fichier ................: sousactiv_inv-index.php
** Description ............:
** Date de création .......: 16/11/2005
** Dernière modification ..: 24/11/2005
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
$url_iIdSousActiv = $_GET["idSousActiv"];

$sParamsUrl = NULL;

foreach ($_GET as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

$sTitrePrincipal = "Accès";
$sSousTitre = $oSousActiv->retNom();

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="sousactiv_inv.js"></script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="sousactiv_inv.php{$sParamsUrl}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
BLOCK_FRAME_PRINCIPALE;
// }}}

$sFrameSrcMenu = "sousactiv_inv-menu.php";

// ---------------------
// Template
// ---------------------
include_once(dir_template("dialogue","dialog-index.tpl.php",TRUE));

$oProjet->terminer();

?>

