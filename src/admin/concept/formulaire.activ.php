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
** Fichier ................: form_activ.php
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
// Initialisations
// ---------------------
$ordre_activ = $oProjet->oActivCourante->retNumOrdre();
$nom_activ   = $oProjet->oActivCourante->retNom(TRUE);
$descr_activ = $oProjet->oActivCourante->retDescr();

// ---------------------
// Permissions
// ---------------------
$bPeutModifier = $oProjet->verifModifierModule();

$g_bModifier  = $oProjet->verifPermission("PERM_MOD_BLOC");
$g_bModifier &= $bPeutModifier;

$g_bModifierStatut  = $oProjet->verifPermission("PERM_MOD_STATUT_TOUS_COURS");
$g_bModifierStatut |= $oProjet->verifPermission("PERM_MOD_STATUT_BLOC");
$g_bModifierStatut &= $bPeutModifier;

unset($bPeutModifier);

// ---------------------
// ---------------------
afficherTitre(INTITULE_ACTIV,$nom_activ);

if ($g_bModifier || $g_bModifierStatut)
	echo "<form name=\"form_admin_modif\""
		." action=\"".$HTTP_SERVER_VARS["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
else
	echo "<form>\n";

?>
<table border="0" cellspacing="0" cellpadding="5" width="100%">
<?php
selectionnerNumeroOrdre("ORDRE",$oProjet->oActivCourante->retNombreLignes(),$ordre_activ,1);
entrerNom("NOM",$nom_activ);
selectionnerStatut("STATUT",retListeStatuts('M'),$oProjet->oActivCourante->retStatut());
//entrerDescription("DESCRIPTION",$descr_activ,NULL,urlencode(addslashes($nom_activ)));
?>
<tr><td>&nbsp;</td><td><hr></td></tr>
<?php selectionnerType("formation_type",$oProjet->oActivCourante->retTypes(),0); ?>
<tr>
<td>&nbsp;</td>
<td>
<fieldset>
<legend>&nbsp;<?=INTITULE_ACTIV?>&nbsp;</legend><br>
<table border="0" cellspacing="1" cellpadding="0">
<tr>
<td><img src="<?=dir_images_communes('/espacer.gif')?>" width="25" height="1" border="0"></td>
<td><span class="intitule">Modalit&eacute;&nbsp;:&nbsp;</span></td>
<td><?php echo selectionner_modalite($oProjet->oActivCourante->retListeModalites(),"MODALITE",$oProjet->oActivCourante->retModalite()); ?></td>
</tr>
</table>
</fieldset>
</td>
</tr>
</table>
