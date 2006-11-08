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
// Récupérer les variables de l'url
// ---------------------
if (isset($_GET["idNiveau"]))
	$url_iIdParent = $_GET["idNiveau"];
else if (isset($_POST["idNiveau"]))
	$url_iIdParent = $_POST["idNiveau"];
else
	$url_iIdParent = 0;

if (isset($_GET["typeNiveau"]))
	$url_iTypeParent = $_GET["typeNiveau"];
else if (isset($_POST["typeNiveau"]))
	$url_iTypeParent = $_POST["typeNiveau"];
else
	$url_iTypeParent = 0;

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "Composer les \"chat\"";

$sBlocHtmlHead =<<< BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript" src="chat.js"></script>
<script type="text/javascript" language="javascript">
<!--
var recharger_fenetre_parente = false;
//-->
</script>
BLOCK_HTML_HEAD;

$sParamsUrl = "?idNiveau={$url_iIdParent}&typeNiveau={$url_iTypeParent}";

$sFramePrincipal =<<< BLOCK_FRAME_PRINCIPALE
<frameset cols="210,1,*" border="0" frameborder="0" framespacing="0" onunload="rafraichir_parent()">
<frame src="chat-liste.php{$sParamsUrl}" name="Liste" frameborder="0" scrolling="no" noresize="noresize">
<frame src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frameset rows="*,20" border="0" frameborder="0" framespacing="0">
<frame src="" name="Principal" frameborder="0" scrolling="auto" noresize="noresize">
<frame src="" name="SousMenu" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</frameset>
BLOCK_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------

$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->ajouter($sBlocHtmlHead);
$oBlockHead->afficher();

$oTpl->remplacer("{titre_page_html}",emb_htmlentities($sTitrePrincipal));
$oTpl->remplacer("{frame_src_haut}","chat-titre.php?tp=".rawurlencode($sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","chat-menu.php");

$oTpl->afficher();

?>

