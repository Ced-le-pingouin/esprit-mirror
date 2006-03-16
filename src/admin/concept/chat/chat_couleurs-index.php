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
** Fichier ................: chat_couleurs-index.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 11/08/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if (isset($HTTP_GET_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_GET_VARS["CouleurChat"];
else if (isset($HTTP_POST_VARS["CouleurChat"]))
	$url_sCouleurChat = $HTTP_POST_VARS["CouleurChat"];
else
	$url_sCouleurChat = 0;
?>
<html>
<head>
<title>Les couleurs du monde</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return top.frames["principal"]; }
//-->
</script>
</head>
</html>
<frameset rows="*,50,24" border="0">
<frame src="chat_couleurs.php?CouleurChat=<?=$url_sCouleurChat?>" name="principal" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto" noresize="noresize">
<frame src="chat_couleurs-site.php" name="site" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
<frame src="chat_couleurs-menu.php" name="menu" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" noresize="noresize">
</frameset>
