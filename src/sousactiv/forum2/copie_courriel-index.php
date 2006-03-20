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
** Fichier ................: copie_courriel-index.php
** Description ............: 
** Date de création .......: 29/11/2004
** Dernière modification ..: 07/12/2004
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
$sParamsUrl = NULL;

foreach ($HTTP_GET_VARS as $sCle => $sValeur)
	$sParamsUrl .= (isset($sParamsUrl) ? "&" : "?")
		."{$sCle}={$sValeur}";

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Copie courriel";

$sFramePrincipale = <<<BLOC_FRAME_PRINCIPALE
<frame src="copie_courriel.php{$sParamsUrl}" name="Principale" frameborder="0" scrolling="no" noresize="noresize">
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Insérer ces lignes dans l'en-tête de la page html
// ---------------------
$sBlockHeader = <<<BLOC_HTML_HEADER
<script type="text/javascript" language="javascript">
<!--
function oFrmPrincipale() { return top.frames["Principale"]; }
function oFrmListeEquipes() { return top.frames["Principale"].frames["LISTE_EQUIPES"]; }
//-->
</script>
BLOC_HTML_HEADER;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlockHeader);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",htmlentities($sTitrePrincipal,ENT_COMPAT,"UTF-8"));
$oTpl->remplacer("{frame_src_haut}","copie_courriel-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","");

$oTpl->afficher();

?>

