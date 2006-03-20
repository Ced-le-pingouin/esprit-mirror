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
** Classe .................: econcept.liste.forms.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 23/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Rechercher les formations de l'utilisateur par rapport à son statut dominant
// ---------------------
$iNbrFormations = $oProjet->initFormationsUtilisateur(FALSE,FALSE);

// ---------------------
// Composer la liste des formations disponibles
// ---------------------
$sHtmlOptions = NULL;

if ($iNbrFormations > 0)
{
	for ($i=0; $i<$iNbrFormations; $i++)
	{
		$iIdForm  = $oProjet->aoFormations[$i]->retId();
		$sNomForm = $oProjet->aoFormations[$i]->retNom();
		
		$sOptionSelect = ($g_iFormation == $iIdForm ? " selected" : NULL);
		
		$sValeurOption = "?type=".TYPE_FORMATION."&params={$iIdForm}:0:0:0:0:0";
		
		$sHtmlOptions .= "<option"
			." value=\"{$sValeurOption}\""
			." title=\"{$sNomForm}\""
			." onmouseover=\"top.status(escape(this.title))\""
			." onmouseout=\"top.status('&nbsp;')\""
			.$sOptionSelect
			.">".htmlentities((strlen($sNomForm) > 23 ? sprintf("%.23s...",$sNomForm) : $sNomForm),ENT_COMPAT,"UTF-8")."</option>\n";
	}
}

$sSelectFormations = <<<BLOCK_SELECT_FORMATIONS
<select name="intitule_rubrique" onchange="ChangerFormation()" style="width: 190px;">
<option value="?type=0&params=0:0:0:0:0:0" title="S&eacute;lectionnez une formation" onmouseover="top.status(escape(this.title))" onmouseout="top.status('&nbsp;')">S&eacute;lectionnez une formation</option>
$sHtmlOptions
</select>
BLOCK_SELECT_FORMATIONS;

?>

