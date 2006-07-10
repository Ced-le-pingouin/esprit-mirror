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
** Fichier ................: archives-index.php
** Description ............:
** Date de création .......: 01/03/2001
** Dernière modification ..: 03/10/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("bdd.class.php"));
require_once(dir_database("chat.tbl.php"));

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_iIdChat = (empty($_GET["idChat"]) ? 0 : $_GET["idChat"]);

$url_sParams = NULL;

foreach ($_GET as $sCle => $sValeur)
	$url_sParams .= (isset($url_sParams) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialisation
// ---------------------
$sTitrePrincipal = "Archives des conversations";

$oChat = new CChat(new CBdd(),$url_iIdChat);
$sSousTitre = $oChat->retNom();
unset($oChat);

// ---------------------
// Index
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oTitre() { return top.frames["Haut"]; }
function oPrincipal() { return top.frames["Principale"]; }
function oSousMenu() { return top.frames["SousMenu"]; }
//-->
</script>
BLOCK_HTML_HEAD;

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "archives-titre.php";

// ---------------------
// Frame principale
// ---------------------
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frameset cols="210,1,*" border="0" frameborder="0" framespacing="0">
<frame name="liste" src="archives-liste.php{$url_sParams}" frameborder="0" marginwidth="5" marginheight="10" scrolling="auto" noresize="noresize">
<frame name="frmSeparation1" src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frameset rows="*,24" border="0" frameborder="0" framespacing="0">
<frame name="Principale" src="" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="SousMenu" src="" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu    = "archives-menu.php";
$sNomFichierIndex = "dialog-index.tpl";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

?>

