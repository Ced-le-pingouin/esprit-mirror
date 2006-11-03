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
** Fichier ................: dossier_formations_event-index.php
** Description ............:
** Date de création .......: 24/05/2005
** Dernière modification ..: 03/10/2005
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
$url_sParams = NULL;

foreach ($_GET as $sCle => $sValeur)
	$url_sParams .= (isset($url_sParams) ? "&" : "?")
		."{$sCle}={$sValeur}";

$url_sEvent = (empty($_GET["event"]) ? NULL : $_GET["event"]);

// ---------------------
// Initialiser
// ---------------------
if ("ajout" == $url_sEvent)
	$sTitrePrincipal = "Ajouter un dossier de formations";
else if ("modif" == $url_sEvent)
	$sTitrePrincipal = "Modifier le dossier de formations";
else if ("supp" == $url_sEvent)
	$sTitrePrincipal = "Supprimer le dossier de formations";

// {{{ Insérer ces lignes dans l'en-tête de la page html
$sBlockHtmlHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
BLOCK_HTML_HEAD;
// }}}

// {{{ Frame principale
$sFramePrincipale = <<<BLOCK_FRAME_PRINCIPALE
<frame name="Principale" src="dossier_formations_event.php{$url_sParams}" frameborder="0" marginwidth="10" marginheight="20" scrolling="no" noresize="noresize">
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
$oTpl->remplacer("{frame_src_haut}","dossier_formations_event-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipale);
$oTpl->remplacer("{frame_src_bas}","dossier_formations_event-menu.php{$url_sParams}");

$oTpl->afficher();

?>

