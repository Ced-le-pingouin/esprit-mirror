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
** Fichier ................: form_mod.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 31/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Permissions
// ---------------------
include_once("perm.modif.mod.php");

// ---------------------
// Initialisation
// ---------------------
$iNumeroOrdre = $oProjet->oModuleCourant->retNumOrdre();
$sNom         = $oProjet->oModuleCourant->retNom(TRUE);
$sDescription = $oProjet->oModuleCourant->retDescr();
$iStatut      = $oProjet->oModuleCourant->retStatut();

$iNbrModule = $oProjet->oModuleCourant->retNombreLignes();

$aoStatut = retListeStatuts("M");

// ---------------------------
// Intitule
// ---------------------------
$sTplOptions = NULL;

$sFormSelectIntitules = "<tr><td><div class=\"intitule\">Intitul&eacute;&nbsp;:&nbsp;</div></td>"
	."<td>"
	."<select name=\"intitule_module\" size=\"1\"".($g_bModifier ? NULL : " disabled")
	." style=\"width: 200px;\""
	.">\n"
	."<option value=\"0\">Pas d'intitul&eacute;</option>\n"
	."[TPL_OPTIONS]"
	."</select>&nbsp;"
	."<input name=\"numdepart_module\" size=\"3\" maxlength=\"3\" value=\"".$oProjet->oModuleCourant->retNumDepart()."\"".($g_bModifier ? NULL : " disabled").">";

if ($g_bModifier)
	$sFormSelectIntitules .= "&nbsp;[&nbsp;<a href=\"javascript: ouvrir_dico_intitules('".TYPE_MODULE."'); void(0);\""
	.">Ajouter</a>&nbsp;]";

$sFormSelectIntitules .= "</td></tr>\n";

$iIdIntituleActuel = $oProjet->oModuleCourant->retIdIntitule();

$oIntitule = new CIntitule($oProjet->oBdd);

$iNbrIntitules = $oIntitule->initIntitules(TYPE_MODULE);

for ($i=0; $i<$iNbrIntitules; $i++)
{
	$iIdIntitule = $oIntitule->aoIntitules[$i]->retId();
	$sNomIntitule = $oIntitule->aoIntitules[$i]->retNom(FALSE);
	
	$sTplOptions .= "<option"
		." value=\"{$sNomIntitule}\""
		.($iIdIntitule == $iIdIntituleActuel ? " selected" : NULL)
		.">".(empty($sNomIntitule) ? "Intitule non trouv&eacute;" : $sNomIntitule)
		."&nbsp;&nbsp;"
		."</option>\n";
}

$sFormSelectIntitules = str_replace("[TPL_OPTIONS]",$sTplOptions,$sFormSelectIntitules);

afficherTitre(INTITULE_MODULE,$sNom);

if ($g_bModifier || $g_bModifierStatut)
	echo "<form name=\"form_admin_modif\""
		." action=\"".$_SERVER["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
else
	echo "<form>\n";

?>

<table border="0" cellspacing="0" cellpadding="5" width="100%">
<?php
selectionnerNumeroOrdre("ordre_module",$iNbrModule,$iNumeroOrdre,1);
entrerNom("nom_module",$sNom);
selectionnerStatut("statut_module",$aoStatut,$iStatut);
?>
<tr><td>&nbsp;</td><td><hr></td></tr>
<?php selectionnerType("module_type",$oProjet->oModuleCourant->retTypes(),0); ?>
<tr>
<td>&nbsp;</td>
<td>
<fieldset>
<legend>&nbsp;<?=INTITULE_MODULE?>&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<?php
echo $sFormSelectIntitules;
entrerDescription("descr_module",$sDescription,NULL,urlencode(addslashes($sNom)));
?>
</table>
</fieldset>
</td>
</tr>
</table>

<script type="text/javascript" language="javascript">
<!--
var g_sNomHtmlSelectIntitules = "intitule_module";
//-->
</script>
