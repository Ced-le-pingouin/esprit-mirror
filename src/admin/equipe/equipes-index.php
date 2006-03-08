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
** Fichier ................: equipes-index.php
** Description ............: 
** Date de création .......: 01/01/2003
** Dernière modification ..: 12/04/2005
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
if (isset($HTTP_GET_VARS["TP"]))
	$sTitrePrincipal = rawurldecode($HTTP_GET_VARS["TP"]);
else
	$sTitrePrincipal = "Equipes";

// ---------------------
// Initialiser
// ---------------------
$sBlocJavascript = <<<BLOC_JAVASCRIPT
<script type="text/javascript" language="javascript">
<!--
	function oTitre() { return top.frames["Haut"]; }
	function oEquipes() { return top.frames["EQUIPES"]; }
	function oFormation() { return top.frames["FORMATION"]; }
	function oEtudiants() { return oEquipes().frames["ETUDIANTS"]; }
	function oMembres() { return oEquipes().frames["MEMBRES"]; }
	function oMenu() { return top.frames["Bas"]; }
//-->
</script>
BLOC_JAVASCRIPT;

$sFramePrincipale = <<<BLOC_FRAME_PRINCIPALE
<frameset cols="180,*" border="0" frameborder="0" framespacing="0">
<frame name="FORMATION" src="equipes-formation.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="EQUIPES" src="equipes.php" frameborder="0" marginwidth="5" marginheight="2" scrolling="no" noresize="noresize">
</frameset>
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlocJavascript);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","equipes-titre.php?TP=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","equipes-menu.php");
$oTpl->afficher();

?>
