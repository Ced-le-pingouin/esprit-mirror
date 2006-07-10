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

$sParamUrl = (isset($_GET["idPers"]) ? "?idPers=".$_GET["idPers"] : NULL);

// ---------------------
// Frame du Titre
// ---------------------
$sTitrePrincipal = "Détails de connexion";
$sFrameSrcTitre = "detail_connexion-titre.php";

// ---------------------
// Frame principal
// ---------------------

// Fichier d'en-tête de la page html
$sBlockHead =<<< BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function recharger() { top.location = top.location; }
//-->
</script>
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = "<frame"
	." src=\"detail_connexion.php{$sParamUrl}\""
	." marginwidth=\"2\" marginheight=\"2\""
	." name=\"Principale\""
	." scrolling=\"yes\">";

// ---------------------
// Frame du Menu
// ---------------------

$sFrameSrcMenu = "detail_connexion-menu.php";

$sNomFichierIndex = "dialog-index.tpl";

require_once(dir_template("dialogue","dialog-index.tpl.php"));

?>
