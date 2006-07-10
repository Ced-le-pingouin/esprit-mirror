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

$oProjet = new CProjet();

// ---------------------
// Initialisation
// ---------------------
$sParamsUrl = "?idPers=".$oProjet->retIdUtilisateur()
	."&idResSA=".(empty($_GET["idResSA"]) ? 0 : $_GET["idResSA"]);

// ---------------------
// Frame du Titre
// ---------------------
$sFrameSrcTitre = "ressource_evaluation-titre.php";
$sTitrePrincipal = "Evaluation du document";

// ---------------------
// Frame principal
// ---------------------
$sBlockHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
BLOCK_HTML_HEAD;

// Frameset
$sFrameSrcPrincipal = <<<BLOCK_FRAME_PRINCIPALE
<frameset rows="23,1,*" frameborder="0" border="0">
<frame name="tuteurs" src="ressource_evaluation-tuteurs.php{$sParamsUrl}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="sep1" src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frame name="Principale" src="" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Frame du Menu
// ---------------------
$sFrameSrcMenu = "ressource_evaluation-menu.php";
$sNomFichierIndex = "dialog-index.tpl";

include_once(dir_template("dialogue","dialog-index.tpl.php"));

$oProjet->terminer();

?>

