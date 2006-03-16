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
** Fichier ................: admin_modif-menu.php
** Description ............: 
** Date de création .......: 25/04/2003
** Dernière modification ..: 07/08/2004
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
$url_iType   = (empty($HTTP_GET_VARS["type"]) ? 0 : $HTTP_GET_VARS["type"]);
$url_sParams = (empty($HTTP_GET_VARS["params"]) ? "0:0:0:0:0:0" : $HTTP_GET_VARS["params"]);

if ($url_iType == 0)
	$sCorpPage = "<div style=\"text-align: center;\">"
		."<b>e&nbsp;C&nbsp;O&nbsp;N&nbsp;C&nbsp;E&nbsp;P&nbsp;T</b>"
		."</div>";
else
	$sCorpPage = "<div style=\"text-align: right;\">"
		."<a href=\"javascript: "
		.($url_iType == TYPE_SOUS_ACTIVITE
			? "verifier()"
			: "envoyer()")
		.";\">Appliquer&nbsp;les&nbsp;changements</a>"
		."&nbsp;&nbsp;&#8226;&nbsp;&nbsp;"
		."<a href=\"javascript: annuler();\">Annuler</a>"
		."&nbsp;&nbsp;"
		."</div>";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?=inserer_feuille_style("concept.css")?>
<script type="text/javascript" language="javascript">
<!--
function verifier()
{
	if (top.frames["ADMINFRAMEMODIF"].type_different())
		envoyer();
}

function envoyer()
{
	top.frames["ADMINFRAMEMODIF"].document.forms[0].submit();
}

function annuler()
{
	top.frames["ADMINFRAMEMODIF"].location = "admin_modif.php"
		+ "?type=<?=$url_iType?>"
		+ "&params=<?=$url_sParams?>";
}
//-->
</script>
</head>
<body class="admin_modif_menu">
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<tr><td><?=$sCorpPage?></td></tr>
</table>
</body>
</html>
