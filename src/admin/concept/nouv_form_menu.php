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

$url_iEtape    = (empty($HTTP_GET_VARS["etape"]) ? 1 : $HTTP_GET_VARS["etape"]);
$url_iNbEtapes = (empty($HTTP_GET_VARS["etapes"]) ? 0 : $HTTP_GET_VARS["etapes"]);
$url_bTerminer = (empty($HTTP_GET_VARS["fin"]) ? FALSE : $HTTP_GET_VARS["fin"]);
?>
<html>
<?php inserer_feuille_style("menu.css; dialog-menu.css"); ?>
<script type="text/javascript" language="javascript">
<!--

function envoyer(v_sAction)
{
	top.frames['main'].document.forms[0].FONCTION.value = v_sAction;
	top.frames['main'].document.forms[0].submit();
}

function precedent() { envoyer('precedent'); }
function suivant() { envoyer('suivant'); }
function valider() { envoyer('valider'); }
function fermer() { top.frames['main'].recharger(); }

//-->
</script>
</head>
<body>
<table border="0" cellspacing="1" cellpadding="2" width="100%" height="100%">
<tr>
<?php
if (!$url_bTerminer)
{
	echo "<td align=\"left\">&nbsp;<a href=\"javascript: top.close();\">Annuler</a></td>\n";
	
	echo "<td class=\"dialogue_menu\" align=\"right\">";
	
	if ($url_iEtape > 1)
		echo "<a href=\"javascript: precedent();\">&#8249;&nbsp;Précédent</a>&nbsp;|&nbsp;";
	
	if ($url_iEtape < $url_iNbEtapes)
		echo "<a href=\"javascript: suivant();\">Suivant&nbsp;&#8250;</a>&nbsp;";
	
	if ($url_iEtape >= $url_iNbEtapes)
		echo "<a href=\"javascript: valider();\">Valider</a>&nbsp;";
	
	echo "</td>";
}
else
{
	echo "<td class=\"dialogue_menu\" align=\"right\" width=\"99%\">"
		."<a href=\"javascript: fermer();\">Fermer</a>"
		."</td>"
		."<td>&nbsp;</td>"
		."\n";
}
?>
</tr>
</table>
</body>
</html>
