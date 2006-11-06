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
** Fichier ................: avertissement-index.php
** Description ............:
** Date de création .......: 08/07/2005
** Dernière modification ..: 12/07/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Avertissement login";
$sSousTitre = NULL;

$sRequeteSql = "SELECT AvertissementLogin FROM Projet LIMIT 1";
$hResult = $oProjet->oBdd->executerRequete($sRequeteSql);
$oEnreg = $oProjet->oBdd->retEnregSuiv($hResult);
$sAvertissementLogin = rawurlencode($oEnreg->AvertissementLogin);
$oProjet->oBdd->libererResult($hResult);

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
function rafraichir() { oPrincipale().location = oPrincipale().location; }

function recuperer() {
	var form = oPrincipale().document.getElementsByTagName("form").item(0);
	form.elements["avertissement"].value = unescape("{$sAvertissementLogin}");
}

function valider() {
	var form = oPrincipale().document.getElementsByTagName("form").item(0);
	var params = form.elements["f"].value = "appliquer";
	form.action = "avertissement.php";
	form.target = "Principale";
	form.method = "post";
	form.submit();
}
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="avertissement.php" frameborder="0" marginwidth="10" marginheight="10" scrolling="no" noresize="noresize">
BLOCK_FRAME_PRINCIPALE;
// }}}

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHtmlHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHtmlHead->ajouter($sBlockHtmlHead);
$oBlockHtmlHead->afficher();

$oTpl->remplacer("{titre_page_html}",mb_convert_encoding($sTitrePrincipal,"HTML-ENTITIES","UTF-8"));
$oTpl->remplacer("{frame_src_haut}","avertissement-titre.php?tp=".rawurlencode($sTitrePrincipal)."&st=".rawurlencode($sSousTitre));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","avertissement-menu.php");

$oTpl->afficher();

$oProjet->terminer();

?>

