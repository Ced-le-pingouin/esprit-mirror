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
$url_sTitrePrincipal = (empty($HTTP_GET_VARS["tp"]) ? "Inscription" : stripslashes(rawurldecode($HTTP_GET_VARS["tp"])));

// ---------------------
// Initialiser
// ---------------------
$sFramePrincipal = <<<BLOC_FRAME_PRINCIPALE
<frameset rows="27,1,*">
<frame src="choix_formation-filtre.php" name="Filtre" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
<frame src="theme://frame_separation.htm" frameborder="0" scrolling="no" noresize="noresize">
<frame src="choix_formation.php" name="Principal" frameborder="0" scrolling="auto" noresize="noresize">
</frameset>
BLOC_FRAME_PRINCIPALE;

// ---------------------
// Template
// ---------------------
$oTpl = new Template(dir_theme("dialog-index.tpl",FALSE,TRUE));

$oBlockHead = new TPL_Block("BLOCK_HEAD",$oTpl);
$oBlockHead->effacer();

$oTpl->remplacer("{titre_page_html}",htmlentities($url_sTitrePrincipal),ENT_COMPAT,"UTF-8");
$oTpl->remplacer("{frame_src_haut}","choix_formation-titre.php?tp=".rawurlencode($url_sTitrePrincipal));
$oTpl->remplacer("{frame_principal}",$sFramePrincipal);
$oTpl->remplacer("{frame_src_bas}","choix_formation-menu.php");

$oTpl->afficher();

?>

