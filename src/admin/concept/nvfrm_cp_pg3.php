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
** Fichier ................: confirm_cp_form.inc.php
** Description ............: 
** Date de création .......: 04-06-2002
** Dernière modification ..: 15-10-2002
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

// *************************************
// Changer la formation courante
// *************************************

$oProjet->defFormationCourante($iIdForm);

// *************************************
// Copier entièrement la formation sélectionnée
// *************************************

if (isset($bConfirmation) && $bConfirmation)
{
	// Identifiant de l'auteur de la formation
	$iIdPers = $oProjet->oUtilisateur->retId();
	
	$url_sNom         = (empty($_POST["formation_nom"]) ? NULL : $_POST["formation_nom"]);
	$url_sDescription = (empty($_POST["formation_description"]) ? NULL : $_POST["formation_description"]);
	
	if (($iIdForm = $oProjet->oFormationCourante->copier()) > 0)
	{
		$oProjet->defFormationCourante($iIdForm);
		
		$oProjet->oFormationCourante->defNom($url_sNom);
		$oProjet->oFormationCourante->defDescr($url_sDescription);
		$oProjet->oFormationCourante->defIdPers($iIdPers);
	}
	
	// Lorsqu'on copie une formation on devient responsable de cette formation
	$sRequeteSql = "REPLACE INTO Formation_Resp SET"
		." IdForm='{$iIdForm}'"
		.", IdPers='{$iIdPers}'";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	
	if ($iIdForm > 0)
	{
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

.titre
{
	font-weight: bold;
	text-align: left;
}

//-->
</style>

<h5>Etape 3&nbsp;: Veuillez donner un nom et &eacute;ventuellement une description &agrave; cette formation</h5>
<table border="0" cellspacing="0" cellpadding="3" width="100%" style="font-size : 10pt;">
<?php
echo "<tr>"
	."<td class=\"titre\" nowrap=\"1\">Nom</td>"
	."<td width=\"5\">:</td>"
	."<td><input"
	." type=\"text\""
	." name=\"formation_nom\""
	." size=\"50\""
	." value=\"".(empty($url_sNomForm)
		? emb_htmlentities($oProjet->oFormationCourante->retNom())
		: $url_sNomForm)
	."\"></td>"
	."</tr>\n"
	."<tr>\n"
	."<td class=\"titre\" valign=\"top\" nowrap=\"nowrap\">Description</td>"
	."<td valign=\"top\" width=\"5\">:</td>"
	."<td>"
	."<textarea name=\"formation_description\" cols=\"50\" rows=\"4\">"
	.(empty($url_sDescrForm)
		? $oProjet->oFormationCourante->retDescr()
		: mb_convert_encoding($url_sDescrForm, 'UTF-8', 'HTML-ENTITIES'))
	."</textarea>"
	."&nbsp;&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"editeur('FRM_GENERAL','formation_description','editeur')\" onfocus=\"blur()\">Editeur</a>&nbsp;]</span>"
	."</td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td class=\"titre\" nowrap=\"1\">Date de cr&eacute;ation</td>"
	."<td width=\"5\">:</td>"
	."<td nowrap=\"1\">"
	.$oProjet->oFormationCourante->retDateDeb()
	."</td>"
	."</tr>";
?>
</table>
<p>Indiquer le nom de votre formation et une description de celle-ci. Si vous indiquez une description, celle-ci apparaîtra à lécran &laquo;&nbsp;Menu&nbsp;&raquo; sous la rubrique &laquo;&nbsp;Description&nbsp;&raquo; de la formation.</p>
<p><b><u>Note</u></b>&nbsp;: Vous pouvez changer ces éléments à tout moment à partir d'eConcept.</p>
