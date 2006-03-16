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
** Fichier .................: nvfrm_lst.php
** Description .............: 
** Date de création ........: 04/06/2002
** Dernière modification ...: 22/02/2005
** Auteurs .................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
require_once(dir_database("formation.tbl.php"));

$oProjet = new CProjet();

// ---------------------
// Récupération des valeurs des formulaires ou des l'urls
// ---------------------
$filtre   = isset($HTTP_GET_VARS["FILTRE"]) ? $HTTP_GET_VARS["FILTRE"] : NULL;
$tri      = isset($HTTP_GET_VARS["TRI"]) ? $HTTP_GET_VARS["TRI"] : NULL;
$sens_tri = isset($HTTP_GET_VARS["SENS_TRI"]) ? $HTTP_GET_VARS["SENS_TRI"] : NULL;

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("dialog.css; ajouter_formation.css"); ?>
<script type="text/javascript" language="javascript">
<!--

var id_timer = null;

function menu()
{
	if (document.getElementById("id_menu"))
	{
		var obj = document.getElementById("id_menu").style;
		obj.top = pageYOffset;
	}
	else if (id_timer != null)
		clearInterval(id_timer);
}

function init()
{
	var i = document.forms["FRM_CHOIX_FORM"].elements.length;
	
	if (top.frames['main'].document.forms["FRM_GENERAL"].ID_FORM.value > 1)
	{
		for (y=0; y<i; y++)
			if (top.frames['main'].document.forms["FRM_GENERAL"].ID_FORM.value == document.forms["FRM_CHOIX_FORM"].elements[y].value)
				document.forms["FRM_CHOIX_FORM"].elements[y].checked = true;
	}
	else if (i > 0)
		top.frames['main'].document.forms["FRM_GENERAL"].ID_FORM.value = document.forms["FRM_CHOIX_FORM"].elements[0].value;

	id_timer = setInterval("menu()",100);
}

//-->
</script>
</head>
<body onload="init()" class="liste_formations">
<form name="FRM_CHOIX_FORM" action="<?=$HTTP_SERVER_VARS['PHP_SELF']?>" method="post">
<table border="0" cellpadding="2" cellspacing="1" width="100%">
<tr>
<td class="dialog_menu_intitule">&nbsp;</td>
<!--<td class="dialog_menu_intitule" width="1%" align="center"><b>&nbsp;Tp.&nbsp;</b></td>-->
<td class="dialog_menu_intitule" width="99%" align="center"><b>&nbsp;Nom&nbsp;</b></td>
<td class="dialog_menu_intitule" align="center"><b>&nbsp;Détail&nbsp;</b></td>
</tr>
<?php

$oFormation = new CFormation($oProjet->oBdd);

$oFormation->defTrier($tri,$sens_tri);

switch ($filtre)
{
	case "4": $iNbrFormations = $oFormation->defTrierParAnnee(date("01-01-".(date("Y")-1)),date("Y-12-31")); break;
	case "3": $iNbrFormations = $oFormation->defTrierParAnnee(date("Y-01-01"),date("Y-12-31")); break;
	case "2": $iNbrFormations = $oFormation->defTrierParType($sens_tri); break;
}

if ($oProjet->verifPermission("PERM_MOD_TOUTES_SESSIONS"))
	$iNbrFormations = $oFormation->initFormations();
else if (is_object($oProjet->oUtilisateur))
	$iNbrFormations = $oFormation->initFormationsPourCopie($oProjet->oUtilisateur->retId());
else
	$iNbrFormations = 0;

$sClasseCss = NULL;

for ($i=0; $i<$iNbrFormations; $i++)
{
	$iIdForm = $oFormation->aoFormations[$i]->retId();
	
	$sClasseCss = ($sClasseCss == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
	
	echo "<tr>\n"
		."<td class=\"$sClasseCss\">\n"
		."<input type=\"radio\" name=\"FORM_SELECT\" value=\"{$iIdForm}\""
		." onfocus=\"blur()\""
		.($i == 0 ? " checked=\"checked\"" : NULL)
		." onclick=top.frames['main'].document.forms['FRM_GENERAL'].ID_FORM.value=value;"
		.">\n"
		."</td>\n"
		//."<td class=\"$sClasseCss\" width=\"1%\" align=\"center\">&nbsp;".$oFormation->aoFormations[$i]->retType()."&nbsp;</td>\n"
		."<td class=\"$sClasseCss\">"
			.htmlentities($oFormation->aoFormations[$i]->retNom())
		."</td>\n"
		."<td class=\"$sClasseCss\" align=\"center\">&#8212;</td>\n"
		."</tr>\n";
}

if ($iNbrFormations == 0)
	echo "<tr class=\"cellule_clair\">\n"
		."<td colspan=\"4\" align=\"center\">Pas de formation trouv&eacute;</td>\n"
		."</tr>\n";

?>
</table>
</form>
</body>
</html>
