<?php

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
			.">".htmlentities((strlen($sNomForm) > 23 ? sprintf("%.23s...",$sNomForm) : $sNomForm))."</option>\n";
	}
}

$sSelectFormations = <<<BLOCK_SELECT_FORMATIONS
<select name="intitule_rubrique" onchange="ChangerFormation()" style="width: 190px;">
<option value="?type=0&params=0:0:0:0:0:0" title="S&eacute;lectionnez une formation" onmouseover="top.status(escape(this.title))" onmouseout="top.status('&nbsp;')">S&eacute;lectionnez une formation</option>
$sHtmlOptions
</select>
BLOCK_SELECT_FORMATIONS;

?>

