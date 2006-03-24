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
** Fichier ................: form_formation.php
** Description ............: 
** Date de création .......: 01/02/2002
** Dernière modification ..: 23/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Permissions
// ---------------------
include_once("perm.modif.form.php");

// ---------------------
// Initialisations
// ---------------------
$iNumeroOrdre      = $oProjet->oFormationCourante->retNumOrdre();
$sNom              = $oProjet->oFormationCourante->retNom(TRUE);
$sDescription      = $oProjet->oFormationCourante->retdescr();
$iStatut           = $oProjet->oFormationCourante->retStatut();
$bInscrAutoModules = $oProjet->oFormationCourante->retInscrAutoModules();

afficherTitre(NULL,$sNom);

// ---------------------
// ---------------------
if ($g_bModifier || $g_bModifierStatut)
	echo "<form name=\"form_admin_modif\""
		." action=\"".$HTTP_SERVER_VARS["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
else
	echo "<form>\n";

// Autoriser visiteur à visiter cette formation
if ($oProjet->oFormationCourante->accessibleVisiteurs())
	$sVisiteurAutoriser = " checked";
else
	$sVisiteurAutoriser = NULL;

?>

<table border="0" cellspacing="0" cellpadding="5" width="100%">
<?php
selectionnerNumeroOrdre("ordre_formation",$oProjet->oFormationCourante->retNombreLignes(),$iNumeroOrdre,1);
entrerNom("nom_formation",$sNom);
selectionnerStatut("statut_formation",$oProjet->oFormationCourante->retListeStatuts(),$iStatut);
?>
<tr><td>&nbsp;</td><td><hr></td></tr>
<?php selectionnerType("formation_type",$oProjet->oFormationCourante->retTypes(),0); ?>
<tr>
<td>&nbsp;</td>
<td>
<fieldset>
<legend>&nbsp;<?=INTITULE_FORMATION?>&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<tr><td colspan="2"><span class="intitule">&nbsp;Modalit&eacute; d'inscription des &eacute;tudiants aux cours&nbsp;:&nbsp;</span></td></tr>
<tr>
<td>&nbsp;</td>
<td>
<input type="radio" name="INSCR_AUTO_MODULES" value="1"<?php echo ($bInscrAutoModules ? " checked" : NULL).($g_bModifier ? NULL : " disabled"); ?>>&nbsp;Tous les &eacute;tudiants sont automatiquement inscrits &agrave; tous les cours de cette formation
<br>
<input type="radio" name="INSCR_AUTO_MODULES" value="0"<?php echo ($bInscrAutoModules ? NULL : " checked").($g_bModifier ? NULL : " disabled"); ?>>&nbsp;Certains &eacute;tudiants seront inscrits &agrave; certains cours, d'autres pas
</td>
</tr>
<?php entrerDescription("descr_formation",$sDescription,NULL,urlencode(addslashes($sNom))); ?>
<tr>
<td>&nbsp;</td>
<td align="right"><input name="VISITEUR_AUTORISER" type="checkbox"<?=$sVisiteurAutoriser.($g_bModifier ? NULL : " disabled")?>>&nbsp;&nbsp;Accessible aux visiteurs</td>
</tr>
</table>
</fieldset>
</td>
</tr>
</table>
