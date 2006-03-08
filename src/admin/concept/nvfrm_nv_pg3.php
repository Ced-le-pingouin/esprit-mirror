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
** Fichier ................: nvfrm_nv_pg3.php
** Description ............: 
** Date de création .......: 04/06/2002
** Dernière modification ..: 03/09/2004
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

if (isset($bConfirmation) && $bConfirmation)
{
	// Identifiant de l'auteur de la formation
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	$url_sNom         = (empty($HTTP_POST_VARS["formation_nom"]) ? NULL : $HTTP_POST_VARS["formation_nom"]);
	$url_sDescription = (empty($HTTP_POST_VARS["formation_description"]) ? NULL : $HTTP_POST_VARS["formation_description"]);
	
	$oFormation = new CFormation($oProjet->oBdd);
	$iIdForm = $oFormation->ajouter($url_sNom,$url_sDescription,$iIdPers);
	
	if ($iIdForm > 0)
	{
		$oProjet->defFormationCourante($iIdForm);
		
		// Créer un nouveau répertoire pour cette formation
		@mkdir(dir_formation($iIdForm),0744);
		
		echo "<script language=\"javascript\" type=\"text/javascript\">\n"
			."<!--\n\n"
			."\ttop.opener.parent.frames['ADMINFRAMELISTE'].location=\"admin_liste.php"
			."?type=".TYPE_FORMATION
			."&params={$iIdForm}:0:0:0:0:0\";\n"
			."\ttop.close();"
			."\n//-->\n"
			."</script>\n";
		exit();
	}
}
?>
<style type="text/css">
<!--
.titre { font-weight: bold; }
//-->
</style>
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr><td colspan="3"><h5>Etape 2&nbsp;: Veuillez donner un nom et &eacute;ventuellement une description &agrave; cette formation</h5></td></tr>
<?php

$oFormation = new CFormation($oProjet->oBdd);
$iMaxType = $oFormation->retValeurMax($oFormation->TYPE) + 1;

echo "<tr>"
	."<td class=\"titre\" nowrap=\"1\">Nom</td>"
	."<td width=\"5\">:</td>"
	."<td>"
	."<input"
	." type=\"text\""
	." name=\"formation_nom\""
	." value=\"{$url_sNomForm}\""
	." size=\"50\""
	." style=\"width: 370px;\""
	.">"
	."</td>"
	."</tr>"
	."<tr>"
	."<td class=\"titre\" nowrap=\"1\" valign=\"top\">Description</td>"
	."<td width=\"5\" valign=\"top\">:</td>"
	."<td>"
	."<textarea name=\"formation_description\" cols=\"50\" rows=\"4\" style=\"width: 370px; height: 70px;\">"
	.html_entity_decode($url_sDescrForm)
	."</textarea>"
	."&nbsp;&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"editeur('FRM_GENERAL','formation_description','editeur')\" onfocus=\"blur()\">Editeur</a>&nbsp;]</span>"
	."</td>"
	."</tr>"
	."<tr>"
	."<td class=\"titre\" nowrap=\"1\">Date de cr&eacute;ation</td>"
	."<td width=\"5\">:</td>"
	."<td nowrap=\"1\">".date("d-m-Y")."</td>"
	."</tr>";
?>
</table>
<p>Indiquer le nom de votre formation et une description de celle-ci. Si vous indiquez une description, celle-ci apparaîtra à l’écran &laquo;&nbsp;Menu&nbsp;&raquo; sous la rubrique &laquo;&nbsp;Description&nbsp;&raquo; de la formation.</p>
<p><b><u>Note</u></b>&nbsp;: Vous pouvez changer ces éléments à tout moment à partir d'eConcept.</p>
