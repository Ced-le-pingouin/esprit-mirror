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
** Fichier ................: modifier_forum-index.php
** Description ............: 
** Date de création .......: 14/05/2004
** Dernière modification ..: 08/10/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Jérôme TOUZE
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

$url_sModaliteFenetre = $_GET["modaliteFenetre"];
$url_iIdForumParent   = (empty($_GET["idForumParent"]) ? "0" : $_GET["idForumParent"]);

// ---------------------
// Définir le titre de la fenêtre
// ---------------------
if ($url_sModaliteFenetre == "ajouter")
	$sTitrePrincipal = "Ajouter un nouveau forum";
else if ($url_sModaliteFenetre == "modifier")
	$sTitrePrincipal = "Modifier le forum";
else if ($url_sModaliteFenetre == "supprimer")
	$sTitrePrincipal = "Supprimer le forum";

$sParamsUrl = "?modaliteFenetre={$url_sModaliteFenetre}"
	."&idForumParent={$url_iIdForumParent}";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?=mb_convert_encoding($sTitrePrincipal,"HTML-ENTITIES","UTF-8")?></title>
<script type="text/javascript" language="javascript" src="forum.js"></script>
</head>
<frameset rows="*,26" border="0">
<frame name="FORUM" src="modifier_forum.php<?=$sParamsUrl?>" frameborder="0" marginwidth="5" marginheight="10" scrolling="no" noresize="noresize">
<frame name="MENU" src="" frameborder="0" marginwidth="5" marginheight="5" scrolling="no" noresize="noresize">
</frameset>
</html>
